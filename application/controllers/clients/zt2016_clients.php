<?php

class Zt2016_clients extends MY_Controller {

	
	function index()
	{
		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		//$this->load->helper(array('form','url','clients','general','userpermissions'));
		$this->load->helper(array('form','url','general','userpermissions'));

		$zowuser=_superuseronly(); 
		
		$templateData['ZOWuser']= _getCurrentUser();
		
		$templateData['title'] = 'Clients';
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this->_display_clients($templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData); 


	}
	

	// ################## display clients info ##################	
	function  _display_clients($ZOWuser)
	{
					
				
		#load clients info	
		$this->load->model('trakclients', '', TRUE);
		$ClientsData = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
		$TrashClientsData = $this->trakclients->GetEntry($options = array('Trash'=>'1','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
		
		
		#Create page
		//$page_content ='<div class="page_content">';
		$page_content ="\n";
		
		######### Display success message
		if($this->session->flashdata('SuccessMessage')){		
			$page_content.='<div class="alert alert-success" role="alert" style="">'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			//$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('SuccessMessage');
			$page_content.='</div>'."\n";
		}

		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			$page_content.='<div class="alert alert-danger" role="alert" style="">'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('ErrorMessage');
			$page_content.='</div>'."\n";
		}
		
		
		######### panel header
		$page_content.='<div class="panel panel-primary"><div class="panel-heading">'."\n"; 
		$page_content.='<h4>'.count($ClientsData)." existing clients";
		if ($TrashClientsData) {
			$page_content.=' <small>('.count($TrashClientsData)." Trashed)</small>"."\n";
		}

		
		########## New client button
		$page_content.= '<a href="'.site_url().'clients/zt2016_client_new'.'" class="btn btn-info btn-sm pull-right">New Client</a>'."\n";

		########## View trash button
		if ($TrashClientsData) {
				$page_content.= '<a href="'.site_url().'trash/zt2016_trash'.'" class="btn btn-warning btn-sm pull-right">View Trash</a>'."\n";
		}
		$page_content.="</h4>\n";
		$page_content.="<div class='clearfix'></div>\n";
		$page_content.="</div><!--panel-heading-->\n";

		
		######### panel body
		$page_content.='<div class="panel-body">'."\n";
		$page_content.='<div id="table_loading_message">Loading ... </div>'."\n";

		
		#fetch clients table
		$page_content .= $this-> _clients_table($ClientsData)	;		


		$page_content.="</div><!--panel body-->\n</div><!--panel-->\n";


  		if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal" ||	$ZOWuser=="alvaro.ollero") {
  			
  		}

		return $page_content;

	}	


	// ################## create client table ##################	
	function   _clients_table($ClientsData)
	{
			
			
		$ClientsTable ='<table class="table table-striped table-condensed responsive" style="width:100%;display:none;" id="clients_table">'."\n";
		$ClientsTable .="<thead><tr><th data-sortable=\"true\">Client</th><th data-sortable=\"true\">Client Code</th><th data-sortable=\"true\">First Year</th><th data-sortable=\"true\">Last Year</th><th data-sortable=\"true\">Group</th><th data-sortable=\"true\">Base price</th><th data-sortable=\"true\">Currency</th></tr></thead>\n";
		$ClientsTable .="<tfoot><tr><th></th><th></th><th data-sortable=\"true\">First Year</th><th data-sortable=\"true\">Last Year</th><th data-sortable=\"true\">Group</th><th data-sortable=\"true\">Base price</th><th data-sortable=\"true\">Currency</th></tr></tfoot>\n";
		$ClientsTable .="<tbody>\n";
		
		
		foreach($ClientsData as $ClientDetails)
		{
			$ClientsTable .="<tr>\n";
			
			$SafeClientName=str_replace(" ", "_", $ClientDetails->CompanyName);
			$SafeClientName=str_replace("&", "~", $SafeClientName);
			
			
			$ClientsTable .='<td> <a href="'.site_url().'clients/zt2016_client_info/'.$SafeClientName.'">'.$ClientDetails->CompanyName.'</a></td>'."\n";
			$ClientsTable .="<td>".$ClientDetails->ClientCode."</td>\n";
			$ClientsTable .="<td>".date("Y",strtotime($ClientDetails->FirstClientIteration))."</td>\n";


			$this->db->select_max('DateOut');
			$this->db->where('Client',$ClientDetails->CompanyName);
			$LastClientDateQuery = $this->db->get('zowtrakentries');
			
			#last client data
			if ($LastClientDateQuery->num_rows() > 0)
			{
			   $row = $LastClientDateQuery->row(); 
			  $LastClientDate= date("Y",strtotime($row->DateOut));
			} else{
				$LastClientDate= "NONE";
			}
			$ClientsTable .="<td>".$LastClientDate."</td>\n";
		
			If 	($ClientDetails->Group!="")	{
				$ClientsTable .="<td>".$ClientDetails->Group."</td>\n";
			} else {
				$ClientsTable .="<td>None</td>\n";				
			}
			
			$ClientsTable .="<td>".$ClientDetails->BasePrice."</td>\n";
			
			$ClientsTable .="<td>".$ClientDetails->Currency."</td>\n";
		
			$ClientsTable .="</tr>\n";
			
		}
		
		
		$ClientsTable .="</tbody>\n";
		$ClientsTable .="</table>\n";




		return $ClientsTable;
	
	}



		

}

/* End of file editclient.php */
/* Location: ./system/application/controllers/clients/editclient.php */
?>