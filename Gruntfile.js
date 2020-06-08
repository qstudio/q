'use strict';
module.exports = function(grunt) {

	// run -- https://github.com/shama/grunt-hub ##
	// grunt hub:all:watch

	grunt.initConfig({
		hub: {
			all: {
				src: ['../*/Gruntfile.js'],
				tasks: 'default'
		  	},
		},
	});

	// Load Tasks ##
	grunt.loadNpmTasks('grunt-hub'); // Clean up Tasks ##

};