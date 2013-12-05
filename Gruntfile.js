/* jshint node:true */
module.exports = function( grunt ) {
	'use strict';

	require( 'load-grunt-tasks' )( grunt );

	var gruntConfig = {

		// gets the package vars
		pkg: grunt.file.readJSON( 'package.json' ),

		// deploy via rsync
		rsync: {
			options: {
				args: ['--verbose'],
				exclude: [
					'**.DS_Store',
					'**Thumbs.db',
					'.editorconfig',
					'.git/',
					'.gitignore',
					'composer.*',
					'dev-config.php',
					'forum/',
					'Gruntfile.js',
					'package.json',
					'node_modules/',
					'README.md',
					'vendor/',
					'wp/readme.html',
					'wp/license.txt',
					'wp/wp-content/',
					'wp-content/uploads'
				],
				recursive: true,
			},
			production: {
				options: {
					src: './',
					dest: '/var/www/wp-brasil',
					host: 'vagrant@wp-brasil.org',
					syncDestIgnoreExcl: true
				}
			}
		}
	};

	// Initialize Grunt Config
	// --------------------------
	grunt.initConfig( gruntConfig );

	// Register Tasks
	// --------------------------

	// Default Task
	grunt.registerTask( 'default', [
		'rsync:production'
	] );
};
