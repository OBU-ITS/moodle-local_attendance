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
require_once('./mail_form.php');


require_login();
$context = context_system::instance();
require_capability('local/attendance:admin', $context);

$home = new moodle_url('/');
$url = $home . 'local/attendance/index.php';

$PAGE->set_pagelayout('standard');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title(get_string('mail', 'local_attendance'));

$message = '';
$txt = '';

$mform = new mail_form(null, array());
//use U08700.234933 as sample course 
if ($mform->is_cancelled()) {
    redirect($home);
} else if ($mform_data = $mform->get_data()) {
	global $USER;
	$sessionids = get_sessionid($mform_data->courseid);
	$recipients = get_module_leader_email($mform_data->courseid);
	$to = '';
	/*=== begin DEBUG params ===*/
	//$to = 'yaburrow@brookes.ac.uk';	
	//$attid = 419676;
	//$sessionid = 1352;
	/*==== end DEBUG params ====*/
	

	foreach($recipients as $recipient) {
		$to .= $recipient->email . ', ';
	}
	$subject = "Attendance registers for ". $mform_data->courseid ;
	$txt = '<html><body>Hi ';
	foreach($recipients as $recipient) {
		$txt .= $recipient->firstname . ', ';
	}
	$txt .= '<br>&nbsp;<br>Here is a list of links to the attendance registers for the module &nbsp;'. $mform_data->courseid .'<br>&nbsp;<br>';
	foreach($sessionids as $sessionid) {
		$sessdate = $sessionid->sessdate;
		$txt .= '<a href="' . $home. '/mod/attendance/take.php?id='. $sessionid->cmo_id . '&sessionid='. $sessionid->ass_id .'&grouptype=0&perpage=0">'. $mform_data->courseid . ' on '. date("d-m-Y h:i", $sessdate) .'</a><br/>';
	}
	$txt .= '</body></html>';
	$headers = "From: moodle@brookes.ac.uk" . "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
	
	mail($to,$subject,$txt,$headers);
	
	$message = 'Email sent';
	
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

