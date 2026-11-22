<div class="card" style="max-width:860px;margin:0 auto">
    <div style="display:flex;gap:22px;align-items:center;flex-wrap:wrap">
        <div style="font-size:5rem;background:linear-gradient(135deg,#ffedd5,#fef3c7);border-radius:16px;padding:10px 24px"><?= e($r['emoji']) ?></div>
        <div style="flex:1">
            <span class="badge"><?= e($r['type']) ?></span>
            <h1><?= e($r['name']) ?></h1>
            <div class="muted">⏱ <?= $r['time'] ?> min · 👥 <?= $r['servings'] ?> porciones · 📶 <?= e($r['difficulty']) ?> · Por <strong><?= e($r['author']) ?></strong> · ❤ <?= $r['saves'] ?> guardados</div>
        </div>
    </div>

    <div class="actions" style="margin:18px 0">
        <form method="post" action="index.php?action=toggleFav">
            <input type="hidden" name="id" value="<?= $r['id'] ?>"><input type="hidden" name="back" value="show">
            <button class="btn <?= $isFav ? '' : 'btn-outline' ?>"><?= $isFav ? '❤ Guardada' : '🤍 Guardar receta' ?></button>
        </form>
        <button class="btn btn-outline" type="button" onclick="share()">🔗 Compartir</button>
        <a class="btn btn-gray" href="index.php">← Volver</a>
    </div>
    <div id="shareBox" class="alert alert-ok" style="display:none">Enlace copiado: <code><?= e($shareUrl) ?></code></div>

    <div class="grid" style="grid-template-columns:1fr 1.5fr">
        <div><h2>🧺 Ingredientes</h2>
            <ul class="ings"><?php foreach ($r['ingredients'] as $i): ?><li><label><input type="checkbox"> <?= e($i) ?></label></li><?php endforeach; ?></ul></div>
        <div><h2>👩‍🍳 Preparación</h2>
            <ol class="steps"><?php foreach ($r['steps'] as $s): ?><li><?= e($s) ?></li><?php endforeach; ?></ol></div>
    </div>
</div>
<style>
.ings{list-style:none}.ings li{padding:7px 0;border-bottom:1px dashed var(--line)}.ings label{font-weight:400;display:flex;gap:8px}
.steps{padding-left:20px}.steps li{margin-bottom:12px;line-height:1.5}
</style>
<script>
function share() {
    const url = <?= json_encode($shareUrl) ?>;
    if (navigator.share) { navigator.share({title: <?= json_encode($r['name']) ?>, url}).catch(() => {}); }
    try { navigator.clipboard.writeText(url); } catch (e) {}
    document.getElementById('shareBox').style.display = 'block';
}
</script>
