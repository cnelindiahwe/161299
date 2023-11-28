

<!-- Modal -->
<div class="modal" id="show_limbo_modal_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display:none;background-color: rgba(0, 0, 0, 0.74);-webkit-transition: 0.5s;overflow: auto;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="show_limbo_modal_popup">Enter  Password</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        </button>
      </div>
      <div class="modal-body">
       <input type="text" class="form-control get_limbo_p" placeholder="Enter your password">
        <div class="process_upload" style="justify-content: center;display: flex;" >
            
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary cancle_key_password" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary generate_key_password">Set Password</button>
      </div>
    </div>
  </div>
</div>


<div class="modal" id="upload_limbo_modal_popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display:none;background-color: rgba(0, 0, 0, 0.74);-webkit-transition: 0.5s;overflow: auto;">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="show_limbo_modal_popup">Upload Files</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
      	<div id="upload_hwe_div" style="padding:20px 30px;"></div>
		<button type="button" id="pick_files"class="btn btn-dark " style="width:100%;">Add file(s)</button>
      </div>
       
      <div class="modal-footer" style="display: block ruby;">
        <button type="button" id="cancel_upload_limbo_modal_popup" class="btn btn-secondary cancel_upload_limbo_modal_popup" data-dismiss="modal">Cancel & Close</button>
        <button type="button" data-dismiss="modal" id="done_upload_limbo_modal_popup" class="btn btn-success" style="display:none;">complete</button>
        
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
     <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js?<?php echo rand(); ?> "></script> -->
    <script src="<?php echo site_url(); ?>web/zt2016/plugins/jquery/jquery-3.3.1.min.js?<?php echo rand(); ?> "></script>
    <script src="<?php echo site_url(); ?>web/zt2016/bootstrap/js/bootstrap.min.js?<?php echo rand(); ?> "></script>
    <!--<script src="<?php echo site_url(); ?>web/bootstrap/js/docs.min.js"></script>-->
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="<?php echo site_url(); ?>web/zt2016/bootstrap/assets/js/ie10-viewport-bug-workaround.js?<?php echo rand(); ?> "></script>
    <!-- Bootstrap dialog-->
    <script src="<?php echo site_url(); ?>web/zt2016/bootstrap/assets/js/bootstrap-dialog.min.js?<?php echo rand(); ?> "></script>
    
    
    <?php if ($title == "Team Profile") { ?>
    	<script src="<?php echo site_url(); ?>web/zt2016/js/team/member_profile.js?<?php echo rand(); ?> "></script> 
    <?php } ?>
    
    <?php //############# Datatables ?>
    <?php // if ($title == "Contacts Search" OR $title == "Existing Invoices" OR substr($title,0,20) == "Client Invoices for " )  { ?>
    <?php $data_tables_pages=array("Contacts Search","Contacts","Existing Invoices","Existing Invoices Fast","Pending Invoices","New Invoices","Clients","Tracking","Past Jobs","Annual Client Figures", "Annual Originator Figures", "Groups") ?>
    <?php if (in_array($title, $data_tables_pages) or
			  substr($title,0,20) == "Client Invoices for " or
			  strpos($title, 'Clients Breakdown') or
			  strpos($title, 'Client Information for')!== false  or
			  strpos($title, 'Originators Breakdown') or
			  strpos($title, 'Group Info') or
			  strpos($title, 'Client Information for')!== false  or
			  strpos($title, 'Monthly Client Report') !== false )  { ?>
    	<script src="<?php echo site_url(); ?>web/zt2016/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js?<?php echo rand(); ?> "></script> 
    	<script src="<?php echo site_url(); ?>web/zt2016/plugins/datatables/DataTables-1.10.16/js/dataTables.bootstrap.min.js?<?php echo rand(); ?> "></script>
     	<?php //responsive ?>
     	<script src="<?php echo site_url(); ?>web/zt2016/plugins/datatables/Responsive-2.2.1/js/dataTables.responsive.min.js?<?php echo rand(); ?> "></script>
     	<script src="<?php echo site_url(); ?>web/zt2016/plugins/datatables/Responsive-2.2.1/js/responsive.bootstrap.min.js"></script>
    <?php } ?>
 
    <?php //############# Datatables date sort ?>
    <?php $table_date_sort_pages=array("Existing Invoices","Existing Invoices Fast","Pending Invoices") ?>
    <?php if (in_array($title, $table_date_sort_pages) or substr($title,0,20) == "Client Invoices for " )  { ?>
    	<script src="<?php echo site_url(); ?>web/zt2016/plugins/datatables/datetime-moment/moment.min.js?<?php echo rand(); ?> "></script>
    	<script src="<?php echo site_url(); ?>web/zt2016/plugins/datatables/datetime-moment/datetime-moment.js?<?php echo rand(); ?> "></script> 
    <?php } ?>
 


    <?php //############# Groups ?>
    <?php if ($title == "Groups") { ?> 
 			<script src="<?php echo site_url(); ?>web/zt2016/js/groups/groups.js?<?php echo rand(); ?> "></script> 

    <?php  //############# Group Info ?> 
	<?php } elseif (strpos($title, 'Group Info') !== false) { ?>
			<script src="<?php echo site_url(); ?>web/zt2016/js/groups/group_info.js?<?php echo rand(); ?> "></script> 

    <?php  //############# Group edit ?> 
	<?php } elseif (strpos($title, 'Group Edit') !== false) { ?>
   			<script src="<?php echo site_url(); ?>web/zt2016/plugins/bootbox/bootbox.min.js?<?php echo rand(); ?> "></script>  
			<script src="<?php echo site_url(); ?>web/zt2016/js/groups/group_edit.js?<?php echo rand(); ?> "></script> 




	<?php //############# Clients ?> 
    <?php     //############# Manage Client Materials?> 
    <?php } elseif ($title == "Manage Client Materials") { ?>    
    		<script src="<?php echo site_url(); ?>web/zt2016/plugins/bootbox/bootbox.min.js?<?php echo rand(); ?> "></script>  
    		<script src="<?php echo site_url(); ?>web/zt2016/js/clients/manage_client_materials.js?<?php echo rand(); ?> "></script>  

    <?php //############# Clients ?>
    <?php } elseif ($title == "Clients") { ?> 
 			<script src="<?php echo site_url(); ?>web/zt2016/js/clients/clients.js?<?php echo rand(); ?> "></script> 

    <?php //############# Client Info ?>
    <?php } elseif (strpos($title, 'Client Information for')!== false) {  ?>     	
 			<script src="<?php echo site_url(); ?>web/zt2016/js/clients/client_info.js?<?php echo rand(); ?> "></script> 

   <?php //############# Client Info ?>
    <?php } elseif (strpos($title, 'Client Quotation for')!== false) {  ?>     	
 			<script src="<?php echo site_url(); ?>web/zt2016/js/clients/client_quotation.js?<?php echo rand(); ?> "></script> 

   <?php //############# Client Edit ?>
    <?php } elseif (substr($title,0,28) == "Edit Client Information for ") {  ?>     	
     		<script src="<?php echo site_url(); ?>web/zt2016/plugins/bootbox/bootbox.min.js?<?php echo rand(); ?> "></script>  
			<script src="<?php echo site_url(); ?>web/zt2016/js/clients/client_edit.js?<?php echo rand(); ?> "></script> 

   <?php //############# New Client ?>
    <?php } elseif ($title == "New Client") {  ?>     	
     		<script src="<?php echo site_url(); ?>web/zt2016/plugins/bootbox/bootbox.min.js?<?php echo rand(); ?> "></script>  
			<script src="<?php echo site_url(); ?>web/zt2016/js/clients/client_new.js?<?php echo rand(); ?> "></script> 

  
   <?php //############# Invoices ?> 
   <?php //############# New Invoices ?>
    <?php } elseif ($title == "New Invoices") { ?> 
 			<script src="<?php echo site_url(); ?>web/zt2016/js/invoices/new_invoices.js?<?php echo rand(); ?> "></script> 
			<script src="<?php echo site_url(); ?>web/zt2016/plugins/datepicker/js/bootstrap-datepicker.js?<?php echo rand(); ?> "></script> 
     		
    <?php //############# New Invoice ?>
	<?php }  elseif (substr($title,0,16) == "New Invoice for ") { ?> 
    	<script src="<?php echo site_url(); ?>web/zt2016/plugins/datatables/DataTables-1.10.16/js/jquery.dataTables.min.js?<?php echo rand(); ?> "></script> 
    	<script src="<?php echo site_url(); ?>web/zt2016/plugins/datatables/DataTables-1.10.16/js/dataTables.bootstrap.min.js?<?php echo rand(); ?> "></script>
     	<?php //responsive ?>
     	<script src="<?php echo site_url(); ?>web/zt2016/plugins/datatables/Responsive-2.2.1/js/dataTables.responsive.min.js?<?php echo rand(); ?> "></script>
     	<script src="<?php echo site_url(); ?>web/zt2016/plugins/datatables/Responsive-2.2.1/js/responsive.bootstrap.min.js"></script>

			<script src="<?php echo site_url(); ?>web/zt2016/plugins/datepicker/js/bootstrap-datepicker.js?<?php echo rand(); ?> "></script> 
    		<script src="<?php echo site_url(); ?>web/zt2016/plugins/bootbox/bootbox.min.js?<?php echo rand(); ?> "></script>  

	    	<script src="<?php echo site_url(); ?>web/zt2016/js/invoices/new_client_invoice.js?<?php echo rand(); ?> "></script> 

        <?php //############# payment Invoices ?>
    <?php } elseif ($title == "Invoices Payment") { ?> 
    		<script src="<?php echo site_url(); ?>web/zt2016/js/invoices/payment_invoices.js?<?php echo rand(); ?> "></script> 

    <?php //############# View Invoice ?>
     <?php } elseif (substr($title,0,12) == "View Invoice") { ?> 
    		<script src="<?php echo site_url(); ?>web/zt2016/plugins/bootbox/bootbox.min.js?<?php echo rand(); ?> "></script>  
   			<script src="<?php echo site_url(); ?>web/zt2016/plugins/datepicker/js/bootstrap-datepicker.js?<?php echo rand(); ?> "></script> 
    		<script src="<?php echo site_url(); ?>web/zt2016/js/invoices/view_invoice.js?<?php echo rand(); ?> "></script> 


    <?php //############# Existing Invoices ?>
    <?php } elseif ($title == "Existing Invoices") { ?> 
    		<script src="<?php echo site_url(); ?>web/zt2016/js/invoices/existing_invoices.js?<?php echo rand(); ?> "></script> 

    <?php //############# Existing Invoices ?>
    <?php } elseif ($title == "Existing Invoices Fast") { ?> 
    		<script src="<?php echo site_url(); ?>web/zt2016/js/invoices/existing_invoices_fast.js?<?php echo rand(); ?> "></script> 



    <?php //############# Pending Invoices ?>
    <?php } elseif ($title == "Pending Invoices") { ?> 
    		<script src="<?php echo site_url(); ?>web/zt2016/js/invoices/pending_invoices.js?<?php echo rand(); ?> "></script> 

    <?php //############# Existing Client Invoices ?>
    <?php } elseif (substr($title,0,20) == "Client Invoices for ") { ?> 
    		<script src="<?php echo site_url(); ?>web/zt2016/plugins/bootbox/bootbox.min.js?<?php echo rand(); ?> "></script>  
    	 <script src="<?php echo site_url(); ?>web/zt2016/js/invoices/existing_client_invoices.js?<?php echo rand(); ?> "></script> 

    <?php //############# Ogone Form ?>
    <?php } elseif (substr($title,0,10) == "Ogone form") { ?> 
    		<script src="<?php echo site_url(); ?>web/zt2016/plugins/bootbox/bootbox.min.js?<?php echo rand(); ?> "></script>  
    		<script src="<?php echo site_url(); ?>web/zt2016/js/invoices/ogone_form.js?<?php echo rand(); ?> "></script>    

    <?php //############# Mollie Form ?>
    <?php } elseif (substr($title,0,11) == "Mollie form") { ?> 
    		<script src="<?php echo site_url(); ?>web/zt2016/plugins/bootbox/bootbox.min.js?<?php echo rand(); ?> "></script>  
    		<script src="<?php echo site_url(); ?>web/zt2016/js/invoices/mollie_form.js?<?php echo rand(); ?> "></script>    
    

    <?php //############# Contacts  ?>
    <?php } elseif ($title == "Contacts") { ?> 
    		<script src="<?php echo site_url(); ?>web/zt2016/js/contacts/contacts.js?<?php echo rand(); ?> "></script> 
    
     <?php //############# Contacts Search ?>
    <?php } elseif ($title == "Contacts Search") { ?> 
    		<script src="<?php echo site_url(); ?>web/zt2016/js/contacts/contacts_search.js?<?php echo rand(); ?> "></script> 

   	<?php //############# Contact Info ?>
    <?php } elseif (substr($title,0,24) == "Contact Information for ") {  ?>     	
 			<script src="<?php echo site_url(); ?>web/zt2016/js/contacts/contact_info.js?<?php echo rand(); ?> "></script> 
  
	<?php //############# Contact Edit ?>
    <?php } elseif (substr($title,0,29) == "Edit Contact Information for ") { ?>
        	<script src="<?php echo site_url(); ?>web/zt2016/plugins/bootbox/bootbox.min.js?<?php echo rand(); ?> "></script>  
			<script src="<?php echo site_url(); ?>web/zt2016/js/contacts/contact_edit.js?<?php echo rand(); ?> "></script> 
   
    <?php //############# Trash ?>
    <?php } elseif ($title == "Trash") { ?> 
    		<script src="<?php echo site_url(); ?>web/zt2016/plugins/bootbox/bootbox.min.js?<?php echo rand(); ?>"></script>  
    		<script src="<?php echo site_url(); ?>web/zt2016/js/trash/trash.js?<?php echo rand(); ?> "></script> 
			
   	<?php //############# tracking ?>
    <?php } elseif ($title == "Tracking") { ?>     	
 			<script src="<?php echo site_url(); ?>web/zt2016/js/tracking/tracking.js?<?php echo rand(); ?> "></script> 

   	<?php //############# past_jobs ?>
    <?php } elseif ($title == "Past Jobs") { ?>     	
 			<script src="<?php echo site_url(); ?>web/zt2016/js/tracking/past_jobs.js?<?php echo rand(); ?> "></script> 
			<script src="<?php echo site_url(); ?>web/zt2016/plugins/datepicker/js/bootstrap-datepicker.js?<?php echo rand(); ?> "></script> 

   	<?php //############# edit job ?>
    <?php } elseif ($title == "Edit Job") { ?>
        	<script src="<?php echo site_url(); ?>web/zt2016/plugins/bootbox/bootbox.min.js?<?php echo rand(); ?> "></script>  
 			<script src="<?php echo site_url(); ?>web/zt2016/js/tracking/edit_job.js?<?php echo rand(); ?> "></script> 

   	<?php //############# new job ?>
    <?php } elseif ($title == "New Job") { ?>     	
        	<script src="<?php echo site_url(); ?>web/zt2016/plugins/bootbox/bootbox.min.js?<?php echo rand(); ?> "></script>  
			<script src="<?php echo site_url(); ?>web/zt2016/js/tracking/new_job.js?<?php echo rand(); ?> "></script> 

   	<?php //############# reports ?>
    <?php } elseif (strpos($title, 'Monthly Report') !== false) { ?>
			<script src="<?php echo site_url(); ?>web/zt2016/js/reports/reports.js?<?php echo rand(); ?> "></script> 

    <?php } elseif (strpos($title, 'Monthly Client Report') !== false) { ?>
			<script type="text/javascript" src="<?php echo site_url(); ?>web/zt2016/plugins/d3/d3.v6.min.js"></script>
			<!--<script type="text/javascript" src="https://d3js.org/d3.v4.js"></script>-->
			<script type="text/javascript" src="<?php echo site_url(); ?>web/zt2016/plugins/d3/d3-array.v2.min.js"></script>
			<script src="<?php echo site_url(); ?>web/zt2016/js/reports/monthly_client_report.js?<?php echo rand(); ?> "></script> 


   	<?php //#############  Client Breakdown Reports ?>
    <?php } elseif (strpos($title, 'Clients Breakdown') !== false) { ?>
			 <script type="text/javascript" src="<?php echo site_url(); ?>web/zt2016/plugins/d3/d3.v6.min.js"></script>
			<!--<script type="text/javascript" src="https://d3js.org/d3.v4.js"></script>-->
			<script type="text/javascript" src="<?php echo site_url(); ?>web/zt2016/plugins/d3/d3-array.v2.min.js"></script>
			<script src="<?php echo site_url(); ?>web/zt2016/js/reports/clients_breakdown.js?<?php echo rand(); ?> "></script> 

   	<?php //#############  Originator Breakdown Reports ?>
    <?php } elseif (strpos($title, 'Originators Breakdown') !== false) { ?>
			 <script type="text/javascript" src="<?php echo site_url(); ?>web/zt2016/plugins/d3/d3.v6.min.js"></script>
			<!--<script type="text/javascript" src="https://d3js.org/d3.v4.js"></script>-->
			<script type="text/javascript" src="<?php echo site_url(); ?>web/zt2016/plugins/d3/d3-array.v2.min.js"></script>
			<script src="<?php echo site_url(); ?>web/zt2016/js/reports/originators_breakdown.js?<?php echo rand(); ?> "></script> 


   	<?php //############# Monthly Momentumm Report ?>
    <?php } elseif (strpos($title, 'Monthy Momentum Report') !== false) { ?>
			 <script type="text/javascript" src="<?php echo site_url(); ?>web/zt2016/plugins/d3/d3.v6.min.js"></script>
			<!--<script type="text/javascript" src="https://d3js.org/d3.v4.js"></script>-->
			<script type="text/javascript" src="<?php echo site_url(); ?>web/zt2016/plugins/d3/d3-array.v2.min.js"></script>
			<script src="<?php echo site_url(); ?>web/zt2016/js/reports/monthly_momentum_report.js?<?php echo rand(); ?> "></script> 

   	<?php //############# Annual Client Figures ?>
    <?php } elseif ($title == "Annual Client Figures") { ?>
			 <script type="text/javascript" src="<?php echo site_url(); ?>web/zt2016/plugins/d3/d3.v6.min.js"></script>
			<!--<script type="text/javascript" src="https://d3js.org/d3.v4.js"></script>-->
			<script type="text/javascript" src="<?php echo site_url(); ?>web/zt2016/plugins/d3/d3-array.v2.min.js"></script>
			<script src="<?php echo site_url(); ?>web/zt2016/js/reports/annual_client_figures.js?<?php echo rand(); ?> "></script> 

    <?php //############# Annual Originator Figures ?>
    <?php } elseif ($title == "Annual Originator Figures") { ?>
			 <script type="text/javascript" src="<?php echo site_url(); ?>web/zt2016/plugins/d3/d3.v6.min.js"></script>
			<!--<script type="text/javascript" src="https://d3js.org/d3.v4.js"></script>-->
			<script type="text/javascript" src="<?php echo site_url(); ?>web/zt2016/plugins/d3/d3-array.v2.min.js"></script>
			<script src="<?php echo site_url(); ?>web/zt2016/js/reports/annual_originator_figures.js?<?php echo rand(); ?> "></script> 


   	<?php //############# limbo ?>
    <?php } elseif ($title == "Limbo") { ?>     	
        	<script src="<?php echo site_url(); ?>web/zt2016/plugins/bootbox/bootbox.min.js?<?php echo rand(); ?> "></script>  
			<script src="<?php echo site_url(); ?>web/zt2016/js/limbo/limbo.js?<?php echo rand(); ?> "></script> 

   	<?php //############# export ?>
    <?php } elseif ($title == "Export") { ?>     	
  			<script src="<?php echo site_url(); ?>web/zt2016/js/export/export.js?<?php echo rand(); ?> "></script> 


    <?php } ?>   
	<script type="text/javascript">
    var url="<?php echo base_url();?>";
	// alert(url);
    function delete_with_popup(id){
       var r=confirm("Are you sure you want to delete?")
        if (r==true){
          window.location = url+"user/delete/"+id;
		}
        else{
          return false;
        } 
	}
		function edit_with_popup(id){
       var r=confirm("Are you sure you want to make changes?")
        if (r==true)
          window.location = url+"user/edit/"+id;
        else
          return false;
        } 
		
		function submit_with_popup(e){
       	var r=confirm("Are you sure you want to Submit Changes?")
        if (r==true){
			$('#edit_user_form').submit();
			return true;
		}
        else{
			e.stopPropagation();
			return false;
		}
        } 
	
		$('.generate_key').on('click',function(){
			var file_path = $(this).data('path');
			var file_name = $(this).data('name');
			var file_type = $(this).data('filetype');
			var post_url ="<?php echo base_url();?>"+"limbo/zt2016_limbo/generate_key";
			$('#show_limbo_modal_popup').show();

			$.ajax({
    //   type: "POST",
      url: post_url,
      data: ({'file_path': file_path, 'file_name': file_name, 'filetype' : file_type}),
	  type: "post",
      success: function(resultData){
		if(resultData != 0){
		//	location.reload();
		$('.get_limbo_p').attr("data-id",resultData);
		$('.get_limbo_p').val('');
		$('#show_limbo_modal_popup').show();
		}else{
			$('.get_limbo_p').attr("data-id","");
			alert('Error : Something went wrong Try again...! ');
		}
      },
	
});
		});
		$('.generate_key_password').on('click',function(){
			var key_id = $('.get_limbo_p').data('id');
			var password = $('.get_limbo_p').val();
			$('.get_limbo_p,.modal-footer,.modal-header').hide();
			var img_path ="<?php echo base_url();?>"+"web/img/please-wait.gif";
			$('.process_upload').html('<img src="'+img_path+'" >');
			var post_url ="<?php echo base_url();?>"+"limbo/zt2016_limbo/set_key_pass";
			
			$.ajax({
      url: post_url,
      data: ({'key_id': key_id, 'password': password}),
	  type: "post",
      success: function(resultData){
		if(resultData == 1){
			location.reload();
		}else{
			alert('Error : Something went wrong Try again...! ');
			location.reload();
		}
      },
	
});
		});
		$('.cancle_key_password').on('click',function(){
			$('#show_limbo_modal_popup').hide();

		});
		$('.remove_generate_key').on('click',function(){
			var file_key = $(this).data('key');
			var file_remove = 1;
			
			var post_url ="<?php echo base_url();?>"+"limbo/zt2016_limbo/generate_key";
			
			$.ajax({
    //   type: "POST",
      url: post_url,
      data: ({'file_key': file_key, 'file_remove': file_remove }),
	  type: "post",
      success: function(resultData){
		if(resultData == 1){
			location.reload();

		}else{
			alert('Error : Something went wrong Try again...! ');
		}
      },
	
});
		});
		$('.copy_text_btn').click(function(){
   	let url = $(this).data('value');
navigator.clipboard.writeText(url).then(function() {
    alert("Link Copied");
}, function() {
    console.log('Copy error')
});
   			 
});
$('#upload_limbo_modal_open').click(function(){

$('#upload_limbo_modal_popup').show();
});
$('.cancel_upload_limbo_modal_popup').click(function(){

$('#upload_limbo_modal_popup').hide();
location.reload();
});
$('#done_upload_limbo_modal_popup').click(function(){

$('#upload_limbo_modal_popup').hide();
location.reload();
});
// 		function myFunction() {
//   // Get the text field
//   var copyText = document.getElementById("copy_text_input");

//   // Select the text field
//   copyText.select();
//   copyText.setSelectionRange(0, 99999); // For mobile devices

//    // Copy the text inside the text field
//   navigator.clipboard.writeText(copyText.value);

//   // Alert the copied text
//   alert("Copied the text: " + copyText.value);
// } 
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/plupload/3.1.5/plupload.full.min.js"></script>
    <script>
    // (C) INITIALIZE UPLOADER
    window.onload = () => {
		
      // (C1) GET HTML FILE LIST
      var list = document.getElementById("upload_hwe_div");
	  //alert("");

      // (C2) INIT PLUPLOAD
	  var path = $('#upload_limbo_modal_open').data("path");
	  var url_hwe = "<?php echo base_url();?>"+"store_files/"+path;
      var uploader = new plupload.Uploader({
        runtimes: "html5",
        browse_button: "pick_files",
        url: url_hwe,
        chunk_size: "90mb",
        init: {
          PostInit: () => list.innerHTML = "<div>Ready</div>",
          FilesAdded: (up, files) => {
            plupload.each(files, file => {
              let row = document.createElement("div");
              row.id = file.id;
              row.innerHTML = `${file.name} (${plupload.formatSize(file.size)}) <strong></strong>`;
              list.appendChild(row);
              // alert(${file.percent});
            });
            uploader.start();
          },
          UploadProgress: (up, file) => {
              if(file.percent == 100){
                  var color = 'progress-bar-success';
              }else{
            var color = '';
              }
            document.querySelector(`#${file.id} strong`).innerHTML = `${file.percent}%
           <div class="progress">
  <div class="progress-bar ${color}" role="progressbar" 
  aria-valuemin="0" aria-valuemax="100" style="width:${file.percent}%">
    <span class="sr-only">${file.percent}% Complete</span>
  </div>
</div>  
`;
            if(file.percent == 100){
            document.getElementById("done_upload_limbo_modal_popup").style.display = "block";    
             document.getElementById("cancel_upload_limbo_modal_popup").style.display = "none";      

            }else{
             document.getElementById("done_upload_limbo_modal_popup").style.display = "none";    
                          document.getElementById("cancel_upload_limbo_modal_popup").style.display = "block";      

              }
console.log(up);
    // if(${file.percent} == 100 ){
    //     alert('done');
    // }
          },
          Error: (up, err) => console.error(err)
        }
      });
      uploader.init();
    };
    </script>

  </body>
</html> 