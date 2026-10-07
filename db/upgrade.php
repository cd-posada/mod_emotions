<?php
defined('MOODLE_INTERNAL') || die();

function xmldb_emotions_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2026100600) {
        // Regla de finalizacion: el estudiante acepto el consentimiento informado.
        $table = new xmldb_table('emotions');
        $field = new xmldb_field('completionconsent', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'timemodified');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2026100600, 'emotions');
    }

    return true;
}
