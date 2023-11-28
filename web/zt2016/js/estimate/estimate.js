$(document).ready(function() {
var estimatselect = {};
    // estimate add feild
$('#addfeild').click(function(){
	const tableBody=$('#addTable tbody');
	let seletchtml = '';
    const rowCount=tableBody.find('tr').length+1;
	$('.rowcount').val(rowCount);
	for (var i = 0; i < estimatselect.length; i++) {
		seletchtml+= `<option value="${estimatselect[i]}">${estimatselect[i]}</option>`;
	}
    var addTable=`    <tr>
	<td class="row-number">${rowCount}</td>
	<td>
	<div class="cal-icon">
	<input class="form-control datetimepicker" type="text" name="date[]" required>
	</div>
	</td>
	<td>
		<select class="form-control originator" type="text" name="originator[]" required>${seletchtml}</select>
	</td>
	<td>
		<input class="form-control" type="text" name="filename[]" required>
	</td>
	<td>
		<input class="form-control newslides text-right" type="text" name="newslides[]" value=0 required>
	</td>
	<td>
		<input class="form-control editslides text-right" type="text" name="editslides[]" value=0 required>
	</td>
	<td >
		<input class="form-control hours text-right" type="text" name="hour[]" value=0 required>
	</td>
	
	<td style="width:3%"><a href="javascript:void(0)" class="text-danger font-18 remove-felid" title="Remove"><i class="fa fa-trash-o"></i></a></td>
	</tr>`;
	$('tbody.tbodyone').append(addTable);
	$('.datetimepicker').datetimepicker({
		format: 'DD/MM/YYYY',
		icons: {
			up: "fa fa-angle-up",
			down: "fa fa-angle-down",
			next: 'fa fa-angle-right',
			previous: 'fa fa-angle-left'
		}
	});
});

	if ($('#clientSelect').length) {
	$('#clientSelect').select2({
		placeholder: 'Search Client',
		allowClear: true
	});
	$('#clientSelect').val(null).trigger('change');
	$("#clientSelect").on("change", function() {
		var selectedCountry = $(this).find(":selected").data("country");
		var selectedAddress = $(this).find(":selected").data("address");
		var PricePerHour = $(this).find(":selected").data("priceperhour");
		var Currency = $(this).find(":selected").data("currency");
		var code = $(this).find(":selected").data("code");
		var email = $(this).find(":selected").data("email");
		var client = $(this).val();
		$.ajax({
            type: 'POST',
            url: baseUrl+'estimate/zt2016_estimateDataSave',
            data: { code: code, action:'quotationNumber',client:client },
            success: function (data) {
				var jsonData = JSON.parse(data);
				// console.log(jsonData.quotation)
				let quotationNumber = jsonData.quotation;
				let contactlist = jsonData.contactlist;
				estimatselect = contactlist;
				// let quotationnum = $('.quotationnum').text();
				$('.quotationnum').text('('+quotationNumber+')');
				$('.quotationnum').removeClass('d-none');
				$('.quotationNumber').val(quotationNumber);

				var originatorSelect = $('.originator');

				// Clear any existing options (if needed)
				originatorSelect.empty();
		
				// Append select box options from contactlist to .originator
				for (var i = 0; i < contactlist.length; i++) {
					originatorSelect.append($('<option>', {
						value: contactlist[i],
						text: contactlist[i]
					}));
				}
            },
        });
		var client = $(this).val();
		var fulladdress = client + '\n' + selectedAddress + '\n' + selectedCountry;
		if(Currency == "USD"){
			var currencySymbol = '$';
		}else if(Currency == 'EUR'){
			 var currencySymbol = '€';
		}
		// console.log(PricePerHour);
		newPricePerHour = '('+currencySymbol+PricePerHour+' per hour)';
		$('.perhourinput').val(PricePerHour);
		$('.country').val(selectedCountry);
		$('#client-address').val(fulladdress);
		$('.preHour').html(newPricePerHour);
		$('.currencySymbol').html(currencySymbol);
		$('.email').val(email);
		

		if(selectedCountry == "The Netherlands" || selectedCountry == 'Netherlands'){
			var $Category = $('#estimate-form select[name="tax"]');
			$Category.val('VAT').trigger('change.select2');
		}


		
	});
}

	let estimat_data = {'grandtotal':'','newslides':0,'editslides':0,'hoursum':0};
	
	function calculateTotal() {
		
		let country = $('.country').val();
		
		let total = $('#totalHours').val();
		let perhour = parseFloat($('.perhourinput').val());
	
		let hourtotal = parseFloat((total*perhour).toFixed(2));
		let tax =0;
		
		$('.perhourvalue').val(hourtotal);
		if(country == "The Netherlands" || country == 'Netherlands'){
			tax = parseFloat(((hourtotal *21)/100).toFixed(2));
			var $Category = $('#edit_expense select[name="tax"]');
			$Category.val('VAT').trigger('change.select2');
		}
		$('.tax').val(tax);
		let grnadTotal = parseFloat(hourtotal+tax).toFixed(2);

		$('.grnadTotal').text(grnadTotal);
		$('.grandTotalinput').val(grnadTotal);
		estimat_data.grandtotal = grnadTotal;
	}

	function newslidesTotal() {
		var total = 0;
		$('.newslides').each(function() {
			var newslides = parseFloat($(this).val()) || 0;
			total += newslides;
		});
		$('.newslidestotal').text(total);
		$('.newslidehours').text(total/5);
		$('input[name="SumNewSlides"]').val(total);
		estimat_data.newslides = parseFloat($('.newslidehours').text());
		estimat_data.editslides = parseFloat($('.editslideshours').text());
		estimat_data.hoursum = parseFloat($('.hourssumtotal').text());
	
		let totalhour = total/5;
		let sumhour = estimat_data.newslides+estimat_data.editslides+estimat_data.hoursum;
		// estimat_data.newslides = totalhour;

		$('#totalHours').val(sumhour.toFixed(2));
		calculateTotal();
	}

	function editslidesTotal() {
		var total = 0;
		$('.editslides').each(function() {
			var editslides = parseFloat($(this).val()) || 0;
			total += editslides;
		});
		$('.editslidestotal').text(total);
		
		let totalnewslies = total/10
		// estimat_data.editslides = totalnewslies;
		$('.editslideshours').text(totalnewslies);
		$('input[name="SumEditSlies"]').val(total);
		totalnewslies += parseFloat(estimat_data.newslides);
		estimat_data.newslides = parseFloat($('.newslidehours').text());
		estimat_data.editslides = parseFloat($('.editslideshours').text());
		estimat_data.hoursum = parseFloat($('.hourssumtotal').text());
		let sumhour = estimat_data.newslides+estimat_data.editslides+estimat_data.hoursum;
		$('#totalHours').val(sumhour.toFixed(2));
		calculateTotal();
	}
	
	function hoursTotal() {
		var total = 0;
		$('.hours').each(function() {
			var hourstotal = parseFloat($(this).val()) || 0;
			total += hourstotal;
		});
		$('.hourstotal').text(total);
		$('.hourssumtotal').text(total);
		$('input[name="SumHours"]').val(total);
		// total += parseFloat(estimat_data.newslides + estimat_data.editslides);
		estimat_data.newslides = parseFloat($('.newslidehours').text());
		estimat_data.editslides = parseFloat($('.editslideshours').text());
		estimat_data.hoursum = total;
		console.log(estimat_data.hoursum);
		let sumhour = estimat_data.newslides+estimat_data.editslides+estimat_data.hoursum;
		$('#totalHours').val(sumhour.toFixed(2));
		// $('#totalHours').val(total);
		calculateTotal();

	}

	// Calculate the initial total
	function calculatediscount(){
		let discount = $(this).val();
		let grnadTotal = estimat_data.grandtotal;
		console.log(grnadTotal);
		let disocunttotal = parseFloat(grnadTotal-discount).toFixed(2);
		$('.grnadTotal').text(disocunttotal);

	}


	$('tbody.tbodyone').on('input', '.hours', hoursTotal);
	$('tbody.tbodyone').on('input', '.newslides', newslidesTotal);
	$('tbody.tbodyone').on('input', '.editslides', editslidesTotal);
	$('tbody.tbodytwo').on('input', '.estimate-discount', calculatediscount);

	//estimate remove feild
	$('#addTable').on('click', '.remove-felid', function () {
		var $table = $("#addTable");
		var $row = $(this).closest('tr');
		if ($table.find('tr').length > 1) {
			$row.remove();
			const rowCount = $('.rowcount').val();
			$('.rowcount').val(rowCount-1);
			newslidesTotal();
			editslidesTotal();
			hoursTotal();
			calculateTotal();
		}
	});

// $('#sendValuesButton').click(function () {
// 	var allInputValues = [];

// 	// Loop through each row and collect the input values for that row
// 	$('.tbodyone tr').each(function () {
// 		var inputValues = [];
// 		$(this).find('input').each(function () {
// 			inputValues.push($(this).val());
// 		});
// 		allInputValues.push(inputValues);
		
// 	});
// 	console.log(allInputValues);
// });



}); 