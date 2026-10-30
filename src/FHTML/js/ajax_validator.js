function FH_VALIDATE( validator, field, error, path, includedir, msg )
{
	var url = path + '/ajax/validate.php';
	// includedir is not used any more (the server finds its classes itself)
	var pars = 'value=' + encodeURIComponent( $('#'+field).val() );
	pars += '&field=' + encodeURIComponent( field );
	pars += '&validator=' + encodeURIComponent( validator );
	pars += '&msg=' + encodeURIComponent( msg );
	pars += '&msgbox=' + encodeURIComponent( 'error_' + error );
	var target = '#error_'+ error;
	$(target).load(url,pars);
}