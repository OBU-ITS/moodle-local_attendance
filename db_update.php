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


function get_register($courseid, $day) {
    global $DB;
	$now = date();
	if ($day < $now) {
		$sql = 'select ass.sessdate, c.shortname as course_name, u.username as student_number, u.firstname, u.lastname
		from {attendance_log} alg, {attendance_statuses} ast, {attendance_sessions} ass, {user} u, {course} c
		where ast.id = alg.statusid
		and ass.id = alg.sessionid 
		and  alg.studentid = u.id
		and att.id = ast.attendanceid
		and c.id = att.course
		and c.idnumber = ?
		and ass.sessdate = ?
		order by u.lastname, u.firstname';
	} else {
  		$sql = 'select ass.sessdate, g.name as groupname, c.shortname as course_name, u.username as student_number, u.firstname, u.lastname
	  	FROM {user} u, {user_enrolments} ue, {enrol} e, {role_assignments} ra, {context} ct, {course} c, {groups} g, {attendance_sessions} ass
		where  u.id = ue.userid
		and  ue.enrolid = e.id
		and  u.id = ra.userid
		and ra.contextid = ct.id
		AND ct.contextlevel = 50
		and ct.instanceid = c.id
		and  c.id = g.courseid
	    and g.id = ass.groupid
	    and c.idnumber = ?
		and ass.sessdate >= ?
		order by u.lastname, u.firstname';
	}
	/*if ($day < $now) {
		$sql = 'select ass.sessdate, c.shortname as course_name, u.username as student_number, u.firstname, u.lastname
		from {attendance_log} alg 
		join {attendance_statuses} ast on ast.id = alg.statusid
		join {attendance_sessions} ass on ass.id = alg.sessionid 
		join {user} u on u.id = alg.studentid
		join {attendance} att on att.id = ast.attendanceid
		join {course} c on c.id = att.course
		where c.idnumber = ?
		and ass.sessdate = ?
		order by u.lastname, u.firstname';
	} else {
  		$sql = 'select ass.sessdate, g.name as groupname, c.shortname as course_name, u.username as student_number, u.firstname, u.lastname
	  	FROM {user} u
		JOIN {user_enrolments} ue ON ue.userid = u.id
		JOIN {enrol} e ON e.id = ue.enrolid
		JOIN {role_assignments} ra ON ra.userid = u.id
		JOIN {context} ct ON ct.id = ra.contextid
		AND ct.contextlevel =50
		JOIN {course} c ON c.id = ct.instanceid
		join {groups} g ON g.courseid = c.id  
	    JOIN {attendance_sessions} ass ON ass.groupid = g.id 
	    where c.idnumber = ?
		and ass.sessdate >= ?
		order by u.lastname, u.firstname';
	}*/
	return $DB->get_records_sql($sql, array($courseid, $day));
}

function get_course_details($courseid) {
    global $DB;
	
		$sql = 'select c.idnumber, c.shortname 
		from {course} c
		where c.idnumber = ?';
	
	return $DB->get_records_sql($sql, array($courseid));
}
