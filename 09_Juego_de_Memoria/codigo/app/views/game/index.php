<div style="text-align:center;margin-bottom:24px">
    <h1>🧠 Juego de Memoria</h1>
    <p class="subtitle">Voltea las cartas y encuentra todas las parejas con la menor cantidad de movimientos y en el menor tiempo.</p>
</div>
<form method="post" action="index.php?action=play" class="card" style="max-width:820px;margin:0 auto">
    <div class="field" style="max-width:360px;margin:0 auto 22px">
        <label for="player">Tu nombre</label>
        <input type="text" id="player" name="player" maxlength="20" value="<?= e($player) ?>" placeholder="Jugador">
    </div>
    <div class="grid grid-3">
        <?php foreach ($levels as $k => $l): ?>
            <button class="level" name="level" value="<?= $k ?>">
                <div class="lv-name"><?= $l['name'] ?></div>
                <div><?= $l['pairs'] * 2 ?> cartas · <?= $l['pairs'] ?> parejas</div>
                <div class="muted">Tiempo objetivo: <?= $l['time'] ?> s</div>
                <div class="muted">Puntaje × <?= $l['mult'] ?></div>
            </button>
        <?php endforeach; ?>
    </div>
</form>
<style>
.level{border:2px solid var(--line);background:#fff;border-radius:14px;padding:22px;cursor:pointer;font:inherit;display:flex;flex-direction:column;gap:6px;transition:.15s}
.level:hover{border-color:var(--accent);transform:translateY(-3px);box-shadow:0 8px 20px rgba(13,148,136,.15)}
.lv-name{font-size:1.4rem;font-weight:700;color:var(--accent)}
</style>
