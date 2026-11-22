<?php
$fmtMonth = function ($m) {
    $names = ['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
    return $names[substr($m, 5, 2)] . ' ' . substr($m, 0, 4);
};
// Gráfico circular (dona) con conic-gradient
$stops = []; $acc = 0;
foreach ($summary['byCat'] as $k => $v) {
    $pct = $summary['total'] ? $v * 100 / $summary['total'] : 0;
    $stops[] = $categories[$k][2] . " {$acc}% " . ($acc + $pct) . '%';
    $acc += $pct;
}
$maxMonth = max($monthly) ?: 1;
?>
<div class="row" style="justify-content:space-between;align-items:center;margin-bottom:16px">
    <div><h1>Resumen de <?= $fmtMonth($month) ?></h1><p class="muted">Análisis de tus gastos del mes.</p></div>
    <form method="get" style="flex:0 0 200px"><input type="hidden" name="action" value="summary">
        <input type="month" name="month" value="<?= e($month) ?>" onchange="this.form.submit()"></form>
</div>

<div class="grid grid-3" style="margin-bottom:22px;grid-template-columns:repeat(4,1fr)">
    <div class="card"><div class="muted">Total gastado</div><div class="stat"><?= money($summary['total']) ?></div></div>
    <div class="card"><div class="muted">N.º de gastos</div><div class="stat"><?= $summary['count'] ?></div></div>
    <div class="card"><div class="muted">Promedio diario</div><div class="stat"><?= money($summary['avg']) ?></div></div>
    <div class="card"><div class="muted">Gasto más alto</div><div class="stat"><?= money($summary['max']) ?></div></div>
</div>

<div class="grid grid-2">
    <div class="card">
        <h2>Gastos por categoría</h2>
        <?php if (!$summary['byCat']): ?><div class="empty">Sin datos este mes.</div><?php else: ?>
        <div style="display:flex;gap:24px;align-items:center;flex-wrap:wrap">
            <div class="donut" style="background:conic-gradient(<?= implode(',', $stops) ?>)"><div><?= money($summary['total']) ?></div></div>
            <div style="flex:1;min-width:200px">
                <?php foreach ($summary['byCat'] as $k => $v): [$cn, $ci, $cc] = $categories[$k]; $pct = round($v * 100 / $summary['total'], 1); ?>
                    <div style="margin-bottom:10px">
                        <div style="display:flex;justify-content:space-between;font-size:.9rem"><span><?= $ci ?> <?= $cn ?></span><span><strong><?= money($v) ?></strong> (<?= $pct ?>%)</span></div>
                        <div class="bar"><span style="width:<?= $pct ?>%;background:<?= $cc ?>"></span></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <div class="card">
        <h2>Comparativo últimos 6 meses</h2>
        <div class="cols">
            <?php foreach ($monthly as $m => $v): ?>
                <div class="col <?= $m === $month ? 'cur' : '' ?>">
                    <div class="val"><?= $v ? money($v) : '' ?></div>
                    <div class="b" style="height:<?= round($v * 180 / $maxMonth) ?>px"></div>
                    <div class="lbl"><?= substr($fmtMonth($m), 0, 3) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="card">
    <h2>Gasto por día</h2>
    <?php if (!$summary['byDay']): ?><div class="empty">Sin datos.</div><?php else: ?>
    <table><tr><th>Día</th><th>Total</th><th style="width:60%"></th></tr>
        <?php $maxDay = max($summary['byDay']); foreach ($summary['byDay'] as $d => $v): ?>
            <tr><td><?= date('d/m/Y', strtotime($d)) ?></td><td><?= money($v) ?></td>
                <td><div class="bar"><span style="width:<?= round($v * 100 / $maxDay) ?>%"></span></div></td></tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>

<style>
.donut{width:190px;height:190px;border-radius:50%;display:grid;place-items:center}
.donut>div{width:110px;height:110px;border-radius:50%;background:#fff;display:grid;place-items:center;font-weight:700;font-size:.95rem}
.cols{display:flex;align-items:flex-end;gap:12px;height:240px;padding-top:10px}
.col{flex:1;text-align:center;display:flex;flex-direction:column;justify-content:flex-end;align-items:center}
.col .b{width:70%;background:#bae6fd;border-radius:6px 6px 0 0;min-height:2px}
.col.cur .b{background:var(--accent)}
.col .val{font-size:.7rem;color:var(--muted);margin-bottom:4px}
.col .lbl{font-size:.8rem;margin-top:6px;font-weight:600}
</style>
