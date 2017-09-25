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
 * Brookes ID - Certificates
 *
 * @package    local_attendance
 * @copyright  2017, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once('../../config.php');
require_once('./db_update.php');
require_once('./attendance_form.php');


require_login();
$context = context_system::instance();
require_capability('local/attendance:admin', $context);

$home = new moodle_url('/');
$url = $home . 'local/attendance/index.php';


$PAGE->set_pagelayout('standard');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title(get_string('attendance', 'local_attendance') . ' (CSV)');

$message = '';

$mform = new attendance_form(null, array());

if ($mform->is_cancelled()) {
    redirect($home);
} 
else if ($mform_data = $mform->get_data()) {
	$attendances = get_attendance($mform_data->student_number); // Get all for selected student
	if (empty($attendances)) {
		$message = get_string('no_students', 'local_attendance');
	} else {
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename=attendance_' . $mform_data->student_number . '.csv');
		$fp = fopen('php://output', 'w');
		fputcsv($fp, array('session date','calendar event id', 'course id', 'course name', 'student number','first name', 'last name','acronym','description'));

		foreach ($attendances as $attendance) {
			// session_date, ass.caleventid, c.idnumber as course_id, c.shortname as course_name, u.username as student_number, u.firstname, u.lastname, ast.acronym, ast.description
			$fields = array();
			$fields[0] = date('d-m-Y', $attendance->session_date);
			$fields[1] = $attendance->caleventid;
			$fields[2] = $attendance->course_id;
			$fields[3] = $attendance->course_name;
			$fields[4] = $attendance->student_number;
			$fields[5] = $attendance->firstname;
			$fields[6] = $attendance->lastname;
			$fields[7] = $attendance->acronym;
			$fields[8] = $attendance->description;
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
