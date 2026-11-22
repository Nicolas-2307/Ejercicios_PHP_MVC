<!-- Notificaciones: consulta periódicamente action=reminders y muestra avisos -->
<div id="toasts"></div>
<style>
#toasts{position:fixed;right:20px;bottom:20px;display:flex;flex-direction:column;gap:10px;z-index:99}
.toast{background:#111827;color:#fff;padding:14px 18px;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,.25);max-width:330px;animation:in .3s}
.toast b{display:block;margin-bottom:2px}
@keyframes in{from{transform:translateY(20px);opacity:0}}
</style>
<script>
(function () {
    let shown = [];
    try { shown = JSON.parse(sessionStorage.getItem('notified') || '[]'); } catch (e) {}
    function toast(title, body) {
        const t = document.createElement('div');
        t.className = 'toast';
        t.innerHTML = '<b>🔔 ' + title + '</b>' + body;
        document.getElementById('toasts').appendChild(t);
        setTimeout(() => t.remove(), 9000);
    }
    async function check() {
        const res = await fetch('index.php?action=reminders');
        const list = await res.json();
        list.forEach(ev => {
            if (shown.includes(ev.id)) return;
            const when = ev.minutes_left <= 0 ? 'está comenzando ahora' :
                ev.minutes_left < 60 ? 'comienza en ' + ev.minutes_left + ' min' :
                ev.minutes_left < 1440 ? 'comienza en ' + Math.round(ev.minutes_left / 60) + ' h' :
                'comienza en ' + Math.round(ev.minutes_left / 1440) + ' día(s)';
            const body = ev.date + ' ' + ev.time + ' — ' + when;
            toast(ev.title, body);
            if ('Notification' in window && Notification.permission === 'granted') {
                new Notification('Recordatorio: ' + ev.title, {body});
            }
            shown.push(ev.id);
        });
        try { sessionStorage.setItem('notified', JSON.stringify(shown)); } catch (e) {}
    }
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
    check();
    setInterval(check, 60000); // cada minuto
})();
</script>
