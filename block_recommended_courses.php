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

        // Die Kurse für den Slider holen.
        $courses = $this->get_recommended_courses();

        // Darstellungsoptionen aus der Konfiguration holen.
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
            'show_enrolled' => isset($this->config->show_enrolled) ? $this->config->show_enrolled : 0,
            'show_enrolled_badge' => isset($this->config->show_enrolled_badge) ? $this->config->show_enrolled_badge : 1,
        ];

        // Button-Text aus Konfiguration.
        $buttontext = isset($this->config->button_text) ? $this->config->button_text : null;

        // Renderable erstellen.
        $renderable = new \block_recommended_courses\output\main($courses, $buttontext, $displayoptions);
        $renderer = $this->page->get_renderer('block_recommended_courses');

        $this->content = new stdClass();
        $this->content->text = $renderer->render($renderable);
        $this->content->footer = '';

        return $this->content;
    }

    /**
     * Gibt die vom Admin ausgewählten Kurse zurück.
     * Optional werden auch Kurse angezeigt, in die der Benutzer bereits eingeschrieben ist.
     *
     * @return array Array mit Kursinformationen
     */
    private function get_recommended_courses() {
        global $DB, $USER, $OUTPUT;

        // Die vom Admin ausgewählten Kurse aus den Einstellungen holen.
        $configcourses = isset($this->config->courses) ? $this->config->courses : [];

        if (empty($configcourses)) {
            return [];
        }

        // Eingeschriebene Kurse anzeigen?
        $showenrolled = isset($this->config->show_enrolled) ? $this->config->show_enrolled : 0;

        // Kurse laden, in die der Benutzer eingeschrieben ist.
        $sql = "SELECT c.id FROM {course} c
                JOIN {enrol} e ON e.courseid = c.id
                JOIN {user_enrolments} ue ON ue.enrolid = e.id
                WHERE ue.userid = :userid";
        $enrolled = $DB->get_records_sql($sql, ['userid' => $USER->id]);
        $enrolledids = array_keys($enrolled);

        // Kurse zurückgeben (je nach Einstellung).
        $recommendedcourses = [];
        foreach ($configcourses as $courseid) {
            $isenrolled = in_array($courseid, $enrolledids);
            
            // Kurs überspringen, wenn bereits eingeschrieben und Option deaktiviert.
            if ($isenrolled && !$showenrolled) {
                continue;
            }
            
            // Kurs anzeigen.
                // Kursinformationen laden.
                $course = $DB->get_record('course', ['id' => $courseid], '*', IGNORE_MISSING);
                if (!$course) {
                    continue; // Kurs existiert nicht mehr, überspringen.
                }

                $courseobj = new \core_course_list_element($course);

                // Kursbild URL - robuste Implementierung für verschiedene Moodle-Versionen.
                $courseimage = null;
                if (class_exists('\core_course\external\course_summary_exporter')) {
                    try {
                        $courseimage = \core_course\external\course_summary_exporter::get_course_image($courseobj);
                    } catch (\Exception $e) {
                        // Fallback bei Fehler.
                        $courseimage = null;
                    }
                }

                if (!$courseimage) {
                    // Fallback: generiertes Bild verwenden.
                    $courseimage = $OUTPUT->get_generated_image_for_id($courseid);
                }

                // Kursbeschreibung.
                $coursesummary = strip_tags($courseobj->summary);

                // Kursbereich holen.
                $category = \core_course_category::get($course->category, IGNORE_MISSING);
                $categoryname = $category ? $category->get_formatted_name() : '';

                // Hauptansprechpartner (Course Contact) ermitteln.
                $contact = $this->get_course_contact($courseid);

                // Datum der letzten Bearbeitung mit führenden Nullen.
                $lastmodified = '';
                if ($course->timemodified > 0) {
                    // Format: 09.10.25 (mit führenden Nullen, Jahr zweistellig).
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
                    'isenrolled' => $isenrolled,
                ];
        }

        return $recommendedcourses;
    }

    /**
     * Ermittelt den Hauptansprechpartner eines Kurses (erster Kursleiter).
     *
     * @param int $courseid ID des Kurses
     * @return array|null Array mit Name, Profilbild-URL und Profil-URL oder null
     */
    private function get_course_contact($courseid) {
        global $DB, $OUTPUT;

        // Kontext des Kurses holen.
        $context = \context_course::instance($courseid);

        // Rollen definieren, die als Kursleiter gelten (editingteacher, teacher).
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

        // Ersten Benutzer mit Kursleiter-Rolle holen.
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

        // Profilbild-URL generieren.
        $userpicture = new \user_picture($teacher);
        $userpicture->size = 50; // Kleine Bildgröße (50x50px).
        $pictureurl = $userpicture->get_url($this->page)->out(false);

        // Vollständigen Namen generieren.
        $fullname = fullname($teacher);

        // Profil-URL.
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
