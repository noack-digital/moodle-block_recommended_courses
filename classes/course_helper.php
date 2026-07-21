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
 * Course loading helpers for the Recommended Courses block.
 *
 * @package    block_recommended_courses
 * @copyright  2025 Alexander Noack
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_recommended_courses;

defined('MOODLE_INTERNAL') || die();

/**
 * Helpers for selecting and formatting recommended courses.
 *
 * @package    block_recommended_courses
 * @copyright  2025 Alexander Noack
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_helper {

    /**
     * Allowed layout modes.
     */
    public const LAYOUT_MODES = ['vertical', 'horizontal', 'card', 'minimal'];

    /**
     * Allowed image fit modes.
     */
    public const IMAGE_FITS = ['cover', 'contain', 'fill'];

    /**
     * Allowed image heights (px).
     */
    public const IMAGE_HEIGHTS = ['150', '200', '250', '300', '350'];

    /**
     * Allowed border radii (px).
     */
    public const BORDER_RADII = ['0', '4', '8', '12'];

    /**
     * Allowed animation speeds (ms).
     */
    public const ANIMATION_SPEEDS = ['0', '200', '300', '500'];

    /**
     * Allowed autoslide intervals (ms).
     */
    public const AUTOSLIDE_INTERVALS = ['0', '3000', '5000', '7000', '10000'];

    /**
     * Allowed title alignments.
     */
    public const TITLE_ALIGNMENTS = ['left', 'center', 'right'];

    /**
     * Normalize configured course IDs, preserving order and uniqueness.
     *
     * @param mixed $configcourses Array or CSV string of course IDs.
     * @return int[]
     */
    public static function normalize_course_ids($configcourses): array {
        if (is_string($configcourses)) {
            $configcourses = preg_split('/\s*,\s*/', $configcourses, -1, PREG_SPLIT_NO_EMPTY);
        }
        if (!is_array($configcourses)) {
            return [];
        }

        $ids = [];
        foreach ($configcourses as $courseid) {
            $courseid = (int) $courseid;
            if ($courseid > 0 && $courseid != SITEID && !in_array($courseid, $ids, true)) {
                $ids[] = $courseid;
            }
        }
        return $ids;
    }

    /**
     * Sanitize display options against allowlists.
     *
     * @param array $options Raw options.
     * @return array
     */
    public static function sanitize_display_options(array $options): array {
        $defaults = [
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
        ];

        $sanitized = $defaults;
        foreach ($defaults as $key => $default) {
            if (!array_key_exists($key, $options)) {
                continue;
            }
            $value = $options[$key];
            switch ($key) {
                case 'layout_mode':
                    $sanitized[$key] = in_array($value, self::LAYOUT_MODES, true) ? $value : $default;
                    break;
                case 'image_fit':
                    $sanitized[$key] = in_array($value, self::IMAGE_FITS, true) ? $value : $default;
                    break;
                case 'image_height':
                    $sanitized[$key] = in_array((string) $value, self::IMAGE_HEIGHTS, true) ? (string) $value : $default;
                    break;
                case 'border_radius':
                    $sanitized[$key] = in_array((string) $value, self::BORDER_RADII, true) ? (string) $value : $default;
                    break;
                case 'animation_speed':
                    $sanitized[$key] = in_array((string) $value, self::ANIMATION_SPEEDS, true) ? (string) $value : $default;
                    break;
                case 'autoslide':
                    $sanitized[$key] = in_array((string) $value, self::AUTOSLIDE_INTERVALS, true) ? (string) $value : $default;
                    break;
                default:
                    $sanitized[$key] = empty($value) ? 0 : 1;
                    break;
            }
        }

        return $sanitized;
    }

    /**
     * Sanitize title alignment value.
     *
     * @param string|null $alignment Raw alignment.
     * @return string
     */
    public static function sanitize_title_alignment(?string $alignment): string {
        if ($alignment !== null && in_array($alignment, self::TITLE_ALIGNMENTS, true)) {
            return $alignment;
        }
        return 'left';
    }

    /**
     * Load recommended courses for a user in the configured order.
     *
     * @param int[] $courseids Configured course IDs in display order.
     * @param int $userid User ID.
     * @param \moodle_page $page Current page (for user pictures).
     * @param bool $includecontact Whether to resolve course contacts.
     * @return array
     */
    public static function get_recommended_courses(array $courseids, int $userid, \moodle_page $page,
            bool $includecontact = true): array {
        global $DB;

        $courseids = self::normalize_course_ids($courseids);
        if (empty($courseids)) {
            return [];
        }

        [$insql, $params] = $DB->get_in_or_equal($courseids, SQL_PARAMS_NAMED);
        $params['userid'] = $userid;
        $params['uestatus'] = ENROL_USER_ACTIVE;
        $params['estatus'] = ENROL_INSTANCE_ENABLED;

        $sql = "SELECT c.*
                  FROM {course} c
                 WHERE c.id $insql
                   AND c.id NOT IN (
                        SELECT e.courseid
                          FROM {enrol} e
                          JOIN {user_enrolments} ue ON ue.enrolid = e.id
                         WHERE ue.userid = :userid
                           AND ue.status = :uestatus
                           AND e.status = :estatus
                   )";

        $records = $DB->get_records_sql($sql, $params);
        if (empty($records)) {
            return [];
        }

        // Preserve admin-configured order.
        $ordered = [];
        foreach ($courseids as $courseid) {
            if (isset($records[$courseid])) {
                $ordered[$courseid] = $records[$courseid];
            }
        }

        $recommended = [];
        foreach ($ordered as $course) {
            if (!\core_course_category::can_view_course_info($course, $userid)) {
                continue;
            }
            $recommended[] = self::format_course_for_display($course, $page, $includecontact);
        }

        return $recommended;
    }

    /**
     * Format a course record for the slider template / JSON payload.
     *
     * @param \stdClass $course Course record.
     * @param \moodle_page $page Current page.
     * @param bool $includecontact Whether to resolve contacts.
     * @return array
     */
    public static function format_course_for_display(\stdClass $course, \moodle_page $page,
            bool $includecontact = true): array {
        global $OUTPUT;

        $courseid = (int) $course->id;
        $context = \context_course::instance($courseid);
        $courseobj = new \core_course_list_element($course);

        $courseimage = null;
        if (class_exists('\core_course\external\course_summary_exporter')) {
            try {
                $courseimage = \core_course\external\course_summary_exporter::get_course_image($courseobj);
            } catch (\Throwable $e) {
                $courseimage = null;
            }
        }
        if (!$courseimage) {
            $courseimage = $OUTPUT->get_generated_image_for_id($courseid);
        }

        $summaryformat = isset($course->summaryformat) ? $course->summaryformat : FORMAT_HTML;
        $coursesummary = content_to_text($course->summary, $summaryformat);

        $category = \core_course_category::get($course->category, IGNORE_MISSING);
        $categoryname = $category ? $category->get_formatted_name() : '';

        $contact = null;
        if ($includecontact) {
            $contact = self::get_course_contact($courseobj, $page);
        }

        $lastmodified = '';
        if (!empty($course->timemodified)) {
            $lastmodified = userdate($course->timemodified, get_string('strftimedatefullshort', 'langconfig'));
        }

        return [
            'id' => $courseid,
            'fullname' => format_string($course->fullname, true, ['context' => $context]),
            'shortname' => format_string($course->shortname, true, ['context' => $context]),
            'summary' => $coursesummary,
            'category' => $categoryname,
            'courseimage' => $courseimage,
            'viewurl' => (new \moodle_url('/course/view.php', ['id' => $courseid]))->out(false),
            'enrollurl' => (new \moodle_url('/enrol/index.php', ['id' => $courseid]))->out(false),
            'contact' => $contact,
            'lastmodified' => $lastmodified,
        ];
    }

    /**
     * Resolve the primary course contact using site course-contact roles.
     *
     * @param \core_course_list_element $courseobj Course list element.
     * @param \moodle_page $page Current page.
     * @return array|null
     */
    public static function get_course_contact(\core_course_list_element $courseobj, \moodle_page $page): ?array {
        global $DB;

        $contacts = $courseobj->get_course_contacts();
        if (empty($contacts)) {
            return null;
        }

        $first = reset($contacts);
        $userid = (int) $first['user']->id;
        $user = $DB->get_record('user', ['id' => $userid, 'deleted' => 0, 'suspended' => 0], '*', IGNORE_MISSING);
        if (!$user) {
            return null;
        }

        $userpicture = new \user_picture($user);
        $userpicture->size = 50;

        return [
            'name' => $first['username'],
            'pictureurl' => $userpicture->get_url($page)->out(false),
            'profileurl' => (new \moodle_url('/user/profile.php', ['id' => $user->id]))->out(false),
        ];
    }

    /**
     * Encode courses for safe embedding in a script context.
     *
     * @param array $courses Courses payload.
     * @return string
     */
    public static function encode_courses_json(array $courses): string {
        return json_encode(
            $courses,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
        );
    }
}
