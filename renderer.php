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

defined('MOODLE_INTERNAL') || die();

class report_courseworkload_renderer extends \plugin_renderer_base
{
    protected function render_course(report_courseworkload\course $course) {
        $out = $this->output->heading("Estimated course workload: {$course->get_name()}", 2);
        $totalworkload = 0;
        $sections = '';
        foreach ($course->get_sections() as $section) {
            $sections .= $this->output->container($this->render_section($section), 'report-courseworkload-section');
            foreach ($section->get_items() as $item) {
                $totalworkload = $totalworkload + $item->get_workload_minutes();
            }
        }
        $totalworkload = round($totalworkload, 1);
        $out .= $this->output->heading("Total workload: $totalworkload minutes", 3);
        $out .= $sections;
        return $out;
    }

    protected function render_section(report_courseworkload\section $section) {
        $out = $this->output->heading("Section: {$section->get_name()}", 3);
        $table = new html_table();
        $table->head = array('Type', 'Name', 'Amount' , 'Est. Workload Mins');
        $tabledata = array();
        $sectiontotalworkload = 0;
        foreach ($section->get_items() as $item) {
            $roundqty = round($item->get_quantity(), 1);
            $roundworkload = round($item->get_workload_minutes(), 1);
            $sectiontotalworkload = $sectiontotalworkload + $item->get_workload_minutes();
            array_push($tabledata, array(
                $item->get_type(),
                $item->get_name(),
                "$roundqty {$item->get_units()}",
                $roundworkload
            ));
        }
        if (count($tabledata) > 1) {
            $sectiontotalworkload = round($sectiontotalworkload, 1);
            array_push($tabledata, array('Total', '', '', $sectiontotalworkload));
        }
        if (count($tabledata) == 0) {
            return '';
        }
        $table->data = $tabledata;
        $out .= html_writer::table($table);
        return $out;
    }
}
