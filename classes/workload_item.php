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
 * Represents a workload item
 *
 * @package    report_courseworkload
 * @author  Simon Hardman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_courseworkload;

defined('MOODLE_INTERNAL') || die();

class workload_item
{
    protected $name;
    protected $type;
    protected $quantity;
    protected $units;
    protected $workloadminutes;


    public function __construct($name, $type, $quantity, $units) {
        $reportconfig = get_config('report_courseworkload');
        $this->name = $name;
        $this->type = $type;
        $this->quantity = $quantity;
        $this->units = $units;
        if ($this->units == 'slides') {
            $this->workload_minutes = $quantity * $reportconfig->minutes_per_slide;
        } else if ($this->units == 'pages') {
            $this->workload_minutes = $quantity * $reportconfig->minutes_per_page;
        } else if ($this->units == 'words') {
            $this->workload_minutes = $quantity / $reportconfig->words_per_minute;
        } else if ($this->units == 'runtime minutes') {
            $this->workload_minutes = $quantity;
        } else {
            $this->workload_minutes = 0;
        }
    }

    public function get_name() {
        return $this->name;
    }

    public function get_type() {
        return $this->type;
    }

    public function get_quantity() {
        return $this->quantity;
    }

    public function get_units() {
        return $this->units;
    }

    public function get_workload_minutes() {
        return $this->workload_minutes;
    }
}
