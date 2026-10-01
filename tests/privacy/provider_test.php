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
 * Privacy provider tests.
 *
 * @package    block_recommended_courses
 * @category   test
 * @copyright  2025 Alexander Noack
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_recommended_courses\privacy;

/**
 * Privacy provider tests.
 *
 * @package    block_recommended_courses
 * @category   test
 * @covers     \block_recommended_courses\privacy\provider
 * @copyright  2025 Alexander Noack
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider_test extends \core_privacy\tests\provider_testcase {
    /**
     * Metadata must declare the enrolment filter preference.
     */
    public function test_get_metadata(): void {
        $collection = new \core_privacy\local\metadata\collection('block_recommended_courses');
        $metadata = provider::get_metadata($collection);
        $this->assertNotEmpty($metadata);
        $this->assertNotEmpty(get_string('privacy:metadata:preference:unenrolledonly', 'block_recommended_courses'));
    }

    /**
     * Export preference when set.
     */
    public function test_export_user_preferences(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        set_user_preference('block_recommended_courses_unenrolledonly', 0, $user);
        provider::export_user_preferences($user->id);

        $writer = \core_privacy\local\request\writer::with_context(\context_user::instance($user->id));
        $this->assertTrue($writer->has_any_data());
    }
}
