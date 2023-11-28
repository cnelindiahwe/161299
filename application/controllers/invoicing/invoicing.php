<?php

class Invoicing extends MY_Controller {


	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('userpermissions','url'));
		$ZOWuser=_superuseronly(); 

		$this->load->helper(array('form','url','invoice','financials'));

		$this->load->model('trakclients', '', TRUE);
		$ClientList= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));

	

		$StartDate = date( 'Y-m-1', strtotime('now'));
		$EndDate = date( 'Y-m-d', strtotime('now'));

	 	$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);

		$templateVars['pageOutput'].= $this->_getinvoicingpage($ClientList);
		$templateVars['pageOutput'].= $this->_getTotals($ClientList,$EndDate);

		$this->load->model('Trakinvoices', '', TRUE);
		//$clientlist=$this->_getClients(); 
		$templateVars['pageOutput'] .= $this->_getBilledInvoiceslist();
		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Invoicing";
		$templateVars['pageType'] = "invoicing";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}

	// ################## top ##################	
	function  _getinvoicingpage($ClientList)
	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>Invoices</h1>";
			$entries .=_clientscontrol($ClientList);

			//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";
		
			
			return $entries;

	}





	
	// ################## Generate client totals ##################	
	function  _getTotals($Clientlist,$EndDate)
	{

		$totalusd=0;
		$totaleuro=0;
		$totalhours=0;
		$total ="<table>\n";
		$total .="<thead><tr><th class=\"header client\">Client</th><th class=\"header revenue\">Revenue</th><th class=\"header currency\">Currency</th><th class=\"header totaljobs\">Total Jobs</th><th class=\"header total\">Total Hours</th><th class=\"header slides\">New</th><th class=\"header slides\">Edits</th><th class=\"header slides\">Hours</th><th class=\"header button\">Last Invoice</th><th class=\"header \"></th></tr></thead>\n<tbody>";
		$this->load->model('trakinvoices', '', TRUE);
		foreach($Clientlist as $client)
		{
		 
		  //$DateLastInvoice =$this->trakinvoices->_getDateLastInvoice($client->CompanyName);
		   //if ($DateLastInvoice=='') {$DateLastInvoice='2010-01-01';}
		  
		  $DateLastInvoice='2010-01-01';
		  $StartDate = date( 'Y-m-d', strtotime('+1 day '.$DateLastInvoice));
		  
		  //Get entry totals from db
		  
		  $this->db->select_sum('Hours','Hours');
		  $this->db->select_sum('NewSlides','NewSlides');
		  $this->db->select_sum('EditedSlides','EditedSlides');
		  $this->db->from('zowtrakentries');
		  $this->db->where('Client',$client->CompanyName);
			$this->db->where('Status','COMPLETED');
			$this->db->where('Invoice','NOT BILLED');
		 
		  $this->db->where('DateOut >=', $StartDate);
		  $this->db->where('DateOut <= ', $EndDate);
		   $this->db->where('Trash =',0);

		  $query = $this->db->get();
		  
		  //countjobs
		  $this->db->from('zowtrakentries');
		  $this->db->where('Client',$client->CompanyName);
			$this->db->where('Status','COMPLETED');
			$this->db->where('Invoice','NOT BILLED');
		  $this->db->where('DateOut >=', $StartDate);
		  $this->db->where('DateOut <= ', $EndDate);
		   $this->db->where('Trash =',0);		  
		  $totaljobs=$this->db->count_all_results();
			
			
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
			 
			 $tprice =_fetchClientMonthPrice($query2,$htotal);
			 $invoicerevenue=$tprice*$htotal;
			 $total .= "<td>".number_format($invoicerevenue, 2, '.', ',')."</td>\n";
			 $total.="<td>".$query2->Currency."</td>\n";
			 $total.="<td>".$totaljobs."</td>\n";
			 $total.="<td>".number_format($htotal, 1, '.', ',')."</td>\n";
			 
			 //add to totals
			 $totalhours=number_format($htotal+$totalhours, 1, '.', ',');
			 if ($query2->Currency=="EUR"){
				$totaleuro=$totaleuro+$invoicerevenue;
			 }
			 else if ($query2->Currency=="USD"){
			 	
				$totalusd=$totalusd+$invoicerevenue;
				
			 }
			 
			  
			 
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
				$total.="<td>".number_format($query->row()->Hours, 1, '.', ',')."</td>\n";
			 }
			 else {
				$total.="<td></td>\n";
			 }
			 
			   $DateLastInvoice =$this->trakinvoices->_getDateLastInvoice($client->CompanyName);
			    if ($DateLastInvoice=='') {$DateLastInvoice='2010-01-01';}
	
			 $total.="<td>".date( 'd/M/Y', strtotime($DateLastInvoice))."</td>\n";
			 $cleanclientname=str_replace("&","~",$client->CompanyName);
			 $total.="<td><a href=\"".site_url()."invoicing/newinvoice/".$cleanclientname. "\">New Invoice</a></td>\n";
	
			
			$total .="</tr>\n";
		}	
	}
	$total.="</tbody></table></div>\n";
	$totaleuro="&euro;".number_format($totaleuro,2);
	$totalusd="$".number_format($totalusd,2);
	$total = "<div class=\"invoices\"><h3>".$totalhours." hours pending invoices (".$totaleuro." and ". $totalusd.")</h3>".$total;
	return $total;
		

	}


	// ################## Load client list ##################	
	function  _getClients()
	{
	
		$this->load->model('trakclients', '', TRUE);
		$getentries = $this->trakclients->GetEntry();
		return $getentries;

	}


	// ################## Load client list ##################	
	function  _getBilledInvoiceslist()
	{

		$invoicelist="";
		$invoicesublist="";
		$clienttotal="";
		$clientcurrency="";
		$usdtotal="";
		$eurototal="";
		
		$this->db->where("Status","BILLED"); 
		$this->db->order_by("Client","asc"); 
		$billedinvoices = $this->db->get('zowtrakinvoices');
		foreach ($billedinvoices->result() as $invoice) {
			
			if (!isset($clientflag)) 
				{
				$clientflag=$invoice->Client;
				$clientcurrency=$invoice->Currency;
				
				}
			
			if	($clientflag!=$invoice->Client) {

					if ($clientcurrency=="EUR") {$invoicecurrencysymbol="&euro;";}
					else if ($clientcurrency=="USD") {$invoicecurrencysymbol="$";}	
					
					$invoicelist.="</div><div><h5>".$clientflag." ".$invoicecurrencysymbol.number_format($clienttotal)." </h5>";
					$invoicelist.=$invoicesublist;
					if ($clientcurrency=="EUR")
					{$eurototal=$eurototal+$clienttotal;}
					else if ($clientcurrency=="USD")
					{$usdtotal=$usdtotal+$clienttotal;}
					
					$clientflag=$invoice->Client;
					$clientcurrency=$invoice->Currency;
					$clienttotal="";
					$invoicesublist="";
			}
			
			if ($clientcurrency=="EUR") {$invoicecurrencysymbol="&euro;";}
			else if ($clientcurrency=="USD") {$invoicecurrencysymbol="$";}	
			$invoicesublist.="<p>";
			$invoicesublist.="<a href=\"".site_url()."invoicing/viewinvoice/".$invoice->InvoiceNumber."\">".$invoice->InvoiceNumber."</a> ";
			$invoicesublist.=$invoicecurrencysymbol."<strong>".number_format($invoice->InvoiceTotal)."</strong>";
			$invoicesublist.=" due on ".date('j F Y',strtotime($invoice->DueDate));
			if (strtotime($invoice->DueDate)<time()) $invoicesublist.="***";
			$invoicesublist.="</p>";
			$clienttotal=$clienttotal+$invoice->InvoiceTotal;

		}
		
		
		
		if ($clientcurrency=="EUR")
		{
			$eurototal=$eurototal+$clienttotal;
			$invoicelist.="</div><div><h5>".$clientflag." &euro;".number_format($clienttotal)."</h5>";
		}
		else if ($clientcurrency=="USD")
		{
			$usdtotal=$usdtotal+$clienttotal;
			$invoicelist.="</div><div><h5>".$clientflag." $".number_format($clienttotal)."</h5>";
		}
		$invoicelist.=$invoicesublist;
		
		$clientflag=$invoice->Client;
		$clientcurrency=$invoice->Currency;
		$clienttotal="";
		$invoicesublist="";
		$invoicelist= "<div class=\"invoices\"><h3> Pending Invoices: &euro;".number_format($eurototal)." and $".number_format($usdtotal)."</h3><div>".$invoicelist;
		
		$invoicelist.="</div>";
		return $invoicelist;
	}


}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>