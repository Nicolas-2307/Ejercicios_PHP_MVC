<?php $dias = ['', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom']; ?>
<h1>Reserva tu cita en línea</h1>
<p class="subtitle">1. Elige el servicio · 2. Elige fecha y hora disponible · 3. Ingresa tus datos y confirma.</p>

<div class="grid grid-3" style="grid-template-columns:repeat(4,1fr);margin-bottom:22px">
    <?php foreach ($services as $id => $s): ?>
        <a href="index.php?service=<?= $id ?>&date=<?= e($date) ?>" class="card svc <?= $id === $serviceId ? 'sel' : '' ?>">
            <div style="font-size:2rem"><?= $s['icon'] ?></div>
            <strong><?= e($s['name']) ?></strong>
            <div class="muted"><?= $s['duration'] ?> min · <?= money($s['price']) ?></div>
        </a>
    <?php endforeach; ?>
</div>

<div class="card">
    <h2>Disponibilidad — próximos 7 días</h2>
    <div class="week">
        <?php foreach ($week as $d => $free): $n = date('N', strtotime($d)); ?>
            <a href="index.php?service=<?= $serviceId ?>&date=<?= $d ?>" class="day <?= $d === $date ? 'sel' : '' ?> <?= $free ? '' : 'full' ?>">
                <span><?= $dias[$n] ?></span><strong><?= date('d', strtotime($d)) ?></strong>
                <small><?= $n == 7 ? 'Cerrado' : ($free ? "$free libres" : 'Lleno') ?></small>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<form method="post" action="index.php?action=store" class="grid grid-2" novalidate>
    <input type="hidden" name="service_id" value="<?= $serviceId ?>">
    <div class="card">
        <div class="row" style="justify-content:space-between;align-items:center">
            <h2 style="margin:0">Horarios</h2>
            <input type="date" name="date" value="<?= e($date) ?>" min="<?= date('Y-m-d') ?>" style="max-width:180px"
                   onchange="location='index.php?service=<?= $serviceId ?>&date='+this.value">
        </div>
        <p class="muted" style="margin:8px 0 14px"><?= e($services[$serviceId]['name']) ?> · <?= date('d/m/Y', strtotime($date)) ?></p>
        <?php if ($isSunday): ?>
            <div class="alert alert-warn">Los domingos no hay atención. Selecciona otro día.</div>
        <?php else: ?>
            <div class="slots">
                <?php foreach ($availability as $slot => $free): ?>
                    <label class="slot <?= $free ? '' : 'off' ?>">
                        <input type="radio" name="time" value="<?= $slot ?>" <?= $free ? '' : 'disabled' ?> <?= ($old['time'] ?? '') === $slot ? 'checked' : '' ?>>
                        <span><?= $slot ?><small><?= $free ? 'Disponible' : 'Ocupado' ?></small></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <p class="muted" style="font-size:.8rem;margin-top:10px">🟩 Disponible &nbsp; ⬜ Ocupado / no disponible</p>
        <?php endif; ?>
    </div>
    <div class="card">
        <h2>Tus datos</h2>
        <div class="field"><label for="name">Nombre completo</label><input type="text" id="name" name="name" value="<?= e($old['name'] ?? '') ?>"></div>
        <div class="field"><label for="email">Correo electrónico</label><input type="email" id="email" name="email" value="<?= e($old['email'] ?? '') ?>"></div>
        <div class="field"><label for="phone">Teléfono (opcional)</label><input type="text" id="phone" name="phone" value="<?= e($old['phone'] ?? '') ?>"></div>
        <button class="btn btn-block" <?= $isSunday ? 'disabled' : '' ?>>Continuar →</button>
    </div>
</form>

<style>
.svc{color:var(--text);text-align:center;border:2px solid transparent;margin:0}
.svc.sel{border-color:var(--accent);background:#fdf2f8}
.week{display:grid;grid-template-columns:repeat(7,1fr);gap:8px}
.day{display:flex;flex-direction:column;align-items:center;padding:10px;border-radius:10px;border:1.5px solid var(--line);color:var(--text)}
.day strong{font-size:1.3rem}.day small{font-size:.72rem;color:var(--ok)}
.day.full small{color:var(--err)}
.day.sel{background:var(--accent);color:#fff;border-color:var(--accent)}.day.sel small{color:#fff}
.slots{display:grid;grid-template-columns:repeat(4,1fr);gap:8px}
.slot input{display:none}
.slot span{display:flex;flex-direction:column;align-items:center;padding:10px;border-radius:8px;border:1.5px solid #86efac;background:#f0fdf4;font-weight:700;cursor:pointer}
.slot small{font-weight:400;font-size:.7rem;color:var(--muted)}
.slot.off span{background:#f3f4f6;border-color:var(--line);color:#9ca3af;cursor:not-allowed;text-decoration:line-through}
.slot input:checked+span{background:var(--accent);border-color:var(--accent);color:#fff}
.slot input:checked+span small{color:#fff}
</style>
