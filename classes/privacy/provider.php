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
 * Privacy API implementation for the recommended_courses block.
 *
 * @package    block_recommended_courses
 * @copyright  2025 Alexander Noack
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_recommended_courses\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\writer;

/**
 * Privacy API implementation for the recommended_courses block.
 *
 * @package    block_recommended_courses
 * @copyright  2025 Alexander Noack
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\user_preference_provider {
    /**
     * Returns metadata about stored user data.
     *
     * @param collection $collection
     * @return collection
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_user_preference(
            'block_recommended_courses_unenrolledonly',
            'privacy:metadata:preference:unenrolledonly'
        );
        return $collection;
    }

    /**
     * Export user preferences owned by this plugin.
     *
     * @param int $userid
     */
    public static function export_user_preferences(int $userid): void {
        $value = get_user_preferences('block_recommended_courses_unenrolledonly', null, $userid);
        if ($value === null) {
            return;
        }

        writer::export_user_preference(
            'block_recommended_courses',
            'block_recommended_courses_unenrolledonly',
            $value,
            get_string('privacy:metadata:preference:unenrolledonly', 'block_recommended_courses')
        );
    }
}
