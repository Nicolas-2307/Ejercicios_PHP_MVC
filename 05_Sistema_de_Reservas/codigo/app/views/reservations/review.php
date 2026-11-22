<?php $confirmed = $r['status'] === 'confirmada'; ?>
<div class="card" style="max-width:620px;margin:0 auto">
    <div style="text-align:center;margin-bottom:18px">
        <div style="font-size:3rem"><?= $confirmed ? '🎉' : ($r['status'] === 'cancelada' ? '❌' : '📝') ?></div>
        <h1><?= $confirmed ? 'Reserva confirmada' : ($r['status'] === 'cancelada' ? 'Reserva cancelada' : 'Revisa y confirma tu reserva') ?></h1>
        <p class="muted">Código de reserva: <strong style="font-size:1.2rem;color:var(--accent)"><?= e($r['code']) ?></strong></p>
    </div>
    <table>
        <tr><th>Servicio</th><td><?= $service['icon'] ?> <?= e($service['name']) ?></td></tr>
        <tr><th>Fecha</th><td><?= date('d/m/Y', strtotime($r['date'])) ?></td></tr>
        <tr><th>Hora</th><td><?= e($r['time']) ?> (<?= $service['duration'] ?> min)</td></tr>
        <tr><th>Valor</th><td><?= money($service['price']) ?></td></tr>
        <tr><th>Cliente</th><td><?= e($r['name']) ?></td></tr>
        <tr><th>Correo</th><td><?= e($r['email']) ?></td></tr>
        <?php if ($r['phone']): ?><tr><th>Teléfono</th><td><?= e($r['phone']) ?></td></tr><?php endif; ?>
        <tr><th>Estado</th><td><span class="badge <?= $confirmed ? 'badge-ok' : 'badge-warn' ?>"><?= ucfirst($r['status']) ?></span></td></tr>
    </table>
    <div class="actions" style="margin-top:20px;justify-content:center">
        <?php if ($r['status'] === 'pendiente'): ?>
            <form method="post" action="index.php?action=confirm"><input type="hidden" name="id" value="<?= $r['id'] ?>">
                <button class="btn btn-ok">✔ Confirmar reserva</button></form>
            <form method="post" action="index.php?action=cancel"><input type="hidden" name="id" value="<?= $r['id'] ?>">
                <button class="btn btn-gray">Cancelar</button></form>
        <?php else: ?>
            <a class="btn" href="index.php">Hacer otra reserva</a>
            <a class="btn btn-outline" href="index.php?action=list">Ver mis reservas</a>
        <?php endif; ?>
    </div>
</div>
