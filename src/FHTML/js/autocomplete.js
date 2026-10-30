/**
 * setAutoCompleteAfter() of FormHandler: while typing, the datalist of the field gets the
 * text up to the character (e.g. "@") followed by every value which starts with the text
 * after it. (setAutoComplete() needs no javascript: the datalist has the values.)
 */
document.addEventListener( 'input', function( event )
{
	var input = event.target;
	var list = input.list;
	if ( !list || !list.hasAttribute( 'data-fh-after' ) )
	{
		return;
	}

	var after = list.getAttribute( 'data-fh-after' );
	var values = JSON.parse( list.getAttribute( 'data-fh-values' ) || '[]' );
	var pos = input.value.indexOf( after );

	list.textContent = '';
	if ( pos < 0 )
	{
		return;
	}

	var prefix = input.value.substring( 0, pos + after.length );
	var rest = input.value.substring( pos + after.length ).toLowerCase();
	values.forEach( function( value )
	{
		if ( value.toLowerCase().indexOf( rest ) === 0 )
		{
			var option = document.createElement( 'option' );
			option.value = prefix + value;
			list.appendChild( option );
		}
	} );
} );
