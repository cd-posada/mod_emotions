<?php
/**
 * mod_emotions: estructura de restauracion de la actividad.
 */
defined('MOODLE_INTERNAL') || die();

class restore_emotions_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {
        $paths = [new restore_path_element('emotions', '/activity/emotions')];
        return $this->prepare_activity_structure($paths);
    }

    protected function process_emotions($data) {
        global $DB;
        $data = (object)$data;
        $data->course = $this->get_courseid();
        $data->timemodified = time();
        $newid = $DB->insert_record('emotions', $data);
        $this->apply_activity_instance($newid);
    }

    protected function after_execute() {
        $this->add_related_files('mod_emotions', 'intro', null);
    }
}
