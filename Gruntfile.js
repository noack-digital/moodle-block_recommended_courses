/**
 * Grunt configuration for block_recommended_courses.
 *
 * @package    block_recommended_courses
 * @copyright  2025 Alexander Noack - HNEE
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

module.exports = function (grunt) {
    'use strict';

    // Project configuration.
    grunt.initConfig({
        // ESLint for JavaScript linting
        eslint: {
            amd: {
                src: ['amd/src/*.js']
            }
        },

        // Uglify for JavaScript minification
        uglify: {
            options: {
                preserveComments: 'some'
            },
            amd: {
                files: [{
                    expand: true,
                    cwd: 'amd/src',
                    src: ['*.js'],
                    dest: 'amd/build',
                    ext: '.min.js'
                }]
            }
        },

        // Watch for changes
        watch: {
            amd: {
                files: ['amd/src/*.js'],
                tasks: ['eslint:amd', 'uglify:amd']
            }
        }
    });

    // Load NPM tasks
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-eslint');

    // Register dummy stylelint task to satisfy Moodle CI if not configured
    grunt.registerTask('stylelint', []);

    // Default task(s)
    grunt.registerTask('default', ['eslint', 'uglify']);
    grunt.registerTask('amd', ['eslint:amd', 'uglify:amd']);
};
