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

class attendance_form extends moodleform {

    function definition() {
        $mform =& $this->_form;
		
		$mform->addElement('html', '<h2>' . get_string('attendance', 'local_attendance')  . '</h2>');

		$mform->addElement('text', 'student_number', get_string('student_number', 'local_attendance'));

        $this->add_action_buttons(true, get_string('save', 'local_attendance'));
    }
	
	function validation($data, $files) {
		$errors = parent::validation($data, $files); // Ensure we don't miss errors from any higher-level validation
		
		// Do our own validation and add errors to array
		/*foreach ($data as $key => $value) {
			if (($key == 'date_from') && ($value > strtotime('today midnight'))) {
				$errors['date_from'] = get_string('invalid_date', 'local_attendance');
			} else if (($key == 'date_to') && ($value < $data['date_from'])) {
				$errors['date_to'] = get_string('invalid_date', 'local_attendance');
			}
		}
		*/
		return $errors;
	}
}