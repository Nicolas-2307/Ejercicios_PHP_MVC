<h1>Próximos eventos</h1>
<p class="subtitle">Eventos futuros ordenados por fecha, con su recordatorio programado.</p>
<div class="card">
    <?php if (!$rows): ?><div class="empty">No hay eventos próximos.</div><?php else: ?>
    <table>
        <tr><th>Fecha</th><th>Hora</th><th>Evento</th><th>Categoría</th><th>Recordatorio</th><th></th></tr>
        <?php foreach ($rows as $ev): [$cn, $cc] = $categories[$ev['category']]; ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($ev['date'])) ?></td>
                <td><?= e($ev['time']) ?></td>
                <td><strong><?= e($ev['title']) ?></strong><br><small class="muted"><?= e($ev['description']) ?></small></td>
                <td><span class="badge" style="background:<?= $cc ?>22;color:<?= $cc ?>"><?= $cn ?></span></td>
                <td><?= $ev['reminder'] ? '🔔 ' . Event::REMINDERS[$ev['reminder']] : '<span class="muted">—</span>' ?></td>
                <td><a class="btn btn-sm btn-outline" href="index.php?action=edit&id=<?= $ev['id'] ?>">Editar</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/_notify.php'; ?>
