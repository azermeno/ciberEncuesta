/*
 Copyright (c) 2007-2017, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.html or http://cksource.com/ckfinder/license
 */

var config = {};

// Set your configuration options below.

// Examples:
 //config.language = 'es';
// config.skin = 'jquery-mobile';
var loc = window.location;
var pathName = loc.pathname.substring(0, loc.pathname.lastIndexOf('/') + 1);
var rutaAbsoluta =  loc.href.substring(0, loc.href.length - ((loc.pathname + loc.search + loc.hash).length - pathName.length));

$baseUrl = rutaAbsoluta + 'ckeditor/samples/ckfinder/userfiles/';

config.filebrowserBrowseUrl = 'ckfinder/ckfinder.html',
config.filebrowserImageBrowseUrl = 'ckfinder/ckfinder.html?type=Images',
config.filebrowserFlashBrowseUrl = 'ckfinder/ckfinder.html?type=Flash',
config.filebrowserUploadUrl = 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
config.filebrowserImageUploadUrl = 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
config.filebrowserFlashUploadUrl = 'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash',
config.htmlEncodeOutput = true;

CKFinder.define( config );
