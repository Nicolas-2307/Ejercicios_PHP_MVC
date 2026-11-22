<div class="card" style="max-width:720px;margin:0 auto">
    <h1><?= e($s['title']) ?></h1>
    <p class="subtitle"><?= e($s['description']) ?> <br><small>🗳 <?= $count ?> respuesta(s) hasta ahora</small></p>
    <form method="post" action="index.php?action=submit">
        <input type="hidden" name="id" value="<?= $s['id'] ?>">
        <?php foreach ($s['questions'] as $qi => $q): ?>
            <fieldset class="q">
                <legend><?= $qi + 1 ?>. <?= e($q['text']) ?></legend>
                <?php foreach ($q['options'] as $oi => $opt): ?>
                    <label class="opt"><input type="radio" name="answers[<?= $qi ?>]" value="<?= $oi ?>"> <?= e($opt) ?></label>
                <?php endforeach; ?>
            </fieldset>
        <?php endforeach; ?>
        <div class="actions">
            <button class="btn">📨 Enviar respuestas</button>
            <a class="btn btn-outline" href="index.php?action=results&id=<?= $s['id'] ?>">Ver resultados</a>
        </div>
    </form>
</div>
<style>
.q{border:none;margin-bottom:20px}
.q legend{font-weight:700;margin-bottom:8px;font-size:1.05rem}
.opt{font-weight:400;display:flex;gap:10px;align-items:center;padding:10px 12px;border:1px solid var(--line);border-radius:8px;margin-bottom:6px;cursor:pointer}
.opt:has(input:checked){border-color:var(--accent);background:#eff6ff}
</style>
