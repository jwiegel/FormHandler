/**
 * linkSelectFields() of FormHandler: when a select field changes, the options of the next
 * one are loaded from a script of the project. The script answers JSON, e.g.
 * [["1", "Text"], ["2", "Other"]] or ["Text", "Other"], or (like before 4.7) javascript
 * which sets the variable "options".
 */

// a field by its id (a select field with more values has the id "name[]")
function GetElement( id )
{
	return document.getElementById( id ) || document.getElementById( id + '[]' );
}

// load the options: the script "filename" gets the params (POST), the options go into
// the field "buffer"; then fnAttach( fnArgs ) is run (the next linked field)
function loadexternal( filename, params, buffer, fnAttach, fnArgs )
{
	fetch( filename, {
		method: 'POST',
		headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
		body: params
	} )
		.then( function( response ) { return response.text(); } )
		.then( function( text ) { displayExternal( { responseText: text }, buffer, fnAttach, fnArgs ); } )
		.catch( function( error ) { alert( 'Could not load dynamic values!\nError: ' + error.message ); } );
}

// put the received options into the field
function displayExternal( oXML, buffer, fnAttach, fnArgs )
{
	var field = GetElement( buffer );
	if ( !field )
	{
		return;
	}

	// the value to select in this field
	var value = Array.isArray( fnArgs ) && fnArgs.length > 0 ? fnArgs.shift() : null;

	var text = oXML.responseText.trim();
	if ( text !== '' )
	{
		try
		{
			// JSON, or the javascript of older scripts which sets "options"
			var options = /^[\[{]/.test( text )
				? JSON.parse( text )
				: new Function( 'var options = null;\n' + text + '\nreturn options;' )();

			if ( options != null )
			{
				loadOptions( field, options, value );
			}
		}
		catch ( error )
		{
			alert(
				'Could not load dynamic values!\n' +
				'Error: ' + error.message + '\n' +
				'Received data: \n' +
				'----------------------------\n' +
				text
			);
		}
	}

	// run the function given by the user (if given)
	if ( fnAttach )
	{
		fnAttach( fnArgs );
	}
}

// set the new options in the field: a string is value and text, an array [value, text]
function loadOptions( oFld, aOptions, sValue )
{
	oFld.options.length = 0;

	aOptions.forEach( function( elem )
	{
		var option = null;
		if ( typeof elem === 'string' )
		{
			option = new Option( elem, elem );
		}
		else if ( Array.isArray( elem ) && elem.length >= 2 )
		{
			option = new Option( elem[1], elem[0] );
		}

		if ( option )
		{
			option.selected = option.value == sValue;
			oFld.add( option );
		}
	} );
}

// attach an event to an element
function attachelement( id, event, func )
{
	var el = GetElement( id );
	if ( el )
	{
		el.addEventListener( event, func );
	}
}
