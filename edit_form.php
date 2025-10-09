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
