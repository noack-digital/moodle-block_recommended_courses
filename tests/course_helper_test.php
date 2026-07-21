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
 * Unit tests for course_helper.
 *
 * @package    block_recommended_courses
 * @category   test
 * @copyright  2025 Alexander Noack
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_recommended_courses;

defined('MOODLE_INTERNAL') || die();

/**
 * Unit tests for course_helper.
 *
 * @package    block_recommended_courses
 * @category   test
 * @copyright  2025 Alexander Noack
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class course_helper_test extends \advanced_testcase {

    /**
     * Test course ID normalization preserves order and uniqueness.
     */
    public function test_normalize_course_ids(): void {
        $this->resetAfterTest();

        $ids = course_helper::normalize_course_ids(['3', '2', '3', '1', SITEID, 0, '']);
        $this->assertSame([3, 2, 1], $ids);

        $ids = course_helper::normalize_course_ids('5, 4, 5');
        $this->assertSame([5, 4], $ids);

        $this->assertSame([], course_helper::normalize_course_ids(null));
    }

    /**
     * Test display option allowlists.
     */
    public function test_sanitize_display_options(): void {
        $options = course_helper::sanitize_display_options([
            'layout_mode' => 'evil',
            'image_fit' => 'contain',
            'image_height' => '999',
            'border_radius' => '8',
            'animation_speed' => '300',
            'autoslide' => '5000',
            'show_cards' => 0,
            'show_button' => '1',
        ]);

        $this->assertSame('vertical', $options['layout_mode']);
        $this->assertSame('contain', $options['image_fit']);
        $this->assertSame('200', $options['image_height']);
        $this->assertSame('8', $options['border_radius']);
        $this->assertSame('5000', $options['autoslide']);
        $this->assertSame(0, $options['show_cards']);
        $this->assertSame(1, $options['show_button']);
    }

    /**
     * Test JSON encoding escapes script breakout characters.
     */
    public function test_encode_courses_json_escapes_tags(): void {
        $json = course_helper::encode_courses_json([
            ['fullname' => '</script><script>alert(1)</script>'],
        ]);
        $this->assertStringNotContainsString('</script>', $json);
        $this->assertStringContainsString('\u003C', $json);
    }

    /**
     * Hidden courses must not appear for regular users.
     */
    public function test_hidden_courses_are_excluded(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        $student = $generator->create_user();
        $visible = $generator->create_course(['visible' => 1]);
        $hidden = $generator->create_course(['visible' => 0]);

        $this->setUser($student);
        $page = new \moodle_page();
        $page->set_context(\context_system::instance());
        $page->set_url(new \moodle_url('/'));

        $courses = course_helper::get_recommended_courses(
            [$hidden->id, $visible->id],
            (int) $student->id,
            $page,
            false
        );

        $this->assertCount(1, $courses);
        $this->assertSame((int) $visible->id, $courses[0]['id']);
    }

    /**
     * Actively enrolled courses must not be recommended.
     */
    public function test_enrolled_courses_are_excluded(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        $student = $generator->create_user();
        $enrolled = $generator->create_course();
        $other = $generator->create_course();
        $generator->enrol_user($student->id, $enrolled->id);

        $this->setUser($student);
        $page = new \moodle_page();
        $page->set_context(\context_system::instance());
        $page->set_url(new \moodle_url('/'));

        $courses = course_helper::get_recommended_courses(
            [$enrolled->id, $other->id],
            (int) $student->id,
            $page,
            false
        );

        $this->assertCount(1, $courses);
        $this->assertSame((int) $other->id, $courses[0]['id']);
    }

    /**
     * Configured course order must be preserved.
     */
    public function test_course_order_is_preserved(): void {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();

        $student = $generator->create_user();
        $coursea = $generator->create_course(['fullname' => 'AAA']);
        $courseb = $generator->create_course(['fullname' => 'BBB']);
        $coursec = $generator->create_course(['fullname' => 'CCC']);

        $this->setUser($student);
        $page = new \moodle_page();
        $page->set_context(\context_system::instance());
        $page->set_url(new \moodle_url('/'));

        $courses = course_helper::get_recommended_courses(
            [$coursec->id, $coursea->id, $courseb->id],
            (int) $student->id,
            $page,
            false
        );

        $this->assertSame(
            [(int) $coursec->id, (int) $coursea->id, (int) $courseb->id],
            array_column($courses, 'id')
        );
    }
}
