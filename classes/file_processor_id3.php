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
 * Abstract base class for calculating workload for attached files with ID3 tags
 *
 * @package    report_courseworkload
 * @author  Simon Hardman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_courseworkload;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../vendor/autoload.php');

abstract class file_processor_id3 extends file_processor
{
    abstract protected function get_type_name();

    protected function get_workload_from_file_handle($filehandle) {
        $filepath = $this->get_file_path($filehandle);
        $getid3 = new \getID3;
        $id3 = $getid3->analyze($filepath);
        return new workload_item(
            $this->file->get_filename(),
            $this->get_type_name(),
            $id3['playtime_seconds'] / 60,
            'runtime minutes'
        );
    }
}
