$(function(){

	// ##################  On load  ################## 


	insertDates ();

	 maketablesortable();
	 
	$("tr.currrent").css("background-color","red");


	// ##################  Entry form validation  ################## 
	$("#ReportFilter").validate({
							
		  
		   rules: {
			 Client: {
			   required: true,
			 	},
			 ReportStartDate: {
			   required: true,
			   date: true
			 	},
			 ReportEndDate: {
			   required: true,
			   date: true
			 	}
		   },			 
			errorPlacement: function(error, element) {
				offset = element.offset();
				error.insertBefore(element);
				error.addClass('message');  // add a class to the wrapper
				error.css('position', 'absolute');
				error.css('left', offset.left-13);
				error.css('top', offset.top+12);
			},

		  highlight: function(element, errorClass) {
			$(element).css({ backgroundColor: '#ff0'});
		  },
		  unhighlight: function(element, errorClass) {
			$(element).css({backgroundColor: '#fff'});
		  }

	});
	$.validator.messages.required= "Required";
	$.validator.messages.date = "Valid date required";




	// ##################  Delete button behavior  ################## 
	$('.button a.delete').click ( function () {
		$(this).parent('td').siblings().removeClass("rowhighlight");
		$(this).parent('td').removeClass("rowhighlight");
		

		$(this).parent('td').siblings().addClass("rowdelete");
		$(this).parent('td').addClass("rowdelete");
		var answer = confirm("Trash entry?")
		if (answer){
			formdata= "id="+$(this).attr("href");
			$.ajax({ 
					type:"POST",
					url: BaseUrl+'tracking/ajax_trashentrynew ',
					data:formdata,
					success: function(data) {
						location.reload();
					},
					error: function(data) {
						alert (data);
						}
				});		

		}
		return false;
	});//
	
	

	

	// ##################  Row highlight on hover ################## 
	$('tr').hover( 
		  function () {
			$(this).children('td').siblings().addClass("rowhighlight");
		  },
		  function () {
			$(this).children('td').siblings().removeClass("rowhighlight");
		  }
	);
	
// ##################  End $(function(){  ################## 
});


// ################## Make Honorees table sortable ##################
	function maketablesortable()
	{	
		//################## Activate table sorting via plugin
		$("#pageOutput table").tablesorter({ 
				// ################## Zebra widget adds proper shading to sorted table
				widgets: ['zebra'],
				// ################## Sort by date
			   headers:
			   {  
				 1 : { sorter: "shortDate"},
				 3: { sorter: "digit"   },
				 4 : { sorter: "digit" },
				 5 : { sorter: "digit"  },
				 7 : { sorter: "false"},
				 8 : { sorter: "false" }
			   },
				//sortList: [[1,1]]

		}); 
		
		
		// ################## Pad left table headers to make space for sorting arrows
		$("#pageOutput table thead tr th.header").css("padding-left","2em");
		
		$('th.header').hover(
  			function () {
				$(this).addClass("tableheaderhighlight");
			},
 			function () {
				$(this).removeClass("tableheaderhighlight");
			}
		); 
	}



// ##################  Insert current month dates into form  ################## 
	function insertDates ()
	
	{
		//Insert current month dates into form   
		var months=new Array(13);
		months[1]="January";
		months[2]="February";
		months[3]="March";
		months[4]="April";
		months[5]="May";
		months[6]="June";
		months[7]="July";
		months[8]="August";
		months[9]="September";
		months[10]="October";
		months[11]="November";
		months[12]="December";
		var time=new Date();
		var lmonth=months[time.getMonth() + 1];
		var date=time.getDate();
		var year=time.getYear();
		if (year < 2000)
		year = year + 1900;   
		if ($('#ReportEndDate').val()=="" ) {
			$('#ReportEndDate').val(date+"/"+lmonth+"/"+year);
		}
		if ($('#ReportStartDate').val()=="" ) {
			$('#ReportStartDate').val(1+"/"+lmonth+"/"+year);
		}
	}






