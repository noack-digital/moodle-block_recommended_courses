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
 * Class containing data for the empfohlene_kurse block.
 *
 * @package    block_empfohlene_kurse
 * @copyright  2025 Moodle Developer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_empfohlene_kurse\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Class containing data for the empfohlene_kurse block.
 *
 * @package    block_empfohlene_kurse
 * @copyright  2025 Moodle Developer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class main implements renderable, templatable {

    /**
     * @var array Liste der Kurse für den Slider
     */
    protected $courses;
    
    /**
     * @var string Text für den Einschreibe-Button
     */
    protected $buttontext;
    
    /**
     * @var string Text, wenn keine Kurse vorhanden sind
     */
    protected $nocoursesmessage;
    
    /**
     * @var array Darstellungsoptionen
     */
    protected $displayoptions;
    
    /**
     * Konstruktor.
     *
     * @param array $courses Liste der Kurse für den Slider
     * @param string $buttontext Text für den Einschreibe-Button
     * @param array $displayoptions Darstellungsoptionen für den Slider
     */
    public function __construct($courses, $buttontext = null, $displayoptions = []) {
        global $PAGE;
        
        $this->courses = $courses;
        
        // Darstellungsoptionen mit Standardwerten
        $this->displayoptions = array_merge([
            'image_fit' => 'cover',
            'image_height' => '200',
            'border_radius' => '8',
            'animation_speed' => '300',
        ], $displayoptions);
        
        // Falls der Button-Text nicht angegeben wurde, den Standardwert aus den Sprachdateien verwenden
        if ($buttontext === null) {
            $this->buttontext = get_string('enrollbutton', 'block_empfohlene_kurse');
        } else {
            $this->buttontext = $buttontext;
        }
        
        // Meldung, wenn keine Kurse vorhanden sind
        $this->nocoursesmessage = get_string('no_courses_to_display', 'block_empfohlene_kurse');
    }

    /**
     * Export für Template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        
        // Eindeutige ID für das Block-Template generieren
        $data->uniqid = uniqid();
        
        // Prüfen, ob Kurse vorhanden sind
        $data->hascourses = !empty($this->courses);
        $data->courses = [];
        $data->buttontext = $this->buttontext;
        $data->no_courses_message = $this->nocoursesmessage;
        $data->prev_course = get_string('previous_course', 'block_empfohlene_kurse');
        $data->next_course = get_string('next_course', 'block_empfohlene_kurse');
        
        // Darstellungsoptionen an Template übergeben
        $data->image_fit = $this->displayoptions['image_fit'];
        $data->image_height = $this->displayoptions['image_height'];
        $data->border_radius = $this->displayoptions['border_radius'];
        $data->animation_speed = $this->displayoptions['animation_speed'];
        
        // Wenn keine Kurse vorhanden sind, leeres Objekt zurückgeben
        if (!$data->hascourses) {
            $data->coursesJson = json_encode([]);
            return $data;
        }
        
        // Sonst die Kurse für das Template vorbereiten
        $first = true;
        $visibleCount = 0;
        
        foreach ($this->courses as $index => $course) {
            $coursedata = new stdClass();
            $coursedata->id = $course['id'];
            $coursedata->fullname = $course['fullname'];
            $coursedata->shortname = $course['shortname'];
            $coursedata->summary = $course['summary'];
            $coursedata->courseimage = $course['courseimage'];
            $coursedata->viewurl = $course['viewurl'];
            $coursedata->enrollurl = $course['enrollurl'];
            
            // Erster Kurs wird groß angezeigt
            $coursedata->first = $first;
            
            // Nur die ersten 4 Kurse sichtbar (1 Hauptkurs + 3 Karten)
            $coursedata->visible = $visibleCount < 4;
            
            $data->courses[] = $coursedata;
            
            if ($first) {
                $first = false;
            }
            $visibleCount++;
        }
        
        // JSON-Daten für JavaScript
        $data->coursesJson = json_encode($this->courses);
        
        return $data;
    }
}
