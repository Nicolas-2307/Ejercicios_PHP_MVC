<?php
$meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
$first = mktime(0, 0, 0, $month, 1, $year);
$daysInMonth = (int)date('t', $first);
$startDow = (int)date('N', $first); // 1 = lunes
$today = date('Y-m-d');
?>
<?php if ($reminders): ?>
    <div class="alert alert-warn">🔔 <strong>Recordatorios activos:</strong>
        <?php foreach ($reminders as $r): ?>
            <span style="margin-left:8px">“<?= e($r['title']) ?>” (<?= date('d/m', strtotime($r['date'])) ?> <?= e($r['time']) ?>)</span>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="cal-head">
        <a class="btn btn-outline btn-sm" href="index.php?y=<?= $year ?>&m=<?= $month - 1 ?>">← Anterior</a>
        <h1><?= $meses[$month] ?> <?= $year ?></h1>
        <div class="actions">
            <a class="btn btn-outline btn-sm" href="index.php">Hoy</a>
            <a class="btn btn-outline btn-sm" href="index.php?y=<?= $year ?>&m=<?= $month + 1 ?>">Siguiente →</a>
        </div>
    </div>
    <div class="cal">
        <?php foreach (['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $d): ?><div class="dow"><?= $d ?></div><?php endforeach; ?>
        <?php for ($i = 1; $i < $startDow; $i++): ?><div class="cell empty-cell"></div><?php endfor; ?>
        <?php for ($d = 1; $d <= $daysInMonth; $d++):
            $date = sprintf('%04d-%02d-%02d', $year, $month, $d); ?>
            <div class="cell <?= $date === $today ? 'today' : '' ?>">
                <div class="cell-top">
                    <span class="num"><?= $d ?></span>
                    <a class="add" href="index.php?action=create&date=<?= $date ?>" title="Agregar evento">+</a>
                </div>
                <?php foreach ($events[$date] ?? [] as $ev): $color = $categories[$ev['category']][1]; ?>
                    <a class="ev" href="index.php?action=edit&id=<?= $ev['id'] ?>" style="background:<?= $color ?>" title="<?= e($ev['description']) ?>">
                        <?= $ev['reminder'] ? '🔔' : '' ?> <?= e($ev['time']) ?> <?= e($ev['title']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endfor; ?>
    </div>
    <div class="legend">
        <?php foreach ($categories as [$name, $color]): ?><span><i style="background:<?= $color ?>"></i><?= $name ?></span><?php endforeach; ?>
        <span class="muted">· Clic en “+” para agregar · Clic en un evento para editarlo o eliminarlo</span>
    </div>
</div>

<?php require __DIR__ . '/_notify.php'; ?>

<style>
.cal-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
.cal{display:grid;grid-template-columns:repeat(7,1fr);gap:6px}
.dow{text-align:center;font-weight:700;color:var(--muted);font-size:.85rem;padding:4px}
.cell{min-height:105px;border:1px solid var(--line);border-radius:8px;padding:6px;background:#fff;overflow:hidden}
.empty-cell{background:#f9fafb;border-style:dashed}
.cell.today{border:2px solid var(--accent);background:#fef2f2}
.cell-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:4px}
.num{font-weight:700}
.add{width:22px;height:22px;border-radius:50%;display:grid;place-items:center;color:var(--muted);font-weight:700;opacity:.4}
.cell:hover .add{opacity:1;background:#fee2e2;color:var(--accent)}
.ev{display:block;color:#fff;font-size:.72rem;padding:3px 6px;border-radius:5px;margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.legend{display:flex;gap:16px;flex-wrap:wrap;margin-top:14px;font-size:.85rem}
.legend i{display:inline-block;width:12px;height:12px;border-radius:3px;margin-right:5px;vertical-align:-1px}
</style>
