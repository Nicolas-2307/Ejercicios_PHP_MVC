<div class="game-wrap">
    <div>
        <div class="card hud">
            <div><span class="muted">Jugador</span><strong><?= e($player) ?></strong></div>
            <div><span class="muted">Nivel</span><strong><?= $cfg['name'] ?></strong></div>
            <div><span class="muted">Movimientos</span><strong id="moves">0</strong></div>
            <div><span class="muted">Parejas</span><strong><span id="pairs">0</span>/<?= $cfg['pairs'] ?></strong></div>
            <div><span class="muted">Tiempo</span><strong id="time">0 s</strong></div>
            <a class="btn btn-sm btn-outline" href="index.php?action=play&level=<?= $level ?>">↻ Reiniciar</a>
        </div>
        <div class="board" style="grid-template-columns:repeat(<?= $cfg['cols'] ?>,1fr)" id="board">
            <?php foreach ($cards as $i => $c): ?>
                <button class="mcard" data-value="<?= e($c) ?>" data-i="<?= $i ?>">
                    <span class="front"><?= e($c) ?></span><span class="back">?</span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
    <aside class="card">
        <h3>🏆 Top 5 — <?= $cfg['name'] ?></h3>
        <?php if (!$top): ?><p class="muted">Sé el primero en el ranking.</p><?php endif; ?>
        <ol class="top"><?php foreach ($top as $t): ?><li><span><?= e($t['player']) ?></span><strong><?= $t['score'] ?></strong></li><?php endforeach; ?></ol>
    </aside>
</div>

<div class="modal" id="win">
    <div class="card" style="text-align:center;max-width:420px">
        <div style="font-size:3.5rem">🎉</div>
        <h1>¡Ganaste!</h1>
        <p id="winText" class="muted" style="margin:8px 0"></p>
        <div class="stat" id="winScore">0</div><div class="muted">puntos</div>
        <p id="winRank" style="margin:10px 0;font-weight:600"></p>
        <div class="actions" style="justify-content:center;margin-top:14px">
            <a class="btn" href="index.php?action=play&level=<?= $level ?>">Jugar otra vez</a>
            <a class="btn btn-outline" href="index.php">Cambiar nivel</a>
            <a class="btn btn-outline" href="index.php?action=scores">Ver puntajes</a>
        </div>
    </div>
</div>

<style>
.game-wrap{display:grid;grid-template-columns:1fr 250px;gap:22px;align-items:start}
.hud{display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap}
.hud div{display:flex;flex-direction:column}.hud strong{font-size:1.2rem}
.board{display:grid;gap:10px;max-width:<?= $cfg['cols'] * 110 ?>px;margin:0 auto}
.mcard{aspect-ratio:1;border:none;background:none;perspective:600px;cursor:pointer;position:relative;padding:0}
.mcard span{position:absolute;inset:0;display:grid;place-items:center;border-radius:12px;backface-visibility:hidden;transition:transform .4s;font-size:2.4rem}
.back{background:linear-gradient(135deg,var(--accent),#14b8a6);color:#fff;font-weight:700;box-shadow:0 3px 8px rgba(0,0,0,.15)}
.front{background:#fff;transform:rotateY(180deg);box-shadow:0 3px 8px rgba(0,0,0,.12)}
.mcard.open .back{transform:rotateY(180deg)}.mcard.open .front{transform:rotateY(0)}
.mcard.done .front{background:#ccfbf1;outline:3px solid var(--accent)}
.top{padding-left:20px}.top li{padding:6px 0}.top li span{display:inline-block;width:110px}
.modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);place-items:center;z-index:50}
.modal.show{display:grid}
</style>

<script>
// Lógica del juego en el cliente; el puntaje se calcula y guarda en el servidor (modelo)
const LEVEL = <?= json_encode($level) ?>, PAIRS = <?= (int)$cfg['pairs'] ?>;
let first = null, lock = false, moves = 0, pairs = 0, seconds = 0, timer = null;
const $ = id => document.getElementById(id);

document.querySelectorAll('.mcard').forEach(card => card.addEventListener('click', () => {
    if (lock || card.classList.contains('open')) return;
    if (!timer) timer = setInterval(() => { seconds++; $('time').textContent = seconds + ' s'; }, 1000);
    card.classList.add('open');
    if (!first) { first = card; return; }
    moves++; $('moves').textContent = moves;
    if (first.dataset.value === card.dataset.value) {
        first.classList.add('done'); card.classList.add('done');
        first = null; pairs++; $('pairs').textContent = pairs;
        if (pairs === PAIRS) finish();
    } else {
        lock = true;
        const a = first; first = null;
        setTimeout(() => { a.classList.remove('open'); card.classList.remove('open'); lock = false; }, 800);
    }
}));

async function finish() {
    clearInterval(timer);
    const res = await fetch('index.php?action=saveScore', {
        method: 'POST', headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({level: LEVEL, moves, seconds})
    });
    const d = await res.json();
    $('winText').textContent = `Completaste el nivel en ${moves} movimientos y ${seconds} segundos.`;
    $('winScore').textContent = d.score;
    $('winRank').textContent = `Posición #${d.rank} en el ranking de este nivel`;
    setTimeout(() => $('win').classList.add('show'), 500);
}
</script>
