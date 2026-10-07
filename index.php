<?php
require_once(dirname(dirname(__DIR__)) . '/config.php');
$id     = required_param('id', PARAM_INT);
$course = get_course($id);
require_login($course);
$PAGE->set_url('/mod/emotions/index.php', ['id' => $id]);
$PAGE->set_title('Emotions');
$PAGE->set_heading($course->fullname);
echo $OUTPUT->header();
echo html_writer::tag('h3', 'Actividades Emotions en este curso');
echo html_writer::tag('p', 'Accede a la actividad directamente desde el curso.');
echo $OUTPUT->footer();