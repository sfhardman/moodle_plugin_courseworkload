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
 * Calculates the workload for an attached PHP file
 * 
 * @package    report_courseworkload
 * @author  Simon Hardman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_courseworkload;

defined('MOODLE_INTERNAL') || die();

class file_processor_pptx extends file_processor_openxml {

  protected function get_target_filename() {
    return 'docProps/app.xml';
  }

  public static function register($factory) {
    $factory->register_class(static::class, 'application/vnd.openxmlformats-officedocument.presentationml.presentation');
  }

  protected function get_workload_from_data($data) {
    $xml = new \SimpleXMLElement($data);
    $pageCount = (int)$xml->Slides;
    return new workload_item($this->file->get_filename(),
      'powerpoint',
      $pageCount,
      'slides');
  }
  
}
