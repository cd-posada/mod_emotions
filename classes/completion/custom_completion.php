<?php
declare(strict_types=1);

namespace mod_emotions\completion;

defined('MOODLE_INTERNAL') || die();

use core_completion\activity_custom_completion;

/**
 * Finalizacion de la actividad Emotions: "completionconsent" = el estudiante acepto el
 * consentimiento informado del registro facial en el curso (local_emotions_send). Restringiendo
 * las secciones del curso a que esta actividad este completada, Moodle bloquea el contenido
 * hasta que el estudiante acepte (y lo vuelve a bloquear si retira el consentimiento).
 */
class custom_completion extends activity_custom_completion {

    public function get_state(string $rule): int {
        $this->validate_rule($rule);
        $consent = '\local_emotions_send\local\consent';
        $accepted = class_exists($consent) && $consent::has_accepted($this->userid, (int) $this->cm->course);
        return $accepted ? COMPLETION_COMPLETE : COMPLETION_INCOMPLETE;
    }

    public static function get_defined_custom_rules(): array {
        return ['completionconsent'];
    }

    public function get_custom_rule_descriptions(): array {
        return ['completionconsent' => get_string('completiondetail:consent', 'emotions')];
    }

    public function get_sort_order(): array {
        return ['completionview', 'completionconsent'];
    }
}
