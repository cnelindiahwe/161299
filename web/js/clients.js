$(function(){

	// ##################  On load  ################## 

	 doformatting();
	 automateform();



	// ##################  Row highlight on hover ################## 
	$('li').hover( 
		  function () {
			$(this).addClass("rowhighlight");
		  },
		  function () {
			$(this).removeClass("rowhighlight");
		  }
	);

// ##################  End $(function(){  ################## 
									  
});




// ################## Insert defaults into form ##################
	function  doformatting()
	{	
		$('.yearpile li span').hide();
		$('.yearpile ul').css('height','150px').css('overflow-y','auto');
		$("<div><a href=\"#\" class=\"morebutton\">Show full details</a></div>").insertBefore('.yearpile:eq(0)');
		$("<div><a href=\"#\" class=\"lessbutton\">Show less details</a></div>").insertBefore('.yearpile:eq(0)');
		
		clickshowfield();
	}

// ################## activate client selector  ##################

	 	function  automateform(){
			$("#clientcontrolsubmit").hide();
			$("#clientselector").change ( function () {
					$("#clientcontrol").submit();
				}
			);
		}
		
// ################## show/hide details ##################

	function  clickshowfield()
	{	
		$(".lessbutton").hide();
		$('.morebutton').click(function() {
			$(".lessbutton").show();
			$(".morebutton").hide();
			
			$('.yearpile li span').show();
			$('.yearpile ul').css('height','auto')
		});		
		$('.lessbutton').click(function() {
			$(".lessbutton").hide();
			$(".morebutton").show();	
			$('.yearpile li span').hide();
			$('.yearpile ul').css('height','150px').css('overflow-y','auto');
		});	

	}