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

class report_courseworkload_renderer extends \plugin_renderer_base {
  protected function render_course(report_courseworkload\course $course) {
    $out = $this->output->heading("Estimated course workload: {$course->get_name()}", 2);
    $total_workload = 0;
    $sections = '';
    foreach ($course->get_sections() as $section) {
      $sections .= $this->output->container($this->render_section($section), 'report-courseworkload-section');
      foreach ($section->get_items() as $item) {
        $total_workload = $total_workload + $item->get_workload_minutes();
      }
    }
    $total_workload = round($total_workload, 1);
    $out .= $this->output->heading("Total workload: $total_workload minutes", 3);
    $out .= $sections;
    return $out;
  }

  protected function render_section(report_courseworkload\section $section) {
    $out = $this->output->heading("Section: {$section->get_name()}", 3);
    $table = new html_table();
    $table->head = array('Type','Name', 'Amount' , 'Est. Workload Mins');
    $table_data = array();
    $section_total_workload = 0;
    foreach ($section->get_items() as $item) {
      $round_qty = round($item->get_quantity(), 1);
      $round_workload = round($item->get_workload_minutes(),1);
      $section_total_workload = $section_total_workload + $item->get_workload_minutes();
      array_push($table_data, array(
        $item->get_type(),
        $item->get_name(),
        "$round_qty {$item->get_units()}",
        $round_workload
      ));
    }
    if (count($table_data) > 1) {
      $section_total_workload = round($section_total_workload, 1);
      array_push($table_data, array('Total','','',$section_total_workload));
    }
    if (count($table_data) == 0) {
      return '';
    }
    $table->data = $table_data;
    $out .= html_writer::table($table);
    return $out;
  }

}