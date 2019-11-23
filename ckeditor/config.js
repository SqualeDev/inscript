/**
 * @license Copyright (c) 2003-2018, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For complete reference see:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	// The toolbar groups arrangement, optimized for a single toolbar row.
	config.toolbarGroups = [
		{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		{ name: 'forms',       groups: [ 'forms' ] },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
		{ name: 'links',       groups: [ 'links' ] },
		{ name: 'insert',      groups: [ 'insert' ] },
		{ name: 'styles',      groups: [ 'styles' ] },
		{ name: 'colors',      groups: [ 'colors' ] },
		{ name: 'tools',       groups: [ 'tools' ] },
		{ name: 'others',      groups: [ 'others' ] },
		{ name: 'about',       groups: [ 'about' ] }
	];

	// The default plugins included in the basic setup define some buttons that
	// are not needed in a basic editor. They are removed here.
	config.removeButtons = 'Cut,Copy,Paste,Undo,Redo,Anchor,Strike,Subscript,Superscript,EasyImageUpload';

	// Dialog windows are also simplified.
	config.removeDialogTabs = 'link:advanced';

	// config.allowedContent = true;

	// config.allowedContent =
    // 'h1 h2 h3 p blockquote strong em;' +
    // 'a[!href];' +
    // 'img(left,right)[!src,alt,width,height];';
};
