<?php
// Student portal home page.

require_once(__DIR__ . '/../../config.php');

require_login();

$context = context_system::instance();
$cansubmit = has_capability('local/training_letter:submit', $context);
$canreview = has_capability('local/training_letter:viewall', $context);
$url = new moodle_url('/local/training_letter/index.php');

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(get_string('portal', 'local_training_letter'));
$PAGE->set_heading(get_string('portal', 'local_training_letter'));

$myrequests = $cansubmit ? $DB->count_records('local_training_letter', ['userid' => $USER->id]) : 0;
$pendingrequests = $canreview ? $DB->count_records('local_training_letter', ['status' => 'pending']) : 0;

echo $OUTPUT->header();

echo html_writer::start_div('training-letter-portal');
echo html_writer::start_div('training-letter-hero');
echo html_writer::tag('span', get_string('cellname', 'local_training_letter'), ['class' => 'training-letter-eyebrow']);
echo html_writer::tag('h2', get_string('portalheading', 'local_training_letter'));
echo html_writer::tag('p', get_string('portalintro', 'local_training_letter'), ['class' => 'training-letter-hero-text']);
echo html_writer::end_div();

echo html_writer::tag('h3', get_string('portalactions', 'local_training_letter'), ['class' => 'training-letter-section-title']);
echo html_writer::start_div('training-letter-cards');

if ($cansubmit) {
    echo html_writer::start_div('training-letter-card');
    echo html_writer::tag('span', '01', ['class' => 'training-letter-card-number']);
    echo html_writer::tag('h4', get_string('applytitle', 'local_training_letter'));
    echo html_writer::tag('p', get_string('applydescription', 'local_training_letter'));
    echo html_writer::link(new moodle_url('/local/training_letter/apply.php'), get_string('applynow', 'local_training_letter'), ['class' => 'btn btn-primary']);
    echo html_writer::end_div();

    echo html_writer::start_div('training-letter-card');
    echo html_writer::tag('span', '02', ['class' => 'training-letter-card-number']);
    echo html_writer::tag('h4', get_string('myrequests', 'local_training_letter'));
    echo html_writer::tag('p', get_string('requestsdescription', 'local_training_letter'));
    echo html_writer::tag('p', get_string('requestcount', 'local_training_letter', $myrequests), ['class' => 'training-letter-count']);
    echo html_writer::link(new moodle_url('/local/training_letter/myrequests.php'), get_string('viewrequests', 'local_training_letter'), ['class' => 'btn btn-outline-primary']);
    echo html_writer::end_div();
}

if ($canreview) {
    echo html_writer::start_div('training-letter-card training-letter-card-coordinator');
    echo html_writer::tag('span', '03', ['class' => 'training-letter-card-number']);
    echo html_writer::tag('h4', get_string('coordinatorportal', 'local_training_letter'));
    echo html_writer::tag('p', get_string('coordinatordescription', 'local_training_letter'));
    echo html_writer::tag('p', get_string('pendingcount', 'local_training_letter', $pendingrequests), ['class' => 'training-letter-count']);
    echo html_writer::link(new moodle_url('/local/training_letter/manage.php'), get_string('openrequests', 'local_training_letter'), ['class' => 'btn btn-outline-primary']);
    echo html_writer::end_div();
}

echo html_writer::end_div();
echo html_writer::tag('p', get_string('portalhelp', 'local_training_letter'), ['class' => 'training-letter-help']);
echo html_writer::end_div();

echo $OUTPUT->footer();
