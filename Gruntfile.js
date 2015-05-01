"use strict";

module.exports = function(grunt) {

  var css_src_files = ['web/static/css/*.css'];
  var js_src_files = ['web/static/js/**/*.js'];

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    bower_concat: {
      all: {
        dest: 'web/static/dist/js/vendor.js',
        cssDest: 'web/static/dist/css/vendor.css'
      }
    },
    concat: {
      js: {
        src: js_src_files,
        dest: 'web/static/dist/js/application.js'
      },
      css: {
        src: css_src_files,
        dest: 'web/static/dist/css/application.css'
      }
    },
    uglify: {
      dist: {
        files: {
          'web/static/dist/js/application.min.js': ['web/static/dist/js/application.js'],
          'web/static/dist/js/vendor.min.js': ['web/static/dist/js/vendor.js']
        }
      }
    },
    cssmin: {
      options: {
        shorthandCompacting: false,
        roundingPrecision: -1
      },
      target: {
        files: {
          'web/static/dist/css/application.min.css': ['web/static/dist/css/application.css'],
          'web/static/dist/css/vendor.min.css': ['web/static/dist/css/vendor.css']
        }
      }
    },
    watch: {
      configFiles: {
        files: [ 'Gruntfile.js' ],
        options: {
          reload: true
        }
      },
      scripts: {
        files: js_src_files,
        tasks: ['concat'],
        options: {
          spawn: false
        }
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-bower-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-watch');

  // Default task(s).
  grunt.registerTask('default', ['bower_concat', 'concat', 'uglify', 'cssmin']);
};