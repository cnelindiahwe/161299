<?php

class Billing extends MY_Controller {

	function Billing()
	{
		parent::MY_Controller();	
	}
	
	function index()
	{
		
		$this->load->helper(array('form','url','reports'));

		$this->load->model('trakclients', '', TRUE);
		$ClientList= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));

		$templateVars['pageSidebar'] = _getClientReportList($ClientList);		


		//Get first day of current month
		$StartDate = date( 'Y-m-1');
		//Get last day of current month
		$EndDate = date( 'Y-m-t');

		$now = strtotime(date('Y-m-15'));

 

		//$templateVars['pageInput'] = $this->_getInputForm($StartDate,$EndDate);
		$templateVars['pageOutput'] = $this->_getMonthTotals($ClientList,$StartDate,$EndDate);
		
		$now = strtotime(date('Y-m-15'));
		for ($i = 1; $i <= 5; $i++) {
			$StartDate = date( 'Y-m-1', strtotime('-'.$i.' month',$now ));
			//echo $StartDate;
			$EndDate = date( 'Y-m-t', strtotime('-'.$i.' month',$now ));
			$templateVars['pageOutput'] .= $this->_getMonthTotals($ClientList,$StartDate,$EndDate);
		} 


		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Billing";
		$templateVars['pageType'] = "billing";
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



	

	// ################## Generate month totals ##################	
	function  _getMonthTotals($Clientlist,$StartDate,$EndMonth)
	{

		
		//Get first day of current month
		//$StartDate = date( 'Y-m-1', strtotime('now'));
		
		//$EndDate = date( 'Y-m-d', strtotime('now +1 day'));
		//Get last day of current month
		//$EndMonth=date ("Y-m-d",strtotime('+1 month -1 day'.$StartDate));
		$ThisMonth = date( 'M Y', strtotime($StartDate));
		if (date( 'M', strtotime($StartDate))!=date( 'M', strtotime('now'))){
			$NotThisMonth=1;
		} else {$NotThisMonth=0;}
		$jobs=0;
		
		$Listtotal = "<table><thead><tr><th>Client</th><th>Total</th><th>New</th><th>Edits</th><th>Hours</th></tr></thead>\n<tbody>\n";
		$grandtotal=0;
		$newgrandtotal=0;
		$editsgrandtotal=0;
		$hoursgrandtotal=0;
		$bookedgrandtotal=0;
		$bookedjobs=0;
		$bookednewgrandtotal=0;
		$bookededitsgrandtotal=0;
		$bookedhoursgrandtotal=0;
		
		if ($NotThisMonth==0){
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
			 
			 $Listtotal.="<tr><td><a href=\"".site_url()."reports/clientreport/".$clientb->ID."\">".$clientb->CompanyName."</a></td>\n";
			 $Listtotal.="<td>". $bookedtotal."</td>\n";
			 $Listtotal.="<td>". $querybooked->row()->NewSlides."</td>\n";
			 $Listtotal.="<td>". $querybooked->row()->EditedSlides."</td>\n";
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

	$Monthtotal = "<div class=\"monthtotal\"> ";
	if ($NotThisMonth==1){
		$grandtotal=$bookedgrandtotal;
		$jobs=$bookedjobs;
		$newgrandtotal=$bookednewgrandtotal;
		$editsgrandtotal=$bookededitsgrandtotal;
		$hoursgrandtotal=$bookedhoursgrandtotal;
		}
		
	if ($NotThisMonth==0){
		$Monthtotal .= "<h3> ".$grandtotal." hours in ".$ThisMonth;}
	else {
		$Monthtotal .= "<h3> ". $bookedgrandtotal." hours in ".$ThisMonth;}

	if ($NotThisMonth==0){$Monthtotal .= " (<em>booked: ".$bookedgrandtotal."</em>)";}
	$Monthtotal.= " <span>".$StartDate." - " .$EndMonth." </span>";
	$Monthtotal .= " </h3>\n";
	
	if ($NotThisMonth==1){
		$monthvar = date( 'm', strtotime($StartDate));
		$yearvar = date( 'Y', strtotime($StartDate));
		$daysEllapsed = cal_days_in_month(CAL_GREGORIAN, $monthvar, $yearvar); 
		}
	else {
		$daysEllapsed = number_format (date( 'd', strtotime('now')));
	}
	
	$dailyAverage = number_format($grandtotal/$daysEllapsed, 2);
	
	$Monthtotal.= "<p><strong>".$jobs." jobs</strong>";
	if ($NotThisMonth==0){ $Monthtotal.= " (<em>". $bookedjobs." booked</em>)";}
	$Monthtotal.= " | <strong>".$dailyAverage." hours billed per day</strong> (average last ".$daysEllapsed." days) ";
	$Monthtotal.= " </p>\n";
	
	$Monthtotal.= "<p>New : ".$newgrandtotal." | Edits: ".$editsgrandtotal." | Hours: ".$hoursgrandtotal;
	if ($NotThisMonth==0){ $Monthtotal.= " (<em>Booked New : ".$bookednewgrandtotal." | Edits: ".$bookededitsgrandtotal." | Hours: ".$bookedhoursgrandtotal."</em>)";}
	$Monthtotal.=  "</p>\n";
	

	$Monthtotal.= $Listtotal;
	$Monthtotal .= "</div>\n"; 
	return $Monthtotal;
		

	}

	// ################## Load client list ##################	
	function  _getClients()
	{
	
		$this->load->model('trakclients', '', TRUE);
		$getentries = $this->trakclients->GetEntry();
		return $getentries;

	}

	// ################## Get last invoice date ##################	
	function  _getDateLastInvoice($client)
	{

		$this->db->select_max('DateOut');
		$this->db->from('zowtrakentries');
		$this->db->where("Client ='".$client."'"); 
		$this->db->where("Invoice <>'NOT BILLED'");
		$this->db->where("Trash ='0'");
		$query = $this->db->get();

		return $query->row()->DateOut;
	}



}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>