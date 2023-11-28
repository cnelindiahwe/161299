<?php

class Zt2016_contacts extends MY_Controller {

	
	public function index()
	{

		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		
		$this->load->helper(array('form','url','clients','general','userpermissions'));

		$this->load->model('zt2016_contacts_model', '', TRUE);
		$this->load->model('zt2016_clients_model', '', TRUE);


		$zowuser=_superuseronly(); 		
		
		$templateData['ZOWuser']= _getCurrentUser();
		
		$templateData['title'] = 'Contacts';
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this->_display_contacts($templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData); 


	}
	

	// ################## display contacts info ##################	
	function  _display_contacts($ZOWuser)
	{
					
				
		#load contacts info	
		$ContactsData=$this->zt2016_contacts_model->GetContact($options = array('Trash'=>'0','sortBy'=>'FirstContactIteration','sortDirection'=>'DESC'));

		$TrashedContactsData=$this->zt2016_contacts_model->GetContact($options = array('Trash'=>'1','sortBy'=>'FirstContactIteration','sortDirection'=>'DESC'));
		
		#load clients info	
		$ClientsData =  $this->zt2016_clients_model->GetClient();
		
		
		####TRASHED contacts
		//$TrashClientsData = $this->trakclients->GetEntry($options = array('Trash'=>'1','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
		
		
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
		$page_content.='<div class="panel panel-success"><div class="panel-heading">'."\n"; 
		$page_content.='<h4>'.count($ContactsData)." existing contacts";
		
		####TRASHED contacts
		if ($TrashedContactsData) {
			$page_content.=' <small>('.count($TrashedContactsData)." Trashed)</small>"."\n";
		}

		
		########## New client button
		$page_content.= '<a href="'.site_url().'contacts/zt2016_contact_new'.'" class="btn btn-success btn-sm pull-right">New Contact</a>'."\n";

		########## Display "View Trash" button
		#### only if there are trashed items
		if ($TrashedContactsData) {
				$page_content.= '<a href="'.site_url().'trash/zt2016_trash'.'" class="btn btn-warning btn-sm pull-right">View Trash</a>'."\n";
		}
		$page_content.="</h4>\n";
		$page_content.="<div class='clearfix'></div>\n";
		$page_content.="</div><!--panel-heading-->\n";

		
		######### panel body
		$page_content.='<div class="panel-body ">'."\n";
		$page_content.='<div id="table_loading_message">Loading ... </div>'."\n";

		#fetch clients table
		$page_content .= $this-> _contacts_table($ContactsData,$ClientsData)	;		

		$page_content.="</div><!--panel body-->\n</div><!--panel-->\n";

  		//if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal" ||	$ZOWuser=="alvaro.ollero") {
  			
  		//}

		return $page_content;

	}	


	// ################## create client table ##################	
	function   _contacts_table($ContactsData,$ClientsData)
	{
		
		
		$ContactsTable ='<table class="table table-striped table-condensed responsive" style="width:100%;display:none;" id="contacts_table">'."\n";
		$ContactsTable .="<thead><tr><th data-sortable=\"true\">Name</th><th data-sortable=\"true\">Client</th><th data-sortable=\"true\">First Year</th><th data-sortable=\"true\">Last Year</th><th data-sortable=\"true\">Group</th><th data-sortable=\"true\">Base price</th><th data-sortable=\"true\">Currency</th></tr></thead>\n";
		$ContactsTable .="<tfoot><tr><th></th><th></th><th data-sortable=\"true\">First Year</th><th data-sortable=\"true\">Last Year</th><th data-sortable=\"true\">Group</th><th data-sortable=\"true\"</th><th data-sortable=\"true\"></th></tr></tfoot>\n";
		$ContactsTable .="<tbody>\n";
		
		foreach($ContactsData as $ContactDetails)
		{

			$SafeClientName=str_replace(" ", "_", $ContactDetails->CompanyName);
			$SafeClientName=str_replace("&", "~", $SafeClientName);
			$contact_name =$ContactDetails->FirstName.' '. $ContactDetails->LastName;
			
			if ($ContactDetails->Active==1) {
				$ContactsTable.= "<tr>";
			} 
			else {
				$ContactsTable.= "<tr class=\"inactive-contact\">";
			}
			
		
			$contact_info_link ='<a href="'.Base_Url().'contacts/zt2016_contact_info/'.$ContactDetails->ID.'">'.$contact_name.'</a>';
			
			$ContactsTable .= '<td>'.$contact_info_link.'</td>';			
			
			$ContactsTable .='<td> <a href="'.site_url().'clients/zt2016_client_info/'.$SafeClientName.'">'.$ContactDetails->CompanyName.'</a></td>'."\n";
			
			$ContactsTable .="<td>".date("Y",strtotime($ContactDetails->FirstContactIteration))."</td>\n";

			#last client date
			
			

			$this->db->select_max('DateOut');
			$this->db->where('Originator',$contact_name);
			$LastContactDateQuery = $this->db->get('zowtrakentries');
			
			
			if ($LastContactDateQuery->num_rows() > 0)
			{
			   $row = $LastContactDateQuery->row(); 
			   $LastContactDate= date("Y",strtotime($row->DateOut));
			} else{
			
				$LastContactDate= "NONE";
			}
			
			$ContactsTable .="<td>".$LastContactDate."</td>\n";
		

			
			foreach($ClientsData as $ClientDetails)
			{
				if($ClientDetails->CompanyName==$ContactDetails->CompanyName){
						$ContactsTable .="<td>$ClientDetails->Group</td>\n";
						$ContactsTable .="<td>$ClientDetails->BasePrice</td>\n";
						$ContactsTable .="<td>$ClientDetails->Currency</td>\n";
						break 1;
				}
			}
			

			$ContactsTable .="</tr>\n";
			
		}
		/**/
		
		$ContactsTable .="</tbody>\n";
		$ContactsTable .="</table>\n";

		return $ContactsTable;
	
	}



		

}

/* End of file editclient.php */
/* Location: ./system/application/controllers/clients/editclient.php */
?>