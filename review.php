<?php
// Coordinator page for changing a request's approval status.

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/local/training_letter/classes/form/review_form.php');

use local_training_letter\form\review_form;

require_login();

$context = context_system::instance();
require_capability('local/training_letter:approve', $context);

$id = required_param('id', PARAM_INT);
$request = $DB->get_record('local_training_letter', ['id' => $id], '*', MUST_EXIST);
$url = new moodle_url('/local/training_letter/review.php', ['id' => $id]);
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('reviewrequest', 'local_training_letter'));
$PAGE->set_heading(get_string('reviewrequest', 'local_training_letter'));

$form = new review_form($url, ['requestid' => $id]);
$form->set_data((object) [
    'id' => $id,
    'status' => $request->status,
    'coordinatornote' => $request->coordinatornote,
]);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/training_letter/manage.php'));
} else if ($data = $form->get_data()) {
    $request->status = $data->status;
    $request->coordinatornote = $data->coordinatornote;
    $request->timemodified = time();
    $DB->update_record('local_training_letter', $request);

    redirect(
        new moodle_url('/local/training_letter/manage.php'),
        get_string('decision_saved', 'local_training_letter'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

echo $OUTPUT->header();
echo html_writer::start_div('training-letter-page');
echo html_writer::link(new moodle_url('/local/training_letter/manage.php'), get_string('backtorequests', 'local_training_letter'), ['class' => 'training-letter-back']);
echo html_writer::start_div('training-letter-panel');
echo html_writer::tag('span', get_string('reviewrequest', 'local_training_letter'), ['class' => 'training-letter-eyebrow']);
echo $OUTPUT->heading(s($request->studentname) . ' — ' . s($request->organization), 2);
echo html_writer::start_div('training-letter-details');
echo html_writer::tag('p', get_string('rollno', 'local_training_letter') . ': ' . s($request->rollno));
echo html_writer::tag('p', get_string('branch', 'local_training_letter') . ': ' . s($request->branch));
echo html_writer::tag('p', get_string('semester', 'local_training_letter') . ': ' . s($request->semester));
echo html_writer::tag('p', get_string('trainingtitle', 'local_training_letter') . ': ' . s($request->trainingtitle));
echo html_writer::tag('p', get_string('duration', 'local_training_letter') . ': ' . s($request->duration));
echo html_writer::tag('p', get_string('startdate', 'local_training_letter') . ': ' . userdate($request->startdate));
echo html_writer::tag('p', get_string('postaladdress', 'local_training_letter') . ': ' . s($request->postaladdress));
echo html_writer::end_div();
echo html_writer::start_div('training-letter-review-form');
$form->display();
echo html_writer::end_div();
echo html_writer::end_div();
echo html_writer::end_div();
echo $OUTPUT->footer();
