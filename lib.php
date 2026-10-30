<?php
// Navigation callback for the local plugin.

defined('MOODLE_INTERNAL') || die();

function local_training_letter_extend_navigation(global_navigation $navigation) {
    $context = context_system::instance();

    if (has_capability('local/training_letter:submit', $context)) {
        $url = new moodle_url('/local/training_letter/index.php');
        $node = $navigation->add(get_string('pluginname', 'local_training_letter'), $url);
        $node->showinflatnavigation = true;
    }

    if (has_capability('local/training_letter:viewall', $context)) {
        $url = new moodle_url('/local/training_letter/manage.php');
        $navigation->add(get_string('allrequests', 'local_training_letter'), $url);
    }
}
