<?php
// This file is part of Moodle - http://moodle.org/
//
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
 *
 * @package    report_courseworkload
 * @author  Simon Hardman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$course_id = required_param('id', PARAM_INT);

$params = array('id' => $course_id);
$db_course = $DB->get_record('course', $params, '*', MUST_EXIST);
require_login($db_course);
$course_context = context_course::instance($db_course->id);


$urlparams = array('id' => $course_id);

$url = new moodle_url('/report/courseworkload/index.php', $urlparams);

$title = get_string('pluginname', 'report_courseworkload');
$coursename = format_string($db_course->fullname, true, array('context' => $course_context));


$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($coursename);
$PAGE->set_pagelayout('incourse');

$course = new \report_courseworkload\course($course_id, $coursename);

$course->process($DB);

$output = $PAGE->get_renderer('report_courseworkload');

echo $output->header();
echo $output->render($course);
echo $output->footer();
