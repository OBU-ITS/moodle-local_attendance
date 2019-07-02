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
 * Attendance - Provide left hand navigation links
 *
 * @package    local_attendance
 * @copyright  2019, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/local/attendance/db_update.php');

function local_attendance_extend_navigation($navigation) {
	global $USER, $PAGE;
	
	if (!isloggedin() || isguestuser()) {
		return;
	}

	$advisees = get_academic_advisees($USER->id);
	if (!is_siteadmin() && empty($advisees) && (!$PAGE->course || ($PAGE->course->id == 1)
		|| (!has_capability('mod/attendance:viewreports', context_course::instance($PAGE->course->id))
			&& (!has_capability('mod/attendance:takeattendances', context_course::instance($PAGE->course->id)) || (strpos($PAGE->course->idnumber, '.') === false))))) {
		return;
	}

	$nodeHome = $navigation->children->get('1')->parent;
	$node = $nodeHome->add(get_string('attendance_reports', 'local_attendance'), '/local/attendance/menu.php', navigation_node::TYPE_SYSTEM);
	$node->showinflatnavigation = true;

	return;
}
