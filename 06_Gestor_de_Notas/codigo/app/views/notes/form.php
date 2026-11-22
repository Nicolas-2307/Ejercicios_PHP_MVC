<div class="card" style="max-width:700px;margin:0 auto">
    <h1><?= $id ? '✏ Editar nota' : '📝 Nueva nota' ?></h1>
    <p class="subtitle">Escribe tu nota y asígnale una categoría y un color.</p>
    <form method="post" action="index.php?action=save">
        <input type="hidden" name="id" value="<?= e($id) ?>">
        <div class="field"><label for="title">Título</label>
            <input type="text" id="title" name="title" value="<?= e($note['title']) ?>" maxlength="80"></div>
        <div class="field"><label for="content">Contenido</label>
            <textarea id="content" name="content" rows="8"><?= e($note['content']) ?></textarea></div>
        <div class="row">
            <div class="field"><label for="category">Categoría</label>
                <select id="category" name="category">
                    <?php foreach ($categories as $c): ?><option <?= $note['category'] === $c ? 'selected' : '' ?>><?= e($c) ?></option><?php endforeach; ?>
                </select></div>
            <div class="field"><label>Color</label>
                <div style="display:flex;gap:8px">
                    <?php foreach ($colors as $c): ?>
                        <label class="sw"><input type="radio" name="color" value="<?= $c ?>" <?= $note['color'] === $c ? 'checked' : '' ?>><span style="background:<?= $c ?>"></span></label>
                    <?php endforeach; ?>
                </div></div>
        </div>
        <div class="actions">
            <button class="btn">💾 Guardar nota</button>
            <a href="index.php" class="btn btn-gray">Cancelar</a>
        </div>
    </form>
</div>
<style>
.sw input{display:none}.sw span{display:block;width:34px;height:34px;border-radius:50%;border:2px solid #d1d5db;cursor:pointer}
.sw input:checked+span{border:3px solid var(--text)}
</style>
