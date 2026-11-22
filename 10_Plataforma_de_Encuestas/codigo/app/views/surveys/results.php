<?php $palette = ['#2563eb', '#16a34a', '#f59e0b', '#dc2626', '#8b5cf6', '#0891b2', '#db2777', '#65a30d']; ?>
<div class="row no-print" style="justify-content:space-between;align-items:center;margin-bottom:16px">
    <div><h1>📊 Informe: <?= e($s['title']) ?></h1><p class="muted"><?= e($s['description']) ?></p></div>
    <div class="actions">
        <a class="btn btn-outline" href="index.php?action=export&id=<?= $s['id'] ?>">⬇ Exportar CSV</a>
        <button class="btn btn-outline" onclick="window.print()">🖨 Imprimir / PDF</button>
        <a class="btn" href="index.php?action=answer&id=<?= $s['id'] ?>">Responder</a>
    </div>
</div>

<div class="grid grid-3" style="margin-bottom:22px">
    <div class="card"><div class="muted">Total de respuestas</div><div class="stat"><?= $a['total'] ?></div></div>
    <div class="card"><div class="muted">Preguntas</div><div class="stat"><?= count($s['questions']) ?></div></div>
    <div class="card"><div class="muted">Última respuesta</div><div class="stat" style="font-size:1.3rem"><?= e($a['last'] ?? '—') ?></div></div>
</div>

<?php foreach ($a['questions'] as $qi => $q):
    $stops = []; $acc = 0;
    foreach ($q['rows'] as $oi => $r) { $c = $palette[$oi % 8]; $stops[] = "$c {$acc}% " . ($acc + $r['pct']) . '%'; $acc += $r['pct']; }
    if (!$a['total']) $stops = ['#e5e7eb 0 100%']; ?>
    <div class="card qres">
        <h2><?= $qi + 1 ?>. <?= e($q['text']) ?></h2>
        <div class="qgrid">
            <div>
                <?php foreach ($q['rows'] as $oi => $r): ?>
                    <div style="margin-bottom:12px">
                        <div style="display:flex;justify-content:space-between"><span><i class="dot" style="background:<?= $palette[$oi % 8] ?>"></i><?= e($r['option']) ?></span>
                            <span><strong><?= $r['votes'] ?></strong> votos · <?= $r['pct'] ?>%</span></div>
                        <div class="bar" style="height:16px"><span style="width:<?= $r['pct'] ?>%;background:<?= $palette[$oi % 8] ?>"></span></div>
                    </div>
                <?php endforeach; ?>
                <?php if ($q['winner']): ?><p class="badge badge-ok">🏅 Opción más votada: <?= e($q['winner']) ?></p><?php endif; ?>
            </div>
            <div class="pie" style="background:conic-gradient(<?= implode(',', $stops) ?>)"></div>
        </div>
    </div>
<?php endforeach; ?>

<style>
.qgrid{display:grid;grid-template-columns:1fr 180px;gap:30px;align-items:center}
.pie{width:170px;height:170px;border-radius:50%;box-shadow:inset 0 0 0 3px #fff}
.dot{display:inline-block;width:10px;height:10px;border-radius:50%;margin-right:8px}
@media print{.topbar,.footer,.no-print{display:none}.card{box-shadow:none;border:1px solid #ddd;break-inside:avoid}}
</style>
