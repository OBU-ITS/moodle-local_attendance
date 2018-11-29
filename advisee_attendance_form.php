<?php

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
 * Attendance - Advisee attendance input form
 *
 * @package    local_attendance
 * @copyright  2018, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */


require_once("{$CFG->libdir}/formslib.php");

class advisee_attendance_form extends moodleform {

    function definition() {
		global $USER;

        $mform =& $this->_form;
		
		$mform->addElement('html', '<h2>' . get_string('advisee_attendance', 'local_attendance')  . '</h2>');
		$mform->addElement('html', '<p>This report allows you to retrieve attendance data for your academic advisees.</p>');

		$adviseeArray = array();
		$myAdvisees = $this->_customdata['advisees'];
		foreach($myAdvisees as $myAdvisee) {
			$key = $myAdvisee->username;
			$value = $myAdvisee->firstname . ' ' . $myAdvisee->lastname . ' (' . $myAdvisee->username . ')';
			$adviseeArray[$key] = $value;
		}
		
		$mform->addElement('select', 'student_number', get_string('student', 'local_attendance'), $adviseeArray);

        $this->add_action_buttons(true, get_string('download', 'local_attendance'));
    }
}