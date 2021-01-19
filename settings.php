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
 *
 * @package    report_courseworkload
 * @author  Simon Hardman
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {

  $ADMIN->add('reports', new admin_category('report_courseworkload_settings', new lang_string('pluginname', 'report_courseworkload')));
  $settingspage = new admin_settingpage('managereportcourseworkload', new lang_string('manage', 'report_courseworkload'));

  if ($ADMIN->fulltree) {
    $settingspage->add(new admin_setting_configtext('report_courseworkload/words_per_minute', 
      get_string('words_per_minute', 'report_courseworkload'),
      get_string('words_per_minute_desc', 'report_courseworkload'), 200, PARAM_INT));
    $settingspage->add(new admin_setting_configtext('report_courseworkload/minutes_per_page', 
      get_string('minutes_per_page', 'report_courseworkload'),
      get_string('minutes_per_page_desc', 'report_courseworkload'), 10, PARAM_INT));    
    $settingspage->add(new admin_setting_configtext('report_courseworkload/minutes_per_slide', 
      get_string('minutes_per_slide', 'report_courseworkload'),
      get_string('minutes_per_slide_desc', 'report_courseworkload'), 1.0, PARAM_FLOAT)); 
    $settingspage->add(new \report_courseworkload\admin_setting_duration_apis('report_courseworkload/video_duration_apis', 
      get_string('video_duration_apis', 'report_courseworkload'),
      get_string('video_duration_apis_desc', 'report_courseworkload'), '[]'));                
  }
 
  $ADMIN->add('reports', $settingspage);
}