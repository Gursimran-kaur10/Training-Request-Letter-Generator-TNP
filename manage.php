<?php
// Coordinator page for viewing all training requests.

require_once(__DIR__ . '/../../config.php');

require_login();

$context = context_system::instance();
require_capability('local/training_letter:viewall', $context);

$url = new moodle_url('/local/training_letter/manage.php');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('allrequests', 'local_training_letter'));
$PAGE->set_heading(get_string('allrequests', 'local_training_letter'));

$requests = $DB->get_records('local_training_letter', null, 'timecreated DESC');

echo $OUTPUT->header();
echo html_writer::start_div('training-letter-page');
echo html_writer::start_div('training-letter-panel');
echo html_writer::tag('span', get_string('coordinatorportal', 'local_training_letter'), ['class' => 'training-letter-eyebrow']);
echo $OUTPUT->heading(get_string('allrequests', 'local_training_letter'), 2);
echo html_writer::tag('p', get_string('coordinatordescription', 'local_training_letter'), ['class' => 'training-letter-help']);

if (!$requests) {
    echo $OUTPUT->notification(get_string('norequests', 'local_training_letter'), 'info');
} else {
    $table = new html_table();
    $table->attributes['class'] = 'training-letter-table';
    $table->head = [
        get_string('studentname', 'local_training_letter'),
        get_string('rollno', 'local_training_letter'),
        get_string('organization', 'local_training_letter'),
        get_string('trainingtitle', 'local_training_letter'),
        get_string('startdate', 'local_training_letter'),
        get_string('status', 'local_training_letter'),
        get_string('coordinatornote', 'local_training_letter'),
        get_string('reviewrequest', 'local_training_letter'),
    ];

    foreach ($requests as $request) {
        $reviewurl = new moodle_url('/local/training_letter/review.php', ['id' => $request->id]);
        $statusclass = 'training-letter-status training-letter-status-' . clean_param($request->status, PARAM_ALPHA);
        $table->data[] = [
            s($request->studentname),
            s($request->rollno),
            s($request->organization),
            s($request->trainingtitle),
            userdate($request->startdate, get_string('strftimedate', 'langconfig')),
            html_writer::tag('span', get_string($request->status, 'local_training_letter'), ['class' => $statusclass]),
            $request->coordinatornote ? s($request->coordinatornote) : get_string('nonote', 'local_training_letter'),
            html_writer::link($reviewurl, get_string('review', 'local_training_letter'), ['class' => 'btn btn-sm btn-outline-primary']),
        ];
    }

    echo html_writer::table($table);
}

echo html_writer::end_div();
echo html_writer::end_div();
echo $OUTPUT->footer();
