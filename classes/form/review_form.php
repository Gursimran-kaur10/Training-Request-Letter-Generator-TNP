<?php
// Form used by coordinators to approve or reject a request.

namespace local_training_letter\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class review_form extends \moodleform {
    public function definition() {
        $form = $this->_form;
        $requestid = $this->_customdata['requestid'];

        $form->addElement('hidden', 'id', $requestid);
        $form->setType('id', PARAM_INT);

        $statuses = [
            'pending' => get_string('pending', 'local_training_letter'),
            'approved' => get_string('approved', 'local_training_letter'),
            'rejected' => get_string('rejected', 'local_training_letter'),
        ];
        $form->addElement('select', 'status', get_string('status', 'local_training_letter'), $statuses);
        $form->setType('status', PARAM_ALPHA);

        $form->addElement('textarea', 'coordinatornote', get_string('coordinatornote', 'local_training_letter'), ['rows' => 4, 'cols' => 50]);
        $form->setType('coordinatornote', PARAM_TEXT);

        $this->add_action_buttons(true, get_string('saveoutcome', 'local_training_letter'));
    }
}
