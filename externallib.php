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
 * External webservice template.
 *
 * @package   local_course_extender
 * @copyright 2022 Nobody
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir.'/externallib.php';

/**
 * External webservice functions.
 *
 * @copyright 2022 Nobody
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_course_extender_external extends external_api
{
    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function get_information_parameters()
    {
        return new external_function_parameters(
            [
                'courseids' => new external_multiple_structure(new external_value(PARAM_INT, 'course ID')),
            ]
        );
    }

    /**
     * Get Information about cours extension.
     *
     * @param array $courseids A list of course ids
     */
    public static function get_information($courseids)
    {
        global  $DB;

        // Parameter validation.
        $params = self::validate_parameters(self::get_information_parameters(), ['courseids' => $courseids]);

        $returnarray = [];

        foreach ($params['courseids'] as $courseid) {
            $course = $DB->get_record('course', ['id' => $courseid]);

            if (empty($course)) {
                throw new moodle_exception('courseID not found: '.$courseid);
            } else {
                $courseextinformation = $DB->get_record('local_course_extender', ['courseid' => $courseid]);
                $returnarray[] = [
                    'id' => $courseid,
                    'category' => $course->category,
                    'fullname' => $course->fullname,
                    'shortname' => $course->shortname,
                    'keepcourse' => $courseextinformation->keepcourse,
                    'term' => $courseextinformation->term,
                ];
                continue;
            }
        }

        return ['courses' => $returnarray];
    }

    /**
     * Returns description of method result value.
     *
     * @return course_information
     */
    public static function get_information_returns()
    {
        return new external_single_structure(
            [
                'courses' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'id' => new external_value(PARAM_INT, 'Course ID'),
                            'category' => new external_value(PARAM_INT, 'ID of the course category'),
                            'fullname' => new external_value(PARAM_TEXT, 'The course  fullname'),
                            'shortname' => new external_value(PARAM_TEXT, 'The course  shortname'),
                            'keepcourse' => new external_value(PARAM_INT, 'Keep this course or not'),
                            'term' => new external_value(PARAM_TEXT, 'The current term'),
                        ]
                    ),
                    'course information'
                ),
            ]
        );
    }
}
