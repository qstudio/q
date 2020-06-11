'use strict';
module.exports = function(grunt) {

	// run -- https://github.com/shama/grunt-hub ##
	// grunt hub:all:watch 
	// OR
	// npm run q:dev
	// OR
	// npm run q:deploy

	grunt.initConfig({
		hub: {
			all: {
				// src: ['../*/Gruntfile.js'],
				src: [
					'../../themes/q*/Gruntfile.js', // q* themes ##
					'../../plugins/q*/Gruntfile.js' // q* plugins ##
				], 
				tasks: 'default'
		  	},
		},
	});

	// Load Tasks ##
	grunt.loadNpmTasks('grunt-hub'); // Clean up Tasks ##

};