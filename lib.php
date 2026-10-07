<?php
defined('MOODLE_INTERNAL') || die();

function emotions_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_ARCHETYPE:   return MOD_ARCHETYPE_OTHER;
        case FEATURE_BACKUP_MOODLE2:  return true;
        case FEATURE_SHOW_DESCRIPTION: return true;
        case FEATURE_COMPLETION_HAS_RULES: return true;
        default: return null;
    }
}

function emotions_add_instance($data, $mform = null) {
    global $DB;
    $record               = new stdClass();
    $record->course       = $data->course;
    $record->name         = $data->name;
    $record->intro        = $data->intro        ?? '';
    $record->introformat  = $data->introformat   ?? FORMAT_HTML;
    $record->timemodified = time();
    $record->completionconsent = empty($data->completionconsent) ? 0 : 1;
    return $DB->insert_record('emotions', $record);
}

function emotions_update_instance($data, $mform = null) {
    global $DB;
    $record               = new stdClass();
    $record->id           = $data->instance;
    $record->course       = $data->course;
    $record->name         = $data->name;
    $record->intro        = $data->intro        ?? '';
    $record->introformat  = $data->introformat   ?? FORMAT_HTML;
    $record->timemodified = time();
    $record->completionconsent = empty($data->completionconsent) ? 0 : 1;
    return $DB->update_record('emotions', $record);
}

function emotions_delete_instance($id) {
    global $DB;
    if (!$DB->record_exists('emotions', ['id' => $id])) {
        return false;
    }
    return $DB->delete_records('emotions', ['id' => $id]);
}

/**
 * Informacion cacheada de la actividad: las reglas de finalizacion propias que lee
 * \mod_emotions\completion\custom_completion (solo con finalizacion automatica).
 */
function emotions_get_coursemodule_info($coursemodule) {
    global $DB;
    $rec = $DB->get_record('emotions', ['id' => $coursemodule->instance], 'id, name, intro, introformat, completionconsent');
    if (!$rec) {
        return false;
    }
    $info = new cached_cm_info();
    $info->name = $rec->name;
    if ($coursemodule->showdescription) {
        $info->content = format_module_intro('emotions', $rec, $coursemodule->id, false);
    }
    if ($coursemodule->completion == COMPLETION_TRACKING_AUTOMATIC) {
        $info->customdata['customcompletionrules']['completionconsent'] = (int)$rec->completionconsent;
    }
    return $info;
}
