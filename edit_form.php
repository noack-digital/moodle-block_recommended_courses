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
 * Form for editing empfohlene_kurse block instances.
 *
 * @package    block_empfohlene_kurse
 * @copyright  2025 Moodle Developer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Form for editing block instance configuration.
 */
class block_empfohlene_kurse_edit_form extends block_edit_form {

    /**
     * Form definition.
     *
     * @param moodleform $mform
     * @return void
     */
    protected function specific_definition($mform) {
        global $DB;

        // Abschnitt für die Titelkonfiguration
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        // Block-Titel
        $mform->addElement('text', 'config_title', get_string('config_title', 'block_empfohlene_kurse'));
        $mform->setDefault('config_title', get_string('config_title_default', 'block_empfohlene_kurse'));
        $mform->setType('config_title', PARAM_TEXT);

        // Titelausrichtung
        $alignmentoptions = [
            'left' => get_string('alignment_left', 'block_empfohlene_kurse'),
            'center' => get_string('alignment_center', 'block_empfohlene_kurse'),
            'right' => get_string('alignment_right', 'block_empfohlene_kurse'),
        ];
        $mform->addElement('select', 'config_title_alignment',
            get_string('config_title_alignment', 'block_empfohlene_kurse'), $alignmentoptions);
        $mform->setDefault('config_title_alignment', 'left');

        // Button-Text
        $mform->addElement('text', 'config_button_text',
            get_string('config_button_text', 'block_empfohlene_kurse'));
        $mform->setDefault('config_button_text', get_string('enrollbutton', 'block_empfohlene_kurse'));
        $mform->setType('config_button_text', PARAM_TEXT);

        // Abschnitt für Darstellungsoptionen
        $mform->addElement('header', 'displayheader', get_string('display_settings', 'block_empfohlene_kurse'));

        // Layout-Modus
        $layoutmodes = [
            'vertical' => get_string('layout_vertical', 'block_empfohlene_kurse'),
            'horizontal' => get_string('layout_horizontal', 'block_empfohlene_kurse'),
            'card' => get_string('layout_card', 'block_empfohlene_kurse'),
            'minimal' => get_string('layout_minimal', 'block_empfohlene_kurse'),
        ];
        $mform->addElement('select', 'config_layout_mode',
            get_string('config_layout_mode', 'block_empfohlene_kurse'), $layoutmodes);
        $mform->setDefault('config_layout_mode', 'vertical');
        $mform->addHelpButton('config_layout_mode', 'config_layout_mode', 'block_empfohlene_kurse');

        // Bilddarstellungsmodus
        $imagefitmodes = [
            'cover' => get_string('imagefit_cover', 'block_empfohlene_kurse'),
            'contain' => get_string('imagefit_contain', 'block_empfohlene_kurse'),
            'fill' => get_string('imagefit_fill', 'block_empfohlene_kurse'),
        ];
        $mform->addElement('select', 'config_image_fit',
            get_string('config_image_fit', 'block_empfohlene_kurse'), $imagefitmodes);
        $mform->setDefault('config_image_fit', 'cover');
        $mform->addHelpButton('config_image_fit', 'config_image_fit', 'block_empfohlene_kurse');

        // Bildhöhe des Hauptkurses
        $imageheights = [
            '150' => '150px',
            '200' => '200px (Standard)',
            '250' => '250px',
            '300' => '300px',
            '350' => '350px',
        ];
        $mform->addElement('select', 'config_image_height',
            get_string('config_image_height', 'block_empfohlene_kurse'), $imageheights);
        $mform->setDefault('config_image_height', '200');

        // Eckenradius (Border Radius)
        $borderradius = [
            '0' => get_string('border_radius_none', 'block_empfohlene_kurse'),
            '4' => get_string('border_radius_small', 'block_empfohlene_kurse'),
            '8' => get_string('border_radius_medium', 'block_empfohlene_kurse'),
            '12' => get_string('border_radius_large', 'block_empfohlene_kurse'),
        ];
        $mform->addElement('select', 'config_border_radius',
            get_string('config_border_radius', 'block_empfohlene_kurse'), $borderradius);
        $mform->setDefault('config_border_radius', '8');

        // Animationsgeschwindigkeit
        $animationspeeds = [
            '0' => get_string('animation_none', 'block_empfohlene_kurse'),
            '200' => get_string('animation_fast', 'block_empfohlene_kurse'),
            '300' => get_string('animation_normal', 'block_empfohlene_kurse'),
            '500' => get_string('animation_slow', 'block_empfohlene_kurse'),
        ];
        $mform->addElement('select', 'config_animation_speed',
            get_string('config_animation_speed', 'block_empfohlene_kurse'), $animationspeeds);
        $mform->setDefault('config_animation_speed', '300');

        // Automatisches Sliding
        $autoslideoptions = [
            '0' => get_string('autoslide_off', 'block_empfohlene_kurse'),
            '3000' => '3 ' . get_string('seconds', 'block_empfohlene_kurse'),
            '5000' => '5 ' . get_string('seconds', 'block_empfohlene_kurse'),
            '7000' => '7 ' . get_string('seconds', 'block_empfohlene_kurse'),
            '10000' => '10 ' . get_string('seconds', 'block_empfohlene_kurse'),
        ];
        $mform->addElement('select', 'config_autoslide',
            get_string('config_autoslide', 'block_empfohlene_kurse'), $autoslideoptions);
        $mform->setDefault('config_autoslide', '0');
        $mform->addHelpButton('config_autoslide', 'config_autoslide', 'block_empfohlene_kurse');

        // Sichtbarkeitsoptionen
        $mform->addElement('advcheckbox', 'config_show_cards',
            get_string('config_show_cards', 'block_empfohlene_kurse'));
        $mform->setDefault('config_show_cards', 1);

        $mform->addElement('advcheckbox', 'config_show_button',
            get_string('config_show_button', 'block_empfohlene_kurse'));
        $mform->setDefault('config_show_button', 1);

        // Abschnitt für Kursinformationen
        $mform->addElement('header', 'courseinfoheader', get_string('course_info_settings', 'block_empfohlene_kurse'));

        $mform->addElement('advcheckbox', 'config_show_category',
            get_string('config_show_category', 'block_empfohlene_kurse'));
        $mform->setDefault('config_show_category', 1);
        $mform->addHelpButton('config_show_category', 'config_show_category', 'block_empfohlene_kurse');

        $mform->addElement('advcheckbox', 'config_show_contact',
            get_string('config_show_contact', 'block_empfohlene_kurse'));
        $mform->setDefault('config_show_contact', 1);
        $mform->addHelpButton('config_show_contact', 'config_show_contact', 'block_empfohlene_kurse');

        $mform->addElement('advcheckbox', 'config_show_contact_picture',
            get_string('config_show_contact_picture', 'block_empfohlene_kurse'));
        $mform->setDefault('config_show_contact_picture', 1);
        $mform->disabledIf('config_show_contact_picture', 'config_show_contact');

        $mform->addElement('advcheckbox', 'config_show_lastmodified',
            get_string('config_show_lastmodified', 'block_empfohlene_kurse'));
        $mform->setDefault('config_show_lastmodified', 1);
        $mform->addHelpButton('config_show_lastmodified', 'config_show_lastmodified', 'block_empfohlene_kurse');

        // Abschnitt für die Kursauswahl
        $mform->addElement('header', 'courseselectionheader', get_string('select_courses', 'block_empfohlene_kurse'));

        // Multiselect für die Kurse mit Suchfunktion
        $courseslist = [];

        // Nur sichtbare Kurse verwenden
        $courses = get_courses('all', 'c.sortorder ASC', 'c.id, c.fullname, c.visible');
        foreach ($courses as $course) {
            // Hauptkurs (Front Page / Startseite) ignorieren
            if ($course->id == SITEID) {
                continue;
            }
            if ($course->visible) {
                $courseslist[$course->id] = $course->fullname;
            }
        }

        // Autocomplete für die Kursauswahl
        $options = [
            'multiple' => true,
            'noselectionstring' => get_string('search_courses', 'block_empfohlene_kurse'),
        ];
        $mform->addElement('autocomplete', 'config_courses',
            get_string('select_courses', 'block_empfohlene_kurse'), $courseslist, $options);
    }
}
