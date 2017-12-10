/* Gruntfile.js */
'use strict';
module.exports = function (grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        // https://github.com/gruntjs/grunt-contrib-sass
        sass: {
            dist: {
                options: {
                    style: 'expanded'
                },
                files: {
                    // 'destination': 'source'
                    'frontend/web/css/style.css': [
                        'frontend/resourses/scss/style.scss'
                    ]
                }
            }
        },
        watch: {
            css: {
                files: [
                    'frontend/resourses/scss/*.scss'
                ],
                tasks: ['sass']
            }
        },
        favicons: {
            options: {
                trueColor: true,
                precomposed: true,
                appleTouchBackgroundColor: "#FFFFFF",
                coast: true,
                tileBlackWhite: false,
                tileColor: "auto",
                // html: 'frontend/views/layouts/main.php',
                HTMLPrefix: "/images/favicon/"
            },
            frontend: {
                src: 'frontend/resourses/images/log-icon.png',
                dest: 'frontend/web/images/favicon'
            },
            backend: {
                src: 'frontend/resourses/images/log-icon.png',
                dest: 'backend/web/images/favicon'
            }
        }
    });

    // Load the plugins for tasks
    grunt.loadNpmTasks('grunt-favicons');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');

    // Default task(s).
    grunt.registerTask('default', ['sass', 'favicons']);

};
