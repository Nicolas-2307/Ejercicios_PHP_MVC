<div class="row" style="justify-content:space-between;align-items:center;margin-bottom:18px">
    <div><h1>Encuestas disponibles</h1><p class="muted">Responde encuestas o crea la tuya.</p></div>
    <a class="btn" href="index.php?action=create">➕ Crear encuesta</a>
</div>
<?php if (!$surveys): ?><div class="card empty">Aún no hay encuestas. ¡Crea la primera!</div><?php endif; ?>
<div class="grid grid-3">
    <?php foreach ($surveys as $s): ?>
        <div class="card" style="margin:0;display:flex;flex-direction:column">
            <h3><?= e($s['title']) ?></h3>
            <p class="muted" style="flex:1;font-size:.9rem"><?= e($s['description']) ?></p>
            <div class="muted" style="font-size:.8rem;margin:10px 0">
                ❓ <?= count($s['questions']) ?> pregunta(s) · 🗳 <?= $s['count'] ?> respuesta(s)<br>Por <?= e($s['author']) ?> · <?= e($s['created_at']) ?>
            </div>
            <div class="actions">
                <a class="btn btn-sm" href="index.php?action=answer&id=<?= $s['id'] ?>">Responder</a>
                <a class="btn btn-sm btn-outline" href="index.php?action=results&id=<?= $s['id'] ?>">📊 Resultados</a>
                <form method="post" action="index.php?action=delete" onsubmit="return confirm('¿Eliminar la encuesta y sus respuestas?')">
                    <input type="hidden" name="id" value="<?= $s['id'] ?>"><button class="btn btn-sm btn-danger">🗑</button></form>
            </div>
        </div>
    <?php endforeach; ?>
</div>
