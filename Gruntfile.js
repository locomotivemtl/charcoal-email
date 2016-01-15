/**
* Gruntfile.js
* Charcoal-Core configuration for grunt. (The JavaScript Task Runner)
*/

module.exports = function(grunt) {
	"use strict";

    function loadConfig(path) {
        var glob = require('glob');
        var object = {};
        var key;

        glob.sync('*.js', {cwd: path}).forEach(function(option) {
            key = option.replace(/\.js$/,'');
            object[key] = require(path + option);
        });

        return object;
    }

    var config = {
        pkg: grunt.file.readJSON('package.json')
    }
    grunt.loadTasks('build/grunt');
    grunt.util._.extend(config, loadConfig('./build/grunt/'));
    grunt.initConfig(config);

    // Load tasks
    require('load-grunt-tasks')(grunt);

	// Register Task(s)
	grunt.registerTask('default', [
		'tests'
	]);


	grunt.registerTask('tests', [
        'phplint',
		'phpunit',
        'phpcs'
	]);

};
