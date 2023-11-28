$(function(){

	// ##################  On load  ################## 

	 insertDate ();
	 maketablesortable();

	// ##################  Entry form validation  ################## 
	$("#newentry").validate({
							
		  highlight: function(element, errorClass) {
			$(element).css({ border: '1px solid #ff0', backgroundColor: '#ff0'});
		  },
		  unhighlight: function(element, errorClass) {
			$(element).css({ border: '1px solid #fff', backgroundColor: '#fff'});
		  },
		  
		   rules: {
			 Client: {
			   require_from_group: [1,"#Client"]
			 },
			 EntryDate: {
			   require_from_group: [1,"#EntryDate"],
			   date: true
			 },
			 Originator: {
			   require_from_group: [1,"#Originator"]
			 },
			 NewSlides: {
			   require_from_group: [1,".workdone"],
			   digits: true
			 	},
			 EditedSlides: {
			   require_from_group: [1,".workdone"],
			   digits: true
			 	},
			 Hours: {
			   require_from_group: [1,".workdone"],
			   digits: true
			 	}

		   },			 
			errorPlacement: function(error, element) {
				offset = element.offset();
				error.insertBefore(element);
				error.addClass('message');  // add a class to the wrapper
				error.css('position', 'absolute');
				error.css('left', offset.left);
				error.css('top', offset.top+20);
			}


	});
	$.validator.messages.required = "Required";
	$.validator.messages.digits = "Digits only";
	$.validator.messages.date = "Valid date required";




	// ##################  Delete button behavior  ################## 
	$('.button a.delete').click ( function () {
		$(this).parent('td').siblings().removeClass("rowhighlight");
		$(this).parent('td').removeClass("rowhighlight");
		

		$(this).parent('td').siblings().addClass("rowdelete");
		$(this).parent('td').addClass("rowdelete");
		var answer = confirm("Delete entry?")
		if (!(answer)){
			$(this).parent('td').siblings().removeClass("rowdelete");
			$(this).parent('td').removeClass("rowdelete");
			return false;				   
		}
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
		$("#TopContent table").tablesorter({ 
				// ################## Zebra widget adds proper shading to sorted table
				widgets: ['zebra'],
				// ################## Sort by date
			   headers:
			   {  
				 1 : { sorter: "usLongDate"  },
				 7 : { sorter: false   },
				 8 : { sorter: false   }
			   },
				sortList: [[1,1]]

		}); 
		
		
		// ################## Pad left table headers to make space for sorting arrows
		$("#currententries thead tr th.header").css("padding-left","2em");
		
		$('th.header').hover(
  			function () {
				$(this).addClass("tableheaderhighlight");
			},
 			function () {
				$(this).removeClass("tableheaderhighlight");
			}
		); 
	}



// ##################  Insert current date into form  ################## 
	function insertDate ()
	
	{
		//Insert current date into form   
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
		$('#DateIn').val(date+"/"+lmonth+"/"+year);
	
	}




// ################## ensure that the user fills in at least one of a group of fields ##################


jQuery.validator.addMethod("require_from_group", function(value, element, options) {
    numberRequired = options[0];
    selector = options[1];
    //Look for our selector within the parent form
    var validOrNot = $(selector, element.form).filter(function() {
         // Each field is kept if it has a value
         return $(this).val();
         // Set to true if there are enough, else to false
      }).length >= numberRequired;

    //The elegent part - this element needs to check the others that match the
    //selector, but we don't want to set off a feedback loop where all the
    //elements check all the others which check all the others which
    //check all the others...
    //So instead we
    //  1) Flag all matching elements as 'currently being validated'
    //  using jQuery's .data()
    //  2) Re-run validation on each of them. Since the others are now
    //     flagged as being in the process, they will skip this section,
    //     and therefore won't turn around and validate everything else
    //  3) Once that's done, we remove the 'currently being validated' flag
    //     from all the elements
    if(!$(element).data('being_validated')) {
    var fields = $(selector, element.form);
    //.valid() means "validate using all applicable rules" (which 
    //includes this one)
    fields.data('being_validated', true).valid();
    fields.data('being_validated', false);
    }
    return validOrNot;
     }, jQuery.format("Required"));


