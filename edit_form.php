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

/*
 * Library of functions for local_course_extender.
 *
 * @package     local_course_extender
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//moodleform is defined in formslib.php
require_once "$CFG->libdir/formslib.php";

class course_extender_form extends moodleform
{
    //Add elements to form
    public function definition()
    {
        global $CFG,$DB, $PAGE;

        $mform = $this->_form; // Don't forget the underscore!
        $attributes = ['size' => '20'];

        // $mform->addElement('text', 'addtime', 'Tage drauf rechnen', $attributes);
        $mform->setType('addtime', PARAM_INT);
        foreach ($PAGE->url->params() as $name => $value) {
            $mform->addElement('hidden', $name, $value);
            $mform->setType($name, PARAM_RAW);
        }

        $recordexists = $DB->get_record('local_course_extender', ['courseid' => $this->_customdata['courseid']]);
        $a = new stdClass();

        if (!$recordexists) {
            $mform->addElement('advcheckbox', 'keepcourse', 'Kursinhalte für das nächste Semester übernehmen?', 'Ja');
            $a->keepcourse = "Course won't be extended";
        } else {
            $dbdata = $DB->get_record('local_course_extender', ['courseid' => $this->_customdata['courseid']]);
            $mform->addElement('advcheckbox', 'keepcourse', 'Kursinhalte für das nächste Semester übernehmen?', 'Ja')->setValue($dbdata->keepcourse);
            if ($dbdata->keepcourse == 1) {
                $a->keepcourse = 'Course will be extended';
            } else {
                $a->keepcourse = "Course wont' be extended";
            }
        }
        $mform->addElement('static', '', '', get_string('info', 'local_course_extender', $a));

        $this->add_action_buttons($cancel = false, $submitlabel = 'Speichern!');
    }
}
