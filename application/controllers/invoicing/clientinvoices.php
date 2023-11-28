<?php

class Clientinvoices extends MY_Controller {


	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('userpermissions', 'url'));
		
		_superuseronly(); 
		
		$this->load->helper(array('form','reports','invoice'));
		
		//Read form input values
		if ($this->input->post('client')) {
			$clientid =$this->input->post('client');
		}
		else if ($this->uri->segment(3)!="")
		{$clientid= $this->uri->segment(3);}
		if (!isset($clientid)) {
			redirect('/invoicing/invoicing', 'refresh');
		}

		$clientid= str_replace('_', ' ', $clientid);
		$clientid= str_replace('%20', ' ', $clientid);
		$clientid=str_replace("~","&",$clientid);

		
		
			$this->load->model('trakclients', '', TRUE);
			$currentclient = $this->trakclients->GetEntry($options = array('ID' => $clientid));
		
		if ($currentclient){
			$this->session->set_flashdata('Client', $currentclient->CompanyName);

	
		//Get entry totals from db


		$this->load->model('trakinvoices', '', TRUE);
	    $DateLastInvoice =$this->trakinvoices->_getDateLastInvoice( $currentclient->CompanyName);
		
		$StartDate = date('Y-m-d', strtotime('+1 day '.$DateLastInvoice ));

		
		  $this->db->from('zowtrakentries');
		  $this->db->where('DateOut >=',$DateLastInvoice);
		  $this->db->where('Invoice','NOT BILLED');
		  $this->db->where('Status','Completed');
		  //$this->db->order_by('DateOut','Desc');
		  $this->db->where('Trash =',0);
		  $query = $this->db->get();
		  	
		 // echo  $this->db->last_query();
		  if ($query){
		  		$lastentries =$query->result_array();
				if (count($lastentries)!=0) {
					$ReportStartDate=$StartDate;
					$ReportEndDate= date('Y-m-d', strtotime('now' ));
					//deleted july 2o14
					//$this->load->model('trakentries', '', TRUE);
					//$lastentries = $this->trakentries->GetEntry($options = array('trash' => '0'));
					$this->session->set_flashdata('ReportStartDate',  $ReportStartDate);
					$this->session->set_flashdata('ReportEndDate', $ReportEndDate);
					redirect('/invoicing/invoicesreport', 'refresh');
				}
				else{
					redirect('/invoicing/invoicing', 'refresh');
				}
			}
			else{
				redirect('/invoicing/invoicing', 'refresh');
			}
		}
		else {
			redirect('/invoicing/invoicing', 'refresh');
		}


	}
	
	/*function _getReport($Client)
	{
	
	$report="<h3>Report for ";
	$report.=$Client->CompanyName."</h3>";
	
	$report.="<p>Existing invoices</p>";
	//$report.=getPastInvoices($Client->CompanyName);
	return $report;
		 
	}*/

}

/* End of file Clientreport .php */
/* Location: ./system/application/controllers/reports/Clientreport .php */
?>