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
 * Calculates the workload for a Moodle Section
 *
 * @package    report_courseworkload
 * @author  Simon Hardman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_courseworkload;

defined('MOODLE_INTERNAL') || die();

class section implements \renderable
{
    protected $name;
    protected $sectionid;
    protected $courseid;
    protected $items = array();
    protected $summary;

    public function __construct($dbsection) {
        $this->section_id = $dbsection->id;
        $this->course_id = $dbsection->course;
        $this->name = $dbsection->name;
        $this->summary = $dbsection->summary;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_course_id() {
        return $this->course_id;
    }

    public function get_section_id() {
        return $this->section_id;
    }

    public function get_items() {
        return $this->items;
    }

    public function process($DB) {
        $contentitems = html_processor::get_workload_items($this->summary, $this->name, 'moodle section');
        $this->items = array_merge($this->items, $contentitems);

        $params = array('course' => $this->course_id, 'section' => $this->section_id);
        $coursemodules = $DB->get_records('course_modules', $params, '', '*');
        foreach ($coursemodules as $coursemodule) {
            $params = array('id' => $coursemodule->module);
            $module = $DB->get_record('modules', $params, '*');
            $cmitems = course_module_processor::get_workload_items($DB, $coursemodule);
            $this->items = array_merge($this->items, $cmitems);
        }
    }
}
