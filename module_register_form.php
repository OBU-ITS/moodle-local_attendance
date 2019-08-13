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
 * Attendance - Module register input form
 *
 * @package    local_attendance
 * @copyright  2019, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once("{$CFG->libdir}/formslib.php");

class module_register_form extends moodleform {

    function definition() {
		global $USER;

        $mform =& $this->_form;
		
		$mform->addElement('html', '<h2>' . get_string('module_register', 'local_attendance')  . '</h2>');

		if ($this->_customdata['id'] > 1) {
			$mform->addElement('hidden', 'id', $this->_customdata['id']);
			$mform->setType('id', PARAM_RAW);
		}
		
		$mform->addElement('select', 'module_id', get_string('module', 'local_attendance'), $this->_customdata['modules']);
		
		$mform->addElement('select', 'sessdate', get_string('day', 'local_attendance'), $this->_customdata['sessions']);
		
        $this->add_action_buttons(true, get_string('download', 'local_attendance'));
    }
}