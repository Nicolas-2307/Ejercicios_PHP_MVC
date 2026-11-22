<h1>Historial de cálculos</h1>
<p class="subtitle">Cálculos guardados con el botón “Calcular y guardar”.</p>
<div class="card">
    <?php if (!$rows): ?>
        <div class="empty">Aún no hay cálculos guardados.</div>
    <?php else: ?>
        <table>
            <tr><th>Fecha</th><th>Cuenta</th><th>%</th><th>Propina</th><th>Total</th><th>Personas</th><th>Por persona</th></tr>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= e($r['date']) ?></td><td><?= money($r['amount']) ?></td><td><?= e($r['percent']) ?>%</td>
                    <td><?= money($r['tip']) ?></td><td><strong><?= money($r['total']) ?></strong></td>
                    <td><?= e($r['people']) ?></td><td><?= money($r['per_person']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <form method="post" action="index.php?action=clear" style="margin-top:16px">
            <button class="btn btn-sm btn-danger">Borrar historial</button></form>
    <?php endif; ?>
</div>
