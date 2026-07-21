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
 * Upgrade script for block_recommended_courses.
 *
 * @package    block_recommended_courses
 * @copyright  2025 Alexander Noack
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the block_recommended_courses plugin.
 *
 * @param int $oldversion The version we are upgrading from.
 * @return bool
 */
function xmldb_block_recommended_courses_upgrade($oldversion) {
    global $DB;

    if ($oldversion < 2026072101) {
        $instances = $DB->get_records('block_instances', ['blockname' => 'recommended_courses']);
        foreach ($instances as $instance) {
            if (empty($instance->configdata)) {
                continue;
            }

            $config = unserialize(base64_decode($instance->configdata));
            if (!is_object($config) || !property_exists($config, 'button_text')) {
                continue;
            }

            unset($config->button_text);
            $DB->set_field(
                'block_instances',
                'configdata',
                base64_encode(serialize($config)),
                ['id' => $instance->id]
            );
        }

        upgrade_block_savepoint(true, 2026072101, 'recommended_courses');
    }

    return true;
}
