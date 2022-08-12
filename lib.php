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
 * Library of functions for local_course_extender.
 *
 * @package     local_course_extender
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();



function local_course_extender_extend_settings_navigation($settingsnav, $context) {
    global $CFG, $PAGE, $USER;

    // Only add this settings item on non-site course pages.
    if (!$PAGE->course or $PAGE->course->id == 1) {
        return;
    }

    // Only let users with the appropriate capability see this settings item.
    if (!has_capability('moodle/backup:backupcourse',context_course::instance($PAGE->course->id))) {
        return;
    }

    if ($settingnode = $settingsnav->find('courseadmin', navigation_node::TYPE_COURSE)) {
        $strfoo = get_string('keep_course', 'local_course_extender');
        $url = new moodle_url('/local/course_extender/index.php', array('id' => $PAGE->course->id));
        $foonode = navigation_node::create(
            $strfoo,
            $url,
            navigation_node::NODETYPE_LEAF,
            'course_extender',
            'course_extender',
            new pix_icon('a/refresh', 'course_extender')
        );
        if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
            $foonode->make_active();
        }
        $settingnode->add_node($foonode);
    }
}

/**
 * Adds/updates a course instance from the database.
 *
 * @param int $courseid 
 * @param string $term Term of the current course.
 * @param string $shortname.
 * @param string $fullname.
 * @param int $keepcourse 
 * @return bool True if successful, false on failure.
 */
function course_extender_update($courseid, $keepcourse) {
    global $DB;

    $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

    $currentterm=get_current_term();
    $exists = $DB->get_record('local_course_extender', array('courseid' => $course->id));
    if (!$exists) {
        $DB->insert_record('local_course_extender', array('courseid' =>$course->id,'term'=>$currentterm,'shortname'=>$course->shortname, 'fullname'=>$course->fullname,'keepcourse'=>$keepcourse));
        return true;
    }

   else {
    $record = $DB->get_record('local_course_extender', array('courseid' => $course->id));
    $DB->update_record('local_course_extender', array('id' => $record->id, 'keepcourse'=>$keepcourse) );
    return true;
   }
return false;
}

/**
 * Returns current term and year
 * @return string Term and year as string.
 */
function get_current_term() {
    // Current month as a digit
    $month=date('n');
    // Summer term 
    if($month >=4 && $month <= 9)    {
        $term="S";
    }
    // Winter term 
    else{
        $term="W";
    }
     $term=$term.strval(date('y'));
  return $term;
}

