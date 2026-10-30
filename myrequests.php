<?php
// Shows the current user's request history.

require_once(__DIR__ . '/../../config.php');

require_login();

$context = context_system::instance();
require_capability('local/training_letter:submit', $context);

$url = new moodle_url('/local/training_letter/myrequests.php');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('myrequests', 'local_training_letter'));
$PAGE->set_heading(get_string('myrequests', 'local_training_letter'));

$requests = $DB->get_records('local_training_letter', ['userid' => $USER->id], 'timecreated DESC');

echo $OUTPUT->header();
echo html_writer::start_div('training-letter-page');
echo html_writer::link(new moodle_url('/local/training_letter/index.php'), get_string('backtoportal', 'local_training_letter'), ['class' => 'training-letter-back']);
echo html_writer::start_div('training-letter-panel');
echo $OUTPUT->heading(get_string('myrequests', 'local_training_letter'), 2);

if (!$requests) {
    echo $OUTPUT->notification(get_string('noownrequests', 'local_training_letter'), 'info');
    echo html_writer::link(new moodle_url('/local/training_letter/apply.php'), get_string('applynow', 'local_training_letter'), ['class' => 'btn btn-primary']);
} else {
    $table = new html_table();
    $table->attributes['class'] = 'training-letter-table';
    $table->head = [
        get_string('organization', 'local_training_letter'),
        get_string('trainingtitle', 'local_training_letter'),
        get_string('startdate', 'local_training_letter'),
        get_string('status', 'local_training_letter'),
        get_string('downloadletter', 'local_training_letter'),
    ];

    foreach ($requests as $request) {
        $letter = get_string('notapproved', 'local_training_letter');
        if ($request->status === 'approved') {
            $previewurl = new moodle_url('/local/training_letter/getletter.php', ['id' => $request->id, 'action' => 'preview']);
            $downloadurl = new moodle_url('/local/training_letter/getletter.php', ['id' => $request->id, 'action' => 'download']);
            $letter = html_writer::link($previewurl, get_string('previewletter', 'local_training_letter')) . ' · ' .
                html_writer::link($downloadurl, get_string('downloadletter', 'local_training_letter'));
        }

        $statusclass = 'training-letter-status training-letter-status-' . clean_param($request->status, PARAM_ALPHA);
        $table->data[] = [
            s($request->organization),
            s($request->trainingtitle),
            userdate($request->startdate, get_string('strftimedate', 'langconfig')),
            html_writer::tag('span', get_string($request->status, 'local_training_letter'), ['class' => $statusclass]),
            $letter,
        ];
    }

    echo html_writer::table($table);
}

echo html_writer::end_div();
echo html_writer::end_div();
echo $OUTPUT->footer();
