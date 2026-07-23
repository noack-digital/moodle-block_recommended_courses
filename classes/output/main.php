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
 * Class containing data for the recommended courses block.
 *
 * @package    block_recommended_courses
 * @copyright  2025 Alexander Noack
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_recommended_courses\output;

defined('MOODLE_INTERNAL') || die();

use block_recommended_courses\course_helper;
use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Class containing data for the recommended courses block.
 *
 * @package    block_recommended_courses
 * @copyright  2025 Alexander Noack
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class main implements renderable, templatable {
    /** @var array List of courses to display in the slider. */
    protected $courses;

    /** @var string Enrollment button text. */
    protected $buttontext;

    /** @var string Message shown when no courses exist. */
    protected $nocoursesmessage;

    /** @var array Display options. */
    protected $displayoptions;

    /** @var bool Whether only unenrolled courses are shown. */
    protected $unenrolledonly;

    /** @var bool Whether the enrolment filter toggle is available. */
    protected $showenrolmentfilter;

    /**
     * Constructor.
     *
     * @param array $courses List of courses for the slider.
     * @param array $displayoptions Display options for the slider.
     * @param bool $unenrolledonly Whether enrolled courses are currently hidden.
     */
    public function __construct($courses, $displayoptions = [], $unenrolledonly = true) {
        $this->courses = $courses;
        $this->displayoptions = course_helper::sanitize_display_options($displayoptions);
        $this->unenrolledonly = (bool) $unenrolledonly;
        $this->showenrolmentfilter = isloggedin() && !isguestuser();
        $this->buttontext = get_string('enrollbutton', 'block_recommended_courses');
        $this->nocoursesmessage = get_string('no_courses_to_display', 'block_recommended_courses');
    }

    /**
     * Export data for the mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->uniqid = uniqid();
        $data->hascourses = !empty($this->courses);
        $data->courses = [];
        $data->buttontext = $this->buttontext;
        $data->no_courses_message = $this->nocoursesmessage;
        $data->prev_course = get_string('previous_course', 'block_recommended_courses');
        $data->next_course = get_string('next_course', 'block_recommended_courses');

        $data->layout_mode = $this->displayoptions['layout_mode'];
        $data->image_fit = $this->displayoptions['image_fit'];
        $data->image_height = $this->displayoptions['image_height'];
        $data->border_radius = $this->displayoptions['border_radius'];
        $data->animation_speed = (int) $this->displayoptions['animation_speed'];
        $data->autoslide = (int) $this->displayoptions['autoslide'];
        $data->show_cards = (int) $this->displayoptions['show_cards'];
        $data->show_button = (int) $this->displayoptions['show_button'];
        $data->show_category = (int) $this->displayoptions['show_category'];
        $data->show_contact = (int) $this->displayoptions['show_contact'];
        $data->show_lastmodified = (int) $this->displayoptions['show_lastmodified'];

        $data->show_enrolment_filter = $this->showenrolmentfilter ? 1 : 0;
        $data->unenrolled_only = $this->unenrolledonly ? 1 : 0;
        $data->enrolment_filter_label = get_string('filter_unenrolled_only', 'block_recommended_courses');
        $data->enrolment_filter_pref = 'block_recommended_courses_unenrolledonly';

        $data->meta_label_category = get_string('meta_label_category', 'block_recommended_courses');
        $data->meta_label_contact = get_string('meta_label_contact', 'block_recommended_courses');
        $data->meta_label_lastmodified = get_string('meta_label_lastmodified', 'block_recommended_courses');
        $data->meta_label_category_json = json_encode(
            $data->meta_label_category,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
        );
        $data->meta_label_contact_json = json_encode(
            $data->meta_label_contact,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
        );
        $data->meta_label_lastmodified_json = json_encode(
            $data->meta_label_lastmodified,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
        );

        if (!$data->hascourses) {
            $data->coursesJson = course_helper::encode_courses_json([]);
            $data->coursescount = 0;
            $data->current_position = 0;
            return $data;
        }

        $first = true;
        $visiblecount = 0;

        foreach ($this->courses as $course) {
            $coursedata = new stdClass();
            $coursedata->id = $course['id'];
            $coursedata->fullname = $course['fullname'];
            $coursedata->shortname = $course['shortname'];
            $coursedata->summary = $course['summary'];
            $coursedata->category = isset($course['category']) ? $course['category'] : '';
            $coursedata->courseimage = $course['courseimage'];
            $coursedata->viewurl = $course['viewurl'];
            $coursedata->enrollurl = $course['enrollurl'];
            $coursedata->isenrolled = !empty($course['isenrolled']);
            $coursedata->actiontext = isset($course['actiontext']) ? $course['actiontext'] : $this->buttontext;

            if (!empty($course['contact'])) {
                $coursedata->has_contact = true;
                $coursedata->contact_name = $course['contact']['name'];
                $coursedata->contact_profileurl = $course['contact']['profileurl'];
            } else {
                $coursedata->has_contact = false;
            }

            $coursedata->lastmodified = isset($course['lastmodified']) ? $course['lastmodified'] : '';
            $coursedata->first = $first;
            $coursedata->visible = $visiblecount < 4;
            $data->courses[] = $coursedata;

            if ($first) {
                $data->buttontext = $coursedata->actiontext;
            }

            $first = false;
            $visiblecount++;
        }

        $data->coursesJson = course_helper::encode_courses_json($this->courses);
        $data->coursescount = count($this->courses);
        $data->current_position = 1;

        return $data;
    }
}
