<?php
// Form for submitting a new training request.

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/training_letter/classes/form/request_form.php');

use local_training_letter\form\request_form;

require_login();

$context = context_system::instance();
require_capability('local/training_letter:submit', $context);

$url = new moodle_url('/local/training_letter/apply.php');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('newrequest', 'local_training_letter'));
$PAGE->set_heading(get_string('newrequest', 'local_training_letter'));

$form = new request_form();
$form->set_data((object) [
    'studentname' => fullname($USER),
    'rollno' => $USER->idnumber ?: $USER->username,
]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/training_letter/index.php'));
} else if ($data = $form->get_data()) {
    $now = time();
    $record = (object) [
        'userid' => $USER->id,
        'studentname' => $data->studentname,
        'rollno' => $data->rollno,
        'branch' => $data->branch,
        'semester' => $data->semester,
        'organization' => $data->organization,
        'postaladdress' => $data->postaladdress,
        'trainingtype' => $data->trainingtype,
        'trainingtitle' => $data->trainingtitle,
        'duration' => $data->duration,
        'startdate' => $data->startdate,
        'status' => 'pending',
        'coordinatornote' => null,
        'timecreated' => $now,
        'timemodified' => $now,
    ];
    $DB->insert_record('local_training_letter', $record);
    redirect(
        new moodle_url('/local/training_letter/myrequests.php'),
        get_string('requestsaved', 'local_training_letter'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

echo $OUTPUT->header();
echo html_writer::start_div('training-letter-page');
echo html_writer::link(new moodle_url('/local/training_letter/index.php'), get_string('backtoportal', 'local_training_letter'), ['class' => 'training-letter-back']);
echo html_writer::start_div('training-letter-form-card');
echo html_writer::tag('span', get_string('cellname', 'local_training_letter'), ['class' => 'training-letter-eyebrow']);
echo $OUTPUT->heading(get_string('newrequest', 'local_training_letter'), 2);
echo html_writer::tag('p', get_string('formintro', 'local_training_letter'), ['class' => 'training-letter-help']);
$form->display();
echo html_writer::end_div();
echo html_writer::end_div();
echo $OUTPUT->footer();
