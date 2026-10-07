<?php
/**
 * mod_emotions/classes/privacy/provider.php
 *
 * La actividad solo marca en que cursos se activa el registro facial: no guarda
 * datos personales. Las detecciones las guarda local_emotions_send, que declara
 * sus propios datos en su provider.
 */
namespace mod_emotions\privacy;

defined('MOODLE_INTERNAL') || die();

class provider implements \core_privacy\local\metadata\null_provider {

    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}
