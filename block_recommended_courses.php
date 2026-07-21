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
 * Contains the class for the Recommended Courses block.
 *
 * @package    block_recommended_courses
 * @copyright  2025 Alexander Noack
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Recommended Courses block class.
 *
 * @package    block_recommended_courses
 * @copyright  2025 Alexander Noack
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
        global $USER;

        if (isset($this->content)) {
            return $this->content;
        }

        $displayoptions = $this->get_display_options();
        $includecontact = !empty($displayoptions['show_contact']);
        $courses = \block_recommended_courses\course_helper::get_recommended_courses(
            isset($this->config->courses) ? $this->config->courses : [],
            (int) $USER->id,
            $this->page,
            $includecontact
        );

        $buttontext = null;
        if (isset($this->config->button_text) && $this->config->button_text !== '') {
            $buttontext = format_string($this->config->button_text, true, ['context' => $this->context]);
        }

        $renderable = new \block_recommended_courses\output\main($courses, $buttontext, $displayoptions);
        $renderer = $this->page->get_renderer('block_recommended_courses');

        $this->content = new stdClass();
        $this->content->text = $renderer->render($renderable);
        $this->content->footer = '';

        return $this->content;
    }

    /**
     * Get sanitized display options from config.
     *
     * @return array
     */
    private function get_display_options() {
        $raw = [];
        $keys = [
            'layout_mode', 'image_fit', 'image_height', 'border_radius', 'animation_speed', 'autoslide',
            'show_cards', 'show_button', 'show_category', 'show_contact', 'show_contact_picture', 'show_lastmodified',
        ];
        foreach ($keys as $key) {
            if (isset($this->config->$key)) {
                $raw[$key] = $this->config->$key;
            }
        }
        return \block_recommended_courses\course_helper::sanitize_display_options($raw);
    }

    /**
     * HTML attributes for this block instance (title alignment class).
     *
     * @return array
     */
    public function html_attributes() {
        $attributes = parent::html_attributes();
        $alignment = 'left';
        if (isset($this->config->title_alignment)) {
            $alignment = \block_recommended_courses\course_helper::sanitize_title_alignment(
                (string) $this->config->title_alignment
            );
        }
        $attributes['class'] .= ' title-' . $alignment;
        return $attributes;
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
     * No global plugin settings page (instance config only).
     *
     * @return boolean
     */
    public function has_config() {
        return false;
    }

    /**
     * Allow instance configuration.
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
     * Set block title from instance config.
     */
    public function specialization() {
        if (isset($this->config) && !empty($this->config->title)) {
            $this->title = format_string($this->config->title, true, ['context' => $this->context]);
        } else {
            $this->title = get_string('pluginname', 'block_recommended_courses');
        }
    }
}
