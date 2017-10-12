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
	
	$node = $nodeParent->add(get_string('mail', 'local_attendance'), '/local/attendance/mail.php');
	$node = $nodeParent->add(get_string('attendance_all', 'local_attendance'), '/local/attendance/att_all_csv.php');
	$node = $nodeParent->add(get_string('attendance', 'local_attendance'), '/local/attendance/attendance_csv.php');
	$node = $nodeParent->add(get_string('attendance_jisc', 'local_attendance'), '/local/attendance/jisc_csv.php');
	$node = $nodeParent->add(get_string('register', 'local_attendance'), '/local/attendance/register.php');
}

function local_attendance_extend_settings_navigation($settingsnav, $context) {
    global $PAGE;

    // Only add this settings item on non-site course pages.
    if (!$PAGE->course or ($PAGE->course->id == 1)) {
        return;
    }
    
    // Only let users with the appropriate capability see this settings item.
     if (!has_capability('mod/attendance:takeattendances', context_course::instance($PAGE->course->id))) {
        return;
    }
    
    if ($nodeParent = $settingsnav->find('courseadmin', navigation_node::TYPE_COURSE)) {
        $title = get_string('register', 'local_attendance');
        $url = new moodle_url('/local/attendance/course_register.php', array('id' => $PAGE->course->id));
        $node = navigation_node::create(
            $title,
            $url,
            navigation_node::NODETYPE_LEAF,
            'register',
            'register',
            new pix_icon('t/addcontact', $title)
        );

        if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
            $node->make_active();
        }

        $nodeParent->add_node($node);
    }
}
