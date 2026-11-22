<div class="sw-wrap">
    <div class="card watch">
        <div class="ring" id="ring">
            <div class="display" id="display">00:00:00<span>.00</span></div>
            <div class="muted" id="status">Detenido</div>
        </div>
        <div class="controls">
            <button class="btn btn-ok big" id="btnStart">▶ Iniciar</button>
            <button class="btn btn-gray big" id="btnLap" disabled>⚑ Vuelta</button>
            <button class="btn btn-danger big" id="btnReset" disabled>↺ Reiniciar</button>
        </div>
        <div class="save-row">
            <input type="text" id="sessionName" placeholder="Nombre de la sesión (opcional)" maxlength="50">
            <button class="btn" id="btnSave" disabled>💾 Guardar sesión</button>
        </div>
        <div id="msg"></div>
        <p class="muted" style="font-size:.8rem;margin-top:10px">Atajos: <kbd>Espacio</kbd> iniciar/pausar · <kbd>V</kbd> vuelta · <kbd>R</kbd> reiniciar</p>
    </div>

    <div class="card">
        <h2>Vueltas / tiempos parciales</h2>
        <div id="lapStats" class="lap-stats"></div>
        <table>
            <thead><tr><th>#</th><th>Tiempo de vuelta</th><th>Tiempo total</th></tr></thead>
            <tbody id="laps"><tr><td colspan="3" class="empty">Presiona “Vuelta” mientras corre el cronómetro.</td></tr></tbody>
        </table>
    </div>
</div>

<?php if ($recent): ?>
<div class="card">
    <h3>Últimas sesiones guardadas</h3>
    <table><tr><th>Sesión</th><th>Total</th><th>Vueltas</th><th>Fecha</th></tr>
        <?php foreach ($recent as $s): ?>
            <tr><td><?= e($s['name']) ?></td><td><code><?= Session::format($s['total_ms']) ?></code></td><td><?= count($s['laps']) ?></td><td><?= e($s['date']) ?></td></tr>
        <?php endforeach; ?>
    </table>
    <a href="index.php?action=history" style="display:inline-block;margin-top:10px">Ver historial completo →</a>
</div>
<?php endif; ?>

<style>
.sw-wrap{display:grid;grid-template-columns:1.1fr 1fr;gap:22px;align-items:start}
.watch{text-align:center}
.ring{width:290px;height:290px;margin:10px auto 22px;border-radius:50%;display:flex;flex-direction:column;justify-content:center;align-items:center;
      background:radial-gradient(circle,#fff 62%,transparent 63%),conic-gradient(#0ea5e9 var(--p,0%),#e2e8f0 0);transition:background .1s}
.display{font-family:Consolas,"Courier New",monospace;font-size:2.6rem;font-weight:700;color:var(--accent)}
.display span{font-size:1.6rem;color:#0ea5e9}
.controls{display:flex;gap:10px;justify-content:center}
.big{padding:14px 22px;font-size:1.05rem}
.btn:disabled{opacity:.45;cursor:not-allowed}
.save-row{display:flex;gap:10px;margin-top:20px}
.lap-stats{display:flex;gap:12px;margin-bottom:10px;flex-wrap:wrap}
.lap-stats div{background:#f1f5f9;border-radius:8px;padding:8px 12px;font-size:.85rem}
tr.best td{color:var(--ok);font-weight:700}tr.worst td{color:var(--err);font-weight:700}
kbd{background:#e2e8f0;border-radius:4px;padding:1px 6px;font-size:.75rem}
</style>

<script>
// ===== Lógica del cronómetro (cliente). La persistencia la hace el controlador (action=save) =====
const $ = id => document.getElementById(id);
let running = false, startTime = 0, elapsed = 0, raf = null, laps = [], lastLapTotal = 0;

function fmt(ms) {
    const cs = Math.floor(ms % 1000 / 10), s = Math.floor(ms / 1000);
    const p = n => String(n).padStart(2, '0');
    return [p(Math.floor(s / 3600)) + ':' + p(Math.floor(s % 3600 / 60)) + ':' + p(s % 60), '.' + p(cs)];
}
function now() { return running ? elapsed + (performance.now() - startTime) : elapsed; }
function draw() {
    const t = now(), [main, cs] = fmt(t);
    $('display').innerHTML = main + '<span>' + cs + '</span>';
    $('ring').style.setProperty('--p', (t % 60000) / 600 + '%'); // progreso del minuto
    if (running) raf = requestAnimationFrame(draw);
}
function startPause() {
    if (!running) {
        running = true; startTime = performance.now(); draw();
        $('btnStart').textContent = '⏸ Pausar'; $('btnStart').className = 'btn big'; $('status').textContent = 'En marcha';
    } else {
        elapsed = now(); running = false; cancelAnimationFrame(raf); draw();
        $('btnStart').textContent = '▶ Continuar'; $('btnStart').className = 'btn btn-ok big'; $('status').textContent = 'En pausa';
    }
    $('btnLap').disabled = !running; $('btnReset').disabled = false; $('btnSave').disabled = now() === 0;
}
function lap() {
    if (!running) return;
    const total = Math.round(now());
    laps.push(total - lastLapTotal); lastLapTotal = total;
    renderLaps();
}
function reset() {
    running = false; cancelAnimationFrame(raf); elapsed = 0; laps = []; lastLapTotal = 0; draw(); renderLaps();
    $('btnStart').textContent = '▶ Iniciar'; $('btnStart').className = 'btn btn-ok big'; $('status').textContent = 'Detenido';
    $('btnLap').disabled = $('btnReset').disabled = $('btnSave').disabled = true;
    $('msg').innerHTML = ''; $('sessionName').value = '';
}
function renderLaps() {
    if (!laps.length) {
        $('laps').innerHTML = '<tr><td colspan="3" class="empty">Presiona “Vuelta” mientras corre el cronómetro.</td></tr>';
        $('lapStats').innerHTML = ''; return;
    }
    const best = Math.min(...laps), worst = Math.max(...laps);
    let acc = 0, rows = laps.map((l, i) => {
        acc += l;
        const cls = laps.length > 1 ? (l === best ? 'best' : l === worst ? 'worst' : '') : '';
        return `<tr class="${cls}"><td>${i + 1}</td><td><code>${fmt(l).join('')}</code></td><td><code>${fmt(acc).join('')}</code></td></tr>`;
    });
    $('laps').innerHTML = rows.reverse().join('');
    const avg = laps.reduce((a, b) => a + b, 0) / laps.length;
    $('lapStats').innerHTML = `<div>🟢 Mejor: <b>${fmt(best).join('')}</b></div><div>🔴 Peor: <b>${fmt(worst).join('')}</b></div><div>⌀ Promedio: <b>${fmt(avg).join('')}</b></div>`;
}
async function save() {
    const res = await fetch('index.php?action=save', {
        method: 'POST', headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({name: $('sessionName').value, total: Math.round(now()), laps})
    });
    const d = await res.json();
    $('msg').innerHTML = d.ok
        ? `<div class="alert alert-ok" style="margin:14px 0 0">✔ Sesión “${d.name}” guardada. <a href="index.php?action=history">Ver historial</a></div>`
        : `<div class="alert alert-err" style="margin:14px 0 0">${d.errors.join(' ')}</div>`;
}
$('btnStart').onclick = startPause; $('btnLap').onclick = lap; $('btnReset').onclick = reset; $('btnSave').onclick = save;
document.addEventListener('keydown', e => {
    if (e.target.tagName === 'INPUT') return;
    if (e.code === 'Space') { e.preventDefault(); startPause(); }
    if (e.key.toLowerCase() === 'v') lap();
    if (e.key.toLowerCase() === 'r') reset();
});
</script>
