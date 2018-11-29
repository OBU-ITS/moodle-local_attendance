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
 * Attendance - Mail
 *
 * @package    local_attendance
 * @copyright  2018, Oxford Brookes University
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
$url = $home . 'local/attendance/mail.php';

$PAGE->set_pagelayout('standard');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title(get_string('mail', 'local_attendance'));

$message = '';
$txt = '';

$mform = new mail_form(null, array());
if ($mform->is_cancelled()) {
    redirect($home);
} else if ($mform_data = $mform->get_data()) {
	$module = get_module_names($mform_data->module_id);
	$session_ids = get_session_ids($mform_data->module_id, date());

	$to = '';
	$recipients = get_module_staff_emails($mform_data->module_id);
	foreach ($recipients as $recipient) {
		if ($to != '') {
			$to .= ', ';
		}
		$to .= $recipient->email;
	}
	$subject = "Attendance registers for ". $module['Shortname'];
	$txt = '';
	foreach ($recipients as $recipient) {
		if ($txt == '') {
			$txt = '<html><body>Hi ';
		} else {
			$txt .= ', ';
		}
		$txt .= $recipient->firstname;
	}
	$txt .= '<br>&nbsp;<br>Here is a list of links to the attendance registers for the module &nbsp;'. $shortname .'<br>&nbsp;<br>';
	foreach ($session_ids as $session_id) {
		$sessdate = $session_id->sessdate;
		$txt .= '<a href="' . $home. '/mod/attendance/take.php?id='. $session_id->cmo_id . '&sessionid='. $session_id->ass_id .'&grouptype=0&perpage=0">'. $module['Shortname'] . ' on '. date("d-m-Y h:i", $sessdate) .'</a><br/>';
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

