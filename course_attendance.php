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
 * Attendance - Course attendance
 *
 * @package    local_attendance
 * @copyright  2018, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once('../../config.php');
require_once('./db_update.php');
require_once('./date_range_form.php');

require_login();

$course = $DB->get_record('course', array('id' => required_param('id', PARAM_INT)), '*', MUST_EXIST);
context_helper::preload_course($course->id);
$context = context_course::instance($course->id, MUST_EXIST);
require_capability('mod/attendance:viewreports', $context);

$home = new moodle_url('/');
if (strpos($course->idnumber, '.') !== false) { // Check that it's not a module
	redirect($home);
}
$url = $home . 'local/attendance/course_attendance.php?id=' . $course->id;

$PAGE->set_pagelayout('standard');
$PAGE->set_url($url);
$PAGE->set_course($course);
$PAGE->set_context($context);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title(get_string('course_attendance', 'local_attendance'));

$message = '';

$parameters = [
	'title' => get_string('course_attendance', 'local_attendance'),
	'id' => $course->id
];

$mform = new date_range_form(null, $parameters);

if ($mform->is_cancelled()) {
    redirect($home);
} 
else if ($mform_data = $mform->get_data()) {
	$attendances = get_course_attendance($course->id, $mform_data->date_from, $mform_data->date_to); // Get all for selected dates
	if (empty($attendances)) {
		$message = get_string('no_attendance', 'local_attendance'). ': ' . date('d-m-Y', $mform_data->date_from) . ' - '. date('d-m-Y', $mform_data->date_to);
	} else {
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=attendance_' . $course->shortname . '.csv');
		
		$fp = fopen('php://output', 'w');
		fputcsv($fp, array('Module', 'Session', 'Description', 'Student', 'First name', 'Surname', 'Attendance'));

		foreach ($attendances as $attendance) {
			$fields = array();
			$fields[0] = $attendance->module;
			$fields[1] = date('d-m-Y H:m', $attendance->session);
			$fields[2] = $attendance->description;
			$fields[3] = $attendance->student;
			$fields[4] = $attendance->firstname;
			$fields[5] = $attendance->lastname;
			$fields[6] = $attendance->attendance;
			
			fputcsv($fp, $fields);
		}
		fclose($fp);
		
		exit();
	}
}
echo $OUTPUT->header();
	 
if ($message) {
    notice($message, $url);    
}
else {
    $mform->display();
}

echo $OUTPUT->footer();

exit();

?>	
