<h1>🏆 Mejores puntajes</h1>
<p class="subtitle">Ranking por nivel de dificultad (puntaje calculado en el servidor).</p>
<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(340px,1fr))">
    <?php foreach ($levels as $k => $l): ?>
        <div class="card">
            <h2><?= $l['name'] ?></h2>
            <?php if (!$tops[$k]): ?><div class="empty">Sin partidas aún.</div><?php else: ?>
            <table>
                <tr><th>#</th><th>Jugador</th><th>Mov.</th><th>Tiempo</th><th>Puntos</th></tr>
                <?php foreach ($tops[$k] as $i => $t): ?>
                    <tr><td><?= ['🥇','🥈','🥉'][$i] ?? $i + 1 ?></td><td><?= e($t['player']) ?></td><td><?= $t['moves'] ?></td><td><?= $t['seconds'] ?> s</td><td><strong><?= $t['score'] ?></strong></td></tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>
            <a class="btn btn-sm btn-outline" style="margin-top:12px" href="index.php?action=play&level=<?= $k ?>">Jugar <?= $l['name'] ?></a>
        </div>
    <?php endforeach; ?>
</div>
