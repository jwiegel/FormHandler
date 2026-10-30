/**
 * The suggestions of setAutoComplete() and setAutoCompleteAfter(): a list under the text
 * field in the font of the field (class fh_autocomplete, a page can change its look).
 * The values come from the datalist of the field; without javascript the browser shows
 * that datalist itself. setAutoCompleteAfter() completes the text after a character
 * (e.g. "@").
 */
( function()
{
	var MAX_ITEMS = 10;

	function addStyle()
	{
		// first in the head, so the page can change it
		var style = document.createElement( 'style' );
		style.textContent =
			'.fh_autocomplete{position:absolute;z-index:1000;margin:0;padding:2px 0;list-style:none;' +
			'background:#fff;color:#222;border:1px solid #aaa;border-radius:3px;' +
			'box-shadow:0 3px 8px rgba(0,0,0,.15);max-height:16em;overflow-y:auto;box-sizing:border-box}' +
			'.fh_autocomplete li{padding:3px 8px;cursor:pointer;white-space:nowrap}' +
			'.fh_autocomplete li[aria-selected=true],.fh_autocomplete li:hover{background:#dbe6f4}' +
			'.fh_autocomplete strong{font-weight:bold}';
		document.head.insertBefore( style, document.head.firstChild );
	}

	function setup( input, list )
	{
		var values = list.hasAttribute( 'data-fh-values' )
			? JSON.parse( list.getAttribute( 'data-fh-values' ) )
			: Array.prototype.map.call( list.options, function( option ) { return option.value; } );
		var after = list.getAttribute( 'data-fh-after' );

		var menu = document.createElement( 'ul' );
		menu.className = 'fh_autocomplete';
		menu.id = list.id + '_menu';
		menu.setAttribute( 'role', 'listbox' );
		menu.hidden = true;
		document.body.appendChild( menu );

		// the own list instead of the one of the browser
		input.removeAttribute( 'list' );
		input.setAttribute( 'autocomplete', 'off' );
		input.setAttribute( 'role', 'combobox' );
		input.setAttribute( 'aria-autocomplete', 'list' );
		input.setAttribute( 'aria-controls', menu.id );
		input.setAttribute( 'aria-expanded', 'false' );

		var items = [];
		var active = -1;

		// the values for the text: first those which start with it, then those which contain it
		function find( all )
		{
			var text = input.value;
			var prefix = '';
			if ( after !== null )
			{
				var pos = text.indexOf( after );
				if ( pos < 0 )
				{
					return [];
				}
				prefix = text.substring( 0, pos + after.length );
			}
			var search = text.substring( prefix.length ).toLowerCase();
			if ( search === '' && !all && after === null )
			{
				return [];
			}

			var start = [];
			var inside = [];
			values.forEach( function( value )
			{
				var pos = value.toLowerCase().indexOf( search );
				var item = { value: prefix + value, mark: prefix.length + pos, length: search.length };
				if ( pos === 0 )
				{
					start.push( item );
				}
				else if ( pos > 0 && after === null )
				{
					inside.push( item );
				}
			} );

			return start.concat( inside ).slice( 0, MAX_ITEMS );
		}

		function open( all )
		{
			items = find( all );
			active = -1;
			menu.textContent = '';

			// nothing to suggest, or the text is already the only suggestion
			if ( items.length === 0 || ( items.length === 1 && items[0].value === input.value ) )
			{
				close();
				return;
			}

			items.forEach( function( item, i )
			{
				var li = document.createElement( 'li' );
				li.id = menu.id + '_' + i;
				li.setAttribute( 'role', 'option' );
				li.setAttribute( 'aria-selected', 'false' );

				// the typed part in bold
				if ( item.length > 0 )
				{
					var mark = document.createElement( 'strong' );
					mark.textContent = item.value.substr( item.mark, item.length );
					li.append( item.value.substring( 0, item.mark ), mark, item.value.substring( item.mark + item.length ) );
				}
				else
				{
					li.textContent = item.value;
				}

				// mousedown: before the field loses the focus
				li.addEventListener( 'mousedown', function( event )
				{
					event.preventDefault();
					choose( i );
				} );
				menu.appendChild( li );
			} );

			var rect = input.getBoundingClientRect();
			var css = window.getComputedStyle( input );
			menu.style.left = ( rect.left + window.scrollX ) + 'px';
			menu.style.top = ( rect.bottom + window.scrollY + 2 ) + 'px';
			menu.style.minWidth = rect.width + 'px';
			menu.style.fontFamily = css.fontFamily;
			menu.style.fontSize = css.fontSize;
			menu.hidden = false;
			input.setAttribute( 'aria-expanded', 'true' );
		}

		function close()
		{
			menu.hidden = true;
			active = -1;
			input.setAttribute( 'aria-expanded', 'false' );
			input.removeAttribute( 'aria-activedescendant' );
		}

		function highlight( i )
		{
			if ( active >= 0 )
			{
				menu.children[ active ].setAttribute( 'aria-selected', 'false' );
			}
			active = i;
			menu.children[ i ].setAttribute( 'aria-selected', 'true' );
			menu.children[ i ].scrollIntoView( { block: 'nearest' } );
			input.setAttribute( 'aria-activedescendant', menu.children[ i ].id );
		}

		function choose( i )
		{
			input.value = items[ i ].value;
			close();
			input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
		}

		input.addEventListener( 'input', function() { open( false ); } );
		input.addEventListener( 'blur', close );
		input.addEventListener( 'keydown', function( event )
		{
			if ( menu.hidden )
			{
				if ( event.key === 'ArrowDown' )
				{
					event.preventDefault();
					open( true );
				}
				return;
			}

			switch ( event.key )
			{
				case 'ArrowDown':
					event.preventDefault();
					highlight( ( active + 1 ) % items.length );
					break;
				case 'ArrowUp':
					event.preventDefault();
					highlight( active <= 0 ? items.length - 1 : active - 1 );
					break;
				case 'Enter':
					// only take the suggestion, do not send the form
					if ( active >= 0 )
					{
						event.preventDefault();
						choose( active );
					}
					break;
				case 'Escape':
					event.preventDefault();
					close();
					break;
				case 'Tab':
					close();
					break;
			}
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function()
	{
		addStyle();
		document.querySelectorAll( 'input[list]' ).forEach( function( input )
		{
			var list = input.list;
			if ( list && list.hasAttribute( 'data-fh-autocomplete' ) )
			{
				setup( input, list );
			}
		} );
	} );
} )();
