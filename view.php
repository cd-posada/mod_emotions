<?php
/**
 * mod/emotions/view.php
 *
 * Pagina informativa de la actividad Emotions.
 * El widget flotante ya esta activo (inyectado por local_emotions_send/lib.php).
 *
 * Incluye una "Vista de diagnostico" con el diseno de la app de referencia
 * web_onnx_test_v5: video espejado con recuadro del rostro y panel de
 * diagnostico (modelo, emocion, nivel, probabilidad, Indice de Atencion,
 * desglose de los 4 estados), estado y consola de registro. No crea un
 * segundo motor: camera_global.js (el mismo que alimenta el widget)
 * redibuja cualquier <canvas data-emotions-view="full"> y llena
 * #emotions_diag_status / #emotions_diag_log.
 *
 * Seguridad:
 *  - require_login($course, true, $cm) valida sesion + matricula en el curso.
 *  - El enlace al reporte solo se muestra si el usuario tiene la capability
 *    propia 'local/emotions_send:viewreport' en el contexto del curso,
 *    en lugar de una capability generica ajena al plugin.
 */
require_once(dirname(dirname(__DIR__)) . '/config.php');

global $USER;

$id     = required_param('id', PARAM_INT);
$cm     = get_coursemodule_from_id('emotions', $id, 0, false, MUST_EXIST);
$course = get_course($cm->course);

require_login($course, true, $cm);

$context     = context_module::instance($cm->id);
require_capability('mod/emotions:view', $context);
$coursectx   = context_course::instance($course->id);
$can_view_report = has_capability('local/emotions_send:viewreport', $coursectx);
$reporturl   = new moodle_url('/local/emotions_send/report.php',
                              ['courseid' => $course->id]);

$PAGE->set_url('/mod/emotions/view.php', ['id' => $id]);
$PAGE->set_title('Registro de Atencion');
$PAGE->set_heading($course->fullname);
$PAGE->set_context($context);

echo $OUTPUT->header();
?>

<div style="max-width:800px;margin:0 auto;padding:24px 16px;font-family:sans-serif;">

    <h2 style="margin:0 0 4px;font-size:1.25em;color:#212529;">
        Registro de Atencion
    </h2>
    <p style="color:#6c757d;margin:0 0 22px;font-size:0.88em;">
        <?php echo s($course->fullname); ?>
    </p>

    <div style="background:#e8f4fd;border-left:4px solid #0d6efd;
                padding:14px 18px;margin-bottom:22px;border-radius:0 6px 6px 0;">
        <div style="font-weight:700;color:#0d6efd;margin-bottom:7px;font-size:0.93em;">
            Aviso de privacidad y uso de camara
        </div>
        <ul style="margin:0;padding-left:18px;color:#333;font-size:0.86em;line-height:1.75;">
            <li>
                La camara se usa <strong>unicamente para detectar el estado de
                atencion</strong> durante las actividades del curso.
            </li>
            <li>
                <strong>El analisis ocurre completamente en su navegador.</strong>
                Ninguna imagen de la camara se envia a Moodle ni a ningun
                servidor externo.
            </li>
            <li>
                <strong>No se graba ni almacena ningun video o fotografia.</strong>
                Solo se registra la emocion detectada
                (Concentrado, Aburrido, Confundido o Frustrado),
                su nivel de intensidad, el nivel de confianza y la marca de tiempo.
            </li>
            <li>
                Los datos son visibles unicamente por el docente del curso,
                con fines pedagogicos.
            </li>
            <li>
                Puede revocar el permiso de camara en cualquier momento
                desde la configuracion de su navegador.
            </li>
            <li>
                Puede solicitar la exportacion o eliminacion de sus datos
                desde su area de privacidad en Moodle (Preferencias &gt;
                Privacidad y politicas).
            </li>
        </ul>
    </div>

    <div style="background:#fff;border:1px solid #e0e0e0;border-radius:8px;
                padding:16px 18px;margin-bottom:20px;">
        <h3 style="margin:0 0 14px;font-size:0.92em;font-weight:700;color:#333;">
            Como funciona el registro
        </h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;">
            <?php
            $steps = [
                ['1. Permiso de camara',
                 'El navegador solicita acceso a la camara al entrar al curso. Acepte el permiso para activar el registro.'],
                ['2. Deteccion de rostro',
                 'Una vez por segundo se ubica el rostro en la imagen, dentro de su navegador. No hay grabacion de video ni audio.'],
                ['3. Analisis local',
                 'Un modelo de IA que corre en su propio equipo estima el nivel (0-3) de aburrimiento, concentracion, confusion y frustracion.'],
                ['4. Widget flotante',
                 'El estado actual se muestra en la esquina inferior derecha mientras navega por el curso.'],
                ['5. Registro para el docente',
                 'Cada deteccion queda registrada junto con el contenido que estaba revisando.'],
            ];
            foreach ($steps as $s): ?>
            <div style="background:#f8f9fa;border-radius:6px;padding:11px 13px;">
                <div style="font-weight:700;color:#0d6efd;font-size:0.86em;margin-bottom:4px;">
                    <?php echo s($s[0]); ?>
                </div>
                <p style="margin:0;font-size:0.80em;color:#555;line-height:1.5;">
                    <?php echo s($s[1]); ?>
                </p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Vista de diagnostico: mismo diseno que web_onnx_test_v5/index.html -->
    <style>
        .emotions-diag {
            background: #14161a; color: #e6e6e6; border-radius: 8px;
            padding: 16px; margin-bottom: 20px;
            font-family: "Segoe UI", Arial, sans-serif;
        }
        .emotions-diag h3 { font-size: 0.95em; margin: 0 0 12px; color: #e6e6e6; }
        .emotions-diag .ed-layout { display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-start; }
        .emotions-diag .ed-panel {
            background: #1d2026; border: 1px solid #2c3038; border-radius: 8px; padding: 12px;
        }
        .emotions-diag .ed-controls { flex: 1 1 180px; min-width: 180px; font-size: 0.85em; line-height: 1.6; }
        .emotions-diag .ed-label { color: #9aa0a8; }
        .emotions-diag .ed-video { flex: 3 1 400px; max-width: 640px; }
        .emotions-diag canvas { width: 100%; display: block; border-radius: 8px; background: #000; }
        .emotions-diag .ed-log {
            margin-top: 14px; height: 180px; overflow-y: auto; background: #101216;
            border: 1px solid #2c3038; border-radius: 6px; padding: 8px 10px;
            font-family: Consolas, "Courier New", monospace; font-size: 0.78em;
            color: #b8c0c8; white-space: pre-wrap;
        }
    </style>
    <div class="emotions-diag">
        <h3>Vista de diagnostico del registro</h3>
        <div class="ed-layout">
            <div class="ed-panel ed-controls">
                <div class="ed-label">Modelo</div>
                <div><?php echo class_exists('\local_emotions_send\local\fer_model')
                    ? s(\local_emotions_send\local\fer_model::MODEL_LABEL) : '—'; ?></div>
                <div class="ed-label" style="margin-top:8px;">Estado</div>
                <div id="emotions_diag_status">Verificando...</div>
                <div class="ed-label" style="margin-top:8px;">Procesamiento</div>
                <div>100% en el navegador (ONNX Runtime Web + OpenCV.js), 1 muestra por segundo.</div>
            </div>
            <div class="ed-panel ed-video">
                <canvas id="emotions_diag_canvas" data-emotions-view="full" width="640" height="480"></canvas>
            </div>
        </div>
        <div class="ed-log" id="emotions_diag_log"></div>
    </div>

    <?php if ($can_view_report): ?>
    <div style="background:#d4edda;border-left:4px solid #28a745;
                padding:12px 16px;border-radius:0 6px 6px 0;">
        <div style="font-weight:700;color:#155724;margin-bottom:4px;font-size:0.88em;">
            Acceso al reporte
        </div>
        <p style="margin:0 0 8px;font-size:0.83em;color:#155724;">
            Consulte el reporte de emociones de los estudiantes de este curso.
        </p>
        <a href="<?php echo $reporturl->out(false); ?>"
           style="display:inline-block;background:#28a745;color:#fff;
                  text-decoration:none;padding:6px 16px;border-radius:5px;font-size:0.83em;">
            Ver reporte de emociones
        </a>
    </div>
    <?php endif; ?>

</div>

<script>
// El estado lo actualiza camera_global.js; si el widget no se inyecto
// (usuario no matriculado, invitado, etc.) no hay nada que lo haga.
document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('emotions_diag_status');
    if (el && !document.getElementById('emotions_widget')) {
        el.textContent = 'El sistema de registro no esta activo para este usuario en este curso.';
    }
});
</script>

<?php echo $OUTPUT->footer(); ?>