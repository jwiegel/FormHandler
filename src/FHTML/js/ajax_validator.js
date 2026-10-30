/**
 * AJAX validation of FormHandler fields (see includes/class.AjaxValidator.php)
 */

/**
 * Validate the field when the event (blur, change) happens
 */
function FH_OBSERVE( event, validator, field, path, msg )
{
	var observe = function()
	{
		var input = document.getElementById( field );
		if ( input )
		{
			input.addEventListener( event, function() { FH_CHECK( validator, field, path, msg ); } );
		}
	};

	if ( document.readyState === 'loading' )
	{
		document.addEventListener( 'DOMContentLoaded', observe );
	}
	else
	{
		observe();
	}
}

/**
 * Validate the field on the server and show the result in its error box
 */
function FH_CHECK( validator, field, path, msg )
{
	var input = document.getElementById( field );
	var box = document.getElementById( 'error_' + field );
	if ( !input || !box )
	{
		return;
	}

	var pars = new URLSearchParams();
	pars.append( 'validator', validator );
	pars.append( 'value', input.value );
	pars.append( 'msg', msg );

	fetch( path.replace( /\/$/, '' ) + '/ajax/validate.php', { method: 'POST', body: pars } )
		.then( function( response ) { return response.json(); } )
		.then( function( answer )
		{
			box.classList.remove( 'fh_error', 'fh_ok', 'fh_mandatory' );
			box.classList.add( 'fh_' + answer.status );
			input.classList.toggle( 'error', answer.status === 'error' );
			box.textContent = answer.status === 'error' ? answer.msg : ( answer.status === 'ok' ? ' ' : '' );
		} )
		.catch( function() {} );
}

/**
 * The function of FormHandler 4.6 and older (includedir is not used)
 */
function FH_VALIDATE( validator, field, error, path, includedir, msg )
{
	FH_CHECK( validator, field, path, msg );
}
