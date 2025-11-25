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
 * Contains the class for the "Recommended Courses" block.
 *
 * @package    block_recommended_courses
 * @copyright  2025 Moodle Developer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Recommended Courses block class.
 *
 * @package    block_recommended_courses
 * @copyright  2025 Moodle Developer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_recommended_courses extends block_base {
    /**
     * Init.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_recommended_courses');
    }

    /**
     * Returns the contents.
     *
     * @return stdClass contents of block
     */
    public function get_content() {
        global $DB, $USER, $OUTPUT;

        if (isset($this->content)) {
            return $this->content;
        }

        // Retrieve courses for the slider.
        $courses = $this->get_recommended_courses();

        // Collect display options from instance configuration.
        $displayoptions = [
            'layout_mode' => isset($this->config->layout_mode) ? $this->config->layout_mode : 'vertical',
            'image_fit' => isset($this->config->image_fit) ? $this->config->image_fit : 'cover',
            'image_height' => isset($this->config->image_height) ? $this->config->image_height : '200',
            'border_radius' => isset($this->config->border_radius) ? $this->config->border_radius : '8',
            'animation_speed' => isset($this->config->animation_speed) ? $this->config->animation_speed : '300',
            'autoslide' => isset($this->config->autoslide) ? $this->config->autoslide : '0',
            'show_cards' => isset($this->config->show_cards) ? $this->config->show_cards : 1,
            'show_button' => isset($this->config->show_button) ? $this->config->show_button : 1,
            'show_category' => isset($this->config->show_category) ? $this->config->show_category : 1,
            'show_contact' => isset($this->config->show_contact) ? $this->config->show_contact : 1,
            'show_contact_picture' => isset($this->config->show_contact_picture) ? $this->config->show_contact_picture : 1,
            'show_lastmodified' => isset($this->config->show_lastmodified) ? $this->config->show_lastmodified : 1,
        ];

        // Use the configured button text if provided.
        $buttontext = isset($this->config->button_text) ? $this->config->button_text : null;

        // Render the template with the collected data.
        $renderable = new \block_recommended_courses\output\main($courses, $buttontext, $displayoptions);
        $renderer = $this->page->get_renderer('block_recommended_courses');

        $this->content = new stdClass();
        $this->content->text = $renderer->render($renderable);
        $this->content->footer = '';

        return $this->content;
    }

    /**
     * Returns admin-selected courses the current user is not enrolled in yet.
     *
     * @return array Array of course information hashes
     */
    private function get_recommended_courses() {
        global $DB, $USER, $OUTPUT;

        // Courses chosen by the administrator in the block settings.
        $configcourses = isset($this->config->courses) ? $this->config->courses : [];

        // Ensure configcourses is an array (Moodle sometimes stores autocomplete as CSV string).
        if (is_string($configcourses)) {
            $configcourses = explode(',', $configcourses);
        }

        if (empty($configcourses) || !is_array($configcourses)) {
            return [];
        }

        // Get safe IN query for course IDs.
        list($in_sql, $params) = $DB->get_in_or_equal($configcourses, SQL_PARAMS_NAMED);
        $params['userid'] = $USER->id;

        // Fetch configured courses where the user is NOT enrolled.
        $sql = "SELECT c.*
                FROM {course} c
                WHERE c.id $in_sql
                AND c.id NOT IN (
                    SELECT e.courseid
                    FROM {enrol} e
                    JOIN {user_enrolments} ue ON ue.enrolid = e.id
                    WHERE ue.userid = :userid
                )";

        $courses = $DB->get_records_sql($sql, $params);

        $recommendedcourses = [];
        foreach ($courses as $course) {
            $courseid = $course->id;

            $courseobj = new \core_course_list_element($course);

            // Resolve the course image URL and provide fallbacks.
            $courseimage = null;
            if (class_exists('\core_course\external\course_summary_exporter')) {
                try {
                    $courseimage = \core_course\external\course_summary_exporter::get_course_image($courseobj);
                } catch (\Exception $e) {
                    // Fall back if detection fails.
                    $courseimage = null;
                }
            }

            if (!$courseimage) {
                // Use a generated pattern image as final fallback.
                $courseimage = $OUTPUT->get_generated_image_for_id($courseid);
            }

            // Course summary text without HTML.
            $coursesummary = strip_tags($courseobj->summary);

            // Fetch the course category.
            $category = \core_course_category::get($course->category, IGNORE_MISSING);
            $categoryname = $category ? $category->get_formatted_name() : '';

            // Determine the main course contact (first teacher).
            $contact = $this->get_course_contact($courseid);

            // Format last modification date with leading zeros.
            $lastmodified = '';
            if ($course->timemodified > 0) {
                // Format: 09.10.25 (two digit year, leading zeros).
                $lastmodified = date('d.m.y', $course->timemodified);
            }

            $recommendedcourses[] = [
                'id' => $courseid,
                'fullname' => $course->fullname,
                'shortname' => $course->shortname,
                'summary' => $coursesummary,
                'category' => $categoryname,
                'courseimage' => $courseimage,
                'viewurl' => (new \moodle_url('/course/view.php', ['id' => $courseid]))->out(false),
                'enrollurl' => (new \moodle_url('/enrol/index.php', ['id' => $courseid]))->out(false),
                'contact' => $contact,
                'lastmodified' => $lastmodified,
            ];
        }

        return $recommendedcourses;
    }

    /**
     * Resolves the primary course contact (first teacher).
     *
     * @param int $courseid ID of the course
     * @return array|null Array containing name, picture URL and profile URL or null
     */
    private function get_course_contact($courseid) {
        global $DB, $OUTPUT;

        // Get the course context.
        $context = \context_course::instance($courseid);

        // Resolve roles that count as course contacts (editingteacher, teacher).
        $teacherroles = $DB->get_records_sql(
            "SELECT DISTINCT r.id
             FROM {role} r
             WHERE r.shortname IN ('editingteacher', 'teacher')
             ORDER BY r.sortorder ASC"
        );

        if (empty($teacherroles)) {
            return null;
        }

        $roleids = array_keys($teacherroles);
        [$insql, $params] = $DB->get_in_or_equal($roleids, SQL_PARAMS_NAMED);
        $params['contextid'] = $context->id;

        // Get the first user that holds one of the teacher roles.
        $sql = "SELECT DISTINCT u.id, u.firstname, u.lastname, u.email, u.picture, u.imagealt,
                       u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename
                FROM {role_assignments} ra
                JOIN {user} u ON u.id = ra.userid
                WHERE ra.contextid = :contextid AND ra.roleid $insql
                ORDER BY ra.id ASC";

        $teachers = $DB->get_records_sql($sql, $params, 0, 1);

        if (empty($teachers)) {
            return null;
        }

        $teacher = reset($teachers);

        // Build profile picture URL.
        $userpicture = new \user_picture($teacher);
        $userpicture->size = 50; // Small 50x50px headshot.
        $pictureurl = $userpicture->get_url($this->page)->out(false);

        // Resolve the localized full name.
        $fullname = fullname($teacher);

        // Link to the public profile page.
        $profileurl = (new \moodle_url('/user/profile.php', ['id' => $teacher->id]))->out(false);

        return [
            'name' => $fullname,
            'pictureurl' => $pictureurl,
            'profileurl' => $profileurl,
        ];
    }

    /**
     * Locations where block can be displayed.
     *
     * @return array
     */
    public function applicable_formats() {
        return [
            'my' => true,
            'site' => true,
        ];
    }

    /**
     * Allow the block to have a configuration page.
     *
     * @return boolean
     */
    public function has_config() {
        return true;
    }

    /**
     * Allow instance configuration
     *
     * @return boolean
     */
    public function instance_allow_config() {
        return true;
    }

    /**
     * Allow multiple instances of the block.
     *
     * @return boolean
     */
    public function instance_allow_multiple() {
        return true;
    }

    /**
     * Custom function to set defaults.
     *
     * @return boolean
     */
    public function specialization() {
        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('pluginname', 'block_recommended_courses');
            } else {
                $this->title = $this->config->title;
            }
        }
    }
}
