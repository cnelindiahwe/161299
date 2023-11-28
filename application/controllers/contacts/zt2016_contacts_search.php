<?php //if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Zt2016_contacts_search extends MY_Controller {


	public function index()
	{

		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		// Libraries, helpers and models
		$this->load->library(array('session')); 
		$this->load->helper(array('contacts','general','form','userpermissions', 'url'));
  		
		$this->load->model('zt2016_contacts_model','','TRUE');
		$this->load->model('zt2016_clients_model','','TRUE');
		
  		// IMPORTANT! This global must be defined BEFORE the flexi auth library is loaded! 
 		// It is used as a global that is accessible via both models and both libraries, without it, flexi auth will not work.
		//$this->auth = new stdClass;
		
		// Load 'standard' flexi auth library by default.
		//$this->load->library('flexi_auth');		
		
		//$this->load->model('zowtrak_auth_model');

		//if (!$this->flexi_auth->is_logged_in()) {
		//	redirect('auth/logout/auto');
		//}	
		
		
		$templateData['ZOWuser']= _getCurrentUser();
		
		$templateData['title'] = 'Contacts Search';
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this->_get_contact_lists_content($templateData['ZOWuser']); 
		$templateData['ZOWuser']=_getCurrentUser();
		
		$this->load->view('admin_temp/main_temp',$templateData); 

	}

	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Get contact lists content
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * _get_contact_lists_content
	 * Gather content
	 */
	function _get_contact_lists_content($ZOWuser) {
		

		$page_content ='<div class="page_content">';
		//$page_content .= $this->_get_contact_lists_tabs();
		//$page_content .='<div class="tab-content">'."\n";
		//$page_content .='	<div id="sectionA" class="tab-pane fade in active">'."\n";
		$page_content .=		$this->_get_newest_contact_lists($ZOWuser)."\n";
		//$page_content .='	</div>'."\n";
		//$page_content .='	<div id="sectionB" class="tab-pane fade in">'."\n";
		//$page_content .="		Add contact form"."\n";
		//$page_content .='	</div>'."\n";		
		//$page_content .='</div>'."\n";
		$page_content .='</div>'."\n";
		return 	$page_content;		
		}



	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Get contact lists pages navigation tabs
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * _get_contact_lists_tabs()
	 * Draw tabs
	 */
	function _get_contact_lists_tabs() {	
		$page_content='<ul class="nav nav-tabs">
		  <li class="active"><a  data-toggle="tab" href="#sectionA">Existing</a></li>
		  <li><a  data-toggle="tab" href="#sectionB">Add Contact</a></li>
		</ul>';
		return 	$page_content;		
		}


	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Get newest contacts
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * _get_newest_contact_lists
	 * Newest contacts
	 */
	function _get_newest_contact_lists($ZOWuser) {
				
		$ContactTableRaw =$this->zt2016_contacts_model->GetContact($options = array('Trash'=>'0','sortBy'=>'FirstContactIteration','sortDirection'=>'DESC'));
		

		#load clients info	
		$ClientsTableRaw =  $this->zt2016_clients_model->GetClient();

		
		
		
		$page_content='<table class="table table-striped table-condensed responsive" style="width:100%; display:none; " id="contacts_lists_table">'."\n";
		$page_content.="<thead>"."\n";
		$page_content.="<tr><th>Name</th><th data-sortable=\"true\">Email</th><th data-sortable=\"true\">Client</th><th data-sortable=\"true\">Since</th></tr>"."\n";
		$page_content.="</thead>"."\n";
		$page_content.="<tfoot>"."\n";
		$page_content.="<tr><th></th><th data-sortable=\"true\"></th><th data-sortable=\"true\">Client</th><th data-sortable=\"true\">Since</th>"."\n";
		$page_content.="</tfoot>"."\n";		
		$page_content.="<tbody>"."\n";
		
		foreach ($ContactTableRaw as $row){
			//$page_content.= "<tr><td><a href=\"".site_url()."contacts/contacts_profile/".$row->ID."\">".$row->FirstName. " ". $row->LastName."</a></td><td>".$row->CompanyName."</td><td>".date('Y', strtotime($row->FirstContactIteration))."</td></tr>" ."\n";
			
			if ($row->Active==1) {
				$page_content.= "<tr>";
			} 
			else {
				$page_content.= "<tr class=\"inactive-contact\">";
			}
			
			foreach ($ClientsTableRaw as $ClientDetails){
				if ($ClientDetails->CompanyName==$row->CompanyName){
					$clientinfo	=$ClientDetails;
					break 1;
				}
			}
			
			//$clientinfo= $this->zt2016_clients_model->GetClient($options = array('CompanyName'=>$row->CompanyName));
					
			
			$contact_info_link ='<a href="'.Base_Url().'contacts/zt2016_contact_info/'.$row->ID.'">'.$row->FirstName.' '. $row->LastName.'</a>';
			
			$page_content.= '<td>'.$contact_info_link.'</td><td>'.$row->Email1.'</td>';
			
			$SafeClientName=str_replace(" ", "_", $clientinfo->CompanyName);
			$SafeClientName=str_replace("&", "~", $SafeClientName);
			
			$client_info_link ='<a href="'.Base_Url().'clients/zt2016_client_info/'.$SafeClientName.'">'.$clientinfo->CompanyName.'</a>';
			
			$clientmaterialslink ='<a href="'.Base_Url().'clients/zt2016_manageclientmaterials/'.$clientinfo->ClientCode.'" class="btn btn-warning btn-xs">Materials</a>';
			
			$page_content.= '<td>'.$client_info_link.'  '.$clientmaterialslink.'</td>';
			
			/*$page_content.= '<td>';
			
			if ($clientinfo->Group) {
				$page_content.= $clientinfo->Group;
			}else{
				$page_content.="None";
			}
					
			$page_content.='</td>';
			*/
			$page_content.= '<td>'.date('Y', strtotime($row->FirstContactIteration)).'</td></tr>' ."\n";

			//$page_content.= "<td>".$row->FirstName. " ". $row->LastName."</td><td>".$row->Email1."</td><td>".$row->CompanyName."</td><td>".date('Y', strtotime($row->FirstContactIteration))."</td></tr>" ."\n";
						
			
		}
		
		$page_content.="</tbody>"."\n";
		$page_content.="</table>"."\n";
		
		#header
		
		$page_header="";
		
		######### Display success message
		if($this->session->flashdata('SuccessMessage')){		
			
			$page_header.='<div class="alert alert-success" role="alert">'."\n";
			$page_header.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			//$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_header.=$this->session->flashdata('SuccessMessage');
			$page_header.='</div>'."\n";
		}
	
	    ######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			
			$page_header.='<div class="alert alert-danger" role="alert">'."\n";
			$page_header.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_header.='  <span class="sr-only">Error:</span>'."\n";
			$page_header.=$this->session->flashdata('ErrorMessage');
			$page_header.='</div>'."\n";
		}
		
		
		
		$page_header.='<div class="panel panel-info"><div class="panel-heading">'."\n"; 
		$page_header.='<h4 >'.count ($ContactTableRaw)." contacts (all time)";
		
		if ($ZOWuser=="miguel" || $ZOWuser=="sunil.singal" ) {
			$page_header.= '<a href="'.site_url().'contacts/zt2016_contact_new'.'" class="btn btn-success btn-sm pull-right">New Contact</a>'."\n";
			$page_header.= '<a href="'.site_url().'clients/zt2016_client_new'.'" class="btn btn-warning btn-sm pull-right">New Client</a>'."\n";
		}
		
		$page_header.="</h4>";
		$page_header.="</div><!--panel-heading-->\n";
		$page_header.='<div class="panel-body">'."\n";
		$page_header.='<div id="table_loading_message">Loading ... </div>'."\n";
		
		$page_content=$page_header.$page_content."</div><!--panel body-->\n</div><!--panel-->\n";
		
		
		return 	$page_content;
	}

	

}

/* End of file members.php */
/* Location: ./application/controllers/team/members.php */