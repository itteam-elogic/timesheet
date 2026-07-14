/**
 * Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-oss-license
 */

/* exported initSample */

if ( CKEDITOR.env.ie && CKEDITOR.env.version < 9 )
	CKEDITOR.tools.enableHtml5Elements( document );

// The trick to keep the editor in the sample quite small
// unless user specified own height.
CKEDITOR.config.height = 100;
CKEDITOR.config.width = 'auto';

var initSample = ( function() {
	var wysiwygareaAvailable = isWysiwygareaAvailable(),
		isBBCodeBuiltIn = !!CKEDITOR.plugins.get( 'bbcode' );

	return function() {
		var scope_of_the_work = CKEDITOR.document.getById( 'scope_of_the_work' );
        var deliverables = CKEDITOR.document.getById( 'deliverables' );
        var provided_by_client_info = CKEDITOR.document.getById( 'provided_by_client_info' );
        var est_remarks = CKEDITOR.document.getById( 'est_remarks' );

		// :(((
		if ( isBBCodeBuiltIn ) {
			editorElement.setHtml(
				'Hello world!\n\n' +
				'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
			);
		}

		// Depending on the wysiwygarea plugin availability initialize classic or inline editor.
		if ( wysiwygareaAvailable ) {
			CKEDITOR.replace( 'scope_of_the_work' );
            CKEDITOR.replace( 'deliverables' );
            CKEDITOR.replace( 'provided_by_client_info' );
            CKEDITOR.replace( 'est_remarks' );
           
		} else {
			scope_of_the_work.setAttribute( 'contenteditable', 'true' );
            deliverables.setAttribute( 'contenteditable', 'true' );
            provided_by_client_info.setAttribute( 'contenteditable', 'true' );
            est_remarks.setAttribute( 'contenteditable', 'true' );
			CKEDITOR.inline( 'scope_of_the_work' );
            CKEDITOR.inline( 'deliverables' );
            CKEDITOR.inline( 'provided_by_client_info' );
            CKEDITOR.inline( 'est_remarks' );
            // TODO we can consider displaying some info box that
			// without wysiwygarea the classic editor may not work.
		}
	};

	function isWysiwygareaAvailable() {
		// If in development mode, then the wysiwygarea must be available.
		// Split REV into two strings so builder does not replace it :D.
		if ( CKEDITOR.revision == ( '%RE' + 'V%' ) ) {
			return true;
		}

		return !!CKEDITOR.plugins.get( 'wysiwygarea' );
	}
} )();

