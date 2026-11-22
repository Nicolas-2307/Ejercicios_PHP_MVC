<h1>Calculadora de propinas</h1>
<p class="subtitle">Ingresa el valor de la cuenta y elige el porcentaje: el resultado se calcula automáticamente.</p>

<?php foreach ($errors as $err): ?>
    <div class="alert alert-err"><?= e($err) ?></div>
<?php endforeach; ?>

<div class="grid grid-2">
    <div class="card">
        <form method="post" action="index.php?action=index" id="tipForm">
            <div class="field">
                <label for="amount">Monto total de la cuenta (COP)</label>
                <input type="number" id="amount" name="amount" min="0" step="100" placeholder="Ej: 85000" value="<?= e($input['amount']) ?>">
            </div>
            <div class="field">
                <label>Porcentaje de propina</label>
                <div class="pct-grid">
                    <?php foreach ($percentages as $p): ?>
                        <label class="pct">
                            <input type="radio" name="percent" value="<?= $p ?>" <?= (string)$input['percent'] === (string)$p ? 'checked' : '' ?>>
                            <span><?= $p ?>%</span>
                        </label>
                    <?php endforeach; ?>
                    <label class="pct">
                        <input type="radio" name="percent" value="custom" <?= $input['percent'] === 'custom' ? 'checked' : '' ?>>
                        <span>Otro</span>
                    </label>
                </div>
                <input type="number" id="custom" name="custom" min="0" max="100" step="0.5" placeholder="Porcentaje personalizado" value="<?= e($input['custom']) ?>" style="margin-top:10px">
            </div>
            <div class="field">
                <label for="people">Número de personas</label>
                <input type="number" id="people" name="people" min="1" value="<?= e($input['people']) ?>">
            </div>
            <button class="btn btn-block" type="submit">Calcular y guardar en historial</button>
        </form>
    </div>

    <div class="card result">
        <h2>Resultado</h2>
        <div class="res-line"><span>Subtotal</span><strong id="r-amount"><?= $result ? money($result['amount']) : '$0' ?></strong></div>
        <div class="res-line"><span>Propina (<span id="r-pct"><?= $result ? $result['percent'] : 0 ?></span>%)</span><strong id="r-tip"><?= $result ? money($result['tip']) : '$0' ?></strong></div>
        <div class="res-line total"><span>Total a pagar</span><strong id="r-total"><?= $result ? money($result['total']) : '$0' ?></strong></div>
        <hr style="border:none;border-top:1px dashed var(--line);margin:14px 0">
        <div class="res-line"><span>Propina por persona</span><strong id="r-tpp"><?= $result ? money($result['tip_per_person']) : '$0' ?></strong></div>
        <div class="res-line"><span>Total por persona</span><strong id="r-pp"><?= $result ? money($result['per_person']) : '$0' ?></strong></div>
        <p class="muted" id="r-msg" style="margin-top:12px;font-size:.85rem"><?= $result ? '✔ Cálculo guardado en el historial.' : 'Escribe un monto para ver el cálculo.' ?></p>
    </div>
</div>

<style>
.pct-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:8px}
.pct input{display:none}
.pct span{display:block;text-align:center;padding:10px;border:1.5px solid var(--line);border-radius:8px;cursor:pointer;font-weight:600}
.pct input:checked+span{background:var(--accent);color:#fff;border-color:var(--accent)}
.result{background:linear-gradient(160deg,#ecfdf5,#fff)}
.res-line{display:flex;justify-content:space-between;padding:8px 0;font-size:1.05rem}
.res-line.total{font-size:1.4rem;color:var(--accent);border-top:2px solid var(--accent);margin-top:6px;padding-top:12px}
</style>

<script>
// Cálculo automático: consulta al controlador (action=api) en cada cambio
const form = document.getElementById('tipForm');
const fmt = n => '$' + Math.round(n).toLocaleString('es-CO');
async function recalc() {
    const fd = new FormData(form);
    let pct = fd.get('percent') === 'custom' ? fd.get('custom') : fd.get('percent');
    const params = new URLSearchParams({amount: fd.get('amount') || 0, percent: pct || 0, people: fd.get('people') || 1});
    const res = await fetch('index.php?action=api&' + params);
    const d = await res.json();
    const msg = document.getElementById('r-msg');
    if (d.errors) { msg.textContent = d.errors[0]; return; }
    document.getElementById('r-amount').textContent = fmt(d.amount);
    document.getElementById('r-pct').textContent = d.percent;
    document.getElementById('r-tip').textContent = fmt(d.tip);
    document.getElementById('r-total').textContent = fmt(d.total);
    document.getElementById('r-tpp').textContent = fmt(d.tip_per_person);
    document.getElementById('r-pp').textContent = fmt(d.per_person);
    msg.textContent = 'Cálculo automático (aún no guardado).';
}
form.addEventListener('input', recalc);
form.addEventListener('change', recalc);
</script>
