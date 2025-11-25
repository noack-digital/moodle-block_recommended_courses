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
 * JavaScript controller for the Recommended Courses block slider.
 *
 * @module     block_recommended_courses/slider
 */
define(['jquery'], function($) {
    var KEYCODES = {
        ENTER: 13,
        SPACE: 32,
    };

    var DEFAULTS = {
        layoutMode: 'vertical',
        imageFit: 'cover',
        imageHeight: 200,
        borderRadius: 8,
        animationSpeed: 300,
        autoslide: 0,
        showButton: true,
        showCards: true,
        showCategory: true,
        showContact: true,
        showContactPicture: true,
        showLastmodified: true,
    };

    var toBool = function(value) {
        if (typeof value === 'boolean') {
            return value;
        }
        if (typeof value === 'number') {
            return value === 1;
        }
        if (typeof value === 'string') {
            return value === '1' || value.toLowerCase() === 'true';
        }
        return false;
    };

    var toInt = function(value, fallback) {
        var parsed = parseInt(value, 10);
        return isNaN(parsed) ? fallback : parsed;
    };

    var isActivationEvent = function(e) {
        if (e.type === 'click') {
            return true;
        }
        return e.type === 'keydown' && (e.which === KEYCODES.ENTER || e.which === KEYCODES.SPACE);
    };

    var init = function(blockId, rawCourses, config) {
        if (!Array.isArray(rawCourses) || rawCourses.length === 0) {
            return;
        }

        var container = $('#' + blockId);
        if (!container.length) {
            return;
        }

        var options = $.extend({}, DEFAULTS, config || {});
        options.imageHeight = toInt(options.imageHeight, DEFAULTS.imageHeight);
        options.borderRadius = toInt(options.borderRadius, DEFAULTS.borderRadius);
        options.animationSpeed = toInt(options.animationSpeed, DEFAULTS.animationSpeed);
        options.autoslide = toInt(options.autoslide, DEFAULTS.autoslide);
        options.showButton = toBool(options.showButton);
        options.showCards = toBool(options.showCards);
        options.showCategory = toBool(options.showCategory);
        options.showContact = toBool(options.showContact);
        options.showContactPicture = toBool(options.showContactPicture);
        options.showLastmodified = toBool(options.showLastmodified);

        var state = {
            currentIndex: 0,
            coursesCount: rawCourses.length,
            autoslideTimer: null,
        };

        var mainCourseWrapper = container.find('.main-course-wrapper');
        if (!mainCourseWrapper.length) {
            return;
        }

        var sliderIndicators = container.find('.slider-indicators');
        var courseCardsWrapper = options.showCards ? container.find('.course-cards') : $();
        if (!courseCardsWrapper.length) {
            courseCardsWrapper = null;
        }

        var applyLayoutAttributes = function() {
            mainCourseWrapper
                .removeClass('layout-vertical layout-horizontal layout-card layout-minimal')
                .addClass('layout-' + options.layoutMode);

            mainCourseWrapper.find('.main-course').css('border-radius', options.borderRadius + 'px');
            mainCourseWrapper.find('.main-course-image').css('height', options.imageHeight + 'px');
            mainCourseWrapper.find('.main-course-image img').css('object-fit', options.imageFit);
        };

        var initializeIndicators = function() {
            if (state.coursesCount <= 1) {
                sliderIndicators.hide();
                return;
            }

            sliderIndicators.empty();
            for (var i = 0; i < state.coursesCount; i++) {
                var $dot = $('<div>').addClass('dot').attr('data-index', i);
                $('<span>').addClass('tooltip').text(rawCourses[i].fullname).appendTo($dot);
                if (i === state.currentIndex) {
                    $dot.addClass('active');
                }
                sliderIndicators.append($dot);
            }
        };

        var updateIndicators = function() {
            if (state.coursesCount <= 1) {
                return;
            }
            sliderIndicators.find('.dot').removeClass('active');
            sliderIndicators.find('.dot[data-index="' + state.currentIndex + '"]').addClass('active');
        };

        var renderMetaInfo = function(course) {
            var meta = mainCourseWrapper.find('.course-meta-info');
            meta.empty();

            if (options.showCategory && course.category) {
                var $category = $('<div>').addClass('meta-item meta-category');
                $('<i>').addClass('fa fa-folder-open').appendTo($category);
                $('<span>').text(course.category).appendTo($category);
                meta.append($category);
            }

            if (options.showContact && course.contact && course.contact.name) {
                var $contact = $('<div>').addClass('meta-item meta-contact');
                $('<i>').addClass('fa fa-user').appendTo($contact);
                if (options.showContactPicture && course.contact.pictureurl) {
                    $('<img>')
                        .addClass('contact-picture')
                        .attr('src', course.contact.pictureurl)
                        .attr('alt', course.contact.name)
                        .appendTo($contact);
                }
                $('<a>')
                    .addClass('contact-name')
                    .attr('href', course.contact.profileurl)
                    .text(course.contact.name)
                    .appendTo($contact);
                meta.append($contact);
            }

            if (options.showLastmodified && course.lastmodified) {
                var $lastmodified = $('<div>').addClass('meta-item meta-lastmodified');
                $('<i>').addClass('fa fa-calendar').appendTo($lastmodified);
                $('<span>').text(course.lastmodified).appendTo($lastmodified);
                meta.append($lastmodified);
            }
        };

        var renderCourseCards = function() {
            if (!courseCardsWrapper) {
                return;
            }
            courseCardsWrapper.empty();
            var cardsToShow = Math.min(3, state.coursesCount - 1);
            for (var i = 1; i <= cardsToShow; i++) {
                var index = (state.currentIndex + i) % state.coursesCount;
                var course = rawCourses[index];
                var $card = $('<div>')
                    .addClass('course-card')
                    .attr('data-course-id', course.id)
                    .attr('role', 'button')
                    .attr('tabindex', '0')
                    .attr('aria-label', course.fullname);
                var $imageContainer = $('<div>').addClass('course-card-image');
                $('<img>').attr('src', course.courseimage).attr('alt', course.fullname).appendTo($imageContainer);
                $card.append($imageContainer);
                $('<div>').addClass('course-card-title').text(course.fullname).appendTo($card);
                courseCardsWrapper.append($card);
            }
        };

        var updateMainCourse = function(course) {
            mainCourseWrapper.attr('data-course-id', course.id);

            mainCourseWrapper
                .find('.main-course-image img')
                .attr('src', course.courseimage)
                .attr('alt', course.fullname);

            var titleLink = mainCourseWrapper.find('.main-course-title a.course-title-link');
            if (titleLink.length) {
                titleLink.attr('href', course.viewurl).text(course.fullname);
            } else {
                mainCourseWrapper.find('.main-course-title').text(course.fullname);
            }

            mainCourseWrapper.find('.main-course-description').text(course.summary || '');

            if (options.showButton) {
                var button = mainCourseWrapper.find('.button-container a');
                if (button.length) {
                    button.attr('href', course.enrollurl);
                }
            }

            renderMetaInfo(course);
        };

        var animateMainCourse = function(callback) {
            if (options.animationSpeed <= 0) {
                callback();
                return;
            }
            mainCourseWrapper.css('opacity', 0);
            window.setTimeout(function() {
                callback();
                mainCourseWrapper.animate({opacity: 1}, options.animationSpeed);
            }, options.animationSpeed / 2);
        };

        var updateSlider = function() {
            if (!state.coursesCount) {
                return;
            }

            var course = rawCourses[state.currentIndex];
            updateIndicators();
            animateMainCourse(function() {
                updateMainCourse(course);
            });
            renderCourseCards();
        };

        var startAutoslide = function() {
            if (options.autoslide <= 0 || state.coursesCount <= 1) {
                return;
            }
            state.autoslideTimer = window.setInterval(function() {
                state.currentIndex = (state.currentIndex + 1) % state.coursesCount;
                updateSlider();
            }, options.autoslide);
        };

        var resetAutoslide = function() {
            if (state.autoslideTimer) {
                window.clearInterval(state.autoslideTimer);
                state.autoslideTimer = null;
            }
            startAutoslide();
        };

        var activateCourseById = function(courseId) {
            for (var i = 0; i < rawCourses.length; i++) {
                if (rawCourses[i].id == courseId) {
                    state.currentIndex = i;
                    updateSlider();
                    resetAutoslide();
                    break;
                }
            }
        };

        container.on('click keydown', '.slider-nav.prev', function(e) {
            if (!isActivationEvent(e)) {
                return;
            }
            e.preventDefault();
            state.currentIndex = (state.currentIndex - 1 + state.coursesCount) % state.coursesCount;
            updateSlider();
            resetAutoslide();
        });

        container.on('click keydown', '.slider-nav.next', function(e) {
            if (!isActivationEvent(e)) {
                return;
            }
            e.preventDefault();
            state.currentIndex = (state.currentIndex + 1) % state.coursesCount;
            updateSlider();
            resetAutoslide();
        });

        container.on('click keydown', '.course-card', function(e) {
            if (!isActivationEvent(e)) {
                return;
            }
            e.preventDefault();
            activateCourseById($(this).attr('data-course-id'));
        });

        container.on('click', '.slider-indicators .dot', function(e) {
            e.preventDefault();
            var index = parseInt($(this).attr('data-index'), 10);
            if (!isNaN(index) && index !== state.currentIndex) {
                state.currentIndex = index;
                updateSlider();
                resetAutoslide();
            }
        });

        container.on('mouseenter', '.main-course-wrapper', function() {
            if (state.autoslideTimer) {
                window.clearInterval(state.autoslideTimer);
            }
        });

        container.on('mouseleave', '.main-course-wrapper', function() {
            if (options.autoslide > 0) {
                startAutoslide();
            }
        });

        applyLayoutAttributes();

        if (state.coursesCount > 1) {
            initializeIndicators();
        } else {
            container.find('.slider-nav').hide();
            sliderIndicators.hide();
        }

        updateSlider();
        startAutoslide();
    };

    return {
        init: init,
    };
});
