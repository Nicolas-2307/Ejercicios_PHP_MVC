<div class="card" style="max-width:820px;margin:0 auto">
    <h1>🍽️ Compartir una receta</h1>
    <p class="subtitle">Publica tu receta para que otros usuarios puedan encontrarla y guardarla.</p>
    <form method="post" action="index.php?action=store">
        <div class="row">
            <div class="field" style="flex:3"><label for="name">Nombre de la receta</label><input type="text" id="name" name="name" value="<?= e($old['name']) ?>"></div>
            <div class="field" style="flex:1"><label for="emoji">Ícono</label>
                <select id="emoji" name="emoji"><?php foreach (['🍽️','🍝','🍕','🌮','🍜','🥘','🍰','🍪','🥤','🍳'] as $em): ?><option <?= $old['emoji'] === $em ? 'selected' : '' ?>><?= $em ?></option><?php endforeach; ?></select></div>
        </div>
        <div class="row">
            <div class="field"><label>Tipo de comida</label><select name="type"><?php foreach (Recipe::TYPES as $t): ?><option <?= $old['type'] === $t ? 'selected' : '' ?>><?= $t ?></option><?php endforeach; ?></select></div>
            <div class="field"><label>Dificultad</label><select name="difficulty"><?php foreach (Recipe::DIFFICULTIES as $d): ?><option <?= $old['difficulty'] === $d ? 'selected' : '' ?>><?= $d ?></option><?php endforeach; ?></select></div>
            <div class="field"><label>Tiempo (min)</label><input type="number" name="time" min="1" value="<?= e($old['time']) ?>"></div>
            <div class="field"><label>Porciones</label><input type="number" name="servings" min="1" value="<?= e($old['servings']) ?>"></div>
        </div>
        <div class="row">
            <div class="field"><label for="ingredients">Ingredientes (uno por línea)</label><textarea id="ingredients" name="ingredients" rows="7"><?= e($old['ingredients']) ?></textarea></div>
            <div class="field"><label for="steps">Pasos de preparación (uno por línea)</label><textarea id="steps" name="steps" rows="7"><?= e($old['steps']) ?></textarea></div>
        </div>
        <div class="field"><label for="author">Tu nombre</label><input type="text" id="author" name="author" value="<?= e($old['author']) ?>"></div>
        <button class="btn">📤 Publicar receta</button>
    </form>
</div>
