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
 * Calculates the workload for a Moodle Course Module
 * 
 * @package    report_courseworkload
 * @author  Simon Hardman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_courseworkload;

defined('MOODLE_INTERNAL') || die();

class course_module_processor {
  public static function get_workload_items($DB, $db_course_module) {
    $result = array();
    if ($db_course_module->deletioninprogress == "1") {
        return $result;
    }
    $params = array('id' => $db_course_module->module);
    $module = $DB->get_record('modules', $params, '*'); 
    if ($module->name == 'resource') {
      return resource_module_processor::get_workload_items($db_course_module->id);
    } else if ($module->name == 'page') {
      return page_processor::get_workload_items($DB, $db_course_module);
    } else if ($module->name == 'book') {
      return book_processor::get_workload_items($DB, $db_course_module);
    } else {
      return [
        new workload_item(
          '',
          "unsupported: {$module->name}",
          0,
          'unknown'
        )
      ];
    }
    return $result;
  }
}