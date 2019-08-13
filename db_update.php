<?php

// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more settings.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Attendance - db updates
 *
 * @package    local_attendance
 * @copyright  2019, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
 
function get_course_attendance($course_id, $date_from, $date_to) {
    global $DB;
	
	$time_to = $date_to + 86399; // 1 second before midnight
	
	$sql = 'SELECT CONCAT(ass.id, ".", u.id) AS unique_id, c.shortname AS module, ass.sessdate AS session, ass.description, u.username AS student, u.firstname, u.lastname, ast.description AS attendance
			FROM {enrol} e
		JOIN {context} ct ON ct.instanceid = e.courseid AND ct.contextlevel = 50
		JOIN {user_enrolments} ue ON ue.enrolid = e.id
		JOIN {role_assignments} ra ON ra.userid = ue.userid AND ra.contextid = ct.id AND ra.roleid = 5
		JOIN {user} u ON u.id = ue.userid
		JOIN {attendance_log} alg ON alg.studentid = ue.userid
		JOIN {attendance_sessions} ass ON ass.id = alg.sessionid
		JOIN {attendance} att ON att.id = ass.attendanceid AND att.name = "Module attendance"
		JOIN {attendance_statuses} ast ON ast.id = alg.statusid
		JOIN {course} c ON c.id = att.course
		WHERE e.courseid = ?
			AND (e.enrol = "database" OR e.enrol = "databaseextended" OR e.enrol = "ethos" OR e.enrol = "lmb")
			AND ass.sessdate >= ?
			AND ass.sessdate <= ?
		ORDER BY c.shortname, ass.sessdate, u.username';

		return $DB->get_records_sql($sql, array($course_id, $date_from, $time_to));
}

function get_module_attendance($course_id, $date_from, $date_to) {
    global $DB;
	
	$time_to = $date_to + 86399; // 1 second before midnight
	
	$sql = 'SELECT CONCAT(ass.id, ".", u.id) AS unique_id, ass.sessdate AS session, ass.description, u.username, u.firstname, u.lastname, ast.description AS status
			FROM {attendance} att
		JOIN {attendance_sessions} ass ON ass.attendanceid = att.id
		JOIN {attendance_log} alg ON alg.sessionid = ass.id
		JOIN {user} u ON u.id = alg.studentid
		JOIN {attendance_statuses} ast ON ast.id = alg.statusid
		WHERE att.course = ?
			AND att.name = "Module attendance"
			AND ass.sessdate >= ?
			AND ass.sessdate <= ?
		ORDER BY ass.sessdate, u.lastname, u.firstname';

	return $DB->get_records_sql($sql, array($course_id, $date_from, $time_to));
}

function get_student_attendance($studentnumber) {
    global $DB;

	$sql = 'SELECT CONCAT(ass.id, ".", u.id) AS unique_id, ass.sessdate AS session, ass.description, c.shortname AS module, u.username AS student, u.firstname, u.lastname, ast.description AS attendance
			FROM {attendance_log} alg 
		JOIN {attendance_statuses} ast ON ast.id = alg.statusid
		JOIN {attendance_sessions} ass ON ass.id = alg.sessionid 
		JOIN {user} u ON u.id = alg.studentid
		JOIN {attendance} att ON att.id = ast.attendanceid
		JOIN {course} c ON c.id = att.course
		WHERE u.username = ?
		ORDER BY ass.sessdate desc';
		
	return $DB->get_records_sql($sql, array($studentnumber));
}

function get_academic_advisees($userid) {
    global $DB;

	$sql = 'SELECT child.username, child.firstname, child.lastname FROM {user} user
		JOIN {role_assignments} ra ON ra.userid = user.id
		JOIN {role} role ON role.id = ra.roleid
		JOIN {context} ctx ON ctx.id = ra.contextid 
		AND ctx.contextlevel = 30
		JOIN {user} child ON child.id = ctx.instanceid
		WHERE role.shortname = "academic_adviser" AND user.id = ?';
		
	return $DB->get_records_sql($sql, array($userid));
}

function get_session_ids($module_id, $date) {
    global $DB;

	$sql = 'SELECT ass.sessdate, ass.id AS ass_id, cm.id AS cmo_id FROM {course} c
		JOIN {course_modules} cm ON cm.course = c.id
		JOIN {attendance_sessions} ass ON ass.attendanceid = cm.instance
		WHERE c.id = ? AND ass.sessdate >= ?
		ORDER BY ass.sessdate desc';
	
	return $DB->get_records_sql($sql, array($module_id, $date));
}

function get_staff_modules($userid, $date) {
	global $DB;
	
	$sql = 'SELECT DISTINCT c.id, c.fullname
			FROM {role_assignments} ra, {user} u, {course} c, {context} cxt, {attendance_sessions} ass, {attendance} att
		WHERE ra.userid = u.id
			AND ra.contextid = cxt.id
			AND att.id = ass.attendanceid
			AND c.id = att.course
			AND cxt.instanceid = c.id
			AND (roleid = 3 OR roleid = 12 OR roleid = 61)
			AND u.id = ?
			AND c.visible = 1
			AND c.enddate >= ?
		ORDER BY c.fullname';
	
	return $DB->get_records_sql($sql, array($userid, $date));
}

function get_module_staff_emails($courseid) {
    global $DB;

	$sql = 'SELECT DISTINCT u.email, u.firstname, u.lastname
			FROM {role_assignments} ra, {user} u, {course} c, {context} cxt, {attendance_sessions} ass, {attendance} att
		WHERE ra.userid = u.id
			AND ra.contextid = cxt.id
			AND att.id = ass.attendanceid
			AND c.id = att.course
			AND cxt.instanceid = c.id
			AND (roleid = 3 OR roleid = 12 OR roleid = 61)
			AND c.idnumber = ?';
	
	return $DB->get_records_sql($sql, array($courseid));
}

function get_module_names($module_id) {
    global $DB;
	
	$sql = 'SELECT fullname, shortname, idnumber FROM {course} WHERE id = ?';
	
	$record = $DB->get_record_sql($sql, array($module_id));
	$module = array();
	$module['Fullname'] = $record->fullname;
	$module['Shortname'] = $record->shortname;
	$module['IDnumber'] = $record->idnumber;
	
	return $module;	
}

function get_staff_sessions($userid, $date) {
    global $DB;
		
	$sql = 'SELECT ass.sessdate
			FROM {role_assignments} ra, {user} u, {course} c, {context} cxt, {attendance_sessions} ass, {attendance} att
		WHERE ra.userid = u.id
			AND ra.contextid = cxt.id
			AND att.id = ass.attendanceid
			AND c.id = att.course
			AND cxt.instanceid = c.id
			AND (roleid = 3 OR roleid = 12 OR roleid = 61)
			AND c.visible = 1
			AND u.id = ?
			AND ass.sessdate >= ?
		ORDER BY ass.sessdate desc';

	return $DB->get_records_sql($sql, array($userid, $date));
}

function get_sessions($module_id, $date) {
    global $DB;
		
	$sql = 'SELECT ass.sessdate FROM {attendance} att
		JOIN {attendance_sessions} ass ON ass.attendanceid = att.id
		WHERE att.course = ? AND ass.sessdate >= ?
		ORDER BY ass.sessdate';

	return $DB->get_records_sql($sql, array($module_id, $date));
}

function get_register($module_id, $sessdate) {
    global $DB;
	$sql = 'SELECT u.username, u.firstname, u.lastname, ass.sessdate
			FROM {role_assignments} ra, {user} u, {course} c, {context} cxt, {attendance_sessions} ass, {attendance} att
		WHERE ra.userid = u.id
			AND ra.contextid = cxt.id
			AND att.id = ass.attendanceid
			AND c.id = att.course
			AND cxt.instanceid = c.id
			AND (roleid = 5)
			AND c.id = ?
			AND ass.sessdate = ?
		ORDER BY u.lastname, u.firstname';

	return $DB->get_records_sql($sql, array($module_id, $sessdate));
}

/* functions for jisc_csv.php */

function get_jisc_data() {
    global $DB;
	
	$sql = 'SELECT
				ass.id AS event_id, 
				att.name AS event_name, 
				CONCAT(c.fullname, ": ", ass.description) AS event_description,
				u.username AS student_id, 
				us.username AS staff_id, 
				ass.attendanceid AS event_type_id, 
				id_counts.id_count AS event_max_count,
				c.shortname AS mod_instance_id,
				ass.sessdate AS start_time, 
				ass.sessdate + ass.duration AS end_time,
				ast.acronym AS event_attended, 
				ast.acronym AS attendance_late, 
				alg.timetaken AS timestamp
			FROM {attendance_log} alg
		JOIN (SELECT sessionid, count(sessionid) AS id_count FROM {attendance_log} GROUP BY sessionid ) id_counts ON alg.sessionid = id_counts.sessionid
		JOIN {attendance_statuses} ast ON alg.statusid = ast.id
		JOIN {attendance_sessions} ass ON alg.sessionid = ass.id
		JOIN {user} u ON alg.studentid = u.id
		JOIN {user} us ON alg.takenby = us.id
		JOIN {attendance} att ON ast.attendanceid = att.id
		JOIN {course} c ON att.course = c.id
		WHERE ass.sessdate > 1420070400
		ORDER BY ass.sessdate desc';
	
	return $DB->get_records_sql($sql, array());
}

function get_pilot_jisc_data() {
    global $DB;
	
	$sql = 'SELECT
				ass.id AS event_id, 
				att.name AS event_name, 
				CONCAT(c.fullname, ": ", ass.description) AS event_description,
				u.username AS student_id, 
				us.username AS staff_id, 
				ass.attendanceid AS event_type_id, 
				id_counts.id_count AS event_max_count,
				c.shortname AS mod_instance_id,
				ass.sessdate AS start_time, 
				ass.sessdate + ass.duration AS end_time,
				ast.acronym AS event_attended, 
				ast.acronym AS attendance_late, 
				alg.timetaken AS timestamp
			FROM {attendance_log} alg
		JOIN (SELECT sessionid, count(sessionid) AS id_count FROM {attendance_log} GROUP BY sessionid ) id_counts ON alg.sessionid = id_counts.sessionid
		JOIN {attendance_statuses} ast ON alg.statusid = ast.id
		JOIN {attendance_sessions} ass ON alg.sessionid = ass.id
		JOIN {user} u ON alg.studentid = u.id
		JOIN {user} us ON alg.takenby = us.id
		JOIN {attendance} att ON ast.attendanceid = att.id
		JOIN {course} c ON att.course = c.id
		WHERE ass.sessdate > 1420070400
			AND c.id IN(31586, 30923, 30927, 31620, 31831, 31816, 32074, 31600, 31746, 32416, 32606, 31678, 32081, 32082, 32084)
		ORDER BY ass.sessdate desc';
	
	return $DB->get_records_sql($sql, array());
}

