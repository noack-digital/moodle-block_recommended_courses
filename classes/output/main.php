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
 * @copyright  2025 Moodle Developer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_recommended_courses\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Class containing data for the recommended courses block.
 *
 * @package    block_recommended_courses
 * @copyright  2025 Moodle Developer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class main implements renderable, templatable {
    /**
     * @var array List of courses to display in the slider.
     */
    protected $courses;

    /**
     * @var string Enrollment button text.
     */
    protected $buttontext;

    /**
     * @var string Message shown when no courses exist.
     */
    protected $nocoursesmessage;

    /**
     * @var array Display options.
     */
    protected $displayoptions;

    /**
     * Constructor.
     *
     * @param array $courses List of courses for the slider.
     * @param string $buttontext Text for the enrollment button.
     * @param array $displayoptions Display options for the slider.
     */
    public function __construct($courses, $buttontext = null, $displayoptions = []) {
        $this->courses = $courses;

        // Merge provided display options with defaults.
        $this->displayoptions = array_merge([
            'layout_mode' => 'vertical',
            'image_fit' => 'cover',
            'image_height' => '200',
            'border_radius' => '8',
            'animation_speed' => '300',
            'autoslide' => '0',
            'show_cards' => 1,
            'show_button' => 1,
            'show_category' => 1,
            'show_contact' => 1,
            'show_contact_picture' => 1,
            'show_lastmodified' => 1,
        ], $displayoptions);

        // Use the default language string if no custom button text is supplied.
        if ($buttontext === null) {
            $this->buttontext = get_string('enrollbutton', 'block_recommended_courses');
        } else {
            $this->buttontext = $buttontext;
        }

        // Message shown when there are no courses to render.
        $this->nocoursesmessage = get_string('no_courses_to_display', 'block_recommended_courses');
    }

    /**
     * Export data for the mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();

        // Generate a unique identifier for this block instance.
        $data->uniqid = uniqid();

        // Determine if courses exist.
        $data->hascourses = !empty($this->courses);
        $data->courses = [];
        $data->buttontext = $this->buttontext;
        $data->no_courses_message = $this->nocoursesmessage;
        $data->prev_course = get_string('previous_course', 'block_recommended_courses');
        $data->next_course = get_string('next_course', 'block_recommended_courses');

        // Pass display options to the template.
        $data->layout_mode = $this->displayoptions['layout_mode'];
        $data->image_fit = $this->displayoptions['image_fit'];
        $data->image_height = $this->displayoptions['image_height'];
        $data->border_radius = $this->displayoptions['border_radius'];
        $data->animation_speed = $this->displayoptions['animation_speed'];
        $data->autoslide = $this->displayoptions['autoslide'];
        $data->show_cards = $this->displayoptions['show_cards'];
        $data->show_button = $this->displayoptions['show_button'];
        $data->show_category = $this->displayoptions['show_category'];
        $data->show_contact = $this->displayoptions['show_contact'];
        $data->show_contact_picture = $this->displayoptions['show_contact_picture'];
        $data->show_lastmodified = $this->displayoptions['show_lastmodified'];

        // Return early with empty JSON when no courses are available.
        if (!$data->hascourses) {
            $data->coursesJson = json_encode([]);
            return $data;
        }

        // Otherwise prepare each course for the template.
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

            // Contact information.
            if (isset($course['contact']) && !empty($course['contact'])) {
                $coursedata->has_contact = true;
                $coursedata->contact_name = $course['contact']['name'];
                $coursedata->contact_pictureurl = $course['contact']['pictureurl'];
                $coursedata->contact_profileurl = $course['contact']['profileurl'];
            } else {
                $coursedata->has_contact = false;
            }

            // Last updated date.
            $coursedata->lastmodified = isset($course['lastmodified']) ? $course['lastmodified'] : '';

            // First course is rendered as the hero entry.
            $coursedata->first = $first;

            // Limit to 4 visible entries (1 hero + 3 cards).
            $coursedata->visible = $visiblecount < 4;

            $data->courses[] = $coursedata;

            if ($first) {
                $first = false;
            }
            $visiblecount++;
        }

        // Provide JSON payload for front-end scripts.
        $data->coursesJson = json_encode($this->courses);

        return $data;
    }
}
