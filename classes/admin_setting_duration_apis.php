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
 * Provides a control with custom validation for setting the admin
 * configuration for video duration APIs
 *
 * @package    report_courseworkload
 * @author  Simon Hardman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_courseworkload;

defined('MOODLE_INTERNAL') || die();

class admin_setting_duration_apis extends \admin_setting_configtextarea {
    public function validate($data) {
      $parentvalidation = parent::validate($data);
      if ($parentvalidation !== true) {
        return $parentvalidation;
      }
      try {
        $value = json_decode($data);
      } catch (Exception $e) {
        return get_string('config_value_not_json', 'report_courseworkload');
      }
      if (!is_array($value)) {
        return get_string('config_value_not_array', 'report_courseworkload');
      }
      foreach ($value as $index => $item) {
        if (!is_object($item)) {
          return str_replace('<index>', $index, get_string('config_value_item_not_object', 'report_courseworkload'));
        }
        $properties = [
          "name", 
          "video_id_in_html_body_regex",
          "get_duration_url",
          "duration_from_response_regex",
          "duration_units",
          "headers",
          "hide_querystrings",
        ];
        foreach ($properties as $property) {
          if (!property_exists($item, $property)) {
            return str_replace('<index>', $index, 
               str_replace('<property>', $property, get_string('config_value_item_missing_property', 'report_courseworkload'))
            );
          }
        }
        $possible_units = html_processor::get_possible_duration_units();
        if (!in_array($item->duration_units, $possible_units, TRUE)) {
          return str_replace('<index>', $index,
            str_replace('<value>', $item->duration_units,
              str_replace('<possible_values>', join($possible_units, ", "),
               get_string('config_value_item_invalid_duration_units', 'report_courseworkload')
              )
            )
          );
        }
      }
      return true;
    }
  }