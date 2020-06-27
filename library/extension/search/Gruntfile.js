'use strict';

module.exports = function(grunt) {

	// root - crude ##
	var $root_path = '../../../';
	// var live_reload = 1339; // port for live reload ##

	// Load Tasks ##
	grunt.loadTasks( $root_path+'node_modules/grunt-contrib-clean/tasks' ); // Clean ##
	grunt.loadTasks( $root_path+'node_modules/grunt-contrib-watch/tasks' ); // Watcher ##
	grunt.loadTasks( $root_path+'node_modules/grunt-postcss/tasks' ); // Post Processing ##
	grunt.loadTasks( $root_path+'node_modules/grunt-dart-sass/tasks' ); // DART SASS ##
	grunt.loadTasks( $root_path+'node_modules/grunt-contrib-copy/tasks' ); // copy ##

	// ------- configuration ------- ##
	grunt.initConfig({

		// common SCSS root for easier SCSS @use / @forward usage ##
		includePaths: [
			'./library/ui/asset/scss/' // common SCSS root ##
		],

		// sass controller file ##
		src: 'library/ui/asset/scss/index.scss',

		// main destination file ##
		dest: 'library/ui/asset/css/index.css',

		// minified destination file ##
		dest_min: 'library/ui/asset/css/index.min.css',

		// object of files to clean up pre-compile ##
		clean_dest: [
			'library/ui/asset/css/*.css', // regex to find all generated css files ##
			'library/ui/asset/css/*.map', // regex to find all generated map files ##
		],

		// scss files to watch ##
		watch_scss: [
			'library/ui/asset/scss/*.scss', // regex to track all Template files ##
		],

		// php files to watch ##
		watch_php: [
			// 'library/ui/view/*.php', // ui view [template] folder ##
		],

		// ------- end config ------- ##

		// clean up old compilled files ##
		'clean': {
			'dist':
				'<%= clean_dest %>'
		},

		// SASS compiller ##
		'dart-sass': {
			'target': {
				'options': {
					'outputStyle'	: 'expanded',
					'sourceMap'		: true,
					'includePaths'	: '<%= includePaths %>',
					'lineNumber'	: true,
				},
			  	'files': {
					'<%= dest %>': '<%= src %>'
			  	}
			}
		},

		// watch task ##
		'watch': {
			// track changes to scss src files ##
			'sass': {
				'options': {
					// 'livereload': live_reload, // dedicated port for live reload ##
				},
				'files':
					'<%= watch_scss %>'
				,
				'tasks': [
					'default',  // only run sass to rebuild main .css file ##
				]
			},
			/*
			// track changes to specific PHP templates ##
			'php': {
				'options': {
					'livereload': live_reload,
				},
				'files':
					'<%= watch_php %>'
				,
				'tasks': [
					'php' // no tasks yet ##
				]
			},
			*/
		},

		'copy': {
			'main': {
			  	'files': [

					{
						'expand'	: true, 
						'cwd'		: 'library/ui/asset/scss/',
						'src'		: 'index.scss', 
						'dest'		: '../../themes/q-parent/library/_source/scss/plugin/q_search.css', 
						// // The function must return a string with the complete destination ##
						// https://gruntjs.com/configuring-tasks#the-rename-property
						'rename': 	function ( ) {
							return '../../themes/q-parent/library/_source/scss/plugin/_q_search.css' ; 
						}
						// 'filter'	: 'isFile',
						// 'flatten'	: true 
					}

					// includes files within path
					// {expand: true, src: ['path/*'], dest: 'dest/', filter: 'isFile'},
			
					// includes files within path and its sub-directories
					// {expand: true, src: ['path/**'], dest: 'dest/'},
			
					// makes all src relative to cwd
					// {expand: true, cwd: 'path/', src: ['**'], dest: 'dest/'},
			
					// flattens results to a single level
					// {expand: true, flatten: true, src: ['path/**'], dest: 'dest/', filter: 'isFile'},

				],
			},
		},

		// post processing formating ##
		'postcss': {
			'options': {
				'map': true, // inline sourcemaps
				'processors': [
					// add fallbacks for rem units ##
					require('pixrem')(),
					// add vendor prefixes -- options defined in package.json 'browserslist' ##
					require('autoprefixer')(),
				]
			},
			'dist': {
				'src': '<%= dest %>',
				'dest': '<%= dest %>',
			},
			'minify': {
			 	'options': {
			 		'processors': [
			 			require('cssnano')() // minifies ##
			 		]
			 	},
				'src': '<%= dest %>',
				'dest': '<%= dest_min %>',
			}
		},

  	});

	// Development tasks Tasks ##
	grunt.registerTask( 'default', [
		// 'clean', // clean up old compilled files ##
		'dart-sass', // Dart SASS ##
		'copy' // copy to Q Parent ##
		// 'postcss', // post processing formating ## ##
	]);

	// Prepare for deployment Tasks ##
	grunt.registerTask( 'deploy', [
		'clean', // clean up old compilled files ##
		'dart-sass', // Dart SASS ##
		'postcss', // post processing formating ## ##
		// 'copy' // copy to  ##
	]);

	// Watch Task ##
	grunt.registerTask( 'php', [
		// No specific tasks, just live reload ##
	]);

};
