<h1>Mis tareas</h1>
<p class="subtitle">Agrega, completa y elimina las tareas de tu lista.</p>

<div class="grid grid-3" style="margin-bottom:22px">
    <div class="card"><div class="muted">Total</div><div class="stat"><?= $stats['total'] ?></div></div>
    <div class="card"><div class="muted">Pendientes</div><div class="stat" style="color:var(--warn)"><?= $stats['pending'] ?></div></div>
    <div class="card"><div class="muted">Completadas</div><div class="stat" style="color:var(--ok)"><?= $stats['done'] ?></div>
        <?php $pct = $stats['total'] ? round($stats['done'] * 100 / $stats['total']) : 0; ?>
        <div class="bar" style="margin-top:8px"><span style="width:<?= $pct ?>%;background:var(--ok)"></span></div>
    </div>
</div>

<div class="card">
    <h2>Nueva tarea</h2>
    <form method="post" action="index.php?action=store" class="row">
        <div style="flex:3"><label for="title">¿Qué necesitas hacer?</label>
            <input type="text" id="title" name="title" placeholder="Ej: Estudiar para el parcial de redes" maxlength="120"></div>
        <div><label for="priority">Prioridad</label>
            <select id="priority" name="priority">
                <?php foreach (Task::PRIORITIES as $k => $v): ?>
                    <option value="<?= $k ?>" <?= $k === 'media' ? 'selected' : '' ?>><?= $v ?></option>
                <?php endforeach; ?>
            </select></div>
        <div class="fit"><button class="btn" type="submit">➕ Agregar</button></div>
    </form>
</div>

<div class="card">
    <div class="row" style="justify-content:space-between;align-items:center;margin-bottom:6px">
        <div class="tabs" style="margin:0">
            <?php foreach (['all' => 'Todas', 'pending' => 'Pendientes', 'done' => 'Completadas'] as $k => $v): ?>
                <a href="index.php?filter=<?= $k ?>" class="<?= $filter === $k ? 'active' : '' ?>"><?= $v ?></a>
            <?php endforeach; ?>
        </div>
        <?php if ($stats['done'] > 0): ?>
            <form method="post" action="index.php?action=clear">
                <button class="btn btn-sm btn-outline">🧹 Limpiar completadas</button></form>
        <?php endif; ?>
    </div>

    <?php if (!$tasks): ?>
        <div class="empty">No hay tareas para mostrar.</div>
    <?php endif; ?>
    <ul class="task-list">
        <?php foreach ($tasks as $t): ?>
            <li class="task <?= $t['done'] ? 'done' : '' ?>">
                <form method="post" action="index.php?action=toggle" class="inline">
                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                    <input type="hidden" name="filter" value="<?= e($filter) ?>">
                    <button class="check" title="Marcar como completada"><?= $t['done'] ? '✔' : '' ?></button>
                </form>
                <div class="task-body">
                    <div class="task-title"><?= e($t['title']) ?></div>
                    <div class="muted" style="font-size:.8rem">Creada: <?= e($t['created_at']) ?></div>
                </div>
                <span class="badge prio-<?= $t['priority'] ?>"><?= Task::PRIORITIES[$t['priority']] ?></span>
                <form method="post" action="index.php?action=delete" class="inline" onsubmit="return confirm('¿Eliminar esta tarea?')">
                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                    <input type="hidden" name="filter" value="<?= e($filter) ?>">
                    <button class="btn btn-sm btn-danger" title="Eliminar">🗑</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<style>
.task-list{list-style:none}
.task{display:flex;align-items:center;gap:14px;padding:12px 6px;border-bottom:1px solid var(--line)}
.task-body{flex:1}
.task-title{font-weight:600}
.task.done .task-title{text-decoration:line-through;color:var(--muted)}
.check{width:28px;height:28px;border-radius:50%;border:2px solid var(--accent);background:#fff;color:#fff;cursor:pointer;font-weight:700}
.task.done .check{background:var(--ok);border-color:var(--ok)}
.prio-alta{background:#fee2e2;color:#991b1b}.prio-media{background:#fef3c7;color:#92400e}.prio-baja{background:#dcfce7;color:#166534}
</style>
