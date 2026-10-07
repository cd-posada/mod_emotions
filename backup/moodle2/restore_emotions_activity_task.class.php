<?php
/**
 * mod_emotions: tarea de restauracion de la actividad.
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/emotions/backup/moodle2/restore_emotions_stepslib.php');

class restore_emotions_activity_task extends restore_activity_task {

    protected function define_my_settings() {
    }

    protected function define_my_steps() {
        $this->add_step(new restore_emotions_activity_structure_step('emotions_structure', 'emotions.xml'));
    }

    public static function define_decode_contents() {
        return [new restore_decode_content('emotions', ['intro'], 'emotions')];
    }

    public static function define_decode_rules() {
        return [
            new restore_decode_rule('EMOTIONSVIEWBYID', '/mod/emotions/view.php?id=$1', 'course_module'),
            new restore_decode_rule('EMOTIONSINDEX', '/mod/emotions/index.php?id=$1', 'course'),
        ];
    }

    public static function define_restore_log_rules() {
        return [];
    }

    public static function define_restore_log_rules_for_course() {
        return [];
    }
}
