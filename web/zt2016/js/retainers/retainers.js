$(function(){
    $('.action-icon.dropdown-toggle').click(function () {
		// Get the row's data attributes
		const rowData = $(this).closest('tr').data();
		console.log(rowData);
		// Populate the edit modal form fields with the row's data
		$('#edit_retainers input[name="client"]').val(rowData.client);
		$('#edit_retainers input[name="retainerhours"]').val(rowData.retainerhours);
		$('#edit_retainers input[name="id"]').val(rowData.id);
		$('#edit_retainers input[name="startDate"]').val(rowData.startdate);
		$('#edit_retainers input[name="endDate"]').val(rowData.enddate);
		$('#edit_retainers textarea[name="note"]').val(rowData.note);
		$('#report_retainers input[name="client"]').val(rowData.client);
        
	});

    $('#table_loading_message').hide() ;
    

    $.fn.dataTable.moment( 'D-MMM-YYYY' );

    $('#retainers-table').css({ "display": "table", "width": "100%"}).dataTable( {
    "dom":' <"col-sm-6"f><"col-sm-6"i>t<"bottom10"p><"bottom20"l>r<"clear">',
    //"dom":'difrtp',
    "order": [[ 2, "desc" ]],
    "scrollY":        "20em",
    "scrollCollapse": true,
    "paging":         true,
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
    // "ajax": "zt2016_existing_invoices_ajax",
           

       
    /*BELOWFROM HERE:
     * https://datatables.net/examples/api/multi_filter_select.html*/
    initComplete: function () {
        $('#invoices_table_wrapper').css({ "display": "block"}).show();
        var icount=0
        // Create a container for filter buttons above the table
        
        // var filterContainer = $('<div style="display: flex; flex-direction: row; align-items: center;"></div>');
        // $(column.header()).append(filterContainer);

        this.api().columns(4).every(function () {
            var column = this;
        
            // Get the current date in the format 'yyyy-mm-dd'
            var currentDate = new Date().toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' });

            // console.log(currentDate);
            // Create a container for filter buttons below the search box
            var filterContainer1 = $('<div class="mb-5 row p-3"></div>');
            $('.filter-row').append(filterContainer1);

            
            
            var createButton = function (text, filterValue) {

                let class12 = '';
                if(text == 'All'){
                    class12 = 'btn-success';
                }else{
                    class12 =  'btn-default';
                }
                var button = $('<button class="btn btn-filter '+class12+' ms-2 col-md-2 mb-3">' + text + '</button>');
                button.on('click', function () {
                    // Remove the btn-success class from all buttons
                    filterContainer1.find('button').removeClass('btn-success');
                    filterContainer1.find('button').addClass('btn-default');
                    
                    // Apply the date filter
                    if (text == 'Active') {
                        column.search(filterValue,true, false).draw();
                        $(this).addClass('btn-success');
                        $(this).removeClass('btn-default');
                    }else if(text == 'Inactive'){
                        var uniqueValues = column.data().unique().toArray();
                        uniqueValues.forEach(function (value) {
                            if(value < currentDate){
                                column.search(value).draw();
                            }
                           
                        });
                        
                       
                        $(this).addClass('btn-success');
                        $(this).removeClass('btn-default');
                    }
                     else {
                        // If "All" is clicked, clear the date filter
                        column.search('').draw();
                        $(this).addClass('btn-success');
                        $(this).removeClass('btn-default');
                    }
                });
                return button;
            };
        
            // Create filter buttons for different date ranges
            filterContainer1.append(createButton("All", ''));
            filterContainer1.append(createButton("Active", currentDate));
            filterContainer1.append(createButton("Inactive",''));
        
            // Initialize the table with "All" filter
            // column.search('').draw();
           
        });
        

        
        
   }, //initComplete: 

     "footerCallback": function ( row, data, start, end, display ) {
        var api = this.api(), data;

        // Remove the formatting to get integer data for summation
        var intVal = function ( i ) {
            return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                typeof i === 'number' ?
                    i : 0;
        };

        // Total over all pages
        /*total = api
            .column( 4 )
            .data()
            .reduce( function (a, b) {
                return intVal(a) + intVal(b);
            }, 0 )
         */
        // Total over this page
        pageTotal = api
            .column( 2, { page: 'current'} )
            .data()
            .reduce( function (a, b) {
                return intVal(a) + intVal(b);
            }, 0 );

    }
    
    
}); //$('#invoices_table').dataTabl

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
	
})