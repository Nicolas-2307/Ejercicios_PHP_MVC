<div class="card" style="max-width:680px;margin:0 auto">
    <h1><?= $id ? '✏ Editar evento' : '➕ Nuevo evento' ?></h1>
    <p class="subtitle">Define la fecha, la hora y cuándo quieres recibir el recordatorio.</p>
    <form method="post" action="index.php?action=save">
        <input type="hidden" name="id" value="<?= e($id) ?>">
        <div class="field"><label for="title">Título</label><input type="text" id="title" name="title" value="<?= e($ev['title']) ?>"></div>
        <div class="field"><label for="description">Descripción</label><textarea id="description" name="description" rows="3"><?= e($ev['description']) ?></textarea></div>
        <div class="row">
            <div class="field"><label for="date">Fecha</label><input type="date" id="date" name="date" value="<?= e($ev['date']) ?>"></div>
            <div class="field"><label for="time">Hora</label><input type="time" id="time" name="time" value="<?= e($ev['time']) ?>"></div>
        </div>
        <div class="row">
            <div class="field"><label for="category">Categoría</label>
                <select id="category" name="category">
                    <?php foreach ($categories as $k => [$n]): ?><option value="<?= $k ?>" <?= $ev['category'] === $k ? 'selected' : '' ?>><?= $n ?></option><?php endforeach; ?>
                </select></div>
            <div class="field"><label for="reminder">Recordatorio</label>
                <select id="reminder" name="reminder">
                    <?php foreach ($reminders as $k => $n): ?><option value="<?= $k ?>" <?= (int)$ev['reminder'] === $k ? 'selected' : '' ?>><?= $n ?></option><?php endforeach; ?>
                </select></div>
        </div>
        <div class="actions" style="justify-content:space-between">
            <div class="actions">
                <button class="btn">💾 Guardar</button>
                <a href="index.php" class="btn btn-gray">Cancelar</a>
            </div>
        </div>
    </form>
    <?php if ($id): ?>
        <form method="post" action="index.php?action=delete" onsubmit="return confirm('¿Eliminar este evento?')" style="margin-top:14px;text-align:right">
            <input type="hidden" name="id" value="<?= e($id) ?>">
            <button class="btn btn-danger">🗑 Eliminar evento</button>
        </form>
    <?php endif; ?>
</div>
