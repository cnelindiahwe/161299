<?php

//Problem online is uri segment number - please read hidden client input 

class createinvoicetable extends MY_Controller {


	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('userpermissions', 'url'));
		
		$ZOWuser=_getCurrentUser();
			if ($ZOWuser!="miguel" &&	$ZOWuser!="sunil.singal") {
			redirect('trackingnew');
		}
		
		$this->load->helper(array('financials'));
		
		$this->load->model('trakclients', '', TRUE);
		$ClientList= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));		

		foreach($ClientList as $current)
		{
			echo $this->_getPastInvoices($current->CompanyName);
		
		}
	}
	// ################## Load invoice list ##################	
	function  _getPastInvoices($current)
	{


		$this->db->distinct();
		$this->db->select('Invoice');
		$this->db->select('Status');
		$this->db->where('Client',$current);
		$getentries = $this->db->get('zowtrakentries');
		$pastinvoices= $getentries->result_array();	

		$subentries ="";	
		$entriescount = 0;
		
		//Read and count all invoice numbers (e.g. skip "NOT BILLED" entries)
		foreach($pastinvoices as $invoice)
		{
			if ($invoice['Invoice']!="NOT BILLED")
			{
				//$subentries .= "     <p><a href=\"".site_url()."invoicing/viewinvoice/".$invoice['Invoice']."\">".$invoice['Invoice']."</a></p>\n";
				$subentries .= "     <p><a href=\"".site_url()."invoicing/viewinvoice/".$invoice['Invoice']."\">".$invoice['Invoice']."</a> (".$invoice['Status'].")</p>\n";
				$subentries .= $this-> _InvoiceTotalsByNumber($invoice['Invoice'],$current);
				$entriescount += 1;
			}	
		}
		

		$entries = "  <div id=\"pastinvoices\">\n";
		$entries .= "     <p>".$entriescount." existing invoices:</p>\n";

		$entries .= $subentries;
		$entries .= "    </div>\n";		
    return $entries;

	}

	function  _InvoiceTotalsByNumber($invoice,$clientName)
	{
	
		$CI =& get_instance();

		  //Get entry totals from db
		  $CI->db->select_sum('Hours','Hours');
		  $CI->db->select_sum('NewSlides','NewSlides');
		  $CI->db->select_sum('EditedSlides','EditedSlides');
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
		 	$tprice =_fetchClientMonthPrice($query2,$htotal);
			 $total .=number_format($tprice, 2, '.', ',')." ".$query2->Currency;
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
	return $total;

	}










}
	


/* End of file viewinvoice.php */
/* Location: ./system/application/controllers/billing/viewinvoice.php */
?>