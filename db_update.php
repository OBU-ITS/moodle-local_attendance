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
 * Brookes ID - db updates
 *
 * @package    local_attendance
 * @copyright  2017, Oxford Brookes University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
 
 // all students within date range
// 
function get_att_all($date_from, $date_to) {
    global $DB;
	
	$time_to = $date_to + 86399; // 1 second before midnight
	
	$sql = 'select ass.sessdate, ass.caleventid, c.idnumber, c.shortname, u.username, u.firstname, u.lastname, ast.acronym, ast.description
			from {attendance_log} alg 
			join {attendance_statuses} ast on ast.id = alg.statusid
			join {attendance_sessions} ass on ass.id = alg.sessionid 
			join {user} u on u.id = alg.studentid
			join {attendance} att on att.id = ast.attendanceid
			join {course} c on c.id = att.course
			where ass.sessdate >= ?
			and ass.sessdate <= ?
			order by ass.sessdate desc';
//
/**/
	return $DB->get_records_sql($sql, array($date_from, $time_to));
}

//individual student

function get_attendance($studentnumber) {
    global $DB;

	$sql = 'select ass.sessdate as session_date, ass.caleventid, c.idnumber as course_id, c.shortname as course_name, u.username as student_number, u.firstname, u.lastname, ast.acronym, ast.description
			from {attendance_log} alg 
			join {attendance_statuses} ast on ast.id = alg.statusid
			join {attendance_sessions} ass on ass.id = alg.sessionid 
			join {user} u on u.id = alg.studentid
			join {attendance} att on att.id = ast.attendanceid
			join {course} c on c.id = att.course
			where u.username = ?
			order by ass.sessdate desc';
		

	return $DB->get_records_sql($sql, array($studentnumber));
}

function get_academic_advisees($userid) {
    global $DB;

	$sql = 'SELECT child.username, child.firstname, child.lastname
			FROM {user} user
			JOIN {role_assignments} ra ON ra.userid = user.id
			JOIN {role} role ON role.id = ra.roleid
			JOIN {context} ctx ON ctx.id = ra.contextid 
			AND ctx.contextlevel = 30
			JOIN {user} child ON child.id = ctx.instanceid
			WHERE role.shortname = "academic_adviser"
			and user.id = ?';
		

	return $DB->get_records_sql($sql, array($userid));
}

/* functions for jisc_csv.php */
function get_jisc_data() {
    global $DB;
	
	$sql = 'select 
    ass.id AS event_id, 
    att.name AS event_name, 
    concat(c.fullname, ": ", ass.description) AS event_description,
    u.username AS student_id, 
    us.username AS staff_id, 
    ass.attendanceid as event_type_id, 
    id_counts.id_count as event_max_count,
    c.shortname AS mod_instance_id,
    ass.sessdate AS start_time, 
    ass.sessdate + ass.duration AS end_time,
    ast.acronym AS event_attended, 
    ast.acronym AS attendance_late, 
    alg.timetaken AS timestamp
from mdl_attendance_log alg
join (select sessionid, count(sessionid) as id_count from mdl_attendance_log group by sessionid ) id_counts on alg.sessionid = id_counts.sessionid
join mdl_attendance_statuses ast on alg.statusid = ast.id
join mdl_attendance_sessions ass on alg.sessionid = ass.id
join mdl_user u on alg.studentid = u.id
join mdl_user us on alg.takenby = us.id
join mdl_attendance att on ast.attendanceid = att.id
join mdl_course c on att.course = c.id
where ass.sessdate > 1420070400
order by ass.sessdate desc';
	
	return $DB->get_records_sql($sql, array());
}

function get_pilot_jisc_data() {
    global $DB;
	
	$sql = 'select 
    ass.id AS event_id, 
    att.name AS event_name, 
    concat(c.fullname, ": ", ass.description) AS event_description,
    u.username AS student_id, 
    us.username AS staff_id, 
    ass.attendanceid as event_type_id, 
    id_counts.id_count as event_max_count,
    c.shortname AS mod_instance_id,
    ass.sessdate AS start_time, 
    ass.sessdate + ass.duration AS end_time,
    ast.acronym AS event_attended, 
    ast.acronym AS attendance_late, 
    alg.timetaken AS timestamp
from mdl_attendance_log alg
join (select sessionid, count(sessionid) as id_count from mdl_attendance_log group by sessionid ) id_counts on alg.sessionid = id_counts.sessionid
join mdl_attendance_statuses ast on alg.statusid = ast.id
join mdl_attendance_sessions ass on alg.sessionid = ass.id
join mdl_user u on alg.studentid = u.id
join mdl_user us on alg.takenby = us.id
join mdl_attendance att on ast.attendanceid = att.id
join mdl_course c on att.course = c.id
where ass.sessdate > 1420070400
	and c.id IN(31586, 30923, 30927, 31620, 31831, 31816, 32074, 31600, 31746, 32416, 32606, 31678, 32081, 32082, 32084)
	order by ass.sessdate desc';
	
	return $DB->get_records_sql($sql, array());
}

/* functions for mail.php */

function get_sessionid($courseid) {
    global $DB;
	$today = date();
			
	$sql = 'SELECT ass.sessdate, ass.id AS ass_id, cm.id AS cmo_id
	FROM mdl_course c
    JOIN mdl_course_modules cm ON cm.course = c.id
    JOIN mdl_attendance_sessions ass ON ass.attendanceid = cm.instance
    where c.idnumber = ?
    and ass.sessdate >= ?
    order by ass.sessdate desc';
	
	return $DB->get_records_sql($sql, array($courseid, $today));
}

function getMyCourses($userid) {
	global $DB;
	
	$sql = 'SELECT c.idnumber, c.shortname
			FROM {role_assignments} ra, {user} u, {course} c, {context} cxt, {attendance_sessions} ass, {attendance} att
			WHERE ra.userid = u.id
			AND ra.contextid = cxt.id
			and att.id = ass.attendanceid
			and c.id = att.course
			AND cxt.instanceid = c.id
			AND (roleid =12 OR roleid=3)
			and c.visible = 1
			and u.id = ?
			ORDER BY c.idnumber';
	
	return $DB->get_records_sql($sql, array($userid));
}

/*get_module_leader_email($mform_data->courseid, $mform_data->day);*/
function get_module_leader_email($courseid) {
    global $DB;
	// query works in MySQL Workbench
	$sql = 'SELECT DISTINCT u.email, u.firstname, u.lastname
			FROM {role_assignments} ra, {user} u, {course} c, {context} cxt, {attendance_sessions} ass, {attendance} att
			WHERE ra.userid = u.id
			AND ra.contextid = cxt.id
			and att.id = ass.attendanceid
			and c.id = att.course
			AND cxt.instanceid = c.id
			AND (roleid =12 OR roleid=3)
			and c.idnumber = ?';
	
	return $DB->get_records_sql($sql, array($courseid));
}

/* functions for register.php */

function get_course_details($courseid) {
    global $DB;
	
		$sql = 'select c.idnumber, c.shortname 
		from {course} c
		where c.idnumber = ?';
	
	return $DB->get_records_sql($sql, array($courseid));
}

function getSessions($today, $userid) {
    global $DB;
		
		$sql = 'select ass.sessdate
			FROM {role_assignments} ra, {user} u, {course} c, {context} cxt, {attendance_sessions} ass, {attendance} att
			WHERE ra.userid = u.id
			AND ra.contextid = cxt.id
			and att.id = ass.attendanceid
			and c.id = att.course
			AND cxt.instanceid = c.id
			AND (roleid =12 OR roleid=3)
			and c.visible = 1
			and ass.sessdate >= ?
			and u.id = ?
			ORDER BY ass.sessdate desc';

	return $DB->get_records_sql($sql, array($today, $userid));
}

function getCourseSessions($course_id, $today) {
    global $DB;
		
	$sql = 'SELECT ass.sessdate
		FROM {attendance} att
		JOIN {attendance_sessions} ass ON ass.attendanceid = att.id
		WHERE att.course = ?
		AND ass.sessdate >= ?
		ORDER BY ass.sessdate';

	return $DB->get_records_sql($sql, array($course_id, $today));
}

function get_register($courseid, $sessdate) {
    global $DB;
	$sql = 'select u.username, u.firstname, u.lastname, ass.sessdate
			FROM {role_assignments} ra, {user} u, {course} c, {context} cxt, {attendance_sessions} ass, {attendance} att
			WHERE ra.userid = u.id
			AND ra.contextid = cxt.id
			and att.id = ass.attendanceid
			and c.id = att.course
			AND cxt.instanceid = c.id
			AND (roleid = 5)
			and c.id = ?
			and ass.sessdate = ?
			order by u.lastname, u.firstname';

	return $DB->get_records_sql($sql, array($courseid, $sessdate));
}

