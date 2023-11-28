<?php class traksummaries extends CI_Model {

// ------------------------------------------------------------------------

	/**
	* GetMonthEntrybyDate returns Month entries between dates
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/	
	function GetMonthEntrybyDate($options = array())
	{

		// check needed values are provides
		$qualificationArray = array('StartDate','EndDate');
		foreach($qualificationArray as $qualifier)
		{
			if(!isset($options[$qualifier])) return false;
		}
		
		$StartDate = $options['StartDate'];
		$EndDate =  $options['EndDate'];
		
		//Check if last date is within last 2 months
		$datetime1 = new DateTime();
		$datetime2 = new DateTime($options['EndDate']);
		$interval = $datetime1->diff($datetime2);
		
		if ($interval->m <2) {
			
			//Update last 2 months

			$now = strtotime(date('Y-m-15'));
			for ($i =2; $i >=0; $i--) {	
				$UStartDate = date( 'Y-m-1', strtotime('-'.$i.' month',$now ));
				$UEndDate = date( 'Y-m-t', strtotime('-'.$i.' month',$now ));
				
				 $this->UpdateMonthSummaries($suboptions=array('StartDate'=>$UStartDate,'EndDate'=>$UEndDate));

			}		
		}
		
		
		
 
	  	$this->db->where('Date >=', $StartDate);
	 	$this->db->where('Date <= ', $EndDate);
	 	$this->db->order_by('ID',"asc");
		
		// select obly filled fields for csv
		   if (array_key_exists('csv', $options)){
		   
		  	 $this->db->select('Date,Jobs,BilledHours,New,Edits,Hours,Clients,Contacts,NewClients,NewContacts');
		   }
		   
		$query = $this->db->get('zowtrakmonthsummaries');
		
		if($query->num_rows() == 0) return false;
		
		
	   	if (array_key_exists('csv', $options)){
			return $this->dbutil->csv_from_result($query);
		} 
		else {
			return $query->result();
		}


	}


// ------------------------------------------------------------------------

	/**
	* UpdateMonthSummaries calculates month totals and updates DB table
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/	
	function UpdateMonthSummaries($options = array())
	{

		// Add where clauses to query
		$qualificationArray = array('StartDate','EndDate');
		foreach($qualificationArray as $qualifier)
		{
			if(!isset($options[$qualifier])) return false;
		}
		$StartDate = $options['StartDate'];
		$EndDate =  $options['EndDate'];
		
		
		
		$Clients =$this->trakreports->ClientsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
		
		$Originators =$this->trakreports->OriginatorsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
		
		$Jobs =$this->trakreports->_NumJobsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
		
		$NewClients =$this->trakclients->GetNewClients($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
		
		$NewContacts =$this->trakcontacts->GetNewContacts($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
		
		$BilledRawSums =$this->trakreports->_getAllClientBilledTotalsByDate($options1=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
		$CompletedRawSums =$this->trakreports->_getAllClientCompletedTotalsByDate($options2=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
		 $TotalSums =$this->_clienttotalsums($options3=array('BilledRawSums'=>$BilledRawSums,'CompletedRawSums'=>$CompletedRawSums));
		
		
		$this->db->select();
			$this->db->where('Date', $StartDate);
			$query = $this->db->get('zowtrakmonthsummaries');			
			if ($query->num_rows() != 0) {$existflag=1;} else {$existflag=2;} ;
			
			$this->db->set("Clients", count($Clients));
			$this->db->set("Contacts", count($Originators));
			$this->db->set("Jobs", $Jobs);
			$this->db->set("NewClients", count($NewClients));
			$this->db->set("NewContacts", count($NewContacts));
			
			$this->db->set("ClientList",$TotalSums["Client"]);
			$this->db->set("BilledHours",$TotalSums["InvoiceTime"]);
			$this->db->set("Hours",$TotalSums["Hours"]);
			$this->db->set("New",$TotalSums["NewSlides"]);
			$this->db->set("Edits",$TotalSums["EditedSlides"]);	
				
			if ($existflag==1) {
				$this->db->where('Date', $options['StartDate']);
				$this->db->update('zowtrakmonthsummaries');
			} 
			else if($existflag==2) {
				$this->db->set("Date", $StartDate);
				$this->db->insert('zowtrakmonthsummaries');	
			}
	}

// ------------------------------------------------------------------------

	/**
	* _clienttotalsums calculates month totals sums
	*
	* @param array $required
	* @param array $data
	* @return bool
	*/	
	
 	 function _clienttotalsums($options = array()){
		  $TotalSums["Client"]="";
		  $TotalSums["InvoiceTime"]=0;
		  $TotalSums["InvoiceEntryTotal"]=0;
		  $TotalSums["Hours"]=0;
		  $TotalSums["NewSlides"]=0;
		  $TotalSums["EditedSlides"]=0;
		  
		  //#### Billed
		//   echo'<pre>';
		//   print_r($options['BilledRawSums']);
		//   echo'</pre>';

		  foreach ($options['BilledRawSums'] as $Row) {
			  $TotalSums["Client"].=$Row["Client"].",";
			  $TotalSums["InvoiceTime"]+=$Row['InvoiceTime'];
			  $TotalSums["InvoiceEntryTotal"]+=$Row['InvoiceEntryTotal'];
			  
			  $TotalSums["Hours"]+=$Row['Hours'];
			  $TotalSums["NewSlides"]+=$Row['NewSlides'];
			  $TotalSums["EditedSlides"]+=$Row['EditedSlides'];
			}
			
		//#### Completed		
	 	foreach ($options['CompletedRawSums'] as $Row) {

			
			$clientdata= $this->trakclients->GetEntry($options = array('CompanyName' => $Row['Client']));
			//Apply edit price
			$subtotalbooked= $Row['EditedSlides']*$clientdata->PriceEdits;
			//Add slides and divide by slides per hour
			$subtotalbooked= $Row['NewSlides']+$subtotalbooked;
			$subtotalbooked= $subtotalbooked/5;
			//Add hours to get the total
			$bookedtotal= number_format ($subtotalbooked+$Row['Hours'],1);	
			
			$TotalSums["Client"].=$Row["Client"].",";
			$TotalSums["InvoiceTime"]+=$bookedtotal;
			$TotalSums["Hours"]+=$Row['Hours'];
			$TotalSums["NewSlides"]+=$Row['NewSlides'];
			$TotalSums["EditedSlides"]+=$Row['EditedSlides'];
			
			}		
		return $TotalSums;
		
	 }


}

?>