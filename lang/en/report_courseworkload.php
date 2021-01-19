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

$string['Course workload'] = 'Course workload';
$string['courseCourse workloadbreakdownsummary'] = 'A report of all the students in the course, and their progress towards the course competencies';
$string['notrated'] = 'Not rated';
$string['pluginname'] = 'Course workload breakdown';
$string['privacy:metadata'] = 'The Course workload breakdown plugin does not store any personal data.';
$string['manage'] = 'Course workload';
$string['words_per_minute'] = 'Words per Minute';
$string['words_per_minute_desc'] = 'Estimated number of words per minute read';
$string['minutes_per_page'] = 'Minutes per Page';
$string['minutes_per_page_desc'] = 'Estimated time to read one page of text (used when word count cannot be determined, e.g. scanned PDF)';
$string['minutes_per_slide'] = 'Minutes per Slide';
$string['minutes_per_slide_desc'] = 'Estimated time to review one slide of a presentation (e.g. PowerPoint)';
$string['video_duration_apis'] = 'Video duration APIs';
$string['video_duration_apis_desc'] = 'Advanced! JSON to configure access to APIs that can determine the duration of a hyperlinked video';
$string['config_value_not_json'] = 'Value is not valid JSON';
$string['config_value_not_array'] = 'Value is not a JSON array';
$string['config_value_item_not_object'] = 'Item at index <index> in the array is not a JSON object';
$string['config_value_item_missing_property'] = 'Item at index <index> in the array is missing property "<property>"';
$string['config_value_item_invalid_duration_units'] = 'Item at index <index> in the array has an invalid value "<value>" for "duration_units", possible values are "<possible_values>"';