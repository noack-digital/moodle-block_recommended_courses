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
 * Library callbacks for block_recommended_courses.
 *
 * @package    block_recommended_courses
 * @copyright  2025 Alexander Noack
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die(); // phpcs:ignore moodle.Files.MoodleInternal.MoodleInternalNotNeeded

/**
 * Declare user preferences used by this plugin.
 *
 * @return array
 */
function block_recommended_courses_user_preferences(): array {
    $preferences = [];
    $preferences[\block_recommended_courses\local\preference::UNENROLLED_ONLY] = [
        'type' => PARAM_INT,
        'null' => NULL_NOT_ALLOWED,
        'default' => 1,
        'choices' => [0, 1],
    ];
    return $preferences;
}

/**
 * Whether the current user preference requests unenrolled-only filtering.
 *
 * @return bool
 */
function block_recommended_courses_is_unenrolled_only(): bool {
    if (!isloggedin() || isguestuser()) {
        return true;
    }
    $prefname = \block_recommended_courses\local\preference::UNENROLLED_ONLY;
    return (int) get_user_preferences($prefname, 1) === 1;
}
