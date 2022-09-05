<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Local Course extender main view.
 *
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once __DIR__.'/../../config.php';
require_once $CFG->libdir.'/adminlib.php';
require_once 'lib.php';
require_once 'edit_form.php';

global $CFG, $DB, $PAGE;
$courseID = required_param('id', PARAM_INT);
$course = $DB->get_record('course', ['id' => $courseID], '*', MUST_EXIST);
require_login($course);

$currentparams = ['id' => $courseID];
$course = $DB->get_record('course', ['id' => $courseID]);
$coursecontext = context_course::instance($course->id);
$url = new moodle_url('/local/course_extender/index.php', $currentparams);
$PAGE->set_url($url);
if (!has_capability('local/course_extender:extendcourse',$coursecontext)) {
    $url_back = new moodle_url('/my');
    redirect($url_back, 'sie haben nicht die passenden Berechtigungen!', null, \core\output\notification::NOTIFY_ERROR);
}

// Set page context.
$PAGE->set_context(context_system::instance());
// Set page layout.
$PAGE->set_pagelayout('standard');
// Set page layout.

$PAGE->set_title($SITE->fullname.': '.'course extender');
$PAGE->set_heading($SITE->fullname);
// $PAGE->set_url(new moodle_url('/local/dexmod/index.php'));
$PAGE->navbar->ignore_active(true);
// $PAGE->navbar->add("Dexpmod", new moodle_url('/local/dexpmod/index.php'));
$PAGE->navbar->add('course_extender', new moodle_url($url));
$PAGE->set_pagelayout('admin');

$mform = new course_extender_form(null, ['courseid' => $courseID]);
//display the form

// $mform->set_data((object)$currentparams);
if ($data = $mform->get_data()) {
    course_extender_update($courseID, $data->keepcourse);
    redirect(new moodle_url('/local/course_extender/index.php', $currentparams));
}

echo $OUTPUT->header();
$mform->display();

$backurl = new moodle_url('/course/view.php', ['id' => $courseID]);

echo $OUTPUT->single_button($backurl, 'Zurück zum Kurs','get');

echo $OUTPUT->footer();
