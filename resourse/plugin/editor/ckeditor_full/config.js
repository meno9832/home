/**
 * @license Copyright (c) 2003-2022, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	
	
	
	
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.uiColor = '#F2F2F2';
	// https://ckeditor.com/latest/samples/old/toolbar/toolbar.html	
	config.toolbar = [
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ], items: [ 'Source', '-','CodeSnippet','-','NewPage', 'Print', '-', 'Templates' ] },
		{ name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
		{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy',  '-', 'Undo', 'Redo' ] },
		'/',
		{ name: 'insert', items: [ 'Image','ImageSlider', 'Flash', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak'] },
		{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-'] },
		{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },
		'/',
		{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
		{ name: 'forms', items: [ ] },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
		{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ], items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ] },
		{ name: 'others', items: [ '-' ] },
		{ name: 'about', items: [ 'About' ] }
	];
	

	
	
	config.allowedContent = true;
	config.height = 400;
	config.removePlugins = 'easyimage, cloudservices, exportpdf';
	
	config.extraPlugins = 'imagepaste,codesnippet,imageslider';
	config.codeSnippet_theme = 'tomorrow-night-bright';	


	//config.removeButtons = 'PasteText,PasteFromWord,Paste,Save,Preview,Checkbox,Scayt,Form, Checkbox, Radio, TextField, Textarea, Select, Button, ImageButton, HiddenField';

	// Set the most common block elements.
	config.format_tags = 'p;h1;h2;h3;pre';

	// Simplify the dialog windows.
	// config.removeDialogTabs = 'image:advanced;link:advanced';
	
	config.filebrowserUploadUrl = g5_editor_url+"/imageUpload/upload.php?mode=defualt";
	config.filebrowserImageUploadUrl = g5_editor_url+"/imageUpload/upload.php?mode=imagepaste";	
	config.font_defaultLabel = '나눔고딕'; // contents.css에 적용해야함 표시만 해줌 
	config.fontSize_defaultLabel = '14'; // contents.css에 적용해야함 표시만 해줌 

};
