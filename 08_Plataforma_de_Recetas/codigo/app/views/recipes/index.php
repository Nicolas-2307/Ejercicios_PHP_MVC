<h1><?= e($heading) ?></h1>
<p class="subtitle"><?= $f !== null ? 'Busca por nombre, filtra por tipo de comida, ingredientes, tiempo o dificultad.' : 'Recetas que guardaste para preparar después.' ?></p>

<?php if ($f !== null): ?>
<form method="get" class="card filters">
    <div style="flex:2"><label>Nombre</label><input type="search" name="q" value="<?= e($f['q']) ?>" placeholder="Ej: arepa"></div>
    <div><label>Tipo de comida</label>
        <select name="type"><option value="">Todos</option>
            <?php foreach (Recipe::TYPES as $t): ?><option <?= $f['type'] === $t ? 'selected' : '' ?>><?= $t ?></option><?php endforeach; ?>
        </select></div>
    <div style="flex:2"><label>Ingredientes (separados por coma)</label><input type="text" name="ingredient" value="<?= e($f['ingredient']) ?>" placeholder="Ej: huevo, queso"></div>
    <div><label>Tiempo máx.</label>
        <select name="maxTime"><option value="0">Cualquiera</option>
            <?php foreach ([15, 30, 60, 120] as $m): ?><option value="<?= $m ?>" <?= $f['maxTime'] === $m ? 'selected' : '' ?>>≤ <?= $m ?> min</option><?php endforeach; ?>
        </select></div>
    <div><label>Dificultad</label>
        <select name="difficulty"><option value="">Todas</option>
            <?php foreach (Recipe::DIFFICULTIES as $d): ?><option <?= $f['difficulty'] === $d ? 'selected' : '' ?>><?= $d ?></option><?php endforeach; ?>
        </select></div>
    <div class="actions"><button class="btn">🔍 Filtrar</button><a class="btn btn-gray" href="index.php">Limpiar</a></div>
</form>
<div class="tabs">
    <?php foreach (array_merge([''], Recipe::TYPES) as $t): ?>
        <a href="index.php?type=<?= urlencode($t) ?>" class="<?= $f['type'] === $t ? 'active' : '' ?>"><?= $t ?: 'Todas' ?></a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<p class="muted" style="margin-bottom:12px"><?= count($recipes) ?> receta(s) encontrada(s)</p>
<?php if (!$recipes): ?><div class="card empty">No hay recetas que coincidan.</div><?php endif; ?>

<div class="recipes">
    <?php foreach ($recipes as $r): $fav = in_array($r['id'], $favorites, true); ?>
        <div class="card recipe">
            <a href="index.php?action=show&id=<?= $r['id'] ?>" class="thumb"><?= e($r['emoji']) ?></a>
            <div class="r-body">
                <span class="badge"><?= e($r['type']) ?></span>
                <h3><a href="index.php?action=show&id=<?= $r['id'] ?>" style="color:var(--text)"><?= e($r['name']) ?></a></h3>
                <div class="muted" style="font-size:.85rem">⏱ <?= $r['time'] ?> min · 👥 <?= $r['servings'] ?> · 📶 <?= e($r['difficulty']) ?></div>
                <div class="muted" style="font-size:.8rem;margin-top:4px">Por <?= e($r['author']) ?> · ❤ <?= $r['saves'] ?></div>
            </div>
            <form method="post" action="index.php?action=toggleFav">
                <input type="hidden" name="id" value="<?= $r['id'] ?>">
                <input type="hidden" name="back" value="<?= $f === null ? 'favorites' : 'index' ?>">
                <button class="btn btn-sm <?= $fav ? '' : 'btn-outline' ?> btn-block"><?= $fav ? '❤ Guardada' : '🤍 Guardar' ?></button>
            </form>
        </div>
    <?php endforeach; ?>
</div>
<style>
.filters{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end}
.filters>div{flex:1;min-width:130px}
.recipes{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:18px}
.recipe{margin:0;display:flex;flex-direction:column;gap:10px;padding:0;overflow:hidden}
.recipe .thumb{font-size:4rem;text-align:center;background:linear-gradient(135deg,#ffedd5,#fef3c7);padding:18px 0}
.r-body{padding:0 16px;flex:1}
.recipe form{padding:0 16px 16px}
</style>
