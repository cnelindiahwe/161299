$(function(){

 		//############################################ Build page
	
		$('.table_loading_message').hide() ;
	

		$('#past_jobs_table').css({ "display": "table", "width": "100%"})	
	
	    $('[data-toggle="tooltip"]').tooltip(); 	
	
		//Datepicker
		$('.datepicker').datepicker({
			format: "dd M yyyy",
    		autoclose: true			
		});
	
	//############################################ Bottom table
	
			var bottomtable =	$('#past_jobs_table').dataTable( {
			"dom":'<"clear">',
			"order": [[ 6, "Desc" ]],
			"scrollCollapse": true,
			"paging":         false,	
			"info":           true,
			"processing": true,
			"orderClasses": false,
			columnDefs: [
			  { targets: 'no-sort', 
			   orderable: false }
			],
			/*BELOWFROM HERE:
			 * https://datatables.net/examples/api/multi_filter_select.html*/
			initComplete: function () {

				this.api().columns([3,4,8]).every( function () {
					var column = this;
					var select = $('<select><option value=""></option></select>')
					   .appendTo( $(column.footer()).empty() )
					   .on( 'change', function () {
							//reset all but changed

							// $('th select').not(this).each(function(){
							//     $(this).prop('selectedIndex',0);
						   // });
							var val = $.fn.dataTable.util.escapeRegex( $(this).val());
							//var choice = $(this).prop('selectedIndex')
						   // table.columns().search( '' );
							//$('th select').prop('selectedIndex',0);
							//$(this).prop('selectedIndex',choice);
							column


								.search( val ? '.*'+val+'.*' : '', true, false )
								.draw();
					   } ); //.on( 'change', function ()

					var ExistingItems= [];
					column.data().unique().sort().each( function ( d, j ) {
						if (d.substring(0, 7)=="<a href" || d.substring(0, 7)=="<small>") {
							//http://stackoverflow.com/questions/960156/regex-in-javascript-to-remove-links
							d=d.replace(/<a\b[^>]*>/i,"").replace(/<\/a>/i, "");
							d=d.replace(/<small>/i, "").replace(/<a\b[^>]*>/i,"").replace(/<\/a>/i, "").replace(/<\/small>/i, "");
							}
						//https://stackoverflow.com/questions/5864408/javascript-is-in-array
						if( ExistingItems.indexOf(d) < 0)
						  {  
							 ExistingItems.push(d);
							select.append( '<option value="'+d+'">'+d+'</option>' );
						  }
					} );
			   } ); //this.api().columns([0,1,2,3,4]).every
			   
			   
   			} //initComplete
       
        
		}); //$('#ongoing_jobs_table').dataTable #############################
	
	
	
	//#############################   past jobs controls
		$('#PastJobsSubmit').remove();
	

		$("#PastJobsViewType").change ( function () {
				$("#PastJobsControlForm").submit();
			}
		);

		$("#NumberPastJobs").change ( function () {
				$("#PastJobsControlForm").submit();
			}
		);	

		$("#PastJobsClient").change ( function () {
				$("#PastJobsControlForm").submit();
			}
		);
	
		$("#PastJobsOriginator").change ( function () {
				$("#PastJobsControlForm").submit();
			}
		);	
	
		$("#PastJobsDate").change ( function () {
				$("#PastJobsControlForm").submit();
			}
		);	

	

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