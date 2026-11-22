<h1>Reservas registradas</h1>
<p class="subtitle">Consulta el estado de las reservas o cancélalas para liberar el horario.</p>
<div class="card">
    <?php if (!$rows): ?><div class="empty">No hay reservas todavía.</div><?php else: ?>
    <table>
        <tr><th>Código</th><th>Servicio</th><th>Fecha</th><th>Hora</th><th>Cliente</th><th>Estado</th><th></th></tr>
        <?php foreach ($rows as $r): $s = $services[$r['service_id']];
            $cls = ['confirmada' => 'badge-ok', 'pendiente' => 'badge-warn', 'cancelada' => 'badge-err'][$r['status']]; ?>
            <tr>
                <td><a href="index.php?action=review&id=<?= $r['id'] ?>"><strong><?= e($r['code']) ?></strong></a></td>
                <td><?= $s['icon'] ?> <?= e($s['name']) ?></td>
                <td><?= date('d/m/Y', strtotime($r['date'])) ?></td>
                <td><?= e($r['time']) ?></td>
                <td><?= e($r['name']) ?><br><small class="muted"><?= e($r['email']) ?></small></td>
                <td><span class="badge <?= $cls ?>"><?= ucfirst($r['status']) ?></span></td>
                <td><?php if ($r['status'] !== 'cancelada'): ?>
                    <form method="post" action="index.php?action=cancel" onsubmit="return confirm('¿Cancelar la reserva?')">
                        <input type="hidden" name="id" value="<?= $r['id'] ?>"><button class="btn btn-sm btn-danger">Cancelar</button></form>
                <?php endif; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>
