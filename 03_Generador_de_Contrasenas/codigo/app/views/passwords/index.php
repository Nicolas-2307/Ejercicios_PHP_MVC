<h1>Generador de contraseñas seguras</h1>
<p class="subtitle">Ajusta la longitud y los tipos de caracteres. Las contraseñas se generan en el servidor con <code>random_int()</code>.</p>

<?php if ($error): ?><div class="alert alert-err"><?= e($error) ?></div><?php endif; ?>

<div class="grid grid-2">
    <div class="card">
        <h2>Opciones</h2>
        <form method="post" action="index.php?action=index">
            <div class="field">
                <label for="length">Longitud: <span id="lenVal"><?= (int)$options['length'] ?></span> caracteres</label>
                <input type="range" id="length" name="length" min="<?= PasswordGenerator::MIN ?>" max="<?= PasswordGenerator::MAX ?>"
                       value="<?= (int)$options['length'] ?>" style="width:100%" oninput="lenVal.textContent=this.value">
            </div>
            <div class="field">
                <label>Tipos de caracteres</label>
                <?php foreach (['upper' => 'Mayúsculas (A-Z)', 'lower' => 'Minúsculas (a-z)', 'digits' => 'Números (0-9)', 'symbols' => 'Símbolos (!@#$...)'] as $k => $v): ?>
                    <label class="chk"><input type="checkbox" name="types[]" value="<?= $k ?>" <?= in_array($k, $options['types'], true) ? 'checked' : '' ?>> <?= $v ?></label>
                <?php endforeach; ?>
                <label class="chk"><input type="checkbox" name="noAmbiguous" <?= $options['noAmbiguous'] ? 'checked' : '' ?>> Excluir caracteres ambiguos (O, 0, l, 1, I)</label>
            </div>
            <div class="field">
                <label for="count">Cantidad a generar</label>
                <select name="count" id="count">
                    <?php foreach ([1, 3, 5, 10] as $c): ?>
                        <option <?= $options['count'] == $c ? 'selected' : '' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="btn btn-block" type="submit">🔄 Generar contraseñas</button>
        </form>
    </div>

    <div class="card">
        <h2>Contraseñas generadas</h2>
        <?php if (!$passwords): ?>
            <div class="empty">Configura las opciones y presiona “Generar”.</div>
        <?php endif; ?>
        <?php foreach ($passwords as $i => $p): ?>
            <div class="pwd">
                <code id="pwd<?= $i ?>"><?= e($p['value']) ?></code>
                <button type="button" class="btn btn-sm btn-outline" onclick="copyPwd(<?= $i ?>, this)">Copiar</button>
            </div>
            <div class="meter lvl<?= $p['strength']['level'] ?>"><span></span><span></span><span></span><span></span></div>
            <div class="muted" style="font-size:.8rem;margin-bottom:14px"><?= $p['strength']['label'] ?> · <?= $p['strength']['bits'] ?> bits de entropía · <?= strlen($p['value']) ?> caracteres</div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.chk{font-weight:400;display:flex;gap:8px;align-items:center;margin:6px 0}
.pwd{display:flex;gap:10px;align-items:center;background:#f5f3ff;border-radius:8px;padding:10px 12px}
.pwd code{flex:1;font-size:1.05rem;word-break:break-all;font-family:Consolas,monospace}
.meter{display:grid;grid-template-columns:repeat(4,1fr);gap:4px;margin:6px 0 4px}
.meter span{height:6px;border-radius:3px;background:#e5e7eb}
.lvl1 span:nth-child(-n+1){background:#dc2626}
.lvl2 span:nth-child(-n+2){background:#d97706}
.lvl3 span:nth-child(-n+3){background:#65a30d}
.lvl4 span{background:#16a34a}
</style>
<script>
function copyPwd(i, btn) {
    const text = document.getElementById('pwd' + i).textContent;
    (navigator.clipboard ? navigator.clipboard.writeText(text) : Promise.reject()).catch(() => {
        const t = document.createElement('textarea'); t.value = text; document.body.appendChild(t); t.select(); document.execCommand('copy'); t.remove();
    });
    btn.textContent = '¡Copiada!';
    setTimeout(() => btn.textContent = 'Copiar', 1500);
}
</script>
