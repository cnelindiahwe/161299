$(function(){
	
		//client dropdown
 		$('#client_dropdown_selector_submit').hide();
		$("#client_dropdown_selector").change ( function () {
				$("#client_dropdown_form").submit();
			}
		);


		//quotation form
		$('#Client_Quotation_Submit').hide();
	
		$("#New_Slides").on('input', function() {
			var NewTotal= this.value/5
			$("#new-billed-hours").html(NewTotal+ '  final hours')
			updatePage()
			}
		);	
	
		$("#Edited_Slides").on('input', function() {
			var EditsTotal= this.value/10
			$("#edits-billed-hours").html(EditsTotal+ '  final hours')
			updatePage()
			}
		);
	
		$("#Additional_Hours").on('input', function()  {
			$("#additional-billed-hours").html(this.value + '  final hours')
			updatePage()
			}
		);		
	
});   //$(function()     


function updatePage() {

	var NewTotal = Number($("#New_Slides").val())/5
	var EditsTotal = Number($("#Edited_Slides").val())/10
	var AdditionalTotal = Number($("#Additional_Hours").val())
	var updatedtotal = Number(NewTotal+EditsTotal+AdditionalTotal).toFixed(2)
	//updatedtotal = Number(updatedtotal).toFixed(2)

	
	var rawclientprice = $("#client-price").html()
	var clientprice = rawclientprice.replace("(", "");
	clientprice = clientprice.replace(" per hour)", "");
	const clientpricearray = clientprice.split(" ");
	var updatedtotalprice =Number(clientpricearray[0]*updatedtotal).toFixed(2)

    var updatedtotaltext= updatedtotal +' Total hours at ' + clientpricearray[0] + " " + clientpricearray[1] + ' = ' + updatedtotalprice + " " + clientpricearray[1]
	
	
	$("#total-billed-hours h4").html(updatedtotaltext)


	
	if (updatedtotal > 0) {
		var TemplateText=""
		var TemplateTextFlag=0
		
		TemplateText="Dear ,\n\n"
		TemplateText+="Regarding costs, the deck has:\n\n"		
	
		if (NewTotal  > 0) {
			TemplateText+=$("#New_Slides").val() + " new or complex slides at 5 slides per hour = " + NewTotal + " billable hours\n"
			TemplateTextFlag=1
		}
				
		if (EditsTotal  > 0) {
			if (TemplateTextFlag ==1) {TemplateText+="and\n"}
			TemplateText+=$("#Edited_Slides").val() + " simple slides at 10 slides per hour = " + EditsTotal + " billable hours\n"
			TemplateTextFlag=1
		}
		
		if (AdditionalTotal  > 0) {
			if (TemplateTextFlag ==1) {TemplateText+="and\n"}
			TemplateText+= AdditionalTotal + " additional hour = " + AdditionalTotal + " billable hours\n"
		}

		TemplateText+="\n";

		TemplateText+="The total is "+ updatedtotal +' hours at ' + clientpricearray[0] + " " + clientpricearray[1] + ' = ' + updatedtotalprice + " " + clientpricearray[1]	
		
		$("#quotation-template").html(TemplateText)

		
		
	} else{
	
		$("#quotation-template").html("")
	
	}
	

	
}