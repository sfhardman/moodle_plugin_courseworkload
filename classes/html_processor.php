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


class html_processor {

  // https://gist.github.com/w0rldart/9e10aedd1ee55fc4bc74
  protected static function ISO8601ToMinutes($ISO8601){
    $interval = new \DateInterval($ISO8601);

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
    } elseif ($units == "sec") {
      return $duration / 60;
    } elseif ($units == "iso8601") {
      return html_processor::ISO8601ToMinutes($duration);
    } elseif ($units = "min") {
      return $duration;
    }
  }

  protected static function get_duration_for_video($video_id, $known_type) {
    $duration_url = $known_type->get_duration_url;
    $duration_url = str_replace("\$_video_id_", $video_id, $duration_url);
    $http_context = stream_context_create([
      "http" => [
        "method" => "GET",
        "header" => $known_type->headers
      ]
    ]);
    $duration_content = @file_get_contents($duration_url, false, $http_context);
    if ($duration_content === FALSE) {
      $err = error_get_last()['message'];
      if ($known_type->hide_querystrings) {
        $query_string = preg_match_all('/file_get_contents.*\?(.*)\)/', $err, $matches, PREG_PATTERN_ORDER);
        if (count($matches) >= 2) {
          foreach ($matches[1] as $match) {
            $err = str_replace($match, 'SUPPRESSED', $err);
          }
        }
      }
      throw new \Exception($err);
    }
    if (preg_match($known_type->duration_from_response_regex, $duration_content, $matches)) {
      if (count($matches) < 2) {
        throw new \Exception("No regex capture groups returned for $known_type->duration_from_response_regex");
      } 
      $duration = $matches[1];
      $duration = html_processor::get_minutes_duration($duration, $known_type->duration_units);
      return new workload_item(
        $video_id,
        $known_type->name,
        $duration,
        'runtime minutes');
    }
    return NULL;
  }

  protected static function get_items_of_type($html, $known_type) {
    $result = [];
    if (preg_match_all($known_type->video_id_in_html_body_regex, $html, $matches, PREG_PATTERN_ORDER)) {
      if (count($matches) < 2) {
        throw new \Exception("No regex capture groups returned for $known_type->video_id_in_html_body_regex");
      }
      foreach ($matches[1] as $video_id) {
        $duration_item = html_processor::get_duration_for_video($video_id, $known_type);
        if (!is_null($duration_item)) {
          array_push($result, $duration_item);
        }
      }
    }
    return $result;
  }

  protected static function get_hyperlinked_items($html) {
    $result = array();
    $reportconfig = get_config('report_courseworkload');
    $known_types = json_decode($reportconfig->video_duration_apis); 

    foreach ($known_types as $known_type) {
      $result = array_merge($result, html_processor::get_items_of_type($html, $known_type));
    }
    return $result;
  }

  public static function get_workload_items($html, $base_name, $base_type) {
    $result = array();
    array_push($result, new workload_item(
      $base_name,
      $base_type,
      word_count::get_word_count($html),
      'words'));
    $linked = html_processor::get_hyperlinked_items($html);
    $result = array_merge($result, $linked);
    return $result;
  }
}