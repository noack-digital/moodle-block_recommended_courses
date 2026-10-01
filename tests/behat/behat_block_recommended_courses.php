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
 * Behat steps for block_recommended_courses.
 *
 * @package    block_recommended_courses
 * @category   test
 * @copyright  2025 Alexander Noack
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../../lib/behat/behat_base.php');

/**
 * Custom Behat steps for configuring and using the Recommended Courses block.
 *
 * @package    block_recommended_courses
 * @category   test
 * @copyright  2025 Alexander Noack
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_block_recommended_courses extends behat_base {
    /**
     * Configure the Recommended Courses block with the given course shortnames.
     *
     * @Given /^the Recommended Courses block is configured with courses "(?P<shortnames>(?:[^"]|\\")*)"$/
     * @param string $shortnames Comma-separated course shortnames.
     */
    public function the_recommended_courses_block_is_configured_with_courses(string $shortnames): void {
        global $DB;

        $names = preg_split('/\s*,\s*/', trim($shortnames), -1, PREG_SPLIT_NO_EMPTY);
        if (empty($names)) {
            throw new Exception('No course shortnames provided for Recommended Courses block configuration.');
        }

        $courseids = [];
        foreach ($names as $shortname) {
            $course = $DB->get_record('course', ['shortname' => $shortname], 'id', MUST_EXIST);
            $courseids[] = (int) $course->id;
        }

        $instances = $DB->get_records('block_instances', ['blockname' => 'recommended_courses']);
        if (empty($instances)) {
            throw new Exception('No Recommended Courses block instance found.');
        }

        foreach ($instances as $instance) {
            $config = empty($instance->configdata)
                ? new stdClass()
                : unserialize_object(base64_decode($instance->configdata));
            $config->courses = $courseids;
            $instance->configdata = base64_encode(serialize($config));
            $DB->update_record('block_instances', $instance);
        }
    }

    /**
     * Toggle the enrolment filter checkbox and wait for the page reload.
     *
     * @When /^I toggle the recommended courses enrolment filter$/
     */
    public function i_toggle_the_recommended_courses_enrolment_filter(): void {
        $xpath = "//input[contains(concat(' ', normalize-space(@class), ' '), ' enrolment-filter-input ')]";
        $this->execute('behat_general::i_click_on', [$xpath, 'xpath_element']);
        $this->getSession()->wait(self::get_timeout() * 1000, behat_base::PAGE_READY_JS);
    }
}
