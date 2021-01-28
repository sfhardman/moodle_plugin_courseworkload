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
 * Calculates workload for a block of HTML, including hyperlinked videos
 *
 * @package    report_courseworkload
 * @author  Simon Hardman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_courseworkload;

defined('MOODLE_INTERNAL') || die();


class html_processor
{

    // Source: https://gist.github.com/w0rldart/9e10aedd1ee55fc4bc74 .
    protected static function iso8601_to_minutes($iso8601) {
        $interval = new \DateInterval($iso8601);

        return ($interval->d * 24 * 60) +
        ($interval->h * 60) +
        ($interval->i) +
        ($interval->s / 60);
    }

    public static function get_possible_duration_units() {
        return [
            'sec',
            'msec',
            'min',
            'iso8601'
        ];
    }

    protected static function get_minutes_duration($duration, $units) {
        if ($units == "msec") {
            return $duration / (1000 * 60);
        } else if ($units == "sec") {
            return $duration / 60;
        } else if ($units == "iso8601") {
            return self::iso8601_to_minutes($duration);
        } else if ($units = "min") {
            return $duration;
        }
    }

    protected static function get_duration_for_video($videoid, $knowntype) {
        $durationurl = $knowntype->get_duration_url;
        $durationurl = str_replace("\$_video_id_", $videoid, $durationurl);
        $httpcontext = stream_context_create([
            "http" => [
                "method" => "GET",
                "header" => $knowntype->headers
            ]
        ]);
        $durationcontent = @file_get_contents($durationurl, false, $httpcontext);
        if ($durationcontent === false) {
            $err = error_get_last()['message'];
            if ($knowntype->hide_querystrings) {
                $querystring = preg_match_all('/file_get_contents.*\?(.*)\)/', $err, $matches, PREG_PATTERN_ORDER);
                if (count($matches) >= 2) {
                    foreach ($matches[1] as $match) {
                        $err = str_replace($match, 'SUPPRESSED', $err);
                    }
                }
            }
            throw new \Exception($err);
        }
        if (preg_match($knowntype->duration_from_response_regex, $durationcontent, $matches)) {
            if (count($matches) < 2) {
                throw new \Exception("No regex capture groups returned for $knowntype->duration_from_response_regex");
            }
            $duration = $matches[1];
            $duration = self::get_minutes_duration($duration, $knowntype->duration_units);
            return new workload_item(
                $videoid,
                $knowntype->name,
                $duration,
                'runtime minutes'
            );
        }
        return null;
    }

    protected static function get_items_of_type($html, $knowntype) {
        $result = [];
        if (preg_match_all($knowntype->video_id_in_html_body_regex, $html, $matches, PREG_PATTERN_ORDER)) {
            if (count($matches) < 2) {
                throw new \Exception("No regex capture groups returned for $knowntype->video_id_in_html_body_regex");
            }
            foreach ($matches[1] as $videoid) {
                $durationitem = self::get_duration_for_video($videoid, $knowntype);
                if (!is_null($durationitem)) {
                    array_push($result, $durationitem);
                }
            }
        }
        return $result;
    }

    protected static function get_hyperlinked_items($html) {
        $result = array();
        $reportconfig = get_config('report_courseworkload');
        $knowntypes = json_decode($reportconfig->video_duration_apis);

        foreach ($knowntypes as $knowntype) {
            $result = array_merge($result, self::get_items_of_type($html, $knowntype));
        }
        return $result;
    }

    public static function get_workload_items($html, $basename, $basetype) {
        $result = array();
        array_push($result, new workload_item(
            $basename,
            $basetype,
            word_count::get_word_count($html),
            'words'
        ));
        $linked = self::get_hyperlinked_items($html);
        $result = array_merge($result, $linked);
        return $result;
    }
}
