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
 * @package    local_brookesid
 * @copyright  2017, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

require_once('../../config.php');


require_login();
$context = context_system::instance();
require_capability('local/attendance:admin', $context);

$home = new moodle_url('/');
$url = $home . 'local/attendance/index.php';

$PAGE->set_pagelayout('standard');
$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_heading($SITE->fullname);
$PAGE->set_title(get_string('attendance:admin', 'local_attendance'));

	
echo $OUTPUT->header();

/*<ul>
 	<li><a href="register.php">Generate register</a> (PDF)</li>
 </ul>
 <h3>Reports (CSV)</h3>*/
echo '
<h2>'. get_string('attendance:admin', 'local_attendance').'</h2>


 <ul>	
 	<li><a href="att_all_csv.php">' . get_string('attendance_all', 'local_attendance') . '</a></li>
 	<li><a href="attendance_csv.php">' . get_string('attendance', 'local_attendance') . '</a></li>
 
 </ul>
';

echo $OUTPUT->footer();

exit();

?>