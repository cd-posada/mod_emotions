<?php
/**
 * mod_emotions: tarea de respaldo de la actividad. Necesaria para respaldar,
 * restaurar, duplicar o mandar a la papelera un curso que tenga esta actividad.
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/emotions/backup/moodle2/backup_emotions_stepslib.php');

class backup_emotions_activity_task extends backup_activity_task {

    protected function define_my_settings() {
    }

    protected function define_my_steps() {
        $this->add_step(new backup_emotions_activity_structure_step('emotions_structure', 'emotions.xml'));
    }

    public static function encode_content_links($content) {
        global $CFG;
        $base = preg_quote($CFG->wwwroot, '/');
        $content = preg_replace('/(' . $base . '\/mod\/emotions\/index.php\?id\=)([0-9]+)/', '$@EMOTIONSINDEX*$2@$', $content);
        return preg_replace('/(' . $base . '\/mod\/emotions\/view.php\?id\=)([0-9]+)/', '$@EMOTIONSVIEWBYID*$2@$', $content);
    }
}
