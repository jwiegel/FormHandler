/**
 * The help texts of FormHandler (setHelpText()): a box next to the help icon while the
 * mouse is over it or it has the focus. The help text is HTML, the title is text.
 */
( function()
{
	var box = null;

	function addStyle()
	{
		// first in the head, so the page can change it with the class fh_help_box
		var style = document.createElement( 'style' );
		style.textContent =
			'.fh_help_box{position:absolute;z-index:1000;max-width:300px;border:1px solid #666666;' +
			'background:#CCCCCC;color:#666666;font:12px Verdana,sans-serif}' +
			'.fh_help_box .fh_help_title{background:#666666;color:#FFFFFF;padding:2px 8px}' +
			'.fh_help_box .fh_help_text{padding:8px}';
		document.head.insertBefore( style, document.head.firstChild );
	}

	function show( icon )
	{
		hide();

		var title = document.createElement( 'div' );
		title.className = 'fh_help_title';
		title.textContent = icon.getAttribute( 'data-fh-help-title' ) || '';

		var text = document.createElement( 'div' );
		text.className = 'fh_help_text';
		text.innerHTML = icon.getAttribute( 'data-fh-help' );

		box = document.createElement( 'div' );
		box.className = 'fh_help_box';
		box.setAttribute( 'role', 'tooltip' );
		box.appendChild( title );
		box.appendChild( text );
		document.body.appendChild( box );

		var rect = icon.getBoundingClientRect();
		box.style.left = ( rect.right + window.scrollX + 8 ) + 'px';
		box.style.top = ( rect.top + window.scrollY ) + 'px';
	}

	function hide()
	{
		if ( box )
		{
			box.remove();
			box = null;
		}
	}

	function helpIcon( event )
	{
		return event.target.closest ? event.target.closest( '[data-fh-help]' ) : null;
	}

	document.addEventListener( 'DOMContentLoaded', addStyle );
	document.addEventListener( 'mouseover', function( event ) { var icon = helpIcon( event ); if ( icon ) show( icon ); } );
	document.addEventListener( 'mouseout', function( event ) { if ( helpIcon( event ) ) hide(); } );
	document.addEventListener( 'focusin', function( event ) { var icon = helpIcon( event ); if ( icon ) show( icon ); } );
	document.addEventListener( 'focusout', function( event ) { if ( helpIcon( event ) ) hide(); } );
	document.addEventListener( 'keydown', function( event ) { if ( event.key === 'Escape' ) hide(); } );
} )();
