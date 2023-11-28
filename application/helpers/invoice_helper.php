<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ZOWTRAK
 *
 * @package		ZOWTRAK
 * @author		Zebra On WHeels
 * @copyright	Copyright (c) 2010 - 2009, Zebra On WHeels
 * @since		Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Client Helpers
 *
 * @package		ZOWTRAK
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Zebra On WHeels

 */

// ------------------------------------------------------------------------

/**
 * _getClientSelectorForm()
 *
 * Generates a clickable list of all existing clients 
 *
 * @access	public
 * @return	string
 */
if ( !function_exists('_getClientListInvoices'))
{
	function  _getClientListInvoices($ClientList,$Selected="" )
	{
		$ClientReportList ="<h3>Active Clients</h3>";

		foreach($ClientList as $client)
		{
			if ($client->CompanyName==$Selected){ $ClientReportList .="<strong>";}
			$ClientReportList .="<a href=\"".base_url()."invoicing/clientinvoices/$client->ID\">".$client->CompanyName."</a>";
			if ($client->CompanyName==$Selected){ $ClientReportList .="</strong>";}
		}
		return $ClientReportList;	


	}
}


	// ################## clients control ##################	
	function   _clientscontrol($Clientlist,$Currentclient="")
	{

		$attributes['id'] = 'clientcontrol';
		$clientscontrol= form_open(site_url()."invoicing/clientinvoices\n",$attributes);
		$selecteditem="";
			
			//Clients

				$options=array();
				foreach($Clientlist as $client)
				{
				$options[$client->ID]=$client->CompanyName;
				if  ($client->CompanyName==$Currentclient) {$selecteditem=$client->ID;}
				}
				asort($options);
				$options=array(''=>"")+$options;		
				$more = 'id="clientselector" class="selector"';			
				$selected=$selecteditem;
				$clientscontrol .=form_label('Client:','client');
				$clientscontrol .=form_dropdown('client', $options,$selected ,$more);
				$more = 'id="clientcontrolsubmit" class="clientcontrolsubmit"';			
				$clientscontrol .=form_submit('clientcontrolsubmit', 'View',$more);
		$clientscontrol .= form_close()."\n";

		return $clientscontrol;
	
	}



/**
 * getInvoiceByDate($client,$StartDate,$EndDate)
 *
 * Retrieves all entries for a given client within a date range from db 
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('getInvoiceByDate'))
{

	function  getInvoiceByDate($client,$StartDate,$EndDate,$excludelist =  array())
	{
		$CI =& get_instance();
		$CI->load->model('trakentries', '', TRUE);
		if ($client==""){
			return false;
		}
		else
		{
			$getentries = $CI->trakentries->GetEntryRange(array('Client'=>$client,'Status'=>'COMPLETED'),$StartDate,$EndDate);
			if($getentries)
			{
				
				$invoicetotals=InvoiceTotalsByDate($client,$StartDate,$EndDate,count($getentries),$excludelist);
				$invoicetotals['Originators']='';
				$entries =$invoicetotals['page'];
				
				$attributes = array('id' => 'excludeform');
				
				/*$urlclient= str_replace('_', ' ', $client);
				$urlclient= str_replace('%20', ' ', $urlclient);
				$urlclient=str_replace("~","&",$urlclient);*/
				$entries.= form_open('invoicing/newinvoice/'.$CI->uri->segment(3), $attributes);
			
				$entries.= "<table id=\"currententries\">\n";
				$entries .= "<thead>\n";
				$entries .= "<tr><th class=\"header\">Client</th><th class=\"header\">Date</th><th class=\"header\">Originator</th><th class=\"header\"># New Slides</th>";
				$entries .= "<th class=\"header\"># Edited Slides</th><th class=\"header\"># Hours</th><th>File Name</th><th>Team</th><th class=\"button\">Trash</th><th class=\"button\">Exclude</th></tr>\n";
				$entries .= "</thead>\n";
				
				$entries .= "<tbody>\n";
				foreach($getentries as $project)
				{
					if (!in_array($project->id, $excludelist)) {
						$entries .= "<tr>";
						$entries .= "<td>".$project->Client . "</td>";
						//Converts MySQL date
						$mysqldate = date( 'd/M/Y',strtotime($project->DateIn));
						$entries .= "<td class=\"date\">".$mysqldate. "</td>";
						$entries .= "<td>".$project->Originator . "</td>";
						$invoicetotals['Originators'].=", ".$project->Originator;
						if ($project->NewSlides > 0) {
							$entries .= "<td class=\"slides\">".$project->NewSlides . "</td>";
						}
						else
						{
							$entries .= "<td class=\"slides\"></td>";
						}
						if ($project->EditedSlides > 0) {
							$entries .= "<td class=\"slides\">".$project->EditedSlides . "</td>";
						}
						else
						{
							$entries .= "<td class=\"slides\"></td>";
						}
						if ($project->Hours > 0) {
							$entries .= "<td class=\"slides\">".$project->Hours . "</td>";
						}
						else
						{
							$entries .= "<td class=\"slides\"></td>";
						}
						$entries .= "<td>".$project->FileName . "</td>";
						
						$entries .= "<td>".$project->ScheduledBy;
						if ($project->ScheduledBy != $project->WorkedBy){
						$entries.=", ".$project->WorkedBy;
						}
						if ($project->ProofedBy != $project->ScheduledBy && $project->ProofedBy != $project->WorkedBy){
							$entries.=", ".$project->ProofedBy;
						}
						if ($project->CompletedBy != $project->ScheduledBy && $project->CompletedBy != $project->WorkedBy && $project->CompletedBy != $project->ProofedBy){
							$entries.=", ".$project->ProofedBy;
						}
						
						$entries .= "</td>";
						//$entries .= "<td class=\"button edit\"><a href=\"".site_url()."editentry/".$project->id . "\" class=\"edit\">Edit</a></td>";
						$entries .= "<td class=\"button delete\"><a href=\"".$project->id . "\" class=\"delete\">Trash</a></td>";
							$data = array(
				              'name'        => 'exclude-'.$project->id,
				              'value'       => 'exclude',
				              'class'       => 'xxx',
	
				            );					
						$entries .= "<td class=\"button exclude\">".form_checkbox($data)."</td>";
						$entries .= "</tr>\n";
					}
					}
					$entries .= "</tbody>\n";
					$entries .= "</table>\n";
					$data = array(
					  'id'        => 'excludeSubmit',
		              'name'        => 'excludeSubmit',
		              'value'       => 'Exclude ticked entries from invoice',
		              'class'       => 'submitButton',
	
		            );

					$entries .= form_submit($data);
					$entries .= "</form>\n";
					if ($excludelist) {
						$entries .=count($excludelist)." entries excluded.";
					}

			}
			else
			{
				$entries = "No entries since last invoice.\n";
				
				//$entries .= $this-> _getPastInvoices($client['Client']);

			}
			
			$invoicetotals['page']=$entries;
			//takes last comma away
			$invoicetotals['Originators']= substr($invoicetotals['Originators'],1,strlen($invoicetotals['Originators'])-1);
			//gets rid of repeats
			$finalOriginators = explode(",", $invoicetotals['Originators']);
			$finalOriginators = array_unique($finalOriginators);
			sort($finalOriginators);
			$invoicetotals['Originators']= implode(",", $finalOriginators);
		return $invoicetotals;
		;
		
		}
	}


}

// ------------------------------------------------------------------------

/**
 * getInvoiceByDate($client,$StartDate,$EndDate)
 *
 * Retrieves all entries for a given client within a date range from db 
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('getInvoiceByNumber'))
{

	function  getInvoiceByNumber($invoice,$clientName='')
	{
		if ($invoice==""){
			return false;
		}
		else
		{
			$CI =& get_instance();
			$CI->load->model('trakentries', '', TRUE);
			$getentries = $CI->trakentries->GetEntry($options = array('Invoice' => $invoice,'sortBy'=> 'id','sortDirection'=> 'asc'));
			if($getentries)
			{
				$InvoiceTotals=InvoiceTotalsByNumber($invoice,$clientName);
				$entries=$InvoiceTotals['page'];
				$entries.= "<p>Total Jobs:".count($getentries)."</p>";
				$entries.= "<table id=\"currententries\">\n";
				$entries .= "<thead>\n";
				$entries .= "<tr><th class=\"header\">Client</th><th class=\"header\">Date</th><th class=\"header\">Originator</th><th class=\"header\"># New Slides</th><th class=\"header\"># Edited Slides</th><th class=\"header\"># Hours</th><th>File Name</th><th>Team</th><th class=\"button\"></th></tr>\n";
				$entries .= "</thead>\n";
				
				$entries .= "<tbody>\n";
				foreach($getentries as $project)
				{
					$entries .= "<tr>";
					$entries .= "<td>".$project->Client . "</td>";
					//Converts MySQL date
					$mysqldate = date( 'd/M/Y',strtotime($project->DateIn));
					$entries .= "<td class=\"date\">".$mysqldate. "</td>";
					$entries .= "<td>".$project->Originator . "</td>";
					if ($project->NewSlides > 0) {
						$entries .= "<td class=\"slides\">".$project->NewSlides . "</td>";
					}
					else
					{
						$entries .= "<td class=\"slides\"></td>";
					}
					if ($project->EditedSlides > 0) {
						$entries .= "<td class=\"slides\">".$project->EditedSlides . "</td>";
					}
					else
					{
						$entries .= "<td class=\"slides\"></td>";
					}
					if ($project->Hours > 0) {
						$entries .= "<td class=\"slides\">".$project->Hours . "</td>";
					}
					else
					{
						$entries .= "<td class=\"slides\"></td>";
					}
					$entries .= "<td>".$project->FileName . "</td>";
					$entries .= "<td>".$project->ScheduledBy;
					if ($project->ScheduledBy != $project->WorkedBy){
					$entries.=", ".$project->WorkedBy;
					}
					if ($project->ProofedBy != $project->ScheduledBy && $project->ProofedBy != $project->WorkedBy){
						$entries.=", ".$project->ProofedBy;
					}
					if ($project->CompletedBy != $project->ScheduledBy && $project->CompletedBy != $project->WorkedBy && $project->CompletedBy != $project->ProofedBy){
						$entries.=", ".$project->ProofedBy;
					}
					
					$entries .= "</td>";
					
					$entries .= "<td class=\"button edit\"><a href=\"".base_url()."editentry/".$project->id . "\" class=\"edit\">Edit</a></td>";

					$entries .= "</tr>\n";
				}
				$entries .= "</tbody>\n";
				$entries .= "</table>\n";
			}
			else
			{
				$entries = "No entries since last invoice.\n";


			}
		return $entries;
		}
	}


}


// ------------------------------------------------------------------------

/**
 * InvoiceTotalsByDate($client,$StartDate,$EndDate)
 *
 * Calculates totals for invoice
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('InvoiceTotalsByDate'))
{

	function  InvoiceTotalsByDate($client,$StartDate,$EndDate,$Totaljobs, $excludelist =  array())
	{
	
		$CI =& get_instance();

		  //Get entry totals from db
		  $CI->db->select_sum('Hours','Hours');
		  $CI->db->select_sum('NewSlides','NewSlides');
		  $CI->db->select_sum('EditedSlides','EditedSlides');
		  $CI->db->from('zowtrakentries');
		  $CI->db->where('Client',$client);
			$CI->db->where('Status','COMPLETED');
		  $CI->db->where('DateOut >=', $StartDate);
		  $CI->db->where('DateOut <= ', $EndDate);
		  foreach ($excludelist as $excludeentry){
		  	$CI->db->where('id != ', $excludeentry);
		  }
		  $CI->db->where('Trash =',0);
		  $query = $CI->db->get();

		  //Get client details from db
		  $CI->load->model('trakclients', '', TRUE);
		  $query2 = $CI->trakclients->GetEntry($options = array('CompanyName' => $client));

 		  //Convert dates from db to human format
		  $StartDate = date( 'd/M/Y',strtotime($StartDate));
		  $EndDate = date( 'd/M/Y',strtotime($EndDate));
		  
		  //Apply edit price
		  $subtotal=$query->row()->EditedSlides*$query2->PriceEdits;
		  //Add slides and divide by slides per hour
		  $subtotal=$subtotal+$query->row()->NewSlides;
		  $subtotal=$subtotal/5;
		  //Add hours to get the total
		  $htotal=$subtotal+$query->row()->Hours;



		  $total ="<div id=\"reportTotals\">\n";
		  $total .="   <div id=\"reportMain\">\n";

		  $total .= "      <h3>Total billable hours: ".number_format($htotal, 1, '.', ',');
		  //if ($query2->RetainerHours!=0){
			//	if (date("m",strtotime($StartDate))==date("m",strtotime($EndDate))){
			//		$Retainerleft=$query2->RetainerHours-$htotal;
			//		$total .="      <br />Hours left in retainer: ".$Retainerleft;
			//	}
		 // }
		//	
		  $total .= " | Price: ";
		 	$tprice =_fetchClientMonthPrice($query2,$htotal);
			 $total .=number_format($tprice, 2, '.', ',')." ".$query2->Currency;
			 $invoicetotal=number_format($tprice*$htotal, 2, '.', ',');
			 $total .= " | Total: ".$invoicetotal." ".$query2->Currency;
		  $total .= "      </h3>\n" ;
			
		  $total .= "  </div>\n" ;
		  $total .="  <div id=\"totals\"><p>\n";
				$xxtotal ="      Total Hours: ".$query->row()->Hours." | \n";
				$xxtotal .="      Total New Slides: ".$query->row()->NewSlides." | \n";
				$xxtotal .="      Total Edited Slides: ".$query->row()->EditedSlides."  \n";
		  $gtotal=$query->row()->NewSlides+$query->row()->EditedSlides;
		  if ($excludelist){		  	
			$total .="<strong>Total Jobs: ".($Totaljobs -count($excludelist))." | Total Slides: ".$gtotal."</strong><br/>\n".$xxtotal;
		  }
		  else {		  	
			$total .="<strong>Total Jobs: ".$Totaljobs." | Total Slides: ".$gtotal."</strong><br/>\n".$xxtotal;
		  }

		  //Get client numbers from db

		  //$total .="      <strong><span>Total billable hours: ".$htotal."</span></strong></p>\n";
		  //If client has retainer, show numbers
			
			// $total .= " <p>Price: ";
		 // if ($query2->RetainerHours!=0){
			//  $total .="      <p id=\"retainer\">Retainer: ".$query2->Retainer."</p>\n";
		 // }
		$total .="  </div>\n";
		//$total .=getPastInvoices($client);		
		$total .="  </div>\n";
		
		$invoicedata ['page']=$total ;
		$invoicedata ['price']=$tprice ;
		$invoicedata ['billedhours']=$htotal ;
		$invoicedata ['invoicetotal']=$invoicetotal ;
		$invoicedata ['invoicejobs']=$Totaljobs ;
		$invoicedata ['SumHours']=$query->row()->Hours ;
		$invoicedata ['SumEditedSlides']=$query->row()->NewSlides ; #**** WRONG
		$invoicedata ['SumNewSlides']=$query->row()->EditedSlides ;
		
	return $invoicedata;

	}

}

// ------------------------------------------------------------------------

/**
 * csvInvoiceTotals($client,$StartDate,$EndDate)
 *
 * Calculates totals for new csv invoice
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('csvInvoiceTotals'))
{

	function  csvInvoiceTotals($clientdata,$StartDate,$EndDate, $excludelist =  array())
	{
	
		$CI =& get_instance();

		  //Get entry totals from db
		  $CI->db->select_sum('Hours','Hours');
		  $CI->db->select_sum('NewSlides','NewSlides');
		  $CI->db->select_sum('EditedSlides','EditedSlides');
		  $CI->db->from('zowtrakentries');
		  $CI->db->where('Client',$clientdata->CompanyName);
			$CI->db->where('Status','COMPLETED');
		  $CI->db->where('DateOut >=', $StartDate);
		  $CI->db->where('DateOut <= ', $EndDate);
		  $CI->db->where('Trash =',0);
		  foreach ($excludelist as $excludeentry){
		  	$CI->db->where('id != ', $excludeentry);
		  }
		  $query = $CI->db->get();

		  //Get client details from db
		  //$CI->load->model('trakclients', '', TRUE);
		  //$query2 = $CI->trakclients->GetEntry($options = array('CompanyName' => $client));

 		  //Convert dates from db to human format
		  $StartDate = date( 'd/M/Y',strtotime($StartDate));
		  $EndDate = date( 'd/M/Y',strtotime($EndDate));
		  
		  //Apply edit price
		  $subtotal=$query->row()->EditedSlides*$clientdata->PriceEdits;
		  //Add slides and divide by slides per hour
		  $subtotal=$subtotal+$query->row()->NewSlides;
		  $subtotal=$subtotal/5;
		  //Add hours to get the total
		  $htotal=$subtotal+$query->row()->Hours;

			$total['billablehourstotal']= $htotal;
			$total['newtotal']= $query->row()->NewSlides;
			$total['editstotal']= $query->row()->EditedSlides;
			$total['hourstotal']= $query->row()->Hours;
			$total['editshours']= $query->row()->EditedSlides*$clientdata->PriceEdits/5;
			$total['newslidehours']= $query->row()->NewSlides/5;
		 	$tprice =_fetchClientMonthPrice($clientdata,$htotal);
			$total['price']=number_format($tprice, 2, '.', ',');
			$total['revenue']=number_format($tprice*$htotal, 2, '.', ',');
			$total['totalslides']=$query->row()->NewSlides+$query->row()->EditedSlides;
			$total['currency']=$clientdata->Currency;
			if ($clientdata->Country =="The Netherlands" || $clientdata->Country =="Netherlands" ){
				$total['VAT']=TRUE;
			}
			else{
				$total['VAT']=FALSE;
			}

	return $total;

	}

}

// ------------------------------------------------------------------------

/**
 * csvPastInvoiceTotals($client,$StartDate,$EndDate)
 *
 * Calculates totals for existing csv invoice
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('csvPastInvoiceTotals'))
{

	function  csvPastInvoiceTotals($clientdata,$invoicenumber)
	{
	
		$CI =& get_instance();

		  //Get entry totals from db
		  $CI->db->select_sum('Hours','Hours');
		  $CI->db->select_sum('NewSlides','NewSlides');
		  $CI->db->select_sum('EditedSlides','EditedSlides');
		  $CI->db->select_avg('InvoicePrice','InvoicePrice');
		  $CI->db->from('zowtrakentries');

			$CI->db->where('Invoice',$invoicenumber);

		  $CI->db->where('Trash =',0);
		  $query = $CI->db->get();



		  
		  //Apply edit price
		  $subtotal=$query->row()->EditedSlides*$clientdata->PriceEdits;
		  //Add slides and divide by slides per hour
		  $subtotal=$subtotal+$query->row()->NewSlides;
		  $subtotal=$subtotal/5;
		  //Add hours to get the total
		  $htotal=$subtotal+$query->row()->Hours;

			$total['billablehourstotal']= $htotal;
			$total['newtotal']= $query->row()->NewSlides;
			$total['editstotal']= $query->row()->EditedSlides;
			$total['hourstotal']= $query->row()->Hours;
			$total['editshours']= $query->row()->EditedSlides*$clientdata->PriceEdits/5;
			$total['newslidehours']= $query->row()->NewSlides/5;
			$tprice=$query->row()->InvoicePrice;
			$total['price']=$tprice;
			$total['revenue']=number_format($tprice*$htotal, 2, '.', ',');
			$total['totalslides']=$query->row()->NewSlides+$query->row()->EditedSlides;
			$total['currency']=$clientdata->Currency;
			if ($clientdata->Country =="The Netherlands" || $clientdata->Country =="Netherlands" ){
				$total['VAT']=TRUE;
			}
			else{
				$total['VAT']=FALSE;
			}

	return $total;

	}

}


// ------------------------------------------------------------------------

/**
 * InvoiceTotalsByNumber($client,$StartDate,$EndDate)
 *
 * Calculates totals for invoice
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('InvoiceTotalsByNumber'))
{

	function  InvoiceTotalsByNumber($invoice,$clientName)
	{
	
		$CI =& get_instance();

		  //Get entry totals from db
		  $CI->db->select_sum('Hours','Hours');
		  $CI->db->select_sum('NewSlides','NewSlides');
		  $CI->db->select_sum('EditedSlides','EditedSlides');
		  //$CI->db->count_all_results();
		  $CI->db->from('zowtrakentries');
		  $CI->db->where('Invoice',$invoice);
		  $CI->db->where('Trash =',0);
		  $query = $CI->db->get();

		  //Get client details from db
		  $CI->load->model('trakclients', '', TRUE);
		  
		  
		  $query2 = $CI->trakclients->GetEntry($options = array('CompanyName' => $clientName));

		  //Apply edit price
		  $subtotal=$query->row()->EditedSlides*$query2->PriceEdits;
		  //Add slides and divide by slides per hour
		  $subtotal=$subtotal+$query->row()->NewSlides;
		  $subtotal=$subtotal/5;
		  //Add hours to get the total
		  $htotal=$subtotal+$query->row()->Hours;

		  //$total = "<p>".$this->db->last_query()."</p>";

		  $total ="<div id=\"reportTotals\">\n";
		  $total .="   <div id=\"reportMain\">\n";
		  $total .="      <h3>\n";
		  $total .="      <a href=\"".site_url()."invoicing/clientinvoices/".$query2->ID."\">".$query2->CompanyName."</a> invoice number: ".$invoice;
		  //$total .= " <br/> from  ".$StartDate." to  ".$EndDate.".\n ";
		  $total .= "      <br/>\n" ;
		  $total .= "      Total hours billed: ".$htotal;
		 // if ($query2['0']->Retainer!=0){
		//		if (date("m",strtotime($StartDate))==date("m",strtotime($EndDate))){
		//			$Retainerleft=$query2->Retainer-$htotal;
		//			$total .="      <br />Hours left in retainer: ".$Retainerleft;
		//		}
		 // }
		 
		  $total .= " | Price: ";
		  
		  $CI =& get_instance();

				  //Get entry totals from db

				  
				  $CI->db->select('PricePerHour');
				  $CI->db->from('zowtrakinvoices');
				  $CI->db->where('InvoiceNumber',$invoice);
				  $CI->db->where('Trash =',0);
		  		  $query3 = $CI->db->get();

		 	 $tprice =$query3->row()->PricePerHour;

			 $total .=number_format($tprice, 2, '.', ',')." ".$query2->Currency;
			 $invoicetotal=number_format($tprice*$htotal, 2, '.', ',');
			 $total .= " | Total: ".number_format($tprice*$htotal, 2, '.', ',')." ".$query2->Currency;
		  $total .= "      </h3>\n" ;
		  $total .= "  </div>\n" ;

		  $total .="  <div id=\"totals\"><p>\n";
		  //if ($query->row()->Hours!=0){ 
		  	$total .=$query->row()->Hours." Hours | \n";
		 //}
		  //if ($query->row()->NewSlides!=0){ 
		  		$total .=$query->row()->NewSlides." New | \n";
			//}
		  //if ($query->row()->EditedSlides!=0){ 
		  		$total .=$query->row()->EditedSlides." Edits \n";
			//}


		  $total .=" </p>\n";
		  //If client has retainer, show numbers
		  //if ($query2['0']->Retainer!=0){
		//	  $total .="      <p id=\"retainer\">Retainer: ".$query2->Retainer."</p>\n";
		 // }
		$total .="  </div>\n";
		//$total .=getPastInvoices($client);		
		$total .="  </div>\n";
	//return $total;
		$invoicedata ['page']=$total ;
		$invoicedata ['price']=$tprice ;
		$invoicedata ['billedhours']=$htotal ;
		$invoicedata ['invoicetotal']=$invoicetotal ;
		$invoicedata ['invoicejobs']=$query->row()->EditedSlides ;
		$invoicedata ['SumHours']=$query->row()->Hours ;
		//$invoicedata ['SumEditedSlides']=$query->row()->NewSlides ; #### WRONG WRONG
		//$invoicedata ['SumNewSlides']=$query->row()->EditedSlides ; #### WRONG WRONG
		
		$invoicedata ['SumEditedSlides']=$query->row()->EditedSlides ; 
		$invoicedata ['SumNewSlides']=$query->row()->NewSlides ; 
				
	return $invoicedata;

	}

}





// ------------------------------------------------------------------------

/**
 * getPastInvoices($current)
 *
 * Calculates totals for invoice
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('getPastInvoices'))
{

	// ################## Load invoice list ##################	
	function  getPastInvoices($current)
	{

		$CI =& get_instance();
		$CI->db->distinct();
		$CI->db->select();
		$CI->db->where('Client',$current);
		$getinvoices = $CI->db->get('zowtrakinvoices');
		$pastinvoices= $getinvoices->result_array();	

		
		$subentries ="<table><thead><tr><th>Invoice Number</th><th>Originators</th><th>Status</th><th>Total</th><th>Date Issued</th><th>Date Paid</th></tr><thead><tbody>";
		$entriescount = 0;
		
		//Read and count all invoice numbers (e.g. skip "NOT BILLED" entries)
		foreach($pastinvoices as $invoice)
		{
				$subentries .= "<tr>";
				$subentries .= "<td><a href=\"".site_url()."invoicing/viewinvoice/".$invoice['InvoiceNumber']."\">".$invoice['InvoiceNumber']."</a></td>";
				$subentries .= "<td>".$invoice['Originators']."</td>";
				$subentries .= "<td>".$invoice['Status']."</td>";
				$subentries .= "<td>".$invoice['InvoiceTotal']."</td>";
				$subentries .= "<td>".$invoice['BilledDate']."</td>";
				$subentries .= "<td>".$invoice['PaidDate']."</td>";
				$subentries .= "</tr>";
				//$subentries .= "     <p><a href=\"".site_url()."invoicing/viewinvoice/".$invoice['Invoice']."\">".$invoice['Invoice']."</a></p>\n";
				//$subentries .= "     <p><a href=\"".site_url()."invoicing/viewinvoice/".$invoice['Invoice']."\">".$invoice['Invoice']."</a> (".$invoice['Status'].")</p>\n";
				$entriescount += 1;

		}
		$subentries .="</tbody></table>";

		$entries = "  <div id=\"pastinvoices\" class=\"clearfix\">\n";
		$entries .="<h3>Statement of account</h3>";
		$entries .= "     <p>".$entriescount." existing invoices:</p>\n";

		$entries .= $subentries;
		$entries .= "    </div>\n";		
        return $entries;

	}
}



/* End of file invoice_helper.php */
/* Location: ./system/application/helpers/invoice_helper.php */