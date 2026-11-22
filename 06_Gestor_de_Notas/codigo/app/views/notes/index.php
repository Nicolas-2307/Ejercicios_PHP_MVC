<div class="layout-notes">
    <aside class="card side">
        <a href="index.php?action=create" class="btn btn-block" style="margin-bottom:16px">➕ Nueva nota</a>
        <h3>Categorías</h3>
        <a href="index.php?q=<?= urlencode($q) ?>" class="cat <?= $cat === '' ? 'active' : '' ?>">📂 Todas <span><?= array_sum($counts) ?></span></a>
        <?php foreach ($counts as $c => $n): ?>
            <a href="index.php?category=<?= urlencode($c) ?>&q=<?= urlencode($q) ?>" class="cat <?= $cat === $c ? 'active' : '' ?>">🏷 <?= e($c) ?> <span><?= $n ?></span></a>
        <?php endforeach; ?>
    </aside>

    <section>
        <form method="get" class="search">
            <input type="hidden" name="category" value="<?= e($cat) ?>">
            <input type="search" name="q" value="<?= e($q) ?>" placeholder="🔍 Buscar en título o contenido...">
            <button class="btn">Buscar</button>
        </form>
        <p class="muted" style="margin:10px 0 16px">
            <?= count($notes) ?> nota(s)<?= $cat ? ' en <strong>' . e($cat) . '</strong>' : '' ?><?= $q ? ' que coinciden con “<strong>' . e($q) . '</strong>”' : '' ?>
            <?php if ($q || $cat): ?> · <a href="index.php">Quitar filtros</a><?php endif; ?>
        </p>
        <?php if (!$notes): ?><div class="card empty">No se encontraron notas.</div><?php endif; ?>
        <div class="notes">
            <?php foreach ($notes as $n):
                $content = e($n['content']);
                $title = e($n['title']);
                if ($q !== '') { // resaltar coincidencias
                    $re = '/(' . preg_quote(e($q), '/') . ')/iu';
                    $content = preg_replace($re, '<mark>$1</mark>', $content);
                    $title = preg_replace($re, '<mark>$1</mark>', $title);
                } ?>
                <article class="note" style="background:<?= e($n['color']) ?>">
                    <div class="note-head">
                        <h3><?= $n['pinned'] ? '📌 ' : '' ?><?= $title ?></h3>
                    </div>
                    <p><?= nl2br($content) ?></p>
                    <div class="note-foot">
                        <span class="badge"><?= e($n['category']) ?></span>
                        <small class="muted"><?= date('d/m/Y H:i', strtotime($n['updated_at'])) ?></small>
                    </div>
                    <div class="actions" style="margin-top:10px">
                        <form method="post" action="index.php?action=pin"><input type="hidden" name="id" value="<?= $n['id'] ?>">
                            <button class="btn btn-sm btn-outline"><?= $n['pinned'] ? 'Desfijar' : 'Fijar' ?></button></form>
                        <a class="btn btn-sm btn-outline" href="index.php?action=edit&id=<?= $n['id'] ?>">✏ Editar</a>
                        <form method="post" action="index.php?action=delete" onsubmit="return confirm('¿Eliminar la nota?')"><input type="hidden" name="id" value="<?= $n['id'] ?>">
                            <button class="btn btn-sm btn-danger">🗑</button></form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</div>
<style>
.layout-notes{display:grid;grid-template-columns:240px 1fr;gap:22px;align-items:start}
.cat{display:flex;justify-content:space-between;padding:8px 10px;border-radius:8px;color:var(--text)}
.cat span{background:#f3f4f6;border-radius:999px;padding:0 8px;font-size:.8rem}
.cat.active{background:#fef9c3;font-weight:700}
.search{display:flex;gap:10px}
.notes{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:16px}
.note{border-radius:12px;padding:16px;box-shadow:0 2px 6px rgba(0,0,0,.08);display:flex;flex-direction:column}
.note p{flex:1;margin:8px 0;font-size:.92rem;line-height:1.45}
.note-foot{display:flex;justify-content:space-between;align-items:center}
.note .badge{background:rgba(255,255,255,.7)}
mark{background:#fde047;padding:0 2px;border-radius:3px}
</style>
