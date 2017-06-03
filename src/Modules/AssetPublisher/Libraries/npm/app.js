/*
 * Demo of UI interaction with jQuery+Uniter
 *
 * MIT license.
 */
'use strict';

var $ = jQuery,
    phpjs = require('phpjs'),
    mainFiles = require('../../../PostInput/Assets/Modules/DefaultSkin/js/mainFiles.js'),
    hasOwn = {}.hasOwnProperty,
    uniter = require('uniter'),
    phpEngine = uniter.createEngine('PHP'),
    output = document.getElementById('output');

var file_require_string = 'require("/Assets/Modules/DefaultSkin/php/main.phpfe") ; ';
console.log("this pn", window.location.pathname) ;
if (window.location.pathname !== '/') {
    var current_page_module = getParameterByName('control') ;
    var mainpath = '/opt/ptbuild/ptbuild/src/Modules/'+current_page_module+'/Assets/php/main.phpfe' ;
    console.log("this main", mainpath) ;
    if (mainFiles.indexOf(mainpath) != -1) {
        var relative_url = '/Assets/Modules/'+current_page_module+'/php/main.phpfe' ;
        console.log("relative url: " + relative_url) ;
        file_require_string += 'require("'+relative_url+'") ; ';
    }

}

// console.log("file_require_string: " + file_require_string) ;
function getParameterByName(name, url) {
    if (!url) {
        url = window.location.href;
    }
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

function getFileData(path) {
    // if (!hasOwn.call(fileData, path)) {
    //     throw new Error('Unknown file "' + path + '"');
    // }
    // console.log("trying to load: ", path) ;
    var filedata ;
    // $.holdReady( true );
    $.ajax({
        url: path,
        dataType: 'text',
        async: false,
        success: function (data, textStatus, jqXHR) {
            filedata = jqXHR.responseText ;
            // $.holdReady( false );
        },
        failure: function (data, textStatus, jqXHR) {
            filedata = jqXHR.responseText ;
            // $.holdReady( false );
        },
    });
    return filedata;
}

// Set up a PHP module loader
phpEngine.configure({
    include: function (path, promise) {
        var fd = getFileData(path) ;
        // console.log("fd: " + fd) ;
        promise.resolve(fd);
    }
});

// Expose jQuery to PHPland
phpEngine.expose($, 'jQuery');
phpEngine.expose(phpjs, 'php');

// Expose Window to PHPland
var this_window = window ;
phpEngine.expose(this_window, 'window');

// Expose Window to PHPland
var this_console = console ;
phpEngine.expose(this_console, 'console');

// Expose Window to PHPland
var jsMath = Math ;
phpEngine.expose(jsMath, 'jsMath');

// Expose Window to PHPland
var jsDate = Date ;
phpEngine.expose(jsDate, 'Date');

// Write content HTML to the DOM
phpEngine.getStdout().on('data', function (data) {
    document.body.insertAdjacentHTML('beforeEnd', data);
});

var on_page_scripts = "" ;

[].forEach.call(document.querySelectorAll('script[type="text/x-uniter"]'), function (script) {
    on_page_scripts += script.textContent ;
    // phpEngine.execute('<?php ' + script.textContent).fail(function (error) {
    //     console.error(error);
    // });
});
var php_code_string = '<?php ' + file_require_string + on_page_scripts + ' ?>' ;

// Go!
phpEngine.execute(php_code_string).fail(function (error) {
    console.warn('ERROR: ' + error.toString());
});
// var phpEngine = uniter.createEngine('PHP');

phpEngine.getStdout().on('data', function (data) {
    document.body.insertAdjacentHTML('beforeEnd', data);
});

phpEngine.getStderr().on('data', function (data) {
    document.body.insertAdjacentHTML('beforeEnd', 'PHP error: ' + data);
});
