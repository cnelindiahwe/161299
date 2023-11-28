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
    <?php } elseif (substr($title,0,23) == "Client Information for ") {  ?>     	
 			<script src="<?php echo site_url(); ?>web/zt2016/js/clients/client_info.js?<?php echo rand(); ?> "></script> 

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

  </body>
</html> 