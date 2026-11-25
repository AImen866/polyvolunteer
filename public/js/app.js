document.addEventListener('DOMContentLoaded', () => {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;

  // 1. Живой поиск мероприятий (афиша)
  const filterForm = document.getElementById('event-filters');
  const live = document.getElementById('live-results');
  if (filterForm && live) {
    let timer;
    const run = async () => {
      const params = new URLSearchParams(new FormData(filterForm));
      if (![...params.values()].some(v => v)) { live.classList.add('hide'); return; }
      const res = await fetch(filterForm.dataset.search + '?' + params, { headers: { Accept: 'application/json' } });
      const items = await res.json();
      live.classList.remove('hide');
      live.innerHTML = items.length
        ? items.map(e => `<li><a href="${e.url}"><b>${e.title}</b></a><span class="muted">${e.date}, ${e.location} · мест: ${e.free}</span></li>`).join('')
        : '<li class="muted">Ничего не найдено</li>';
    };
    filterForm.querySelectorAll('input,select').forEach(el =>
      el.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(run, 250); }));
  }

  // 2. Обратный отсчёт до начала мероприятия
  const cd = document.getElementById('countdown');
  if (cd) {
    const target = new Date(cd.dataset.start).getTime();
    const tick = () => {
      let s = Math.max(0, Math.floor((target - Date.now()) / 1000));
      const d = Math.floor(s / 86400); s %= 86400;
      const h = Math.floor(s / 3600); s %= 3600;
      const m = Math.floor(s / 60); s %= 60;
      cd.querySelector('[data-d]').textContent = d;
      cd.querySelector('[data-h]').textContent = String(h).padStart(2, '0');
      cd.querySelector('[data-m]').textContent = String(m).padStart(2, '0');
      cd.querySelector('[data-s]').textContent = String(s).padStart(2, '0');
    };
    tick(); setInterval(tick, 1000);
  }

  // 3. Калькулятор волонтёров
  const calc = document.getElementById('calc-form');
  if (calc) {
    const out = document.getElementById('calc-result');
    const run = async () => {
      const res = await fetch(calc.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
        body: new FormData(calc),
      });
      if (!res.ok) { out.classList.add('hide'); return; }
      const r = await res.json();
      out.classList.remove('hide');
      out.querySelector('[data-total]').textContent = r.total;
      out.querySelector('[data-detail]').innerHTML =
        `Норма категории: 1 волонтёр на ${r.norm} гостей.<br>Основной состав: ${r.base}, резерв 15%: ${r.reserve}, смен: ${r.shifts}.<br>Баллы каждому волонтёру: ${r.points}.`;
    };
    calc.addEventListener('input', run);
    calc.addEventListener('submit', e => { e.preventDefault(); run(); });
  }

  // 4. Форма регистрации: поле «Организация» только для организатора
  const role = document.getElementById('role');
  const org = document.getElementById('org-box');
  const vol = document.getElementById('vol-box');
  if (role && org) {
    const sync = () => {
      org.classList.toggle('hide', role.value !== 'organizer');
      vol?.classList.toggle('hide', role.value !== 'volunteer');
    };
    role.addEventListener('change', sync); sync();
  }

  // 5. Подтверждение опасных действий
  document.querySelectorAll('form[data-confirm]').forEach(f =>
    f.addEventListener('submit', e => { if (!confirm(f.dataset.confirm)) e.preventDefault(); }));
});
