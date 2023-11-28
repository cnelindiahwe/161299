$(function(){
 		$('#table_loading_message').hide() ;
        $('#contacts_lists_table').css({ "display": "table", "width": "100%"})
		.DataTable( {
        "dom":' <"col-sm-6"f><"col-sm-6"i>t<"bottom10"p><"clear">',
        "scrollY":        "20em",
        "scrollCollapse": true,
        "paging":         true,
        "info":           true,
        "orderClasses": false,
		"bProcessing": true,
        "columnDefs": [ {
            targets: [ 0 ],
            orderData: [ 0, 1 ]
        }, {
            targets: [ 1 ],
            orderData: [ 1, 0 ]
        } ],
        
        /*BELOWFROM HERE:
         * https://datatables.net/examples/api/multi_filter_select.html*/
        initComplete: function () {
        	var icount=0;
            this.api().columns([2,3]).every( function () {
                var column = this;
                var select = $('<select class="form-control"><option value=""></option></select>')
                   .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                        	//if (icount=0) { 
                        	column
                            	//.search( val ? '^.*"\>'+val+'\<\/a\>$' : '', true, false )
                            	.search( val ? '.*'+val+'.*' : '', true, false )
                            	//.search( val ? '^'+val+'$' : '', true, false )
                            	.draw();
                          //}
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                	
                	if (d.substring(0, 7)=="<a href") {
	                	//http://stackoverflow.com/questions/960156/regex-in-javascript-to-remove-links
	               		d=d.replace(/<a\b[^>]*>/i,"").replace(/<\/a>/i, "");
						d=d.replace(/<a\b[^>]*>/i,"").replace(/<\/a>/i, "");
						d=d.replace("  Materials","");
	               		}
                    select.append( '<option value="'+d+'">'+d+'</option>' );
                } );
                icount=icount+1;   
            } );
            

        }
    } );

});        
