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
 * Calculates the workload for a Moodle Course
 *
 * @package    report_courseworkload
 * @author  Simon Hardman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_courseworkload;

defined('MOODLE_INTERNAL') || die();

class course implements \renderable
{
    protected $courseid;
    protected $name;
    protected $sections = array();

    public function __construct($courseid, $name) {
        $this->name = $name;
        $this->course_id = $courseid;
    }

    public function get_name() {
        return $this->name;
    }

    public function get_sections() {
        return $this->sections;
    }

    public function process($DB) {
        $params = array('course' => $this->course_id);
        $dbsections = $DB->get_records('course_sections', $params, '', '*');

        foreach ($dbsections as $dbsection) {
            $section = new section($dbsection);
            array_push($this->sections, $section);
            $section->process($DB);
        }
    }
}
