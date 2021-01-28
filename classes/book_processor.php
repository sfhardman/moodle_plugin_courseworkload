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
 * Calculates the workload for a Moodle Book
 *
 * @package    report_courseworkload
 * @author  Simon Hardman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_courseworkload;

defined('MOODLE_INTERNAL') || die();

class book_processor
{
    public static function get_workload_items($DB, $dbcoursemodule) {
        $items = array();
        $params = array('id' => $dbcoursemodule->instance);
        $book = $DB->get_record('book', $params, '*');
        $contentitems = html_processor::get_workload_items($book->intro, $book->name, 'book intro');
        $items = array_merge($items, $contentitems);
        $params = array('bookid' => $dbcoursemodule->instance);
        $chapters = $DB->get_records('book_chapters', $params, '', '*');
        foreach ($chapters as $chapter) {
            if ($chapter->subchapter == 0) {
                $chaptertype = 'book chapter';
            } else {
                $chaptertype = 'book subchapter';
            }
            $contentitems = html_processor::get_workload_items($chapter->content, $chapter->title, $chaptertype);
            $items = array_merge($items, $contentitems);
        }
        return $items;
    }
}
