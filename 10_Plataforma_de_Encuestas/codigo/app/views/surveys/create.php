<?php $qs = $old['questions'] ?? ['', '']; $os = $old['options'] ?? ['', '']; ?>
<div class="card" style="max-width:820px;margin:0 auto">
    <h1>Crear encuesta</h1>
    <p class="subtitle">Escribe las preguntas y sus opciones de respuesta (una opción por línea).</p>
    <form method="post" action="index.php?action=store">
        <div class="field"><label for="title">Título</label><input type="text" id="title" name="title" value="<?= e($old['title'] ?? '') ?>"></div>
        <div class="row">
            <div class="field" style="flex:2"><label for="description">Descripción</label><input type="text" id="description" name="description" value="<?= e($old['description'] ?? '') ?>"></div>
            <div class="field"><label for="author">Autor</label><input type="text" id="author" name="author" value="<?= e($old['author'] ?? '') ?>"></div>
        </div>
        <div id="questions">
            <?php foreach ($qs as $i => $q): ?>
                <div class="qblock">
                    <div class="qnum">Pregunta <?= $i + 1 ?></div>
                    <input type="text" name="questions[]" placeholder="Texto de la pregunta" value="<?= e($q) ?>">
                    <textarea name="options[]" rows="4" placeholder="Opción 1&#10;Opción 2&#10;Opción 3"><?= e($os[$i] ?? '') ?></textarea>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="actions">
            <button type="button" class="btn btn-outline" onclick="addQuestion()">➕ Agregar pregunta</button>
            <button class="btn">💾 Publicar encuesta</button>
        </div>
    </form>
</div>
<style>
.qblock{background:#f8fafc;border:1px solid var(--line);border-left:4px solid var(--accent);border-radius:10px;padding:14px;margin-bottom:14px;display:grid;gap:8px}
.qnum{font-weight:700;color:var(--accent)}
</style>
<script>
function addQuestion() {
    const box = document.getElementById('questions');
    const n = box.children.length + 1;
    const div = document.createElement('div');
    div.className = 'qblock';
    div.innerHTML = `<div class="qnum">Pregunta ${n}</div>
        <input type="text" name="questions[]" placeholder="Texto de la pregunta">
        <textarea name="options[]" rows="4" placeholder="Opción 1&#10;Opción 2&#10;Opción 3"></textarea>`;
    box.appendChild(div);
}
</script>
