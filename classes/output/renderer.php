<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or.
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of.
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License.
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Renderer for the empfohlene_kurse block.
 *
 * @package    block_recommended_courses
 * @copyright  2025 Moodle Developer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_recommended_courses\output;


use plugin_renderer_base;

/**
 * Renderer for the empfohlene_kurse block.
 *
 * @package    block_recommended_courses
 * @copyright  2025 Moodle Developer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {
    /**
     * Render the main content of the block.
     *
     * @param main $main The main renderable
     * @return string HTML string
     */
    public function render_main(main $main) {
        return $this->render_from_template('block_recommended_courses/main', $main->export_for_template($this));
    }
}
