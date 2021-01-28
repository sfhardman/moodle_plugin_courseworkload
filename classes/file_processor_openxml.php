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

abstract class file_processor_openxml extends file_processor
{
    abstract protected function get_target_filename();

    abstract protected function get_workload_from_data($data);

    protected function get_workload_from_file_handle($filehandle) {
        $filepath = $this->get_file_path($filehandle);
        $zip = new \ZipArchive();
        $zipresult = $zip->open($filepath);
        if ($zipresult) {
            try {
                $targetfilename = $this->get_target_filename();
                if ($index = $zip->locateName($targetfilename)) {
                    $data = $zip->getFromIndex($index);
                    return $this->get_workload_from_data($data);
                } else {
                    throw new \Exception("Could not find $targetfilename in zip archive $filepath");
                }
            } finally {
                $zip->close();
            }
        } else {
            throw new \Exception("Could not unzip $filepath");
        }
    }
}
