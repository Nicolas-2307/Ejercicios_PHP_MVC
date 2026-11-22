<h1>Historial de sesiones</h1>
<p class="subtitle">Tiempos totales y vueltas registradas en cada sesión guardada.</p>
<?php if (!$sessions): ?><div class="card empty">No hay sesiones guardadas.</div><?php endif; ?>
<?php foreach ($sessions as $s): $st = Session::stats($s['laps']); ?>
    <div class="card">
        <div class="row" style="justify-content:space-between;align-items:center">
            <div style="flex:2"><h2 style="margin:0"><?= e($s['name']) ?></h2><span class="muted"><?= e($s['date']) ?></span></div>
            <div><div class="muted">Tiempo total</div><div class="stat" style="font-family:Consolas,monospace;font-size:1.6rem"><?= Session::format($s['total_ms']) ?></div></div>
            <div><div class="muted">Vueltas</div><div class="stat" style="font-size:1.6rem"><?= count($s['laps']) ?></div></div>
            <form method="post" action="index.php?action=delete" onsubmit="return confirm('¿Eliminar la sesión?')">
                <input type="hidden" name="id" value="<?= $s['id'] ?>"><button class="btn btn-sm btn-danger">🗑</button></form>
        </div>
        <?php if ($s['laps']): ?>
            <div class="muted" style="margin:12px 0 6px;font-size:.9rem">
                Mejor: <b style="color:var(--ok)"><?= Session::format($st['best']) ?></b> ·
                Peor: <b style="color:var(--err)"><?= Session::format($st['worst']) ?></b> ·
                Promedio: <b><?= Session::format($st['avg']) ?></b></div>
            <table><tr><th>Vuelta</th><th>Tiempo</th><th>Acumulado</th></tr>
                <?php $acc = 0; foreach ($s['laps'] as $i => $l): $acc += $l; ?>
                    <tr><td><?= $i + 1 ?></td><td><code><?= Session::format($l) ?></code></td><td><code><?= Session::format($acc) ?></code></td></tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
