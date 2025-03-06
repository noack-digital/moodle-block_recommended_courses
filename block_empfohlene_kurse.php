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
 * Contains the class for the "Empfohlene Kurse" block.
 *
 * @package    block_empfohlene_kurse
 * @copyright  2025 Moodle Developer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Empfohlene Kurse block class.
 *
 * @package    block_empfohlene_kurse
 * @copyright  2025 Moodle Developer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_empfohlene_kurse extends block_base {

    /**
     * Init.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_empfohlene_kurse');
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

        // Die Kurse für den Slider holen.
        $courses = $this->get_recommended_courses();

        // Renderable erstellen.
        $renderable = new \block_empfohlene_kurse\output\main($courses);
        $renderer = $this->page->get_renderer('block_empfohlene_kurse');

        $this->content = new stdClass();
        $this->content->text = $renderer->render($renderable);
        $this->content->footer = '';

        return $this->content;
    }

    /**
     * Gibt die vom Admin ausgewählten Kurse zurück, in die der Benutzer noch nicht eingeschrieben ist.
     *
     * @return array Array mit Kursinformationen
     */
    private function get_recommended_courses() {
        global $DB, $USER;

        // Die vom Admin ausgewählten Kurse aus den Einstellungen holen
        $configcourses = isset($this->config->courses) ? $this->config->courses : [];
        
        if (empty($configcourses)) {
            return [];
        }

        // Kurse laden, in die der Benutzer eingeschrieben ist
        $sql = "SELECT c.id FROM {course} c
                JOIN {user_enrolments} ue ON ue.enrolid = e.id
                JOIN {enrol} e ON e.courseid = c.id
                WHERE ue.userid = :userid";
        $enrolled = $DB->get_records_sql($sql, ['userid' => $USER->id]);
        $enrolledids = array_keys($enrolled);

        // Nur Kurse zurückgeben, in die der Benutzer noch nicht eingeschrieben ist
        $recommendedcourses = [];
        foreach ($configcourses as $courseid) {
            if (!in_array($courseid, $enrolledids)) {
                // Kursinformationen laden
                $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
                $courseobj = new \core_course_list_element($course);
                
                // Kursbild URL
                $courseimage = \core_course\external\course_summary_exporter::get_course_image($courseobj);
                if (!$courseimage) {
                    $courseimage = $OUTPUT->get_generated_image_for_id($courseid);
                }
                
                // Kursbeschreibung
                $coursesummary = strip_tags($courseobj->summary);
                
                $recommendedcourses[] = [
                    'id' => $courseid,
                    'fullname' => $course->fullname,
                    'shortname' => $course->shortname,
                    'summary' => $coursesummary,
                    'courseimage' => $courseimage,
                    'viewurl' => new \moodle_url('/course/view.php', ['id' => $courseid]),
                    'enrollurl' => new \moodle_url('/enrol/index.php', ['id' => $courseid])
                ];
            }
        }
        
        return $recommendedcourses;
    }

    /**
     * Locations where block can be displayed.
     *
     * @return array
     */
    public function applicable_formats() {
        return [
            'my' => true,
            'site' => true
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
                $this->title = get_string('pluginname', 'block_empfohlene_kurse');
            } else {
                $this->title = $this->config->title;
            }
        }
    }
}
