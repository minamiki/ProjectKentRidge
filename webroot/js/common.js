// Common Scripts

function goToURL(loc){
	self.location = loc;
	return false;
}

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