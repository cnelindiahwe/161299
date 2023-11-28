$(function(){

    //############################################ Build page

   $('.table_loading_message').hide() ;


   $('#ongoing_jobs_table').css({ "display": "table", "width": "100%"})

   $('#past_jobs_table').css({ "display": "table", "width": "100%"})	

   $('[data-toggle="tooltip"]').tooltip(); 

   $("#ongoing_jobs_table td:nth-of-type(4)").find("a").each(function () {
       $(this).attr("data-original-title", localuserdate($(this).attr("data-original-title")));
   });	
   
   //############################################ Show / hide tentatives	

   var myTentatives = $("#ongoing_jobs_table a.btn-default").length;

   if (myTentatives>0) {			
       $('#top-panel .panel-heading h4 .add_new_b').append("<a  id='showtentative' class='col btn btn-info me-1 ' style='border-radius: 20px;'>Show " + myTentatives + " Tentative(s)</a>");
       $('#top-panel .panel-heading h4 .add_new_b').append("<a id='hidetentative' class='col  btn btn-info ' style='border-radius: 20px;'>Hide " + myTentatives + " Tentative(s)</a>");
       $('#hidetentative').hide();		
   }

   
   //http://live.datatables.net/fehobiti/1/edit
   $.fn.dataTable.ext.search.push(
   function (settings, searchData, index, rowData, counter) {

     //var checked = $('input:checkbox[name="chk_box"]').is(':checked');
       var checked = $('#showtentative').is(':visible');

       // If checked and Position column is blank don't display the row
     //if (checked && searchData[4] === '*TENTATIVE*') {
   if (checked) {
       if ( searchData[4].search("TENTATIVE")>=0) {
         return false;
       }
     }

     // Otherwise display the row
     return true;

   });	


       //############################################ Top table
       var toptable =	$('#ongoing_jobs_table').DataTable( {
           "dom":'<"clear">',
           "order": [[ 4, "desc" ],[ 3, "asc" ]],
           "scrollCollapse": true,
           "paging":         false,	
           "info":           false,
           "processing": true,
           "orderClasses": false,
           columnDefs: [
             { orderable: false, targets: 'no-sort'},
             { "width": "3em", "targets": [0, 1, 2] },
             { "width": "6em", "targets": [3] },
             { "width": "7em", "targets": [4] },
             { "width": "5.5em", "targets": [5] },
             { "width": "4em", "targets": 7 },
           ],
           /*BELOWFROM HERE:
            * https://datatables.net/examples/api/multi_filter_select.html*/
           initComplete: function () {

               this.api().columns([4,5,6,7]).every( function () {
                   var column = this;
                   var select = $('<select Class="form-control" style="height: 27px;font-size: 10px;"><option value=""></option></select>')
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



       //	toptable.fnFilter( '^(?:(?!TENTATIVE).)*$\r?\n?',1, true, false);
           //Show / hide tentatives
           //https://datatables.net/forums/discussion/2521/reverse-filter
           $("#hidetentative").click(function() {
               //toptable.fnFilter( '^(?:(?!TENTATIVE).)*$\r?\n?',1, true, false);
               $('#hidetentative').hide();
               $("#showtentative").show();	
               toptable.draw();
               return false;
           });   

           $("#showtentative").click(function() {
               //toptable.fnFilter('',1, true, false);
               $('#hidetentative').show();
               $("#showtentative").hide();	
               toptable.draw();
               return false;

           });
   //}
   //}
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

//#############################  hide past jobs if no client is selected	

   var myPastClient = $("#PastJobsClient").val();
   var myPastJobsViewType = $("#PastJobsViewType").val();
   var myNumberPastJobs = $("#NumberPastJobs").val();

   if (myPastClient=="" && myPastJobsViewType=="list" && myNumberPastJobs=="20" ) {	
       $("#bottom-panel .panel-heading form, #bottom-panel .panel-body").hide();
   }

   $('#bottom-panel .panel-heading h4').append("<a  id='showcompletedjobs' class='btn btn-default btn-sm pull-right'>Show</a>");
   $('#bottom-panel .panel-heading h4').append("<a id='hidecompletedjobs' class='btn btn-default btn-sm pull-right'>Hide</a>");

   
   if (myPastClient=="") {	
       $('#hidecompletedjobs').hide();
   } else {
       $('#showcompletedjobs').hide();
   }

   $("#hidecompletedjobs").click(function() {
       $("#bottom-panel .panel-heading form, #bottom-panel .panel-body").hide();

       $("#hidecompletedjobs").hide();
       $("#showcompletedjobs").show();	
       return false;
   });

   $("#showcompletedjobs").click(function() {
       $("#bottom-panel .panel-heading form, #bottom-panel .panel-body").show();

       $("#hidecompletedjobs").show();
       $("#showcompletedjobs").hide();	
       return false;
   });	


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



function localuserdate(duedate) {
var localdate= new Date(duedate*1000);
return "Due ".concat(localdate);
}