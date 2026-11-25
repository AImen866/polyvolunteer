<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VolunteerPlatformTest extends TestCase
{
    use RefreshDatabase;

    private function makeEvent(User $org, array $o = []): Event
    {
        $cat = Category::firstOrCreate(['name' => 'Форумы'], ['hours_coefficient' => 1.5, 'guests_per_volunteer' => 25]);

        return Event::forceCreate(array_merge([
            'organizer_id' => $org->id, 'category_id' => $cat->id, 'title' => 'Тестовое событие',
            'description' => 'Описание', 'location' => 'Корпус', 'starts_at' => now()->addDays(3),
            'ends_at' => now()->addDays(3)->addHours(8), 'capacity' => 2, 'status' => 'published',
        ], $o));
    }

    public function test_public_pages_open(): void
    {
        $org = User::factory()->create(['role' => 'organizer']);
        $event = $this->makeEvent($org);
        $article = $org->articles()->create(['title' => 'Т', 'excerpt' => 'к', 'body' => 'текст', 'published_at' => now()]);

        foreach (['/', '/events', '/articles', '/calculator', '/login', '/register', "/events/{$event->id}", "/articles/{$article->id}"] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/events/search?q=Тест')->assertOk()->assertJsonCount(1);
    }

    public function test_registration_and_login(): void
    {
        $this->post('/register', [
            'name' => 'Иван Иванов', 'email' => 'i@test.ru', 'password' => 'secret1', 'password_confirmation' => 'secret1', 'role' => 'volunteer',
        ])->assertRedirect('/profile');
        $this->assertAuthenticated();
        $this->post('/logout');
        $this->post('/login', ['email' => 'i@test.ru', 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/login', ['email' => 'i@test.ru', 'password' => 'secret1'])->assertRedirect('/profile');
    }

    public function test_cannot_self_register_as_admin(): void
    {
        $this->post('/register', [
            'name' => 'Хакер', 'email' => 'h@test.ru', 'password' => 'secret1', 'password_confirmation' => 'secret1', 'role' => 'admin',
        ])->assertSessionHasErrors('role');
    }

    public function test_volunteer_applies_and_capacity_is_respected(): void
    {
        $org = User::factory()->create(['role' => 'organizer']);
        $event = $this->makeEvent($org, ['capacity' => 1]);
        [$a, $b] = [User::factory()->create(['role' => 'volunteer']), User::factory()->create(['role' => 'volunteer'])];

        $this->actingAs($a)->post("/events/{$event->id}/register", ['motivation' => 'Хочу'])->assertSessionHas('status');
        $this->actingAs($b)->post("/events/{$event->id}/register")->assertSessionHas('error');
        $this->actingAs($a)->post("/events/{$event->id}/register")->assertSessionHas('error');
        $this->assertDatabaseCount('registrations', 1);

        $this->actingAs($a)->delete("/events/{$event->id}/register");
        $this->assertDatabaseCount('registrations', 0);
    }

    public function test_organizer_cannot_register_and_cannot_edit_foreign_event(): void
    {
        $org = User::factory()->create(['role' => 'organizer']);
        $other = User::factory()->create(['role' => 'organizer']);
        $event = $this->makeEvent($org);

        $this->actingAs($org)->post("/events/{$event->id}/register")->assertForbidden();
        $this->actingAs($other)->get("/organizer/events/{$event->id}/edit")->assertForbidden();
        $this->actingAs($other)->delete("/organizer/events/{$event->id}")->assertForbidden();
    }

    public function test_organizer_creates_event_with_validation(): void
    {
        $org = User::factory()->create(['role' => 'organizer']);
        $cat = Category::create(['name' => 'Спорт', 'hours_coefficient' => 1.2, 'guests_per_volunteer' => 40]);

        $this->actingAs($org)->post('/organizer/events', ['title' => ''])->assertSessionHasErrors(['title', 'starts_at', 'capacity']);
        $this->actingAs($org)->post('/organizer/events', [
            'title' => 'Забег', 'category_id' => $cat->id, 'description' => 'Д', 'location' => 'Парк',
            'starts_at' => now()->addDay()->format('Y-m-d H:i'), 'ends_at' => now()->addDay()->subHour()->format('Y-m-d H:i'),
            'capacity' => 5, 'status' => 'published',
        ])->assertSessionHasErrors('ends_at');
        $this->actingAs($org)->post('/organizer/events', [
            'title' => 'Забег', 'category_id' => $cat->id, 'description' => 'Д', 'location' => 'Парк',
            'starts_at' => now()->addDay()->format('Y-m-d H:i'), 'ends_at' => now()->addDay()->addHours(3)->format('Y-m-d H:i'),
            'capacity' => 5, 'status' => 'published',
        ])->assertRedirect();
        $this->assertDatabaseHas('events', ['title' => 'Забег', 'organizer_id' => $org->id]);
    }

    public function test_hours_and_points_are_calculated_after_attendance(): void
    {
        $org = User::factory()->create(['role' => 'organizer']);
        $vol = User::factory()->create(['role' => 'volunteer']);
        $event = $this->makeEvent($org, ['starts_at' => now()->subHours(9), 'ends_at' => now()->subHour()]);
        $reg = $event->registrations()->create(['user_id' => $vol->id, 'status' => 'approved']);

        $this->assertSame(8.0, $event->hours);
        $this->assertSame(12.0, $event->points);

        $this->actingAs($org)->patch("/organizer/registrations/{$reg->id}", ['status' => 'attended'])->assertSessionHas('status');
        $this->assertSame(['events' => 1, 'hours' => 8.0, 'points' => 12.0], $vol->volunteerStats());
    }

    public function test_calculator(): void
    {
        $cat = Category::create(['name' => 'Форум', 'hours_coefficient' => 1.5, 'guests_per_volunteer' => 25]);
        // 200 гостей / 25 = 8; резерв ceil(1.2) = 2; смен ceil(8/6) = 2; итого (8+2)*2 = 20; баллы 8*1.5 = 12
        $this->postJson('/calculator', ['category_id' => $cat->id, 'guests' => 200, 'hours' => 8])
            ->assertOk()->assertJson(['base' => 8, 'reserve' => 2, 'shifts' => 2, 'total' => 20, 'points' => 12]);
        $this->postJson('/calculator', ['category_id' => $cat->id, 'guests' => 0, 'hours' => 8])->assertUnprocessable();
    }

    public function test_admin_area_is_protected(): void
    {
        $vol = User::factory()->create(['role' => 'volunteer']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->get('/admin')->assertRedirect('/login');
        $this->actingAs($vol)->get('/admin')->assertForbidden();
        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/users')->assertOk();
        $this->actingAs($admin)->get('/admin/articles')->assertOk();
        $this->actingAs($admin)->get('/admin/categories')->assertOk();
        $this->actingAs($admin)->get('/admin/events')->assertOk();
        $this->actingAs($admin)->post('/admin/articles', ['title' => 'Н', 'excerpt' => 'к', 'body' => 'т', 'publish' => 1])->assertRedirect('/admin/articles');
        $this->assertNotNull(Article::first()->published_at);
    }

    public function test_profile_pages_and_update(): void
    {
        $vol = User::factory()->create(['role' => 'volunteer']);
        $org = User::factory()->create(['role' => 'organizer']);
        $this->actingAs($vol)->get('/profile')->assertOk();
        $this->actingAs($org)->get('/profile')->assertOk();
        $this->actingAs($vol)->get('/profile/edit')->assertOk();
        $this->actingAs($vol)->put('/profile', ['name' => 'Новое Имя', 'email' => $vol->email, 'phone' => 'abc'])->assertSessionHasErrors('phone');
        $this->actingAs($vol)->put('/profile', ['name' => 'Новое Имя', 'email' => $vol->email, 'faculty' => 'ИТ'])->assertRedirect('/profile');
        $this->assertSame('Новое Имя', $vol->fresh()->name);
    }
}
