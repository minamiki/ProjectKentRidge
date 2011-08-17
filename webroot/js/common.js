/***************************************************************************
 *Common Scripts: consists of common functions which will be used through out the programme
 *	-gotoURL(loc)
 *	-featureUnavailable()
 ***************************************************************************/

/*************************************
 *going to the wanted URL
 *************************************/
function goToURL(loc){
	self.location = loc;
	return false;
}

/*************************************
 * When a feature are not available, display a message informing the user
 *************************************/
function featureUnavailable(){
	$("#dialog-message").dialog({
		modal: true,
		buttons: {
			Ok: function() {
				$( this ).dialog( "close" );
			}
		}
	});
}