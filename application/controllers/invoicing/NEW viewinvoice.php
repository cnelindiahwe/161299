	<?php

//Problem online is uri segment number - please read hidden client input 

class Viewinvoice extends MY_Controller {


	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('clients','general','form','userpermissions', 'url','invoice','reports','financials'));
		
		$zowuser=_superuseronly(); 

		$invoicenumber=$this->uri->segment(3);
		
		$this->load->model('trakclients', '', TRUE);
		$ClientList= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));
		
		$clientName=$this-> _getclient($invoicenumber);

	 	$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		
		$templateVars['pageOutput'] .=  $this->_getinvoicingpage($invoicenumber,$clientName);

		$templateVars['pageOutput'] .= "<div class=\"content\">";
		$templateVars['pageOutput'] .= getInvoiceByNumber($invoicenumber,$clientName);
		$templateVars['pageOutput'] .= "</div><!-- content -->";

		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Invoicing";
		$templateVars['pageType'] = "invoice";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');
	}

	// ################## top ##################	
	function  _getinvoicingpage($invoicenumber,$clientName)
	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>Invoices</h1>";
			$entries .=$this-> _ViewInvoiceForm($invoicenumber,$clientName);

			//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";
		
			
			return $entries;

	}
	
	// ################## View Invoice Form ##################	
	function  _ViewInvoiceForm($invoice,$client)
	{

		$this->db->limit(1);
		$this->db->select('Status');
		$this->db->where('Invoice',$invoice);
		$getentries = $this->db->get('zowtrakentries');
		$status = $getentries->row(0)->Status;

		$attributes = array( 'id' => 'ReportFilter');

		$ViewInvoiceForm = form_open(site_url().'invoicing/invoicestatus',$attributes )."\n";
		
		$ViewInvoiceForm.="<fieldset >";
		$ViewInvoiceForm .=form_hidden('Invoice',$invoice);
		$ViewInvoiceForm .=form_hidden('Client',$client);
		$ViewInvoiceForm .= form_label('Status:','Status')."\n";
		if ($status=='PAID') {
			$options = array('BILLED'=>'Billed', 'PAID'=>'Paid');
		}
		else {
			$options = array('CANCEL'=>'Cancel','BILLED'=>'Billed', 'PAID'=>'Paid');
		}
		$more = 'id="Status" class="Status"';	
		$ViewInvoiceForm .=form_dropdown('Status', $options,$status,$more);


		$ViewInvoiceForm.="</fieldset >";
		
		$ViewInvoiceForm.="<fieldset >";
		$ndata = array('name' => 'submit','value' => 'Change','class' => 'submitButton');
		$ViewInvoiceForm .= form_submit($ndata)."\n";
		$ViewInvoiceForm .="</fieldset>";
		$ViewInvoiceForm .= form_close()."\n";

		return $ViewInvoiceForm;
	}
	
	
		// ################## View Invoice Form ##################	
	function  _getclient($invoicenumber)
	{

		$this->load->model('trakclients', '', TRUE);
		

		$i = 0;
		$clientcode ='';
		$longcode='';
		while ($clientcode =='') {
			$longcode=substr($invoicenumber, $i,1);
			if (is_numeric($longcode)){
				$clientcode=substr($invoicenumber,0, $i);
			}
		    $i++;
		}
		$currentclientcode = $this->trakclients->GetEntry($options = array('ClientCode' => $clientcode));
		return $currentclientcode['0']->CompanyName;
	}

	
}

/* End of file viewinvoice.php */
/* Location: ./system/application/controllers/billing/viewinvoice.php */
?>