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
 * Abstract base class for calculating workload from an OpenXML
 * document (e.g. Office doc)
 * 
 * @package    report_courseworkload
 * @author  Simon Hardman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace report_courseworkload;

defined('MOODLE_INTERNAL') || die();

abstract class file_processor_openxml extends file_processor {

  protected abstract function get_target_filename();

  protected abstract function get_workload_from_data($data);

  protected function get_workload_from_file_handle($file_handle) {
    $file_path = $this->get_file_path($file_handle);
    $zip = new \ZipArchive();
    $zip_result=$zip->open($file_path);
    if ($zip_result) {
      try {
        $target_filename = $this->get_target_filename();
        if ($index = $zip->locateName($target_filename))  {
          $data = $zip->getFromIndex($index);
          return $this->get_workload_from_data($data);
        } else {
          throw new \Exception("Could not find $target_filename in zip archive $file_path");
        }
      } finally {
        $zip->close();
      }
    } else {
      throw new \Exception("Could not unzip $file_path");
    }
  }
  
}