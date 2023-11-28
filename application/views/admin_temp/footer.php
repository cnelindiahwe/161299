
           </div>
           <!-- /Page Content -->

       </div>
       <!-- /Page Wrapper -->
</div>
<!-- end main content-->




<!-- DEBUG-VIEW START 8 APPPATH\Views\partials\vendor-scripts.php -->
   <!-- jQuery -->
   <script src="<?php echo base_url();?>web/assets/js/jquery-3.6.0.min.js"></script>
   
   <!-- Bootstrap Core JS -->
   <script src="<?php echo base_url();?>web/assets/js/bootstrap.bundle.min.js"></script>
   
   <!-- Slimscroll JS -->
   <script src="<?php echo base_url();?>web/assets/js/jquery.slimscroll.min.js"></script>

   <!-- Select2 JS -->
   <script src="<?php echo base_url();?>web/assets/js/select2.min.js"></script>
   
   <!-- Datetimepicker JS -->
   <script src="<?php echo base_url();?>web/assets/js/moment.min.js"></script>
   <script src="<?php echo base_url();?>web/assets/js/bootstrap-datetimepicker.min.js"></script>

   <!-- Tagsinput JS -->
   <script src="<?php echo base_url();?>web/assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
   
   
   <!-- Calendar JS -->
   <script src="<?php echo base_url();?>web/assets/js/jquery-ui.min.js"></script>
   <script src="<?php echo base_url();?>web/assets/js/fullcalendar.min.js"></script>
   <script src="<?php echo base_url();?>web/assets/js/jquery.fullcalendar.js"></script>
   
   <!-- Datatable JS -->
   <script src="<?php echo base_url();?>web/assets/js/jquery.dataTables.min.js"></script>
   <script src="<?php echo base_url();?>web/assets/js/dataTables.bootstrap4.min.js"></script>
   
   <!-- validation init -->
   <script src="<?php echo base_url();?>web/assets/js/validation.init.js"></script>

   
   <!-- Custom JS -->
   <script src="<?php echo base_url();?>web/assets/js/app.js"></script>

   

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
        <h3 class="modal-title" id="show_limbo_modal_popup">Upload Files</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body">
      	<div id="upload_hwe_div" style="padding:20px 30px;"></div>
		<button type="button" id="pick_files"class="btn " style="width:100%; background:rgb(251, 122, 28);">Click Me</button>
      </div>
       
      <div class="modal-footer">
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
    <!-- <script src="<?php echo site_url(); ?>web/zt2016/plugins/jquery/jquery-3.3.1.min.js?<?php echo rand(); ?> "></script> -->
    <script src="<?php echo site_url(); ?>web/zt2016/bootstrap/js/bootstrap.min.js?<?php echo rand(); ?> "></script>
    <!--<script src="<?php echo site_url(); ?>web/bootstrap/js/docs.min.js"></script>-->
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!-- <script src="<?php echo site_url(); ?>web/zt2016/bootstrap/assets/js/ie10-viewport-bug-workaround.js?<?php echo rand(); ?> "></script> -->
    <!-- Bootstrap dialog-->
    <!-- <script src="<?php echo site_url(); ?>web/zt2016/bootstrap/assets/js/bootstrap-dialog.min.js?<?php echo rand(); ?> "></script> -->
    
    
    <?php 
   
    if ($title == "Team Profile") { ?>
    	<script src="<?php echo site_url(); ?>web/zt2016/js/team/member_profile.js?<?php echo rand(); ?> "></script> 
    <?php } ?>
    
    <?php //############# Datatables ?>
    <?php // if ($title == "Contacts Search" OR $title == "Existing Invoices" OR substr($title,0,20) == "Client Invoices for " )  { ?>
    <?php $data_tables_pages=array("Contacts Search","Contacts","Retainers","Expenses","Invoices Payment","Zow India Invoice","Existing Invoices","Existing Invoices Fast","Pending Invoices","New Invoices","Clients","Tracking","Past Jobs","Annual Client Figures", "Annual Originator Figures", "Trash", "Groups") ?>
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
    <?php $table_date_sort_pages=array("Existing Invoices","Retainers","Expenses","Invoices Payment","Zow India Invoice","Existing Invoices Fast","Pending Invoices") ?>
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
    
     	<?php //responsive ?>
     	<script src="<?php echo site_url(); ?>web/zt2016/plugins/datatables/Responsive-2.2.1/js/dataTables.responsive.min.js?<?php echo rand(); ?> "></script>
     	<script src="<?php echo site_url(); ?>web/zt2016/plugins/datatables/Responsive-2.2.1/js/responsive.bootstrap.min.js"></script>

			<script src="<?php echo site_url(); ?>web/zt2016/plugins/datepicker/js/bootstrap-datepicker.js?<?php echo rand(); ?> "></script> 
    		<script src="<?php echo site_url(); ?>web/zt2016/plugins/bootbox/bootbox.min.js?<?php echo rand(); ?> "></script>  

	    	<script src="<?php echo site_url(); ?>web/zt2016/js/invoices/new_client_invoice.js?<?php echo rand(); ?> "></script> 
        
        <?php //############# payment Invoices ?>
    <?php } elseif ($title == "Invoices Payment") { ?> 
    		<script src="<?php echo site_url(); ?>web/zt2016/js/invoices/payment_invoices.js?<?php echo rand(); ?> "></script> 
        
        <?php //############# payment Invoices ?>
    <?php } elseif ($title == "Zow India Invoice") { ?> 
    		<script src="<?php echo site_url(); ?>web/zt2016/js/invoices/zowindia_invoices.js?<?php echo rand(); ?> "></script> 
       
        <?php //############# CreateEstimate ?>
    <?php } elseif ($title == "CreateEstimate") { ?> 
    		<script src="<?php echo site_url(); ?>web/zt2016/js/estimate/estimate.js?<?php echo rand(); ?> "></script> 
        <?php //############# CreateEstimate ?>
    <?php } elseif ($title=="estimateedit") { ?> 

    		<script src="<?php echo site_url(); ?>web/zt2016/js/estimate/estimateEdit.js?<?php echo rand(); ?> "></script> 
        <?php //############# Email Setting ?>
    <?php } elseif ($title=="Email Setting") { ?> 
    		<script src="<?php echo site_url(); ?>web/zt2016/js/invoices/sendmail.js?<?php echo rand(); ?> "></script> 
        <?php //############# Expenses ?>
    <?php } elseif ($title=="Expenses") { ?> 
    		<script src="<?php echo site_url(); ?>web/zt2016/js/expenses/expenses.js?<?php echo rand(); ?> "></script> 
    		<!-- <script src="<?php echo site_url(); ?>web/zt2016/js/invoices/sendmail.js?<?php echo rand(); ?> "></script>  -->
        <?php //############# Retainers ?>
    <?php } elseif ($title=="Retainers") { ?> 
    		<script src="<?php echo site_url(); ?>web/zt2016/js/retainers/retainers.js?<?php echo rand(); ?> "></script> 
        <?php //############# employee ?>
    <?php } elseif ($title=="Edit Employee Information") { ?> 
    		<script src="<?php echo site_url(); ?>web/zt2016/js/employee/employee_edit.js?<?php echo rand(); ?> "></script> 


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
    <?php } elseif ($title == "Tracking" || $title == "Users") { ?>     	
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
    <script src="<?php echo site_url(); ?>web/zt2016/plugins/bootbox/bootbox.min.js?<?php echo rand(); ?>"></script>  

	<script type="text/javascript">
    $(document).ready(function () {
var WorkedBy_count  =$('.clone_WorkedBy_btn').data('workedby');
$('.clone_WorkedBy_btn').on('click',function(){
  // 
  $('.NewSlides_clone_meta_hwe > input,.EditedSlides_clone_meta_hwe > input, .Hours_clone_meta_hwe > input').removeAttr('readonly');
  
  if(WorkedBy_count < 4){
    
if(WorkedBy_count ==2 ){
  // WorkedBy_count--;
  // var get_select_val_NewSlides = $('.NewSlides_clone_meta_hwe').html();
  // var filter_content_label_NewSlides = get_select_val_NewSlides.replace('>New','>New '+WorkedBy_count);
  // var filter_content_select_NewSlides = filter_content_label_NewSlides.replace('name="NewSlides"','name="NewSlides_'+WorkedBy_count+'"')
  // var complete_validation_NewSlides = '<div class="clone_success_Worked_'+WorkedBy_count+'">'+filter_content_select_NewSlides+'</div>';
  // ////////////////////////////
  
  // var get_select_val_EditedSlides = $('.EditedSlides_clone_meta_hwe').html();
  // var filter_content_label_EditedSlides = get_select_val_EditedSlides.replace('>Edits','>Edits '+WorkedBy_count);
  // var filter_content_select_EditedSlides = filter_content_label_EditedSlides.replace('name="EditedSlides"','name="EditedSlides_'+WorkedBy_count+'"')
  // var complete_validation_EditedSlides = '<div class="clone_success_Worked_'+WorkedBy_count+'">'+filter_content_select_EditedSlides+'</div>';
  // ////////////////////////////


  // var get_select_val_Hours = $('.Hours_clone_meta_hwe').html();
  // var filter_content_label_Hours = get_select_val_Hours.replace('>Hours','>Hours '+WorkedBy_count);
  // var filter_content_select_Hours = filter_content_label_Hours.replace('name="Hours"','name="Hours_'+WorkedBy_count+'"')
  // var complete_validation_Hours = '<div class="clone_success_Worked_'+WorkedBy_count+'">'+filter_content_select_Hours+'</div>';
  // ////////////////////////////
  

  //   var get_select_val = $('.clone_meta_hwe').html();
  // var filter_content_label = get_select_val.replace('>Worked','>Worked '+WorkedBy_count);
  // var filter_content_select = filter_content_label.replace('name="WorkedBy"','name="WorkedBy_'+WorkedBy_count+'"')
  // var complete_validation = '<div class="clone_success_Worked_'+WorkedBy_count+'"><button class="btn btn-danger remove_clone float-end mt-1 " type="button" data-remove="'+WorkedBy_count+'" style="font-size: 10px;padding: 1px 1px;background: transparent;border: navajowhite;"><i class="fa fa-times-circle" aria-hidden="true" style="font-size: 16px;color: #000;"></i></button>'+filter_content_select+'</div>';
  //   ////////////////////////////
  // $('.NewSlides_put_content_hwe').append(complete_validation_NewSlides);
  // $('.EditedSlides_put_content_hwe').append(complete_validation_EditedSlides);
  // $('.Hours_put_content_hwe').append(complete_validation_Hours);
  // WorkedBy_count++;
}

  // var get_select_val_NewSlides = $('.NewSlides_clone_meta_hwe').html();
  // var filter_content_label_NewSlides = get_select_val_NewSlides.replace('>New','>New '+WorkedBy_count);
  // var filter_content_select_NewSlides = filter_content_label_NewSlides.replace('name="NewSlides"','name="NewSlides_'+WorkedBy_count+'"')
  // var complete_validation_NewSlides = '<div class="clone_success_Worked_'+WorkedBy_count+'">'+filter_content_select_NewSlides+'</div>';
  ////////////////////////////
  
  // var get_select_val_EditedSlides = $('.EditedSlides_clone_meta_hwe').html();
  // var filter_content_label_EditedSlides = get_select_val_EditedSlides.replace('>Edits','>Edits '+WorkedBy_count);
  // var filter_content_select_EditedSlides = filter_content_label_EditedSlides.replace('name="EditedSlides"','name="EditedSlides_'+WorkedBy_count+'"')
  // var complete_validation_EditedSlides = '<div class="clone_success_Worked_'+WorkedBy_count+'">'+filter_content_select_EditedSlides+'</div>';
  // ////////////////////////////


  // var get_select_val_Hours = $('.Hours_clone_meta_hwe').html();
  // var filter_content_label_Hours = get_select_val_Hours.replace('>Hours','>Hours '+WorkedBy_count);
  // var filter_content_select_Hours = filter_content_label_Hours.replace('name="Hours"','name="Hours_'+WorkedBy_count+'"')
  // var complete_validation_Hours = '<div class="clone_success_Worked_'+WorkedBy_count+'">'+filter_content_select_Hours+'</div>';
  // ////////////////////////////
  
    var get_select_val = $('.clone_meta_hwe').html();
  var filter_content_label = get_select_val.replace('>Worked','>Worked '+WorkedBy_count);
  var filter_content_select = filter_content_label.replace('name="WorkedBy"','name="WorkedBy_'+WorkedBy_count+'"').replace('selected="selected"','');
  var complete_validation = '<div class="clone_success_Worked_'+WorkedBy_count+'"><button class="btn btn-danger remove_clone float-end mt-1 " type="button" data-remove="'+WorkedBy_count+'" style="font-size: 10px;padding: 1px 1px;background: transparent;border: navajowhite;"><i class="fa fa-times-circle" aria-hidden="true" style="font-size: 16px;color: #000;"></i></button>'+filter_content_select+'</div>';
    ////////////////////////////

  
    //$('.NewSlides_clone_meta_hwe > input,.EditedSlides_clone_meta_hwe > input, .Hours_clone_meta_hwe > input').attr('readonly','true');
 
 
   var $match = $(document).find('.clone_success_Worked_3');
var found = ($match.length > 0);
  if((WorkedBy_count == 2) && (found == true)){
   
    $('.NewSlides_put_content_hwe').prepend('<div class="clone_success_Worked_'+WorkedBy_count+'"><label for="NewSlides_'+WorkedBy_count+'">New '+WorkedBy_count+'</label><input type="number" name="NewSlides_'+WorkedBy_count+'" value="" id="NewSlides_'+WorkedBy_count+'" class="form-control" min="0" required="" ></div>');
  $('.EditedSlides_put_content_hwe').prepend('<div class="clone_success_Worked_'+WorkedBy_count+'"><label for="EditedSlides_'+WorkedBy_count+'">Edit '+WorkedBy_count+'</label><input type="number" name="EditedSlides_'+WorkedBy_count+'" value="" id="EditedSlides_'+WorkedBy_count+'" class="form-control" min="0" required="" ></div>');
  $('.Hours_put_content_hwe').prepend('<div class="clone_success_Worked_'+WorkedBy_count+'"><label for="Hours_'+WorkedBy_count+'">Hours '+WorkedBy_count+'</label><input type="number" name="Hours_'+WorkedBy_count+'" value="" id="Hours_'+WorkedBy_count+'" class="form-control" min="0" step="0.01" required="" ></div>');
  if(WorkedBy_count ==2 ){
    WorkedBy_count--;
  $('.NewSlides_put_content_hwe').prepend('<div class="clone_success_Worked_'+WorkedBy_count+'"><label for="NewSlides_'+WorkedBy_count+'">New '+WorkedBy_count+'</label><input type="number" name="NewSlides_'+WorkedBy_count+'" value="" id="NewSlides_'+WorkedBy_count+'" class="form-control" min="0" readonly="readonly" ></div>');
  $('.EditedSlides_put_content_hwe').prepend('<div class="clone_success_Worked_'+WorkedBy_count+'"><label for="EditedSlides_'+WorkedBy_count+'">Edit '+WorkedBy_count+'</label><input type="number" name="EditedSlides_'+WorkedBy_count+'" value="" id="EditedSlides_'+WorkedBy_count+'" class="form-control" min="0" readonly="readonly" ></div>');
  $('.Hours_put_content_hwe').prepend('<div class="clone_success_Worked_'+WorkedBy_count+'"><label for="Hours_'+WorkedBy_count+'">Hours '+WorkedBy_count+'</label><input type="number" name="Hours_'+WorkedBy_count+'" value="" id="Hours_'+WorkedBy_count+'" class="form-control" min="0" step="0.01" readonly="readonly"></div>');
  WorkedBy_count++;
 }
  $('.Worked_clone_content').prepend(complete_validation);
    WorkedBy_count=3;
    
  }else{
    if(WorkedBy_count ==2 ){
    WorkedBy_count--;
  $('.NewSlides_put_content_hwe').append('<div class="clone_success_Worked_'+WorkedBy_count+'"><label for="NewSlides_'+WorkedBy_count+'">New '+WorkedBy_count+'</label><input type="number" name="NewSlides_'+WorkedBy_count+'" value="" id="NewSlides_'+WorkedBy_count+'" class="form-control" min="0" readonly="readonly" ></div>');
  $('.EditedSlides_put_content_hwe').append('<div class="clone_success_Worked_'+WorkedBy_count+'"><label for="EditedSlides_'+WorkedBy_count+'">Edit '+WorkedBy_count+'</label><input type="number" name="EditedSlides_'+WorkedBy_count+'" value="" id="EditedSlides_'+WorkedBy_count+'" class="form-control" min="0" readonly="readonly" ></div>');
  $('.Hours_put_content_hwe').append('<div class="clone_success_Worked_'+WorkedBy_count+'"><label for="Hours_'+WorkedBy_count+'">Hours '+WorkedBy_count+'</label><input type="number" name="Hours_'+WorkedBy_count+'" value="" id="Hours_'+WorkedBy_count+'" class="form-control" min="0" step="0.01" readonly="readonly"></div>');
  WorkedBy_count++;
 }
    $('.NewSlides_put_content_hwe').append('<div class="clone_success_Worked_'+WorkedBy_count+'"><label for="NewSlides_'+WorkedBy_count+'">New '+WorkedBy_count+'</label><input type="number" name="NewSlides_'+WorkedBy_count+'" value="" id="NewSlides_'+WorkedBy_count+'" class="form-control" min="0" required="" ></div>');
  $('.EditedSlides_put_content_hwe').append('<div class="clone_success_Worked_'+WorkedBy_count+'"><label for="EditedSlides_'+WorkedBy_count+'">Edit '+WorkedBy_count+'</label><input type="number" name="EditedSlides_'+WorkedBy_count+'" value="" id="EditedSlides_'+WorkedBy_count+'" class="form-control" min="0" required="" ></div>');
  $('.Hours_put_content_hwe').append('<div class="clone_success_Worked_'+WorkedBy_count+'"><label for="Hours_'+WorkedBy_count+'">Hours '+WorkedBy_count+'</label><input type="number" name="Hours_'+WorkedBy_count+'" value="" id="Hours_'+WorkedBy_count+'" class="form-control" min="0" step="0.01" required="" ></div>');
 
    $('.Worked_clone_content').append(complete_validation);
  }
  
 
  WorkedBy_count++;
  }
})

$('.limbo_file_remove_popup').on('click',function(e){
  var limbo_del = $(this).attr('href');

  e.preventDefault();
bootbox.confirm("Are you sure you want to delete file?", function(result) {
            if (result) {
              document.location = limbo_del;
            }
          }); 


});

$('body').delegate('.remove_clone ','click',function(){

  if(WorkedBy_count > 2){
  WorkedBy_count = $(this).data('remove');
}else{
  WorkedBy_count = 2;
}
  var remove_element = $(this).data('remove');
  if(remove_element == 2){
    WorkedBy_count --;
$('.NewSlides_clone_meta_hwe > input,.EditedSlides_clone_meta_hwe > input, .Hours_clone_meta_hwe > input').removeAttr('readonly');

$('.clone_success_Worked_'+WorkedBy_count).remove();
WorkedBy_count++;

  }


$('.clone_success_Worked_'+remove_element).remove();
$('.clone_WorkedBy_btn').removeClass('d-none');
});
   var zwtable =  $('.zwt_dataTable').DataTable();
    $('.search_filter').on('click',function(){
      var fbn = $('.fbn').val();
     var fbr = $('.fbr').val();
      zwtable.column(1).search(fbn, true, false)
      zwtable.column(3).search(fbr, true, false)
        // var data_search =$(this).val();
        // console.log(zwtable);
        zwtable.draw();
     });
});
    var url="<?php echo base_url();?>";
	// alert(url);
  $('.add_user_btn').click ( function () {
        var add_user_btn_location =$(this).attr('href') 
        //ask for deletion confimation

        bootbox.confirm("Are you sure you want Add New user?", function(result) {
            if (result) {

                document.location = url+'user/add';
                
            } 
        });
    return false;
    });
    $('.cancle_back_hsd').click ( function () {

        bootbox.confirm("Are you sure you want to Close?", function(result) {
            if (result) {

                document.location = document.referrer;
                
            } 
        });
    return false;
    });
    $('.edit_hsd').click ( function () {
     var id =  $(this).data('id');
bootbox.confirm("Are you sure you want to make changes?", function(result) {
    if (result) {
      
        document.location =  url+"user/edit/"+id+'/'+567 + Math.floor(Math.random() * 100);
        
    } 
});
return false;
});


$('.delete_hsd').click ( function () {
     var id =  $(this).data('id');
bootbox.confirm("Are you sure you want to delete?", function(result) {
    if (result) {

        document.location =   url+"user/delete/"+id+'/'+567 + Math.floor(Math.random() * 100);

    } 
});
return false;
});
$('.submit_hsd').click ( function(e) {
     var id =  $(this).data('id');
bootbox.confirm("Are you sure you want to Submit Changes?", function(result) {
    if (result) {
      $('#edit_user_form').submit();
        
    }else{
			e.stopPropagation();
			return false;
		}
});
	e.stopPropagation();

return false;
});

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
        chunk_size: "10mb",
        init: {
          PostInit: () => list.innerHTML = "<div>Ready</div>",
          FilesAdded: (up, files) => {
            plupload.each(files, file => {
              let row = document.createElement("div");
              row.id = file.id;
              row.innerHTML = `${file.name} (${plupload.formatSize(file.size)}) <strong></strong>`;
              list.appendChild(row);
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
          },
          Error: (up, err) => console.error(err)
        }
      });
      uploader.init();
    };
    </script>

<!-- DEBUG-VIEW ENDED 8 APPPATH\Views\partials\vendor-scripts.php -->





<div class="sidebar-overlay"></div><div id="toolbarContainer"></div></body></html>