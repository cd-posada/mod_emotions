<?php
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_emotions_mod_form extends moodleform_mod {
    public function definition() {
        $mform = $this->_form;
        $mform->addElement('text', 'name', get_string('modulename', 'emotions'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $this->standard_intro_elements();
        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    /** Regla de finalizacion: el estudiante acepto el consentimiento informado (local_emotions_send). */
    public function add_completion_rules() {
        $mform = $this->_form;
        $el = 'completionconsent' . $this->get_suffix();
        $mform->addElement('checkbox', $el, '', get_string('completionconsent', 'emotions'));
        $mform->setDefault($el, 1);
        return [$el];
    }

    public function completion_rule_enabled($data) {
        return !empty($data['completionconsent' . $this->get_suffix()]);
    }

    public function data_postprocessing($data) {
        parent::data_postprocessing($data);
        if (!empty($data->completionunlocked)) {
            $el = 'completionconsent' . $this->get_suffix();
            if (empty($data->{$el})) {
                $data->{$el} = 0;
            }
        }
    }
}