'use strict';

module.exports = function(grunt) {

	// root - crude ##
	var $root_path = '../../../';

	// Load Tasks ##
	grunt.loadTasks( $root_path+'node_modules/grunt-contrib-clean/tasks' ); // Clean ##
	grunt.loadTasks( $root_path+'node_modules/grunt-contrib-watch/tasks'); // Watcher ##
	// grunt.loadTasks( $root_path+'node_modules/grunt-postcss/tasks'); // Post Processing ##
	// grunt.loadTasks( $root_path+'node_modules/grunt-dart-sass/tasks'); // DART SASS ##
	grunt.loadTasks( $root_path+'node_modules/grunt-contrib-uglify/tasks'); // UGLIFY / Minify JS ##
	grunt.loadTasks( $root_path+'node_modules/grunt-contrib-copy/tasks' ); // copy ##

	// ------- configuration ------- ##
	grunt.initConfig({

		// JS files to watch and minify ##
		watch_js: [
			'library/**/*.js', // all .js files in Q ##
		],


		// ------- end config ------- ##

		'copy': {
			'files': {
			  'cwd': 'library/_source/js/module',  // set working folder / root to copy
			  'src': '*.js',           // copy all files and subfolders
			  'dest': 'library/asset/js/module',    // destination folder
			  'expand': true,           // required when using cwd
			  'filter': 'isFile'
			}
		},

		'uglify': {
			'min': {
				'options': {
					'mangle': false,
					'banner': '/*! Q Studio ~~ <%= grunt.template.today("yyyy-mm-dd") %> */\n'
				},
				'files': grunt.file.expandMapping(['library/_source/js/*.js'], 'library/asset/js/', {
					flatten: true,
					rename: function(destBase, destPath) {
						return destBase+destPath.replace('.js', '.min.js');
					}
				})
			}
			
		},

		// clean up old compilled files ##
		'clean': {
			'dist':
				'<%= clean_dest %>'
		},

		// SASS compiller ##
		'dart-sass': {
			'target': {
				'options': {
					// 'outputStyle'	: 'expanded',
					'outputStyle'	: 'compressed',
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

			// track changes to js _source files ##
			'sass': {
				'options': {
					// 'livereload': live_reload, // dedicated port for live reload ##
				},
				'files':
					'<%= watch_js %>'
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

	// Development Tasks ##
	grunt.registerTask( 'default', [
		'clean', // clean up old compilled files ##
		'uglify:min', // minify js /_source/js to /assets/js
		'copy', // copy _source/js/*.js -> /asset/js/*.js
	]);

	// Prepare for deployment Tasks ##
	grunt.registerTask( 'deploy', [
		'clean', // clean up old compilled files ##
		'dart-sass', // Dart SASS ##
		'postcss', // post processing formating ## ##
		'uglify:min', // minify js /_source/js to /assets/js
		'copy', // copy _source/js/*.js -> /asset/js/*.js
	]);

	// Watch Task ##
	grunt.registerTask( 'php', [
		// No specific tasks, just live reload ##
	]);

};
