<?php /* Menú de inicio: abre cada ejercicio (pensado para WAMP: http://localhost/Ejercicios_PHP_MVC/) */ ?>
<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><title>Ejercicios PHP MVC</title>
<style>
body{font-family:"Segoe UI",Arial,sans-serif;background:#f4f6fb;margin:0;color:#1f2937}
header{background:#4f46e5;color:#fff;padding:26px 40px}h1{margin:0}header p{margin:6px 0 0;opacity:.9}
main{max-width:1100px;margin:30px auto;padding:0 20px;display:grid;grid-template-columns:repeat(auto-fill,minmax(310px,1fr));gap:18px}
.c{background:#fff;border-radius:12px;padding:18px;text-decoration:none;color:inherit;box-shadow:0 2px 10px rgba(0,0,0,.06);display:flex;flex-direction:column;gap:6px;transition:.15s}
.c:hover{transform:translateY(-3px);box-shadow:0 8px 20px rgba(79,70,229,.18)}
.c b{background:#eef2ff;color:#4f46e5;width:34px;height:34px;border-radius:50%;display:grid;place-items:center}
.c span{font-weight:700;font-size:1.1rem}.c small{color:#6b7280}
footer{text-align:center;color:#6b7280;padding:20px;font-size:.85rem}
</style></head><body>
<header><h1>Ejercicios PHP — Arquitectura MVC</h1><p>Selecciona un ejercicio para abrirlo.</p></header>
<main>
<a class="c" href="01_Lista_de_Tareas/codigo/"><b>1</b><span>Lista de Tareas</span><small>Agregar, eliminar y marcar como completadas las tareas de una lista.</small></a>
<a class="c" href="02_Calculadora_de_Propinas/codigo/"><b>2</b><span>Calculadora de Propinas</span><small>Calcula automáticamente la propina según el monto de la cuenta y el porcentaje elegido.</small></a>
<a class="c" href="03_Generador_de_Contrasenas/codigo/"><b>3</b><span>Generador de Contraseñas Seguras</span><small>Genera contraseñas aleatorias seguras, ajustando la longitud y los tipos de caracteres.</small></a>
<a class="c" href="04_Gestor_de_Gastos/codigo/"><b>4</b><span>Gestor de Gastos</span><small>Registra gastos diarios, los categoriza y muestra un resumen mensual.</small></a>
<a class="c" href="05_Sistema_de_Reservas/codigo/"><b>5</b><span>Sistema de Reservas</span><small>Permite reservar citas en línea mostrando la disponibilidad y confirmando la reserva.</small></a>
<a class="c" href="06_Gestor_de_Notas/codigo/"><b>6</b><span>Gestor de Notas</span><small>Escribir y guardar notas organizadas por categorías, con búsqueda.</small></a>
<a class="c" href="07_Calendario_de_Eventos/codigo/"><b>7</b><span>Calendario de Eventos</span><small>Calendario interactivo para agregar, editar y eliminar eventos, con recordatorios y notificaciones.</small></a>
<a class="c" href="08_Plataforma_de_Recetas/codigo/"><b>8</b><span>Plataforma de Recetas</span><small>Buscar, guardar y compartir recetas de cocina con filtros por tipo de comida, ingredientes, etc.</small></a>
<a class="c" href="09_Juego_de_Memoria/codigo/"><b>9</b><span>Juego de Memoria</span><small>Juego clásico de emparejar cartas con imágenes, con niveles de dificultad y puntajes.</small></a>
<a class="c" href="10_Plataforma_de_Encuestas/codigo/"><b>10</b><span>Plataforma de Encuestas</span><small>Crear y responder encuestas en línea, analizar resultados y generar informes visuales.</small></a>
<a class="c" href="11_Cronometro_Online/codigo/"><b>11</b><span>Cronómetro Online</span><small>Cronómetro con inicio, pausa, reinicio y registro de vueltas o tiempos parciales.</small></a>
</main>
<footer>PHP <?= PHP_VERSION ?><?= version_compare(PHP_VERSION, '8.0.0', '<') ? ' — ⚠ Se requiere PHP 8 o superior: cámbialo en el menú de WAMP (PHP → Versión)' : '' ?></footer>
</body></html>
