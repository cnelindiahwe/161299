$(function(){
	
		// ####################### date pickers
		$('.datepicker').datepicker({
			format: "dd M yyyy",
    		autoclose: true			
		});
	
	 	//datesform submit on datepickers change
 		$('#cutoff-submit').hide();
		$(".datepicker").change ( function () {
				$("#cutoff-date-form").submit();
			}
		); 	
	
		// ####################### Clients new invoice table
	
	
 		$('#table_loading_message').hide() ;
 	
        $('#invoices_table').css({ "display": "table", "width": "100%"}).dataTable( {
        	        "sDom": "t",
        "order": [[ 2, "asc" ],[ 1, "desc" ]],
        "scrollCollapse": true,
        "paging":         false,
        "info":           true,
        "processing": true,
        "orderClasses": false,
			columnDefs: [
			  { targets: 'no-sort', orderable: false }
			],

        /*columnDefs: [ {
            targets: [ 3 ],
            orderData: [ 0, 1 ]
        }, {
            targets: [ 1 ],
            orderData: [ 1, 0 ]
        } ],
               
    */
           
        /*BELOWFROM HERE:
         * https://datatables.net/examples/api/multi_filter_select.html*/
        initComplete: function () {

        	var icount=0
            this.api().columns([0,2,8]).every( function () {
                var column = this;
                var select = $('<select class="form-control"><option value=""></option></select>')
                   .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                         	column
                             	.search( val ? '.*'+val+'.*' : '', true, false )
                            	.draw();
                    } );
 
 				if (column.index()== 0) { // Clients col
	                column.data().unique().sort().each( function ( d, j ) {
	                	
	                	if (d.substring(0, 7)=="<a href") {
		                	//http://stackoverflow.com/questions/960156/regex-in-javascript-to-remove-links
		               		d=d.replace(/<a\b[^>]*>/i,"").replace(/<\/a>/i, "");
		               		}
	                    select.append( '<option value="'+d+'">'+d+'</option>' )
	                } ); //column.data().unique().sort().each
                } 
                
  				else if (column.index()== 2) { // Clients col
	                column.data().unique().sort().each( function ( d, j ) {
	                	

	                    select.append( '<option value="'+d+'">'+d+'</option>' )
	                } ); //column.data().unique().sort().each
                } 
               
                else { // dates col
 			        var dates = [];
                	column.data().each(function (d, j) {
                		
                		
                		if (d.length == 11) {
                			 d= d.substring(3,11)
                		} else{
                			d= d.substring(2,10)
                		} 

						if (dates.indexOf(d) < 0) {
			                dates.push(d);
			                select.append('<option value="' + d + '">' + d + '</option>');
			            }    
                		

	                } ); //column.data().unique().sort().each
                }//if //column.index()!=2 && column.index()!=3
                
                icount=icount+1   
            } );

			
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
			//total revenue
            pageTotal = api
                .column( 1, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 1 ).footer() ).html(
                pageTotal.formatNumber(2)  //+ ' ('+ total.formatMoney(2) +')'
            );

			//total jobs
            pageTotal = api
                .column( 3, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 3 ).footer() ).html(
                pageTotal.formatNumber(0)  //+ ' ('+ total.formatMoney(2) +')'
            );

			//total billed hours
            pageTotal = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 4 ).footer() ).html(
                pageTotal.formatNumber(2)  //+ ' ('+ total.formatMoney(2) +')'
            );

			//total new
            pageTotal = api
                .column( 5, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 5 ).footer() ).html(
                pageTotal.formatNumber(0)  //+ ' ('+ total.formatMoney(2) +')'
            );

			//total edits
            pageTotal = api
                .column( 6, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 6 ).footer() ).html(
                pageTotal.formatNumber(0)  //+ ' ('+ total.formatMoney(2) +')'
            );

			//total hours
            pageTotal = api
                .column( 7, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Update footer
            $( api.column( 7 ).footer() ).html(
                pageTotal.formatNumber(2)  //+ ' ('+ total.formatMoney(2) +')'
            );


        }



        
        
    }); //$('#invoices_table').dataTable



});   //$(function()     


Number.prototype.formatNumber = function(c, d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };