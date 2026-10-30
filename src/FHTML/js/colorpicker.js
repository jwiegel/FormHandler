/**
 * The color picker of FormHandler (see fields/class.ColorPicker.php): the color input of the
 * browser fills the text field with the hex value (FFCC00), the text field moves the color
 */
document.addEventListener( 'DOMContentLoaded', function()
{
	document.querySelectorAll( 'input[type=color][data-fh-color]' ).forEach( function( picker )
	{
		var text = document.getElementById( picker.getAttribute( 'data-fh-color' ) );
		if ( !text )
		{
			return;
		}

		picker.addEventListener( 'input', function()
		{
			text.value = picker.value.substring( 1 ).toUpperCase();
		} );
		text.addEventListener( 'input', function()
		{
			var hex = text.value.replace( /^#/, '' );
			if ( /^[0-9a-fA-F]{6}$/.test( hex ) )
			{
				picker.value = '#' + hex.toLowerCase();
			}
		} );
	} );
} );
