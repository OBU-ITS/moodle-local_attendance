<?php
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
 * Attendance - User Menu
 *
 * @package    local_attendance
 * @copyright  2018, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('./db_update.php');

require_login();

$home = new moodle_url('/');
$advisees = get_academic_advisees($USER->id);
if (!is_siteadmin() && empty($advisees) && (!$PAGE->course || ($PAGE->course->id == 1) || (!has_capability('mod/attendance:viewreports', context_course::instance($PAGE->course->id))
	&& (!has_capability('mod/attendance:takeattendances', context_course::instance($PAGE->course->id)) || (strpos($PAGE->course->idnumber, '.') === false))))) {
		redirect($home);
}

$dir = $home . 'local/attendance/';
$url = $dir . 'menu.php';

$PAGE->set_url($url);
$PAGE->set_pagelayout('standard');
$title = get_string('attendance_reports', 'local_attendance');
$heading = get_string('attendance_reports', 'local_attendance');
$PAGE->set_title($title);
$PAGE->set_heading($title);

// The page contents
echo $OUTPUT->header();
echo $OUTPUT->heading($heading);

if (is_siteadmin()) {
	$url = $dir . 'jisc_attendance.php';
	echo '<h4><a href="' . $url . '">' . get_string('jisc_attendance', 'local_attendance') . '</a></h4>';
	$url = $dir . 'module_register.php';
	echo '<h4><a href="' . $url . '">' . get_string('module_register', 'local_attendance') . '</a></h4>';
}

if (!empty($advisees)) {
	$url = $dir . 'advisee_attendance.php';
	echo '<h4><a href="' . $url . '">' . get_string('advisee_attendance', 'local_attendance') . '</a></h4>';
}

if ($PAGE->course && ($PAGE->course->id != 1)) {

	if (has_capability('mod/attendance:viewreports', context_course::instance($PAGE->course->id))) {
		if (strpos($PAGE->course->idnumber, '.') === false) { // Programme
			$url = new moodle_url('/local/attendance/course_attendance.php', array('id' => $PAGE->course->id));
			echo '<h4><a href="' . $url . '">' . get_string('course_attendance', 'local_attendance') . '</a></h4>';
		} else { // Module
			$url = new moodle_url('/local/attendance/module_attendance.php', array('id' => $PAGE->course->id));
			echo '<h4><a href="' . $url . '">' . get_string('module_attendance', 'local_attendance') . '</a></h4>';
		}
	}

	if ((strpos($PAGE->course->idnumber, '.') !== false) && has_capability('mod/attendance:takeattendances', context_course::instance($PAGE->course->id))) { // Only for modules
		$url = new moodle_url('/local/attendance/register.php', array('id' => $PAGE->course->id));
		echo '<h4><a href="' . $url . '">' . get_string('register', 'local_attendance') . '</a></h4>';
	}
}

echo $OUTPUT->footer();
