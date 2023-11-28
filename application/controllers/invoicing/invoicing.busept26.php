<?php

class Invoicing extends MY_Controller {

	function Invoicing()
	{
		parent::MY_Controller();	
	}
	
	function index()
	{
		
		$this->load->helper(array('form','url','invoice'));

		$this->load->model('trakclients', '', TRUE);
		$ClientList= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));

		$templateVars['pageSidebar'] = _getClientListInvoices($ClientList);		


		//Get first day of current month
		$StartDate = date( 'Y-m-1', strtotime('now'));
		//Get last day of current month
		//$EndDate = date( 'Y-m-d', strtotime('+1 month -1 day '.$StartDate));
		$EndDate = date( 'Y-m-d', strtotime('now'));




		
		$templateVars['pageInput'] = $this->_getInputForm($StartDate,$EndDate);
		$templateVars['pageOutput'] = $this->_getTotals($ClientList,$EndDate);
		$this->load->model('Trakinvoices', '', TRUE);
		//$clientlist=$this->_getClients(); 
		$templateVars['pageOutput'] .= $this->Trakinvoices->_getBilledInvoices();
		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Invoicing";
		$templateVars['pageType'] = "invoicing";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtraktemplate');


	}
	

	// ################## Page Input ##################	
	function  _getInputForm ($StartDate,$EndDate)
	{
		$attributes = array( 'id' => 'ReportFilter');
		
		$FilterForm = form_open(site_url().'reports/generatereport',$attributes)."\n";
		
		$FilterForm .="<fieldset>";
		$FilterForm.= form_label('Client:','Client');
		
		$options = array(''  => '');
		$Clientlist=$this->_getClients();
		foreach($Clientlist as $client)
		{
			$options[$client->CompanyName]=$client->CompanyName;
		}		
		asort($options);
		$more = 'id="Client"';
					

			$FilterForm .=form_dropdown('Client', $options, '',$more);

		$FilterForm .="</fieldset>";


		$FilterForm .="<fieldset>";
		$FilterForm .= form_label('Start Date:','ReportStartDate')."\n";
		//If date comes from db, format it for human display
		if ($StartDate!=""){$StartDate = date( 'd/M/Y',strtotime($StartDate));}
		$ndata = array('name' => 'ReportStartDate', 'id' => 'ReportStartDate', 'size' => '15', 'class'=>'StartDate', 'value'=>$StartDate);
		$FilterForm .= "\n".form_input($ndata)."\n";
		$FilterForm .="</fieldset>";
		
		$FilterForm .="<fieldset>";
		$FilterForm .= form_label('End Date:','ReportEndDate')."\n";
		//If date comes from db, format it for human display
		if ($EndDate!=""){$EndDate = date( 'd/M/Y',strtotime($EndDate));}
		$ndata = array('name' => 'ReportEndDate', 'id' => 'ReportEndDate', 'size' => '15', 'class'=>'EndDate', 'value'=>$EndDate);
		$FilterForm .= "\n".form_input($ndata)."\n";
		$FilterForm .="</fieldset>";

		$FilterForm .="<fieldset class=\"formbuttons\">";
		$ndata = array('name' => 'submit','value' => 'Report','class' => 'submitButton');
		$FilterForm .= form_submit($ndata)."\n";
		$FilterForm .="</fieldset>";
		$FilterForm .= form_close()."\n";
		return $FilterForm;
	}



	
	// ################## Generate client totals ##################	
	function  _getTotals($Clientlist,$EndDate)
	{
	
		$total = "<div id=\"invoices\"><h3>Hours since last invoice</h3>";

		$total .="<table>\n";
		$total .="<thead><tr><th class=\"header client\">Client</th><th class=\"header total\">Total</th><th class=\"header slides\">New</th><th class=\"header slides\">Edits</th><th class=\"header slides\">Hours</th><th class=\"header button\">Last Invoice</th><th class=\"header \"></th></tr></thead>\n<tbody>";
		$this->load->model('trakinvoices', '', TRUE);
		foreach($Clientlist as $client)
		{
		  
		  $DateLastInvoice =$this->trakinvoices->_getDateLastInvoice($client->CompanyName);
		  
		  
		  if ($DateLastInvoice=='') {$DateLastInvoice='2010-01-01';}
		  $StartDate = date( 'Y-m-d', strtotime('+1 day '.$DateLastInvoice));
		  
		  //Get entry totals from db
		  $this->db->select_sum('Hours','Hours');
		  $this->db->select_sum('NewSlides','NewSlides');
		  $this->db->select_sum('EditedSlides','EditedSlides');
		  $this->db->from('zowtrakentries');
		  $this->db->where('Client',$client->CompanyName);
		 
		  $this->db->where('DateOut >=', $StartDate);
		  $this->db->where('DateOut <= ', $EndDate);
		   $this->db->where('Trash =',0);

		  $query = $this->db->get();
		  
		
		  //Get client details from db
		  $this->load->model('trakclients', '', TRUE);
		  $query2 = $this->trakclients->GetEntry($options = array('CompanyName' => $client->CompanyName));

	  
		  //Apply edit price
		  $subtotal=$query->row()->EditedSlides*$query2->PriceEdits;
		  //Add slides and divide by slides per hour
		  $subtotal=$subtotal+$query->row()->NewSlides;
		  $subtotal=$subtotal/5;
		  //Add hours to get the total
		  $htotal=$subtotal+$query->row()->Hours;

		  //$total = "<p>".$this->db->last_query()."</p>";
		  if ($htotal!=0){
			 $total.="<tr><td>".$client->CompanyName."</td>\n";
			 $total.="<td>".$htotal."</td>\n";
			 if ($query->row()->NewSlides!=0){
				$total.="<td>".$query->row()->NewSlides."</td>\n";
			 }
			 else {
				$total.="<td></td>\n";
			 }
			 if ($query->row()->EditedSlides!=0){
				$total.="<td>".$query->row()->EditedSlides."</td>\n";
			 }
			 else {
				$total.="<td></td>\n";
			 }
			 if ($query->row()->Hours!=0){
				$total.="<td>".$query->row()->Hours."</td>\n";
			 }
			 else {
				$total.="<td></td>\n";
			 }
	
			 $total.="<td>".date( 'd/M/Y', strtotime($DateLastInvoice))."</td>\n";
			 $cleanclientname=str_replace("&","~",$client->CompanyName);
			 $total.="<td><a href=\"".site_url()."invoicing/newinvoice/".$cleanclientname. "\">New Invoice</a></td>\n";
	
			
			$total .="</tr>\n";
		}	
	}
	$total.="</tbody></table></div>\n";
	return $total;
		

	}

	// ################## Generate month totals ##################	
	function  _getMonthTotals($Clientlist,$StartDate,$EndDate)
	{

		
		//Get first day of current month
		$StartDate = date( 'Y-m-1', strtotime('now'));
		
		//$EndDate = date( 'Y-m-d', strtotime('now +1 day'));
		//Get last day of current month
		$EndMonth=date ("Y-m-d",strtotime('+1 month -1 day'.$StartDate));
		$ThisMonth = date( 'M Y', strtotime('now'));
		$jobs=0;
		
		$Listtotal = "<table><thead><tr><th>Client</th><th>Total</th><th>New</th><th>Edits</th><th>Hours</th></tr></thead>\n<tbody>";
		$grandtotal=0;
		$newgrandtotal=0;
		$editsgrandtotal=0;
		$hoursgrandtotal=0;
		$bookedgrandtotal=0;
		$bookedjobs=0;
		$bookednewgrandtotal=0;
		$bookededitsgrandtotal=0;
		$bookedhoursgrandtotal=0;
		
		foreach($Clientlist as $client)
		{
		  //Get ELLAPSED totals from db
		  $this->db->select_sum('Hours','Hours');
		  $this->db->select_sum('NewSlides','NewSlides');
		  $this->db->select_sum('EditedSlides','EditedSlides');
		  $this->db->from('zowtrakentries');
		  $this->db->where('Client',$client->CompanyName);
		  $this->db->where('Status','COMPLETED');
		  
		  $this->db->where('DateOut >=', $StartDate);
		  $this->db->where('DateOut <= ', $EndMonth);
		   $this->db->where('Trash =',0);

		  $query = $this->db->get();
		  
		
		  //Get client details from db
		  $this->load->model('trakclients', '', TRUE);
		  $query2 = $this->trakclients->GetEntry($options = array('CompanyName' => $client->CompanyName));

	  
		  //Apply edit price
		  $subtotal=$query->row()->EditedSlides*$query2->PriceEdits;
		  //Add slides and divide by slides per hour
		  $subtotal=$subtotal+$query->row()->NewSlides;
		  $subtotal=$subtotal/5;
		  //Add hours to get the total
		  $htotal=$subtotal+$query->row()->Hours;
		  if ($htotal!=0){
			 $newgrandtotal=$newgrandtotal+$query->row()->NewSlides;
			 $editsgrandtotal=$editsgrandtotal+$query->row()->EditedSlides;
			 $hoursgrandtotal=$hoursgrandtotal+$query->row()->Hours;
			 $grandtotal=$grandtotal+$htotal;
			  $this->db->from('zowtrakentries');
			  $this->db->where('Client',$client->CompanyName);
			  $this->db->where('DateOut >=', $StartDate);
			 $this->db->where('DateOut <= ', $EndMonth);
		  $this->db->where('Status','COMPLETED');
			  $this->db->where('Trash =',0);
			  $jobsthisclient = $this->db->get();
			  $jobs= $jobs+$jobsthisclient->num_rows();
			}
			
		}

		foreach($Clientlist as $clientb)
		{
		  //Get BOOKED totals from db
		  $this->db->select_sum('Hours','Hours');
		  $this->db->select_sum('NewSlides','NewSlides');
		  $this->db->select_sum('EditedSlides','EditedSlides');
		  $this->db->from('zowtrakentries');
		  $this->db->where('Client',$clientb->CompanyName);
		 
		  $this->db->where('DateOut >=', $StartDate);
		  $this->db->where('DateOut <= ', $EndMonth);
		   $this->db->where('Trash =',0);

		  $querybooked = $this->db->get();
		  //Get client details from db
		  $this->load->model('trakclients', '', TRUE);
		  $query2 = $this->trakclients->GetEntry($options = array('CompanyName' => $clientb->CompanyName));

		  //Apply edit price
		  $subtotalbooked= $querybooked->row()->EditedSlides*$query2->PriceEdits;
		  //Add slides and divide by slides per hour
		   $subtotalbooked= $subtotalbooked+$querybooked->row()->NewSlides;
		   $subtotalbooked= $subtotalbooked/5;
		  //Add hours to get the total
		  $bookedtotal= $subtotalbooked+$querybooked->row()->Hours;

		  //$total = "<p>".$this->db->last_query()."</p>";
		  if ($bookedtotal!=0){
		  	$bookednewgrandtotal=$bookednewgrandtotal+$querybooked->row()->NewSlides;
			$bookededitsgrandtotal=$bookededitsgrandtotal+$querybooked->row()->EditedSlides;
			$bookedhoursgrandtotal=$bookedhoursgrandtotal+$querybooked->row()->Hours;
			 
			 $Listtotal.="<tr><td>".$clientb->CompanyName."</td>";
			 $Listtotal.="<td>". $bookedtotal."</td>";
			 $Listtotal.="<td>". $querybooked->row()->NewSlides."</td>";
			 $Listtotal.="<td>". $querybooked->row()->EditedSlides."</td>";
			 $Listtotal.="<td>".$querybooked->row()->Hours."</td></tr>";
			 $bookedgrandtotal=$bookedgrandtotal+$bookedtotal;
		  $this->db->from('zowtrakentries');
		  $this->db->where('Client',$clientb->CompanyName);
		 
		  $this->db->where('DateOut >=', $StartDate);
		  $this->db->where('DateOut <= ', $EndMonth);
		   $this->db->where('Trash =',0);

		  $bookedjobsthisclient = $this->db->get();
		 $bookedjobs= $bookedjobs+ $bookedjobsthisclient ->num_rows();
		}	




	}
	
	
		$Listtotal .= "</tbody></table>\n";


	$Monthtotal = "<h3> ".$grandtotal." hours in ".$ThisMonth;
	$Monthtotal .= " (<em>booked: ".$bookedgrandtotal."</em>)</h3>";
	$daysEllapsed = number_format (date( 'd', strtotime('now')));
	$dailyAverage = number_format($grandtotal/$daysEllapsed, 2);

	$Monthtotal.= "<p>New : ".$newgrandtotal." | Edits: ".$editsgrandtotal." | Hours: ".$hoursgrandtotal;
	$Monthtotal.= " (<em>Booked New : ".$bookednewgrandtotal." | Edits: ".$bookededitsgrandtotal." | Hours: ".$bookedhoursgrandtotal."</em>)</p>";
	$Monthtotal.= "<p><strong>Total jobs: ". $jobs."</strong>";
	$Monthtotal.= " | Hour billed daily - average last ".$daysEllapsed." days:  <strong>".$dailyAverage." hours per day</strong><br/>";
	$Monthtotal.= " <em> Booked: ". $bookedjobs." Jobs</em></p>";
	$Monthtotal.= $Listtotal;
	return $Monthtotal;
		

	}

	// ################## Load client list ##################	
	function  _getClients()
	{
	
		$this->load->model('trakclients', '', TRUE);
		$getentries = $this->trakclients->GetEntry();
		return $getentries;

	}





}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>