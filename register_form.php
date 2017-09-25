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
 * Input form for attendance
 *
 * @package    local_attendance
 * @copyright  2017, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once("{$CFG->libdir}/formslib.php");

class register_form extends moodleform {

    function definition() {
        $mform =& $this->_form;
		
		$mform->addElement('html', '<h2>' . get_string('register', 'local_attendance')  . '</h2>');

		$mform->addElement('text', 'courseid', get_string('courseid', 'local_attendance'));
		
		$mform->addElement('date_time_selector', 'day', get_string('day', 'local_attendance'));
		
        $this->add_action_buttons(true, get_string('save', 'local_attendance'));
    }
	
	function validation($data, $files) {
		$errors = parent::validation($data, $files); // Ensure we don't miss errors from any higher-level validation

		return $errors;
	}
}