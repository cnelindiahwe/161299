//expenses
$(document).ready(function () {
	// Attach a click event handler to the "Edit" button
	$('.action-icon.dropdown-toggle').click(function () {
		// Get the row's data attributes
		const rowData = $(this).closest('tr').data();
		console.log(rowData);
		
		// Populate the edit modal form fields with the row's data
		$('#edit_expense input[name="item"]').val(rowData.itemName);
		$('#edit_expense input[name="Reference"]').val(rowData.reference);
		$('#edit_expense input[name="purchaseDate"]').val(rowData.date);
		$('#edit_expense input[name="paymentDate"]').val(rowData.paymentdate);
		// $('#edit_expense select[name="Category"]').val(rowData.category);
		$('#edit_expense input[name="amount"]').val(rowData.amount);
		$('#edit_expense input[name="paymentAmount"]').val(rowData.paymentAmount);
		$('#edit_expense input[name="attch"]').val(rowData.attch);
		$('#edit_expense img[name="attach"]').attr('src', rowData.url+rowData.attch);
		$('#edit_expense.attach-files a').attr('src', rowData.url+rowData.attch);
		$('#edit_expense input[name="status"]').val(rowData.status);
		$('#edit_expense input[name="Remark"]').val(rowData.remark);
		$('#edit_expense input[name="id"]').val(rowData.id);
		$('#delete_expense input[name="id"]').val(rowData.id);
		var categoryValue = rowData.category;
		var $Category = $('#edit_expense select[name="Category"]');
		$Category.val(categoryValue).trigger('change.select2');
		var $paidBy = $('#edit_expense select[name="paidBy"]');
		$paidBy.val(rowData.paidby).trigger('change.select2');
		var $currency = $('#edit_expense select[name="currency"]');
		$currency.val(rowData.currency).trigger('change.select2');
		var $status = $('#edit_expense select[name="status"]');
		$status.val(rowData.status).trigger('change.select2');
        
	});

	$('.file-remove').click(function(){
		$(this).closest('li').remove();
	})

	if ($('#attach').length) {
		document.getElementById("attach").addEventListener("change", function (event) {
			const imageList = document.getElementById("image-list");
			imageList.innerHTML = ""; // Clear existing images
		
			const files = event.target.files;
			for (const file of files) {
				if (file.type.startsWith("image/")) {
					const listItem = document.createElement("li");
					const image = document.createElement("img");
					image.src = URL.createObjectURL(file);
					image.alt = file.name;
					listItem.appendChild(image);
					const removeLink = document.createElement("a");
					removeLink.href = "#";
					removeLink.className = "fa fa-close file-remove";
					removeLink.addEventListener("click", function () {
						listItem.remove();
					});
					listItem.appendChild(removeLink);
					imageList.appendChild(listItem);
				}
			}
		});
	}

	$('.expenses .dropdown-item').click(function (event) {
        event.preventDefault();

        const newStatus = $(this).data('status');
        
		var row = $(this).closest('tr');
        const itemId =  row.data('id');
		// console.log(itemId);
        $.ajax({
            type: 'POST',
            url: BaseUrl+'expenses/zt2016_expensesdata',
            data: { id: itemId, status: newStatus,action:'statusUpdate' },
            success: function (data) {
				location.reload();

            },
            error: function () {
                console.error('Failed to update status');
            }
        });
    });
	$('.zowindiaexpenses .dropdown-item').click(function (event) {
        event.preventDefault();

        const newStatus = $(this).data('status');
        
		var row = $(this).closest('tr');
        const itemId =  row.data('id');
		// console.log(itemId);
        $.ajax({
            type: 'POST',
            url: BaseUrl+'zowindia_expenses/zt2016_expensesdata',
            data: { id: itemId, status: newStatus,action:'statusUpdate' },
            success: function (data) {
				location.reload();

            },
            error: function () {
                console.error('Failed to update status');
            }
        });
    });


	$('#table_loading_message').hide() ;
    

    $.fn.dataTable.moment( 'D-MMM-YYYY' );

    $('#expenses_table1').css({ "display": "table", "width": "100%"}).dataTable( {
    "dom":' <"col-sm-6"f><"col-sm-6"i>t<"bottom10"p><"bottom20"l>r<"clear">',
    //"dom":'difrtp',
    "order": [[ 2, "desc" ]],
    "scrollY":        "20em",
    "scrollCollapse": true,
    "paging":         false,
    "info":           true,
    // "processing": true,
    "orderClasses": false,
    "lengthMenu": [[10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "All"]],
    columnDefs: [ {
        targets: [ 3 ],
        orderData: [ 0, 1 ]
    }, {
        targets: [ 1 ],
        orderData: [ 1, 0 ]
    } ],
    "processing": true,
	"deferRender": true,
	// "searching": false,

	initComplete: function () {
		$('#expenses_table1_filter').hide();
		var table = $('#expenses_table1').DataTable();
		
			$('#customSearch').keyup(function() {
				// Get the value entered in the search input
				var searchTerm = $(this).val();
			
				// Perform the DataTable search
				table.search(searchTerm).draw();
			});

		this.api().columns(3).every(function () {
			var column = this;
			$('.category').on('change',function() {
				// Get the value entered in the search input
				var searchTerm = $(this).val();

				if(searchTerm == 'Category (All)'){
					searchTerm = '';
				}
			
				// Perform the DataTable search
				column.search(searchTerm ? searchTerm : '', true, false).draw();
			});
		})
		this.api().columns(5).every(function () {
			var column = this;
			$('.paidby').on('change',function() {
				// Get the value entered in the search input
				var searchTerm = $(this).val();
				if(searchTerm == 'Paid By (All)'){
					searchTerm = '';
				}
			
				// Perform the DataTable search
				column.search(searchTerm ? searchTerm : '', true, false).draw();
			});
		})
	},


    
    
}); //$('#invoices_table').dataTable
	
	
	
	
	Number.prototype.formatMoney = function(c, d, t){
	var n = this, 
	c = isNaN(c = Math.abs(c)) ? 2 : c, 
	d = d == undefined ? "." : d, 
	t = t == undefined ? "," : t, 
	s = n < 0 ? "-" : "", 
	i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
	j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	};
	
});