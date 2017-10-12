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
 * attendance
 *
 * @package    local_attendance
 * @copyright  2017, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */


require_once('../../config.php');
require_once('./db_update.php');
require_once('./jisc_form.php');


require_login();
$context = context_system::instance();
require_capability('local/attendance:admin', $context);

$home = new moodle_url('/');
$url = $home . 'local/attendance/index.php';


$PAGE->set_pagelayout('standard');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title(get_string('attendance_jisc', 'local_attendance') . ' (CSV)');

$message = '';

$mform = new jisc_form(null, array());

if ($mform->is_cancelled()) {
    redirect($url);
} 
else if ($mform_data = $mform->get_data()) {
	if($mform_data->pilot_data == 0){
		$attendanceJisc = get_jisc_data(); // Get all data
	} else {
		$attendanceJisc = get_pilot_jisc_data(); // Get pilot data only 
	}
	
	
	if (empty($attendanceJisc)) {
		$message = get_string('no_attendance', 'local_attendance');
	} else {
		header('Content-Type: text/csv');
		if($mform_data->pilot_data == 0){
			header('Content-Disposition: attachment;filename=attendance_jisc_udd_data.csv');
		} else {
			header('Content-Disposition: attachment;filename=attendance_jisc_udd_pilot_data.csv');
		}
		
		
		$fp = fopen('php://output', 'w');
		fputcsv($fp, array('event_id', 'event_name', 'event_description','student_id', 'staff_id', 'event_type_id', 'event_max_count','mod_instance_id', 'start_time', 'end_time', 'event_attended',
    'attendance_late','timestamp'));

		foreach ($attendanceJisc as $attendance) {
			$fields = array();
			$fields[0] = $attendance->event_id;
			$fields[1] = $attendance->event_name;
			$fields[2] = $attendance->event_description;
			$fields[3] = $attendance->student_id;
			$fields[4] = $attendance->staff_id;
			$fields[5] = $attendance->event_type_id;
			$fields[6] = $attendance->event_max_count;
			$fields[7] = $attendance->mod_instance_id;
			$fields[8] = date('d-m-Y h:m', $attendance->start_time);
			$fields[9] = date('d-m-Y h:m', $attendance->end_time);
			$fields[10] = $attendance->event_attended;
			$fields[11] = $attendance->attendance_late;
			$fields[12] = date('d-m-Y h:m', $attendance->timestamp);
			
			
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
//echo $mform_data->date_from . ' '. $mform_data->date_to;
echo $OUTPUT->footer();

exit();

?>	
