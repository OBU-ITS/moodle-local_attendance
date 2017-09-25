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
 * Brookes ID - Provide left hand navigation links
 *
 * @package    local_attendance
 * @copyright  2017, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function local_attendance_extend_navigation($navigation) {
	
	if (!isloggedin() || isguestuser() || !has_capability('local/attendance:admin', context_system::instance())) {
		return;
	}
	
	// Find the 'brookesid' node
	$nodeParent = $navigation->find(get_string('attendance', 'local_attendance'), navigation_node::TYPE_SYSTEM);
	
	// If necessary, add the 'brookesid' node to 'home'
	if (!$nodeParent) {
		$nodeHome = $navigation->children->get('1')->parent;
		if ($nodeHome) {
			$nodeParent = $nodeHome->add(get_string('attendance:admin', 'local_attendance'), null, navigation_node::TYPE_SYSTEM);
		}
	}
	
	//$node = $nodeParent->add(get_string('register', 'local_attendance'), '/local/attendance/register.php');
	$node = $nodeParent->add(get_string('attendance_all', 'local_attendance'), '/local/attendance/att_all_csv.php');
	$node = $nodeParent->add(get_string('attendance', 'local_attendance'), '/local/attendance/attendance_csv.php');
}
