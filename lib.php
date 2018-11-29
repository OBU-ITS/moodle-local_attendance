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
 * @copyright  2018, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/local/attendance/db_update.php');

function local_attendance_extend_navigation($navigation) {
	global $USER, $PAGE;
	
	if (!isloggedin() || isguestuser()) {
		return;
	}

	$advisees = get_academic_advisees($USER->id);
	if (!is_siteadmin and empty($advisees) and (!$PAGE->course or ($PAGE->course->id == 1)
		or (!has_capability('mod/attendance:viewreports', context_course::instance($PAGE->course->id))
			and (!has_capability('mod/attendance:takeattendances', context_course::instance($PAGE->course->id)) or (substr($PAGE->course->idnumber, 6, 1) != '.'))))) {
		return;
	}
    
	// Find the 'Attendance reports' node
	$nodeParent = $navigation->find(get_string('attendance', 'local_attendance'), navigation_node::TYPE_SYSTEM);
	
	// If necessary, add the 'Attendance reports' node to 'home'
	if (!$nodeParent) {
		$nodeHome = $navigation->children->get('1')->parent;
		if ($nodeHome) {
			$nodeParent = $nodeHome->add(get_string('attendance_reports', 'local_attendance'), null, navigation_node::TYPE_SYSTEM);
		}
	}
	
//	$node = $nodeParent->add(get_string('module_register', 'local_attendance'), '/local/attendance/module_register.php');
//	$node = $nodeParent->add(get_string('mail', 'local_attendance'), '/local/attendance/mail.php');
		
	if (is_siteadmin()) {
		$node = $nodeParent->add(get_string('jisc_attendance', 'local_attendance'), '/local/attendance/jisc_attendance.php');
	}

	if (!empty($advisees)) {
		$node = $nodeParent->add(get_string('advisee_attendance', 'local_attendance'), '/local/attendance/advisee_attendance.php');
	}

	if (!$PAGE->course or ($PAGE->course->id == 1)) {
		return;
	}

	if (has_capability('mod/attendance:viewreports', context_course::instance($PAGE->course->id))) {
		if (substr($PAGE->course->idnumber, 6, 1) != '.') { // Programme
			$node = $nodeParent->add(get_string('course_attendance', 'local_attendance'), new moodle_url('/local/attendance/course_attendance.php', array('id' => $PAGE->course->id)));
		} else { // Module
			$node = $nodeParent->add(get_string('module_attendance', 'local_attendance'), new moodle_url('/local/attendance/module_attendance.php', array('id' => $PAGE->course->id)));
		}
	}

	if ((substr($PAGE->course->idnumber, 6, 1) == '.') && has_capability('mod/attendance:takeattendances', context_course::instance($PAGE->course->id))) { // Only for modules
		$node = $nodeParent->add(get_string('register', 'local_attendance'), new moodle_url('/local/attendance/register.php', array('id' => $PAGE->course->id)));
	}
}

function local_attendance_extend_settings_navigation($navigation, $context) {
}