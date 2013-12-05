/* jshint node:true */
module.exports = function( grunt ) {
	'use strict';

	require( 'load-grunt-tasks' )( grunt );

	var gruntConfig = {

		// Gets the package vars
		pkg: grunt.file.readJSON( 'package.json' ),

		// Run shell commands.
		shell: {
			installThemeDependencies: {
				options: {
					stdout: true
				},
				command: [
					'cd wp-content/themes/tema/src/',
					'npm install',
					'grunt'
				].join('&&')
			}
		},

		// Deploy via rsync
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
					'wp/themes/tema/src/',
					'wp/themes/tema/assets/sass',
					'wp/themes/tema/assets/js/main.js',
					'wp/themes/tema/assets/js/jquery.fitvids.min.js',
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
		},

		// Remove files
		clean: {
			dist: [
				'vendor/',
				'wp/',
				'wp-content/mu-plugins/*/**',
				'!wp-content/plugins/.gitignore',
				'!wp-content/plugins/index.php',
				'!wp-content/plugins/register-theme-directory.php',
				'!wp-content/plugins/wp-setup.php',
				'wp-content/plugins/*/**',
				'!wp-content/plugins/.gitignore',
				'!wp-content/plugins/index.php',
				'wp-content/themes/*/**',
				'!wp-content/themes/.gitignore',
				'!wp-content/themes/index.php',
				'composer.lock'
			]
		}
	};

	// Initialize Grunt Config
	// --------------------------
	grunt.initConfig( gruntConfig );

	// Register Tasks
	// --------------------------

	// Default task
	grunt.registerTask( 'default', [
		'composer:install:no-dev',
		'shell:installThemeDependencies'
	] );

	// Build task
	grunt.registerTask( 'build', [
		'default'
	] );

	// Update dependencies task
	grunt.registerTask( 'update', [
		'composer:update',
		'shell:installThemeDependencies'
	] );

	// Deploy task
	grunt.registerTask( 'deploy', [
		'update',
		'rsync:production'
	] );
};
