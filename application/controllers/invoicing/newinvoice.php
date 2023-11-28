<?php

class Newinvoice extends MY_Controller {


	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('userpermissions', 'url'));
		
		$templateVars['ZOWuser']=_superuseronly(); 
		
		$this->load->helper(array('form','invoice','reports','financials'));
		
		//Read form input values
		$client= $this->uri->segment(3);
		$client= str_replace('_', ' ', $client);
		$client= str_replace('%20', ' ', $client);
		$client=str_replace("~","&",$client);

		$this->load->model('trakclients');
		$clientobject =$this->trakclients->GetEntry($options = array('CompanyName'=>$client));

		//Check for excluded entries
		$excludearray =$this->input->post();
		

		$excludelist=array();
		$excludeflat="";
		if ($excludearray) {
			
			$excludelist=array();
			foreach (array_keys($excludearray)as $key) {
				if ($key !='excludeSubmit') {
				    $excludelist []= str_replace("exclude-","",$key);
				    if($excludeflat!=""){$excludeflat.=","; }
				    $excludeflat.= $excludelist [count($excludelist)-1];

				}
			}
			$this->session->set_flashdata('excludelist',$excludeflat);
		} 


		
		//$StartDate =$this->trakinvoices->_getDateLastInvoice($client);
		
		//if ($StartDate==""){$StartDate='2010-06-01';}//If no previous invoice
		$StartDate='2010-06-01';
		
		
		$StartDate = date('Y-m-d', strtotime('+1 day'.$StartDate));
		$EndDate = date('Y-m-d', strtotime('now'));
		$this->load->model('trakinvoices', '', TRUE);

		$invoicedata=getInvoiceByDate($client,$StartDate,$EndDate, $excludelist);
		$templateVars['pageOutput'] = "<div class=\"content\">";
		$templateVars['pageOutput'] .=  $invoicedata['page'];
		$templateVars['pageOutput'].=getPastInvoices($client);
		
		if ($templateVars['pageOutput']!="No entries since last invoice.\n") {
			$templateVars['pageOutput']=$this-> _getnewinvoicetop($clientobject,$StartDate,$EndDate,$invoicedata,$excludelist).$templateVars['pageOutput'];
		}
		else {
			$templateVars['pageOutput'] =$this-> _getnewinvoicetop($client).$templateVars['pageOutput'];

		}
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']).$templateVars['pageOutput'];

		//	$this->load->model('trakclients', '', TRUE);
		//	$ClientList= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));


	
		
		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Invoicing";
		$templateVars['pageType'] = "invoice";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}
	
	// ################## top ##################	
	function  _getnewinvoicetop($clientobject,$StartDate="",$EndDate="",$invoicedata="",$excludelist=array())
	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>New Invoice for ".$clientobject->CompanyName."</h1>";
			if ($StartDate!="") {
				$entries .=$this->_NewInvoiceForm($clientobject,$StartDate,$EndDate,$invoicedata,$excludelist);
			}
			else{
				$entries .=$this->_NoInvoiceForm($clientobject->CompanyName);
			}

			//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";
		
			
			return $entries;

	}



	// ################## Get last invoice date ##################	
	function  _getDateLastInvoice($client)
	{

		$this->db->select_max('DateOut');
		$this->db->from('zowtrakentries');
		$this->db->where("Client ='".$client."'"); 
		$this->db->where("Invoice <>'NOT BILLED'");
		$query = $this->db->get();
		//echo $this->db->last_query();

		if ($query->row()->DateOut=="") {
			$this->db->select_min('DateOut');
			$this->db->from('zowtrakentries');
			$this->db->where("Client ='".$client."'"); 
			$query = $this->db->get();
			$StartDate = date('Y-m-d', strtotime('-1 day'.$query->row()->DateOut));
		}
		else
		{
		$StartDate =  $query->row()->DateOut;
		}
		return $StartDate;
	}

	// ################## New Invoice Form ##################	
	function  _NewInvoiceForm($clientobject,$StartDate,$EndDate,$invoicedata,$excludelist=array())
	{
		$excludecommas="";	
		foreach($excludelist as $excludename){
			if ($excludecommas!="") {
				$excludecommas.=",";				
			}
			$excludecommas.=$excludename;
		}
		$temptotal=str_replace(",","",$invoicedata['invoicetotal']);
		$newtotal= number_format(floatval($temptotal),2, '.', '');
		$attributes = array( 'id' => 'ReportFilter');
		$hidden = array('Client' => $clientobject->CompanyName,
						'DueDays' => $clientobject->PaymentDueDate,
						'Currency' => $clientobject->Currency,
						'PriceEdits' => $clientobject->PriceEdits,
						'PricePerHour' => $invoicedata['price'],
						'BilledHours' => $invoicedata['billedhours'],
						'InvoiceTotal' => $newtotal,								
						'SumHours' => $invoicedata['SumHours'],										
						'SumEditedSlides' => $invoicedata['SumEditedSlides'],										
						'SumNewSlides' => $invoicedata['SumNewSlides'],
						'Originators' => $invoicedata['Originators'],
						'ExcludeList' => $excludecommas												
						);
		$NewInvoiceForm = form_open(site_url().'invoicing/createinvoice',$attributes,$hidden)."\n";

		$NewInvoiceForm .="<fieldset>";
		$NewInvoiceForm.= form_label('Start:','StartDate')."\n";
		if ($StartDate!=""){$StartDate = date( 'd/M/Y',strtotime($StartDate));}
		$ndata = array('name' => 'StartDate', 'id' => 'StartDate', 'size' => '9', 'class'=>'StartDate', 'value'=>$StartDate);
		$NewInvoiceForm .= "\n".form_input($ndata)."\n";
		$NewInvoiceForm .="</fieldset>";

		$NewInvoiceForm .="<fieldset>";
		$NewInvoiceForm .= form_label('End:','EndDate')."\n";
		//If date comes from db, format it for human display
		if ($EndDate!=""){$EndDate = date( 'd/M/Y',strtotime($EndDate));}
		$ndata = array('name' => 'EndDate', 'id' => 'EndDate', 'size' => '9', 'class'=>'EndDate', 'value'=>$EndDate);
		$NewInvoiceForm .= "\n".form_input($ndata)."\n";
		$NewInvoiceForm .="</fieldset>";

		$NewInvoiceForm .="<fieldset>";
		$NewInvoiceForm .= form_label('Number:','InvoiceNumber')."\n";
		
		
			$this->db->select('ClientCode');
			$this->db->from('zowtrakclients');
			$this->db->where("CompanyName ='".$clientobject->CompanyName."'"); 
			$query = $this->db->get();

		
		$InvoiceNumber = $query->row()->ClientCode;
		//$InvoiceNumber .= date("Ym");
		//$TestInvoiceNumber .= $InvoiceNumber.'01';
		
		$InvoiceCount=0;
		$finished = false;
		while (! $finished)
		{ 
			$InvoiceCount+=1;
			if ($InvoiceCount<10) {
				$TestInvoiceNumber = $InvoiceNumber.date("mY").'0'.$InvoiceCount;
			}
			else{
				$TestInvoiceNumber = $InvoiceNumber.date("mY").$InvoiceCount;
			}
			$this->db->select('Invoice');
			$this->db->from('zowtrakentries');
			$this->db->limit(1);
			$this->db->where("Invoice ='".$TestInvoiceNumber ."'"); 
			$query = $this->db->get();
			if ($query->num_rows()==0) { $finished = true;   }
		}
		$InvoiceNumber=$TestInvoiceNumber;

		$ndata = array('name' => 'InvoiceNumber', 'id' => 'InvoiceNumber', 'size' => '9', 'class'=>'InvoiceNumber', 'value'=>$InvoiceNumber);
		$NewInvoiceForm .= "\n".form_input($ndata)."\n";
		$NewInvoiceForm .="</fieldset>";



		
		$NewInvoiceForm.="<fieldset>";
		$ndata = array('name' => 'submit','value' => 'Create','class' => 'submitButton');
		$NewInvoiceForm .= form_submit($ndata)."\n";
		$NewInvoiceForm  .= "<a href=\"".site_url()."invoicing\" class=\"cancelEdit\">Cancel</a>\n";
		$client= str_replace(' ', '_', $clientobject->CompanyName);
		$client= str_replace(' ', '%20', $client);
		$client=str_replace("&","~",$client);
		$NewInvoiceForm  .= "<a href=\"".site_url()."invoicing/csvinvoice/".$client."\" class=\"exportInvoice\">Export</a>\n";
		$NewInvoiceForm .="</fieldset>";
		$NewInvoiceForm .= form_close()."\n";

		return $NewInvoiceForm;
	}



	// ################## No New Invoice Form ##################	
	function  _NoInvoiceForm($client)
	{

		$attributes = array( 'id' => 'ReportFilter');
		
		$NoInvoiceForm = form_open(site_url().'billing',$attributes )."\n";

		$NoInvoiceForm.="<fieldset >";
		$NoInvoiceForm.= "You cannot create a new invoice without new entries";
		$NoInvoiceForm.="</fieldset >";

		$NoInvoiceForm.="<fieldset >";
		$NoInvoiceForm.= getPastInvoices($client);
		$NoInvoiceForm.="</fieldset >";
		
		$NoInvoiceForm.="<fieldset class=\"formbuttons\">";
		$ndata = array('name' => 'submit','value' => 'Back to Billing','class' => 'submitButton');
		$NoInvoiceForm .= form_submit($ndata)."\n";
		$NoInvoiceForm .="</fieldset>";
		$NoInvoiceForm .= form_close()."\n";

		return $NoInvoiceForm;
	}

}

/* End of file newinvoice.php */
/* Location: ./system/application/controllers/billing/newinvoice.php */
?>