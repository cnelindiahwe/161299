
$(function(){
	timezoneadd ();
	processform();
})

function timezoneadd () {
	var timezone = jzTimezoneDetector.determine_timezone().timezone; // Now you have an instance of the TimeZone object.
	$('#loginbox form fieldset').append  ('<input type="hidden" id="login_timezone" name="login_timezone" value="'+timezone.olson_tz+'"/>');
	$('body').append  ('<div id="msgbox"></div>');
}

function processform () {
	$('#loginbox form').submit  ( function(){
		if ($("#who").val()=="" || $("#which").val()=="" ) {
			
			$('#msgbox').empty().append('You must fill in both values');
		
		} else {
			$('#msgbox').empty();
			urlform=$("#loginbox form").attr("action");
			formdata=$("#loginbox form").serialize();
			$.ajax({
			 type:"POST",
			 url: urlform,
			 data:formdata,
			 success: function(data) {
			 	if (data.slice(0,5) == "Error") {
			    	$('#msgbox').empty().append(data);
			  	} else {
			  		document.location.reload(true);
			  	}
			  }
			});
		}
		return false;
	});
}