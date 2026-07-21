/**
 * Grunt configuration for block_recommended_courses.
 *
 * @package    block_recommended_courses
 * @copyright  2025 Alexander Noack
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

module.exports = function(grunt) {
    'use strict';

    grunt.initConfig({
        eslint: {
            amd: {
                src: ['amd/src/*.js']
            }
        },

        uglify: {
            options: {
                preserveComments: false,
                sourceMap: {
                    includeSources: true
                },
                sourceMapName: function(dest) {
                    return dest + '.map';
                }
            },
            amd: {
                files: [{
                    expand: true,
                    cwd: 'amd/src',
                    src: ['*.js'],
                    dest: 'amd/build',
                    ext: '.min.js',
                    extDot: 'last'
                }]
            }
        },

        watch: {
            amd: {
                files: ['amd/src/*.js'],
                tasks: ['eslint:amd', 'uglify:amd']
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-eslint');

    // Satisfy moodle-plugin-ci grunt stylelint step when no CSS lint config is needed.
    grunt.registerTask('stylelint', []);

    grunt.registerTask('default', ['eslint', 'uglify']);
    grunt.registerTask('amd', ['eslint:amd', 'uglify:amd']);
};
