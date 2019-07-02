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
 * Attendance - Advisee attendance
 *
 * @package    local_attendance
 * @copyright  2019, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once('../../config.php');
require_once('./db_update.php');
require_once('./advisee_attendance_form.php');

require_login();

$home = new moodle_url('/');
$advisees = get_academic_advisees($USER->id);
if (empty($advisees)) {
	redirect($home);
}

$dir = $home . 'local/attendance/';
$url = $dir . 'advisee_attendance.php';
$back = $dir . 'menu.php';

$context = context_system::instance();
$PAGE->set_pagelayout('standard');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title(get_string('advisee_attendance', 'local_attendance'));
 
$message = '';

$parameters = [
	'advisees' => $advisees
];

$mform = new advisee_attendance_form(null, $parameters);

if ($mform->is_cancelled()) {
    redirect($back);
} 
else if ($mform_data = $mform->get_data()) {
	$attendances = get_student_attendance($mform_data->student_number); // Get all for selected student
	if (empty($attendances)) {
		$message = get_string('no_attendance', 'local_attendance');
	} else {
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=attendance_' . $mform_data->student_number . '.csv');
		$fp = fopen('php://output', 'w');
		fputcsv($fp, array('Session', 'Description', 'Module', 'Student', 'First name', 'Surname', 'Attendance'));

		foreach ($attendances as $attendance) {
			$fields = array();
			$fields[0] = date('d-m-Y H:m', $attendance->session);
			$fields[1] = $attendance->description;
			$fields[2] = $attendance->module;
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
