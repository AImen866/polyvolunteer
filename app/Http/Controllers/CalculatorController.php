<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    public function index()
    {
        return view('calculator', ['categories' => Category::orderBy('name')->get()]);
    }

    /**
     * Расчёт нужного числа волонтёров.
     * N = ceil(гости / норма категории); резерв = ceil(N * 0.15);
     * смены = ceil(часы / 6); итого = (N + резерв) * смены, баллы одного волонтёра = часы * коэффициент.
     */
    public function calculate(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'guests' => 'required|integer|min:1|max:10000',
            'hours' => 'required|numeric|min:0.5|max:24',
        ]);

        $category = Category::findOrFail($data['category_id']);
        $base = (int) ceil($data['guests'] / $category->guests_per_volunteer);
        $reserve = (int) ceil($base * 0.15);
        $shifts = (int) ceil($data['hours'] / 6);

        return response()->json([
            'norm' => $category->guests_per_volunteer,
            'base' => $base,
            'reserve' => $reserve,
            'shifts' => $shifts,
            'total' => ($base + $reserve) * $shifts,
            'points' => round($data['hours'] * (float) $category->hours_coefficient, 1),
        ]);
    }
}
