<?php
/**
 * mod_emotions: estructura de respaldo de la actividad (solo la fila de la
 * tabla emotions; la actividad no guarda datos de usuario).
 */
defined('MOODLE_INTERNAL') || die();

class backup_emotions_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {
        $emotions = new backup_nested_element('emotions', ['id'], ['name', 'intro', 'introformat', 'timemodified', 'completionconsent']);
        $emotions->set_source_table('emotions', ['id' => backup::VAR_ACTIVITYID]);
        $emotions->annotate_files('mod_emotions', 'intro', null);
        return $this->prepare_activity_structure($emotions);
    }
}
