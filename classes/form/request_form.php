<?php
// Form used by students to submit training details.

namespace local_training_letter\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class request_form extends \moodleform {
    public function definition() {
        $form = $this->_form;

        $form->addElement('header', 'studentdetails', get_string('studentdetails', 'local_training_letter'));

        $form->addElement('text', 'studentname', get_string('studentname', 'local_training_letter'), ['maxlength' => 100]);
        $form->setType('studentname', PARAM_TEXT);
        $form->addRule('studentname', null, 'required', null, 'both');

        $form->addElement('text', 'rollno', get_string('rollno', 'local_training_letter'), ['maxlength' => 50]);
        $form->setType('rollno', PARAM_TEXT);
        $form->addRule('rollno', null, 'required', null, 'both');

        $form->addElement('text', 'branch', get_string('branch', 'local_training_letter'), ['maxlength' => 100]);
        $form->setType('branch', PARAM_TEXT);
        $form->addRule('branch', null, 'required', null, 'both');

        $semesters = ['' => get_string('choosedots')] + array_combine(range(1, 8), range(1, 8));
        $form->addElement('select', 'semester', get_string('semester', 'local_training_letter'), $semesters);
        $form->setType('semester', PARAM_INT);
        $form->addRule('semester', null, 'required', null, 'both');

        $form->addElement('header', 'companydetails', get_string('companydetails', 'local_training_letter'));

        $form->addElement('text', 'organization', get_string('organization', 'local_training_letter'), ['maxlength' => 255]);
        $form->setType('organization', PARAM_TEXT);
        $form->addRule('organization', null, 'required', null, 'both');

        $form->addElement('textarea', 'postaladdress', get_string('postaladdress', 'local_training_letter'), ['rows' => 3, 'cols' => 50]);
        $form->setType('postaladdress', PARAM_TEXT);
        $form->addRule('postaladdress', null, 'required', null, 'both');

        $form->addElement('header', 'trainingdetails', get_string('trainingdetails', 'local_training_letter'));

        $types = [
            '' => get_string('choosedots'),
            'software' => get_string('software', 'local_training_letter'),
            'industrial' => get_string('industrial', 'local_training_letter'),
        ];
        $form->addElement('select', 'trainingtype', get_string('trainingtype', 'local_training_letter'), $types);
        $form->setType('trainingtype', PARAM_ALPHANUMEXT);
        $form->addRule('trainingtype', null, 'required', null, 'both');

        $form->addElement('text', 'trainingtitle', get_string('trainingtitle', 'local_training_letter'), ['maxlength' => 255]);
        $form->setType('trainingtitle', PARAM_TEXT);
        $form->addRule('trainingtitle', null, 'required', null, 'both');

        $form->addElement('text', 'duration', get_string('duration', 'local_training_letter'), ['maxlength' => 50]);
        $form->setType('duration', PARAM_TEXT);
        $form->addRule('duration', null, 'required', null, 'both');

        $form->addElement('date_selector', 'startdate', get_string('startdate', 'local_training_letter'));
        $form->setType('startdate', PARAM_INT);

        $this->add_action_buttons(true, get_string('submitrequest', 'local_training_letter'));
    }
}
