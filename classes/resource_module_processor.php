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
 * Calculates the workload for a Moodle Resource Module
 *
 * @package    report_courseworkload
 * @author  Simon Hardman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_courseworkload;

defined('MOODLE_INTERNAL') || die();

class resource_module_processor
{
    public static function get_workload_items($coursemoduleid) {
        $result = array();
        $context = \context_module::instance($coursemoduleid);
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_resource', 'content', 0, 'sortorder DESC, id ASC', false);
        $processorfactory = new file_processor_factory();
        foreach ($files as $file) {
            $processor = $processorfactory->create($file);
            if ($processor) {
                $item = $processor->get_workload_item();
                if ($item) {
                    array_push($result, $item);
                }
            } else {
                array_push($result, new workload_item(
                    $file->get_filename(),
                    "unsupported: {$file->get_mimetype()}",
                    0,
                    "unknown"
                ));
            }
        }
        return $result;
    }
}
