# Moodle Course Workload Report Plugin

## Overview
This is a report plugin for Moodle, which provides an estimate of the time required for a student to view the material in a Moodle course.

The report provides estimates for the following content types:
* Attached MP3 audio
* Attached MP4 video
* Attached PDF documents
* Attached Word documents (DOCX format only)
* Attached PowerPoint documents (PPTX format only)
* Hyperlinked videos.  Tested with YouTube and Mediasite, but hopefully generic enough to support most video content providers.
* Moodle Book content
* Moodle Section content
* Moodle Page content

## Usage
* To install:
  * Have PHP Composer installed.
  * Run `composer.phar install` from this directory to install PHP dependencies.
  * Copy this directory to the "report" folder under your Moodle root directory.
* To run the report:
  * Select a course in Moodle
  * Click "Settings (Gear) | More", then "Reports | Course Workload Breakdown"

## Configuration
* The report can be configured from the Site Administration Dashboard ("Reports | Course Workload").
* The basic settings allow you to configure the site-wide estimated student reading speeds.
* The "Video duration APIs" setting allows you to configure the report to obtain durations from hyperlinked videos (e.g. on YouTube).  As these almost invariably require an API key or authentication against the hosting site, nothing is set up by default.

### Video Duration APIs Setting
* This setting should be a JSON array consisting of zero or more objects, each of which configures for a video hosting provider.
* The required properties for each provider object are:
  * `name` : Display name for the provider
  * `video_id_in_html_body_regex`: A regular expression which detects URLs in Moodle HTML that correspond to hyperlinked videos from this provider.  The regex must return the individual video's unique ID in the first regex capture group.
  * `get_duration_url`: A templated URL which can be called with HTTP GET to retrieve a video's duration.  The string `$_video_id_` must be present in the URL, this will be replaced with the individual video's unique ID.
  * `headers`: any HTTP headers that must be passed with the call to `get_duration_url`.  Multiple headers can be seperated with a newline character.  If no extra headers are needed, supply an empty string
  * `duration_from_response_regex`: A regular expression which extracts the video duration from the response from `get_duration_url`.  The duration must be returned in the first regex capture group.
  * `duration_units`: The units of duration in the `get_duration_url` response.  Supported values are:
    * `sec`: seconds
    * `msec`: milliseconds
    * `min`: minutes
    * `iso8601`: ISO8601 duration format, as used by YouTube (e.g. "PT20M1S")
  * `hide_querystrings`: Determines if any querystrings in get_duration_url should be hidden in error messages.  Set to `true` if you have an api key or other secret in the URL.
  

  Example:

  ```javascript
    [ 
      { 
        "name": "Youtube Video", 
        "video_id_in_html_body_regex": "%(?:youtube(?:-nocookie)?\\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\\.be\/)([^\"&?\/\\s]{11})%i",
        "get_duration_url": "https:\/\/www.googleapis.com\/youtube\/v3\/videos?id=$_video_id_&part=contentDetails&key=YOUR_API_KEY", 
        "duration_from_response_regex": "\/\"duration\":\\s\"(\\w+)\"\/", 
        "duration_units": "iso8601",
        "headers": "", 
        "hide_querystrings": true,
      },
      { 
        "name": "Mediasite Video",
        "video_id_in_html_body_regex": "\/https:\\\/\\\/example.org\\\/Mediasite\\\/Play\\\/(\\w+)\/", 
        "get_duration_url": "https:\/\/example.org\/Mediasite\/Api\/v1\/Presentations('$_video_id_')?$select=card", 
        "duration_from_response_regex": "\/\"Duration\":\\s*([0-9]*),\/", 
        "duration_units": "msec", 
        "headers": "sfapikey: YOUR_API_KEY\nAuthorization: Basic YOUR_CREDENTIALS",
        "hide_querystrings": false,
      }
    ]
  ```

  