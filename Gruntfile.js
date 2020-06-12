'use strict';

module.exports = function(grunt) {

	// root - crude ##
	var $root_path = '../../../';
	var live_reload = 1339; // port for live reload ##

	// Load Tasks ##
	grunt.loadTasks( $root_path+'node_modules/grunt-contrib-clean/tasks' ); // Clean ##
	grunt.loadTasks( $root_path+'node_modules/grunt-contrib-watch/tasks'); // Watcher ##
	grunt.loadTasks( $root_path+'node_modules/grunt-postcss/tasks'); // Post Processing ##
	grunt.loadTasks( $root_path+'node_modules/grunt-dart-sass/tasks'); // DART SASS ##

	// ------- configuration ------- ##
	grunt.initConfig({

		// common SCSS root for easier SCSS @use / @forward usage ##
		includePaths: [
			'./library/ui/asset/scss/' // common SCSS root ##
		],

		// sass controller file ##
		src: 'library/ui/asset/scss/index.scss',

		// main destination file ##
		dest: 'library/ui/asset/css/q.scss.theme.css',

		// minified destination file ##
		dest_min: 'library/ui/asset/css/q.scss.theme.min.css',

		// object of files to clean up pre-compile ##
		clean_dest: [
			'library/ui/asset/css/q.scss.*.css', // regex to find all generated css files ##
			'library/ui/asset/css/q.scss.*.map', // regex to find all generated map files ##
		],

		// scss files to watch ##
		watch_scss: [
			'library/ui/asset/scss/view/*.scss', // regex to track all Template files ##
			'library/ui/asset/scss/ui/*.scss', // regex to track all UI files ##
			'library/ui/asset/scss/q/*.scss', // regex to track all config files ##
			'library/ui/asset/scss/module/*.scss', // regex to track all config files ##
			'library/ui/asset/scss/bootstrap/*.scss', // regex to track main Bootstrap include ##
		],

		// php files to watch ##
		watch_php: [
			'library/ui/view/*.php', // ui view [template] folder ##
			'library/ui/furniture/*.php' // furniture [ header, footer, blocks.. ] folder ##
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
					'livereload': live_reload, // dedicated port for live reload ##
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
		// 'postcss', // post processing formating ## ##
	]);

	// Prepare for deployment Tasks ##
	grunt.registerTask( 'deploy', [
		'clean', // clean up old compilled files ##
		'dart-sass', // Dart SASS ##
		'postcss', // post processing formating ## ##
	]);

	// Watch Task ##
	grunt.registerTask( 'php', [
		// No specific tasks, just live reload ##
	]);

};
