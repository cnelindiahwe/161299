<?php
$CI = get_instance();
$user_id =  $this->session->userdata('user_id');

// You may need to load the model if it hasn't been pre-loaded
  $CI->load->model('zt2016_users_model');
  $id_data =  $CI->zt2016_users_model->getsuer_visibility($user_id);

  ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

	<?php if ($title == "Limbo") { ?> 
 			<meta http-equiv="refresh" content="900" />
   	<?php } else { ?>
			<meta http-equiv="refresh" content="300" />
	<?php } ?> 
	  
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.ico">

    <title><?php echo $title; ?> - ZOWtrak 2016</title>

    <!-- Bootstrap core CSS -->
    <link href=" <?php echo site_url(); ?>web/zt2016/bootstrap/css/bootstrap.min.css" rel="stylesheet">
   
	  
    <!-- Bootstrap theme -->
    <link href="<?php echo site_url(); ?>web/zt2016/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">
    
    <!-- Bootstrap dialog -->
    <link href="<?php echo site_url(); ?>web/zt2016/bootstrap/assets/css/bootstrap-dialog.css" rel="stylesheet">
     
    <!-- Fontawesome -->
    <link href="<?php echo site_url(); ?>web/zt2016/plugins/fontawesome-free-5.0.6/web-fonts-with-css/css/fontawesome-all.css" rel="stylesheet">

    <!-- ZOWtrak2016 theme -->
    <link href="<?php echo site_url(); ?>web/zt2016/css/zt2016_general.css" rel="stylesheet">

    <!-- Custom styles for this template 
    <link href="web/css/dashboard.css" rel="stylesheet"> -->

    <?php //############# Datatables ?>
    <?php $data_tables_pages=array("Contacts","Contacts Search","Existing Invoices","Existing Invoices Fast","Pending Invoices","New Invoices", "Clients","Tracking", "Past Jobs","Annual Client Figures","Annual Originator Figures","Groups") ?>
    <?php if (in_array($title, $data_tables_pages) or
			 // substr($title,0,20) == "Client Invoices for " or
			  strpos($title, '"Client Invoices for') !== false or
			  strpos($title, 'Monthly Report') !== false  or
			  strpos($title, 'Clients Breakdown')  !== false or
			  strpos($title, 'Originators Breakdown')  !== false or
			  strpos($title, 'Group Info')  !== false or
			  
			  $title == "Annual Client Figures" or
			  $title == "Annual Originator Figures" )  { ?>
    	
    	 <link href="<?php echo site_url(); ?>web/zt2016/plugins/datatables/DataTables-1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet"> 
      	 <link href="<?php echo site_url(); ?>web/zt2016/plugins/datatables/Responsive-2.2.1/css/responsive.bootstrap.min.css" rel="stylesheet">
      	 <link href="<?php echo site_url(); ?>web/zt2016/css/zt2016_datatables.css" rel="stylesheet">
	
	  
		  <?php $safe_title =str_replace(" ", "_",strtolower($title)); ?>
		  <?php if ($title == "Existing Invoices" || $title == "Existing Invoices Fast" || $title == "Pending Invoices" || $title == "New Invoices" || substr($title,0,20) == "Client Invoices for ") { 
     	 		$section="invoices";
    	  	} else if ($title == "Contacts Search" || $title == "Contacts" )  { 
 				$section="contacts";
    	  	} else if ($title == "Clients" )  { 
 				$section="clients";
    	  	} else if ($title == "Groups" or strpos($title, 'Group Info'))  { 
 				$section="groups";	
    	  	} else if (strpos($title, 'Monthly Report') !== false or strpos($title, 'Monthly Client Report')  !== false or $title == "Annual Client Figures" or
			  $title == "Annual Originator Figures" or strpos($title, '"Client Invoices for') !== false )  { 
 				$section="reports";
			}elseif (strpos($title, 'Clients Breakdown')  !== false) {
				$section="reports";
				$safe_title="breakdown";			
			}elseif (strpos($title, 'Originators Breakdown')  !== false) {
				$section="reports";
				$safe_title="breakdown";			
			}else if ($title == "Tracking" )  { 
 				$section="tracking";
	    	} else if ($title == "Past Jobs" )  { 
 				$section="tracking";
	 			$safe_title="pastjobs";
    	  	}  ?>
	  		<?php if (strpos($title, '"Client Invoices for') !== false ) $safe_title="existing_client_invoices"; 
	  		else if (strpos($title, 'Group Info') !== false ) $safe_title="group_info";  
			else if ($safe_title=="existing_invoices_fast") $safe_title="existing_invoices"; ?>
	  
 	  		<?php if ($section!=""){?>
	  			<link href="<?php echo site_url(); ?>web/zt2016/css/<?php echo $section ?>/<?php echo $safe_title ?>.css" rel="stylesheet">
	  		<?php }?>
	        
   <?php //############# New Job ?>
   <?php } elseif ($title == "New Job") { ?> 
    	 <link href="<?php echo site_url(); ?>web/zt2016/css/tracking/new_job.css" rel="stylesheet"> 
  
   <?php //############# Edit Job ?>
   <?php } elseif ($title == "Edit Job") { ?> 
    	 <link href="<?php echo site_url(); ?>web/zt2016/css/tracking/edit_job.css" rel="stylesheet"> 
	  
	  
    <?php //############# Invoices ?>
    <?php //############# View Invoice ?>
    <?php }  elseif (substr($title,0,12) == "View Invoice") { ?> 
    	 <link href="<?php echo site_url(); ?>web/zt2016/css/invoices/view_invoice.css" rel="stylesheet"> 

    <?php //############# New Invoice ?>   
    <?php }  elseif (substr($title,0,16) == "New Invoice for ") { ?> 
    	 <link href="<?php echo site_url(); ?>web/zt2016/plugins/datatables/DataTables-1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet"> 
      	 <link href="<?php echo site_url(); ?>web/zt2016/plugins/datatables/Responsive-2.2.1/css/responsive.bootstrap.min.css" rel="stylesheet">
    	 <link href="<?php echo site_url(); ?>web/zt2016/css/invoices/new_client_invoice.css" rel="stylesheet"> 
  	 
    <?php //############# Existing Client Invoices ?>
   <?php } elseif (substr($title,0,20) == "Client Invoices for ") { ?> 
    	 <link href="<?php echo site_url(); ?>web/zt2016/plugins/datatables/DataTables-1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet"> 
      	 <link href="<?php echo site_url(); ?>web/zt2016/plugins/datatables/Responsive-2.2.1/css/responsive.bootstrap.min.css" rel="stylesheet">
    	 <link href="<?php echo site_url(); ?>web/zt2016/css/invoices/existing_client_invoices.css" rel="stylesheet"> 

   <?php //#############  Groups ?>
   <?php } elseif (strpos($title, 'Group Info') !== false) { ?>
    	 <link href="<?php echo site_url(); ?>web/zt2016/css/groups/group_info.css" rel="stylesheet"> 	  

	<?php //#############  Group new?>
    <?php } elseif (strpos($title, 'New Group') !== false) { ?>
    	 <link href="<?php echo site_url(); ?>web/zt2016/css/groups/group_new.css" rel="stylesheet"> 	  

	  	<?php //#############  Group edit?>
    <?php } elseif (strpos($title, 'Group Edit') !== false) { ?>
    	 <link href="<?php echo site_url(); ?>web/zt2016/css/groups/group_edit.css" rel="stylesheet"> 	  

	  
   <?php //############# Clients ?>   
   <?php //############# Client Info ?>
   <?php } elseif (substr($title,0,23) == "Client Information for ") { ?> 
     	 <link href="<?php echo site_url(); ?>web/zt2016/plugins/datatables/DataTables-1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet"> 
      	 <link href="<?php echo site_url(); ?>web/zt2016/plugins/datatables/Responsive-2.2.1/css/responsive.bootstrap.min.css" rel="stylesheet">
	     <link href="<?php echo site_url(); ?>web/zt2016/css/clients/client_info.css" rel="stylesheet"> 

   <?php //############# Client Edit ?>
   <?php } elseif (substr($title,0,28) == "Edit Client Information for ") { ?> 
    	 <link href="<?php echo site_url(); ?>web/zt2016/css/clients/client_edit.css" rel="stylesheet"> 

   <?php //############# Client quotation ?>
   <?php } elseif (strpos($title, 'Client Quotation for ') !== false) { ?>	  
    	 <link href="<?php echo site_url(); ?>web/zt2016/css/clients/client_quotation.css" rel="stylesheet"> 

   <?php //############# Client New ?>
   <?php } elseif ($title == "New Client") { ?> 
    	 <link href="<?php echo site_url(); ?>web/zt2016/css/clients/client_new.css" rel="stylesheet"> 

    <?php //############# Contacts ?>   

    <?php //############# Contact Info ?>
    <?php } elseif (substr($title,0,24) == "Contact Information for ") { ?> 
    	 <link href="<?php echo site_url(); ?>web/zt2016/css/contacts/contact_info.css" rel="stylesheet"> 

   <?php //############# Contact Info ?>
   <?php } elseif (substr($title,0,24) == "Contact Information for ") { ?> 
    	 <link href="<?php echo site_url(); ?>web/zt2016/css/contacts/contact_info.css" rel="stylesheet"> 

    <?php //############# Contact Edit ?>
   <?php } elseif (substr($title,0,29) == "Edit Contact Information for ") { ?> 
    	 <link href="<?php echo site_url(); ?>web/zt2016/css/contacts/contact_edit.css" rel="stylesheet"> 
    	 
   <?php //############# Contact New ?>
   <?php } elseif ($title == "New Contact") { ?> 
    	 <link href="<?php echo site_url(); ?>web/zt2016/css/contacts/contact_new.css" rel="stylesheet"> 
   
   <?php //############# Limbo ?>
   <?php } elseif ($title == "Limbo") { ?> 
    	 <link href="<?php echo site_url(); ?>web/zt2016/css/limbo/limbo.css" rel="stylesheet"> 

   
   <?php //############# Reports ?>
   <?php } elseif (strpos($title, 'Monthy Momentum Report') !== false) { ?>
    	 <link href="<?php echo site_url(); ?>web/zt2016/css/reports/monthly-momentum-report.css" rel="stylesheet"> 
   <?php } ?>

	  
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <span class="navbar-brand">ZOWtrak 2016</span>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-left">
          <li  ><a href="<?php echo site_url(); ?>tracking/zt2016_tracking">Tracking</a></li>
           <li  ><a href="<?php echo site_url(); ?>contacts/zt2016_contacts_search">Search</a></li>
          <li  ><a href="<?php echo site_url(); ?>reports/zt2016_monthly_originators_breakdown">Reports</a></li>
          <li  ><a href="<?php echo site_url(); ?>limbo">Limbo</a></li>
          <!-- <?php //if ($ZOWuser=="miguel" || $ZOWuser=="sunil.singal" || $ZOWuser=="jirka.blom" || $ZOWuser=="invoices") { ?> -->
          <?php if ($id_data->user_type == 2) { ?>
	        <li class="dropdown">
	          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Manager<span class="caret"></span></a>
	          <ul class="dropdown-menu">
				  
	            <li><a href="<?php echo site_url(); ?>groups\zt2016_groups">Groups</a></li>
	             <li role="separator" class="divider"></li>
				  
	            <li><a href="<?php echo site_url(); ?>clients\zt2016_clients">Clients</a></li>
	            <li><a href="<?php echo site_url(); ?>clients\zt2016_client_new">New Client</a></li>

	             <li role="separator" class="divider"></li>
	            
	            <li><a href="<?php echo site_url(); ?>contacts\zt2016_contacts">Contacts</a></li>
	            <li><a href="<?php echo site_url(); ?>contacts\zt2016_contact_new">New Contact</a></li>
	           
	             <li role="separator" class="divider"></li>
	            
	            <li><a href="<?php echo site_url(); ?>invoicing/zt2016_new_invoices">Create New Invoice</a></li>
	            <li><a href="<?php echo site_url(); ?>invoicing/zt2016_pending_invoices">Pending Invoices</a></li>
	            <li><a href="<?php echo site_url(); ?>invoicing/zt2016_existing_invoices/fast">All Existing Invoices (fast)</a></li>
				  <li><a href="<?php echo site_url(); ?>invoicing/zt2016_existing_invoices">All Existing Invoices (complete)</a></li>
	            <li role="separator" class="divider"></li>

	            <li><a href="<?php echo site_url(); ?>financials">Financials</a></li>
	            
	            <li role="separator" class="divider"></li>

	            <li><a href="<?php echo site_url(); ?>export">Export</a></li>
	            
	            <li role="separator" class="divider"></li>
	            
	            <li><a href="<?php echo site_url(); ?>trash/zt2016_trash">Trash</a></li>
	            	            
	          </ul>
	        </li>
      <li><a href="<?php echo site_url(); ?>user">User</a></li>

		  <?php }?>
      
          
           <!-- <li  ><a href="<?php echo site_url(); ?>/dashboard">Dashboard</a></li>
            <li><a href="#">Reports</a></li>
            <li class="dropdown <?php if (substr($title, 0, 8) == "Contacts") {echo 'active';} ?>" >
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Contacts<span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                  <li <?php if ($title == "Contacts Search") {echo 'class="active"';} ?>><a href="<?php echo site_url(); ?>contacts/contacts_search">Search</a></li>
                  <li <?php if ($title == "Contacts Lists") {echo 'class="active"';} ?>><a href="<?php echo site_url(); ?>contacts/contacts_lists">Lists</a></li>
                  <li <?php if ($title == "Contacts Profile") {echo 'class="active"';} ?>><a href="<?php echo site_url(); ?>contacts/contacts_profile">Profiles</a></li>
               </ul>
             </li>
            <li class="dropdown <?php if (substr($title, 0, 5) == "Team ") {echo 'active';} ?>" >
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Team <span class="caret"></span></a>
                <ul class="dropdown-menu" role="menu">
                  <li <?php if ($title == "Team Roster") {echo 'class="active"';} ?>><a href="<?php echo site_url(); ?>team/team_roster">Roster</a></li>
                  <li <?php if ($title == "Team Members") {echo 'class="active"';} ?>><a href="<?php echo site_url(); ?>team/team_members">Members</a></li>
                  <li <?php if ($title == "Team Profile") {echo 'class="active"';} ?>><a href="<?php echo site_url(); ?>team/team_profile">Profile</a></li>
                </ul>
              </li>
             -->
         </ul>   
         <ul class="nav navbar-nav navbar-right">
			 <?php $logoutusername = str_replace(".", " ",$ZOWuser);
			 $logoutusername = ucwords($logoutusername ); ?>
			 ?>
            <li><a href="<?php echo site_url(); ?>main/logout">Log Out (<?php echo $logoutusername; ?>)</a></li>
          </ul>
        </div>
      </div>
    </div>
    


