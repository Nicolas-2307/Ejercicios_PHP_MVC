<h1>Registro de gastos</h1>
<p class="subtitle">Anota tus gastos diarios y clasifícalos por categoría.</p>

<div class="grid" style="grid-template-columns:1fr 2fr">
    <div class="card">
        <h2>Nuevo gasto</h2>
        <form method="post" action="index.php?action=store" novalidate>
            <div class="field"><label for="date">Fecha</label>
                <input type="date" id="date" name="date" value="<?= e($old['date'] ?? date('Y-m-d')) ?>"></div>
            <div class="field"><label for="description">Descripción</label>
                <input type="text" id="description" name="description" placeholder="Ej: Almuerzo" value="<?= e($old['description'] ?? '') ?>"></div>
            <div class="field"><label for="category">Categoría</label>
                <select id="category" name="category">
                    <?php foreach ($categories as $k => [$name, $icon]): ?>
                        <option value="<?= $k ?>" <?= ($old['category'] ?? '') === $k ? 'selected' : '' ?>><?= $icon ?> <?= $name ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div class="field"><label for="amount">Monto (COP)</label>
                <input type="number" id="amount" name="amount" min="0" step="50" placeholder="Ej: 15000" value="<?= e($old['amount'] ?? '') ?>"></div>
            <button class="btn btn-block">💾 Registrar gasto</button>
        </form>
    </div>

    <div class="card">
        <form method="get" class="row" style="margin-bottom:14px">
            <input type="hidden" name="action" value="index">
            <div><label>Mes</label><input type="month" name="month" value="<?= e($month) ?>" onchange="this.form.submit()"></div>
            <div><label>Categoría</label>
                <select name="category" onchange="this.form.submit()">
                    <option value="">Todas</option>
                    <?php foreach ($categories as $k => [$name, $icon]): ?>
                        <option value="<?= $k ?>" <?= $cat === $k ? 'selected' : '' ?>><?= $icon ?> <?= $name ?></option>
                    <?php endforeach; ?>
                </select></div>
            <div style="text-align:right"><div class="muted">Total del filtro</div><div class="stat" style="font-size:1.5rem"><?= money($total) ?></div></div>
        </form>
        <?php if (!$rows): ?>
            <div class="empty">No hay gastos registrados para este filtro.</div>
        <?php else: ?>
            <table>
                <tr><th>Fecha</th><th>Descripción</th><th>Categoría</th><th style="text-align:right">Monto</th><th></th></tr>
                <?php foreach ($rows as $r): [$cn, $ci, $cc] = $categories[$r['category']]; ?>
                    <tr>
                        <td><?= e(date('d/m/Y', strtotime($r['date']))) ?></td>
                        <td><?= e($r['description']) ?></td>
                        <td><span class="badge" style="background:<?= $cc ?>22;color:<?= $cc ?>"><?= $ci ?> <?= $cn ?></span></td>
                        <td style="text-align:right;font-weight:600"><?= money($r['amount']) ?></td>
                        <td><form method="post" action="index.php?action=delete" onsubmit="return confirm('¿Eliminar este gasto?')">
                            <input type="hidden" name="id" value="<?= $r['id'] ?>"><input type="hidden" name="month" value="<?= e($month) ?>">
                            <button class="btn btn-sm btn-danger">🗑</button></form></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>
