<?php
defined('MOODLE_INTERNAL') || die();

$plugin->component = 'mod_emotions';
$plugin->version   = 2026100600;
$plugin->requires  = 2024042200; // Moodle 4.4+ (probado en 4.5)
$plugin->supported = [404, 405];
$plugin->maturity  = MATURITY_BETA;
$plugin->release   = 'v3.3 - finalizacion al aceptar el consentimiento informado';
$plugin->dependencies = [
    // La actividad solo activa el registro facial en el curso; el registro lo hace local_emotions_send.
    'local_emotions_send' => 2026100600,
];
