<?php
/**
 * mod/emotions/db/access.php
 *
 * Capabilities estandar de un modulo de actividad:
 *  - addinstance: quien puede agregar la actividad a un curso (Moodle la
 *    exige para todo mod_*; sin ella emite un aviso de depuracion cada vez
 *    que se agrega la actividad).
 *  - view: quien puede abrir la pagina de la actividad (view.php).
 */
defined('MOODLE_INTERNAL') || die();

$capabilities = [

    'mod/emotions:addinstance' => [
        'riskbitmask'  => RISK_XSS,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => [
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW,
        ],
        'clonepermissionsfrom' => 'moodle/course:manageactivities',
    ],

    'mod/emotions:view' => [
        'captype'      => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => [
            'student'        => CAP_ALLOW,
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW,
        ],
    ],

];
