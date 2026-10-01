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
 * Enrolment filter toggle for the Recommended Courses block.
 *
 * @module     block_recommended_courses/enrolment_filter
 */
define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {
    /**
     * Move the filter control into the Moodle block header (between title and controls).
     *
     * @param {jQuery} blockRoot Root element of the plugin content.
     * @param {jQuery} filterEl Filter element to relocate.
     */
    var placeInHeader = function(blockRoot, filterEl) {
        var blockSection = blockRoot.closest('.block_recommended_courses, .block.block_recommended_courses');
        if (!blockSection.length) {
            return;
        }

        var header = blockSection.find('> .card-body > .card-title, > .header, .card-title').first();
        if (!header.length) {
            return;
        }

        // Prefer inserting before block action menu / controls.
        var controls = header.find('.block-controls, .menubar, .card-body-actions, [data-region="block-controls"]').first();
        filterEl.addClass('enrolment-filter-in-header');
        if (controls.length) {
            filterEl.insertBefore(controls);
        } else {
            header.append(filterEl);
        }
        blockSection.addClass('has-enrolment-filter');
    };

    /**
     * Initialise the enrolment filter toggle.
     *
     * @param {string} blockId DOM id of the block content root.
     */
    var init = function(blockId) {
        var blockRoot = $('#' + blockId);
        if (!blockRoot.length) {
            return;
        }

        var filterEl = blockRoot.children('.enrolment-filter').first();
        if (!filterEl.length) {
            return;
        }

        placeInHeader(blockRoot, filterEl);

        var input = filterEl.find('.enrolment-filter-input');
        var preference = filterEl.attr('data-preference');
        if (!input.length || !preference) {
            return;
        }

        input.on('change', function() {
            var checked = input.is(':checked');
            input.prop('disabled', true);
            filterEl.addClass('is-saving');

            var request = Ajax.call([{
                methodname: 'core_user_set_user_preferences',
                args: {
                    preferences: [{
                        name: preference,
                        value: checked ? '1' : '0',
                    }],
                },
            }])[0];

            request.then(function() {
                window.location.reload();
            }).catch(function(error) {
                input.prop('disabled', false);
                filterEl.removeClass('is-saving');
                input.prop('checked', !checked);
                Notification.exception(error);
            });
        });
    };

    return {
        init: init,
    };
});
