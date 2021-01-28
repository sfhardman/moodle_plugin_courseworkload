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
 * Calculates the workload for an attached PDF file
 *
 * @package    report_courseworkload
 * @author  Simon Hardman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_courseworkload;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../vendor/autoload.php');

class file_processor_pdf extends file_processor
{
    public static function register($factory) {
        $factory->register_class(static::class, 'application/pdf');
    }

    protected function get_workload_from_file_handle($filehandle) {
        $parser = new \Smalot\PdfParser\Parser();
        $filepath = $this->get_file_path($filehandle);
        $pdf = $parser->parseFile($filepath);
        $text = $pdf->getText();
        if (strlen($text) == 0) {
            $details = $pdf->getDetails();
            return new workload_item(
                $this->file->get_filename(),
                'scanned pdf',
                $details['Pages'],
                'pages'
            );
        }
        return new workload_item(
            $this->file->get_filename(),
            'pdf',
            word_count::get_word_count($text),
            'words'
        );
    }
}
