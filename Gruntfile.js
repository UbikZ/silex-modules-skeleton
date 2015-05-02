"use strict";

module.exports = function(grunt) {
  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    bower_concat: {
      all: {
        dest: 'web/static/dist/js/vendor.js',
        cssDest: 'web/static/dist/css/vendor.css'
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
    }
  });

  // Module static configuration (before concat)
  grunt.registerTask("prepareModules", "Finds and prepares modules for concatenation.", function() {
    var concat = grunt.config.get('concat') || {};
    grunt.file.expand("src/Ubikz/SMS/Module/*").forEach(function (dir) {
      var dirName = dir.substr(dir.lastIndexOf('/')+1);

      concat[dirName+'_js'] = {
        src: [dir + '/Resources/public/js/**/*.js'],
        dest: 'web/static/js/' + dirName + '.js'
      };

      concat[dirName+'_css'] = {
        src: [dir + '/Resources/public/css/*.css'],
        dest: 'web/static/css/' + dirName + '.css'
      };

    });

    concat['js'] = {
      src: ['web/static/js/**/*.js'],
      dest: 'web/static/dist/js/application.js'
    };

    concat['css'] = {
      src: ['web/static/css/*.css'],
      dest: 'web/static/dist/css/application.css'
    };

    grunt.config.set('concat', concat);
  });

  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-bower-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');

  // Default task(s).
  grunt.registerTask('default', ['bower_concat', 'prepareModules', 'concat', 'uglify', 'cssmin']);
};