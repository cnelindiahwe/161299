$(function(){

	// ##################  On load  ################## 
	managerbutton();
	
// ##################  End onload  ################## 
});


	function managerbutton() {
	$('.zowtrakui-managerbar').hide();
	$('.zowtrakui-topbar').eq(0).append('<a href="#" id="managerbarview" style="float:right">Manager</a>');
	

		$("#managerbarview").click ( function () {
			$('.zowtrakui-managerbar').toggle('fast');
			return false;
			}
		);
	}

