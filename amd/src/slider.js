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
 * JavaScript for the empfohlene_kurse block.
 *
 * @module     block_empfohlene_kurse/slider
 * @copyright  2025 Moodle Developer
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery'], function($) {

    /**
     * Initialize the slider functionality
     *
     * @param {string} blockId The unique ID of the block instance
     * @param {Array} courses Array of course data
     */
    var init = function(blockId, courses) {
        if (!courses || !Array.isArray(courses) || courses.length === 0) {
            return;
        }

        // Finde den Container
        var container = $('#' + blockId);
        if (!container.length) {
            return;
        }

        var coursesCount = courses.length;
        var currentIndex = 0;
        var visibleCards = 3; // Maximale Anzahl der Karten

        // Funktion zum Aktualisieren des Sliders
        function updateSlider() {
            try {
                // Hauptkurs aktualisieren
                var mainCourse = courses[currentIndex];
                var $mainCourseWrapper = container
        .find('.main-course-wrapper');

                if ($mainCourseWrapper.length && mainCourse) {
                    $mainCourseWrapper.attr('data-course-id', mainCourse.id);
                    $mainCourseWrapper.find('.main-course-image img').attr('src', mainCourse.courseimage).attr('alt', mainCourse.fullname);
                    $mainCourseWrapper.find('.main-course-title').text(mainCourse.fullname);
                    $mainCourseWrapper.find('.main-course-description').text(mainCourse.summary);
                    $mainCourseWrapper.find('.button-container a').attr('href', mainCourse.enrollurl);

                    // Kurskarten aktualisieren
                    var $courseCards = container
        .find('.course-cards');
                    $courseCards.empty();

                    // Nur so viele Karten hinzufügen wie wir Kurse haben (maximal visibleCards)
                    var cardsToShow = Math.min(visibleCards, coursesCount - 1);

                    for (var i = 1; i <= cardsToShow; i++) {
                        var index = (currentIndex + i) % coursesCount;
                        var course = courses[index];

                        if (course) {
                            var $card = $('<div>').addClass('course-card').attr('data-course-id', course.id);
                            var $imageContainer = $('<div>').addClass('course-card-image');
                            var $image = $('<img>').attr('src', course.courseimage).attr('alt', course.fullname);
                            var $title = $('<div>').addClass('course-card-title').text(course.fullname);

                            $imageContainer.append($image);
                            $card.append($imageContainer).append($title);
                            $courseCards.append($card);
                        }
                    }
                }
            } catch (e) {
                console.error('Fehler beim Aktualisieren des Sliders: ', e);
            }
        }

        // Event-Listener für Kurskarten
        container.on('click', '.course-card', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var courseId = $(this).attr('data-course-id');

            // Finde den Index des Kurses
            for (var i = 0; i < courses.length; i++) {
                if (courses[i].id == courseId) {
                    currentIndex = i;
                    updateSlider();
                    break;
                }
            }
            return false;
        });

        // Event-Listener für die Navigationspfeile
        container.on('click', '.slider-nav.prev', function(e) {
            e.preventDefault();
            e.stopPropagation();
            currentIndex = (currentIndex - 1 + coursesCount) % coursesCount;
            updateSlider();
            return false;
        });

        container.on('click', '.slider-nav.next', function(e) {
            e.preventDefault();
            e.stopPropagation();
            currentIndex = (currentIndex + 1) % coursesCount;
            updateSlider();
            return false;
        });

        // Slider initialisieren
        updateSlider();

        // Bei nur einem Kurs die Navigation ausblenden
        if (coursesCount <= 1) {
            container
        .find('.slider-nav').hide();
        }
    };

    return {
        init: init
    };
});
