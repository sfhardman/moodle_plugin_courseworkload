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
 * Abstract base class for calculating workload for attached files
 *
 * @package    report_courseworkload
 * @author  Simon Hardman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_courseworkload;

defined('MOODLE_INTERNAL') || die();

abstract class file_processor
{
    protected $file;

    public function __construct($file) {
        $this->file = $file;
    }

    abstract protected function get_workload_from_file_handle($filehandle);

    protected function get_file_path($filehandle) {
        $md = stream_get_meta_data($filehandle);
        return $md['uri'];
    }

    public function get_workload_item() {
        $filehandle = $this->file->get_content_file_handle();
        try {
            return $this->get_workload_from_file_handle($filehandle);
        } finally {
            fclose($filehandle);
        }
    }
}
