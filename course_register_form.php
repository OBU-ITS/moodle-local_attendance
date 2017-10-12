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
 * Input form for course register
 *
 * @package    local_attendance
 * @copyright  2017, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once("{$CFG->libdir}/formslib.php");

class course_register_form extends moodleform {

    function definition() {
        $mform =& $this->_form;

		$mform->addElement('html', '<h2>' . get_string('register', 'local_attendance')  . '</h2>');

		$mform->addElement('hidden', 'id', $this->_customdata['course_id']);
		$mform->setType('id', PARAM_RAW);

		$sessionArray = array();
		foreach ($this->_customdata['course_sessions'] as $key => $value) {
			if (empty($value)) {
			   unset($playerlist[$key]);
			}
		}
		if (empty($this->_customdata['course_sessions'])) {
			 echo '<div style="z-index: 1; position: relative; left: 40%; top: 10em; text-align: center; width: 20em; background-color: #ffffcc; color: #000;">' . get_string('no_register', 'local_attendance') . '</div>';
		} else {
			foreach($this->_customdata['course_sessions'] as $session) {
				$sessionArray[$session->sessdate] = date("d M y, h:i", $session->sessdate);
			}
			$mform->addElement('select', 'sessdate', get_string('day', 'local_attendance'), $sessionArray);
		}
		
        $this->add_action_buttons(true, get_string('save', 'local_attendance'));
    }
	
	function validation($data, $files) {
		$errors = parent::validation($data, $files); // Ensure we don't miss errors from any higher-level validation

		return $errors;
	}
}