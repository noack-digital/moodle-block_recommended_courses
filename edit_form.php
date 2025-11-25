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
 * Form for editing recommended_courses block instances.
 *
 * @package    block_recommended_courses
 * @copyright  2025 Moodle Developer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Form for editing block instance configuration.
 */
class block_recommended_courses_edit_form extends block_edit_form {
    /**
     * Form definition.
     *
     * @param moodleform $mform
     * @return void
     */
    protected function specific_definition($mform) {
        // Header for block title configuration.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // Block title.
        $mform->addElement('text', 'config_title', get_string('config_title', 'block_recommended_courses'));
        $mform->setDefault('config_title', get_string('config_title_default', 'block_recommended_courses'));
        $mform->setType('config_title', PARAM_TEXT);

        // Title alignment.
        $alignmentoptions = [
            'left' => get_string('alignment_left', 'block_recommended_courses'),
            'center' => get_string('alignment_center', 'block_recommended_courses'),
            'right' => get_string('alignment_right', 'block_recommended_courses'),
        ];
        $mform->addElement(
            'select',
            'config_title_alignment',
            get_string('config_title_alignment', 'block_recommended_courses'),
            $alignmentoptions
        );
        $mform->setDefault('config_title_alignment', 'left');

        // Custom enrollment button text.
        $mform->addElement(
            'text',
            'config_button_text',
            get_string('config_button_text', 'block_recommended_courses')
        );
        $mform->setDefault('config_button_text', get_string('enrollbutton', 'block_recommended_courses'));
        $mform->setType('config_button_text', PARAM_TEXT);

        // Display options section.
        $mform->addElement('header', 'displayheader', get_string('display_settings', 'block_recommended_courses'));

        // Layout mode.
        $layoutmodes = [
            'vertical' => get_string('layout_vertical', 'block_recommended_courses'),
            'horizontal' => get_string('layout_horizontal', 'block_recommended_courses'),
            'card' => get_string('layout_card', 'block_recommended_courses'),
            'minimal' => get_string('layout_minimal', 'block_recommended_courses'),
        ];
        $mform->addElement(
            'select',
            'config_layout_mode',
            get_string('config_layout_mode', 'block_recommended_courses'),
            $layoutmodes
        );
        $mform->setDefault('config_layout_mode', 'vertical');
        $mform->addHelpButton('config_layout_mode', 'config_layout_mode', 'block_recommended_courses');

        // Image fit configuration.
        $imagefitmodes = [
            'cover' => get_string('imagefit_cover', 'block_recommended_courses'),
            'contain' => get_string('imagefit_contain', 'block_recommended_courses'),
            'fill' => get_string('imagefit_fill', 'block_recommended_courses'),
        ];
        $mform->addElement(
            'select',
            'config_image_fit',
            get_string('config_image_fit', 'block_recommended_courses'),
            $imagefitmodes
        );
        $mform->setDefault('config_image_fit', 'cover');
        $mform->addHelpButton('config_image_fit', 'config_image_fit', 'block_recommended_courses');

        // Image height selection.
        $imageheights = [
            '150' => get_string('config_image_height_150', 'block_recommended_courses'),
            '200' => get_string('config_image_height_200', 'block_recommended_courses'),
            '250' => get_string('config_image_height_250', 'block_recommended_courses'),
            '300' => get_string('config_image_height_300', 'block_recommended_courses'),
            '350' => get_string('config_image_height_350', 'block_recommended_courses'),
        ];
        $mform->addElement(
            'select',
            'config_image_height',
            get_string('config_image_height', 'block_recommended_courses'),
            $imageheights
        );
        $mform->setDefault('config_image_height', '200');

        // Border radius options.
        $borderradius = [
            '0' => get_string('border_radius_none', 'block_recommended_courses'),
            '4' => get_string('border_radius_small', 'block_recommended_courses'),
            '8' => get_string('border_radius_medium', 'block_recommended_courses'),
            '12' => get_string('border_radius_large', 'block_recommended_courses'),
        ];
        $mform->addElement(
            'select',
            'config_border_radius',
            get_string('config_border_radius', 'block_recommended_courses'),
            $borderradius
        );
        $mform->setDefault('config_border_radius', '8');

        // Animation speed options.
        $animationspeeds = [
            '0' => get_string('animation_none', 'block_recommended_courses'),
            '200' => get_string('animation_fast', 'block_recommended_courses'),
            '300' => get_string('animation_normal', 'block_recommended_courses'),
            '500' => get_string('animation_slow', 'block_recommended_courses'),
        ];
        $mform->addElement(
            'select',
            'config_animation_speed',
            get_string('config_animation_speed', 'block_recommended_courses'),
            $animationspeeds
        );
        $mform->setDefault('config_animation_speed', '300');

        // Auto slide interval.
        $autoslideoptions = [
            '0' => get_string('autoslide_off', 'block_recommended_courses'),
            '3000' => '3 ' . get_string('seconds', 'block_recommended_courses'),
            '5000' => '5 ' . get_string('seconds', 'block_recommended_courses'),
            '7000' => '7 ' . get_string('seconds', 'block_recommended_courses'),
            '10000' => '10 ' . get_string('seconds', 'block_recommended_courses'),
        ];
        $mform->addElement(
            'select',
            'config_autoslide',
            get_string('config_autoslide', 'block_recommended_courses'),
            $autoslideoptions
        );
        $mform->setDefault('config_autoslide', '0');
        $mform->addHelpButton('config_autoslide', 'config_autoslide', 'block_recommended_courses');

        // Toggle course cards.
        $mform->addElement(
            'advcheckbox',
            'config_show_cards',
            get_string('config_show_cards', 'block_recommended_courses')
        );
        $mform->setDefault('config_show_cards', 1);

        // Toggle button visibility.
        $mform->addElement(
            'advcheckbox',
            'config_show_button',
            get_string('config_show_button', 'block_recommended_courses')
        );
        $mform->setDefault('config_show_button', 1);

        // Course information section.
        $mform->addElement('header', 'courseinfoheader', get_string('course_info_settings', 'block_recommended_courses'));

        // Toggle course category display.
        $mform->addElement(
            'advcheckbox',
            'config_show_category',
            get_string('config_show_category', 'block_recommended_courses')
        );
        $mform->setDefault('config_show_category', 1);
        $mform->addHelpButton('config_show_category', 'config_show_category', 'block_recommended_courses');

        // Toggle contact information.
        $mform->addElement(
            'advcheckbox',
            'config_show_contact',
            get_string('config_show_contact', 'block_recommended_courses')
        );
        $mform->setDefault('config_show_contact', 1);
        $mform->addHelpButton('config_show_contact', 'config_show_contact', 'block_recommended_courses');

        // Toggle contact profile image.
        $mform->addElement(
            'advcheckbox',
            'config_show_contact_picture',
            get_string('config_show_contact_picture', 'block_recommended_courses')
        );
        $mform->setDefault('config_show_contact_picture', 1);
        $mform->disabledIf('config_show_contact_picture', 'config_show_contact');

        // Toggle last modified date.
        $mform->addElement(
            'advcheckbox',
            'config_show_lastmodified',
            get_string('config_show_lastmodified', 'block_recommended_courses')
        );
        $mform->setDefault('config_show_lastmodified', 1);
        $mform->addHelpButton('config_show_lastmodified', 'config_show_lastmodified', 'block_recommended_courses');

        // Course selection section.
        $mform->addElement('header', 'courseselectionheader', get_string('select_courses', 'block_recommended_courses'));

        // Build the available course list.
        $courseslist = [];

        $courses = get_courses('all', 'c.sortorder ASC', 'c.id, c.fullname, c.visible');
        foreach ($courses as $course) {
            if ($course->id == SITEID) {
                continue;
            }
            if ($course->visible) {
                $courseslist[$course->id] = $course->fullname;
            }
        }

        $options = [
            'multiple' => true,
            'noselectionstring' => get_string('search_courses', 'block_recommended_courses'),
        ];
        $mform->addElement(
            'autocomplete',
            'config_courses',
            get_string('select_courses', 'block_recommended_courses'),
            $courseslist,
            $options
        );
    }
}
