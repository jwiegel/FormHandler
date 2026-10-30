/**
 * The calendar of jsDateField() and jsDateTextField(): the date picker of the browser.
 * A hidden date input next to the calendar icon is opened, the chosen date fills the
 * date fields. cal_<field> = new FH_DatePicker(...) replaces the CalendarPopup of
 * FormHandler 4.6 and older; setReturnFunction() and LZ() still work.
 */
function FH_DatePicker( min, max, onSelect )
{
	var self = this;

	this.returnFunction = null;
	this.onSelect = onSelect || null;

	this.input = document.createElement( 'input' );
	this.input.type = 'date';
	this.input.tabIndex = -1;
	this.input.setAttribute( 'aria-hidden', 'true' );
	this.input.style.cssText = 'position:absolute;width:1px;height:1px;margin:0;padding:0;border:0;opacity:0;';
	if ( min )
	{
		this.input.min = min;
	}
	if ( max )
	{
		this.input.max = max;
	}

	this.input.addEventListener( 'change', function()
	{
		var parts = self.input.value.split( '-' );
		if ( parts.length !== 3 )
		{
			return;
		}

		var y = parseInt( parts[0], 10 );
		var m = parseInt( parts[1], 10 );
		var d = parseInt( parts[2], 10 );
		var returnFunction = self.returnFunction ? window[ self.returnFunction ] : null;

		if ( typeof returnFunction === 'function' )
		{
			returnFunction( y, m, d );
		}
		else if ( self.onSelect )
		{
			self.onSelect( y, m, d );
		}
	} );
}

/**
 * The name of a global function ( y, m, d ) which gets the chosen date
 */
FH_DatePicker.prototype.setReturnFunction = function( name )
{
	this.returnFunction = name;
};

/**
 * Open the date picker next to the anchor, with the date (yyyy-mm-dd) or today
 */
FH_DatePicker.prototype.show = function( anchor, value )
{
	// the browser opens the picker at the date input: put it under the calendar icon
	var rect = anchor.getBoundingClientRect();
	this.input.style.left = ( rect.left + window.scrollX ) + 'px';
	this.input.style.top = ( rect.bottom + window.scrollY ) + 'px';
	if ( this.input.parentNode !== document.body )
	{
		document.body.appendChild( this.input );
	}
	// the position must be known before the picker opens (else the first one opens in a corner)
	void this.input.getBoundingClientRect();

	this.input.value = value || '';

	try
	{
		this.input.showPicker();
	}
	catch ( e )
	{
		this.input.focus();
	}
};

/**
 * The date (yyyy-mm-dd) of the three fields of a jsDateField, '' without year or month
 */
FH_DatePicker.fromFields = function( form, year, month, day )
{
	var frm = document.forms[ form ] || document.getElementById( form );
	if ( !frm )
	{
		return '';
	}

	var y = parseInt( frm.elements[ year ].value, 10 );
	var m = parseInt( frm.elements[ month ].value, 10 );
	var d = parseInt( frm.elements[ day ].value, 10 ) || 1;
	if ( !( y >= 1000 ) || !m )
	{
		return '';
	}

	return y + '-' + LZ( m ) + '-' + LZ( d );
};

/**
 * The date as text of a PHP date() mask (d, j, m, n, Y, y)
 */
FH_DatePicker.format = function( mask, y, m, d )
{
	return mask.replace( /[djmnYy]/g, function( c )
	{
		return { d: LZ( d ), j: d, m: LZ( m ), n: m, Y: y, y: LZ( y % 100 ) }[ c ];
	} );
};

/**
 * The date (yyyy-mm-dd) of a text in a PHP date() mask, '' when it is no date
 */
FH_DatePicker.parse = function( mask, text )
{
	var order = mask.replace( /[^djmnYy]/g, '' ).replace( 'j', 'd' ).replace( 'n', 'm' ).toLowerCase();
	var numbers = String( text ).match( /\d+/g );
	if ( order.length !== 3 || !numbers || numbers.length !== 3 )
	{
		return '';
	}

	var date = {};
	for ( var i = 0; i < 3; i++ )
	{
		date[ order.charAt( i ) ] = parseInt( numbers[ i ], 10 );
	}
	if ( date.y < 100 )
	{
		date.y += date.y <= 50 ? 2000 : 1900;
	}
	if ( !( date.y >= 1000 ) || !date.m || !date.d )
	{
		return '';
	}

	return date.y + '-' + LZ( date.m ) + '-' + LZ( date.d );
};

/**
 * A number with a leading zero (from CalendarPopup, return functions use it)
 */
function LZ( x )
{
	return ( x < 0 || x > 9 ? '' : '0' ) + x;
}
