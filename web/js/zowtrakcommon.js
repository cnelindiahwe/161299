$(function(){

	// ##################  On load  ################## 
		// Table headers
		$('th.header').hover(
  			function () {
				$(this).addClass("tableheaderhighlight");
			},
 			function () {
				$(this).removeClass("tableheaderhighlight");
			}
		); 

		// Page switcher
		resetPageswitcher();
// ##################  End $(function(){  ################## 
});

	function resetPageswitcher()
	{	
		$('#pageswitchersubmit').hide();
		$('#pageswitcherselect').change( function () {
			var selecteditem=$('#pageswitcherselect option:selected').val();
			
  			if (selecteditem!=""){
  					window.location=BaseUrl+selecteditem;
  			}
			return false;
		}); 
	

	}

