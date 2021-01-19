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
 * Constructs a file processor subclass that can handle a given mime type
 * 
 * @package    report_courseworkload
 * @author  Simon Hardman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_courseworkload;

defined('MOODLE_INTERNAL') || die();

class file_processor_factory {
  private $processors = [];

  public function __construct() {
    file_processor_audio::register($this);
    file_processor_video::register($this);
    file_processor_docx::register($this);
    file_processor_pptx::register($this);
    file_processor_pdf::register($this);
  }

  public function register_class($class_name, $supported_mime_type) {
    $this->processors[$class_name] = $supported_mime_type;
  }

  public function create($file) {
    $mime_type = $file->get_mimetype();
    $class_name = array_search($mime_type, $this->processors);
    if ($class_name === FALSE) {
      return NULL;
    }
    return new $class_name($file);
  }
}