<?php //if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Zt2016_contacts_search extends MY_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{

		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('contacts','general','form','userpermissions', 'url'));
		
		$templateVars['ZOWuser']=_getCurrentUser();


  		// Helpers
		//$this->load->helper(array("url","form"));	
		$this->load->helper(array("url"));
		$this->load->model('zt2016_contacts_model','','TRUE');
		
  		// IMPORTANT! This global must be defined BEFORE the flexi auth library is loaded! 
 		// It is used as a global that is accessible via both models and both libraries, without it, flexi auth will not work.
		//$this->auth = new stdClass;
		
		// Load 'standard' flexi auth library by default.
		//$this->load->library('flexi_auth');		
		
		//$this->load->model('zowtrak_auth_model');

		//if (!$this->flexi_auth->is_logged_in()) {
		//	redirect('auth/logout/auto');
		//}	
		
		$data['title'] = 'Contacts Search';
		$data['sidebar_content']='sidebar';
		$data['main_content'] =$this->_get_contact_lists_content(); 
		echo "ok";
		
		$this->load->view('admin_temp/main_temp',$data); 

	}

	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Get contact lists content
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * _get_contact_lists_content
	 * Gather content
	 */
	function _get_contact_lists_content() {
		

		$page_content ='<div class="page_content">';
		//$page_content .= $this->_get_contact_lists_tabs();
		$page_content .='<div class="tab-content">'."\n";
		$page_content .='	<div id="sectionA" class="tab-pane fade in active">'."\n";
		$page_content .=		$this->_get_newest_contact_lists()."\n";
		$page_content .='	</div>'."\n";
		$page_content .='	<div id="sectionB" class="tab-pane fade in">'."\n";
		$page_content .="		Add contact form"."\n";
		$page_content .='	</div>'."\n";		
		$page_content .='</div>'."\n";
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
	function _get_newest_contact_lists() {
				
		$ContactTableRaw =$this->zt2016_contacts_model->GetContact($options = array('Trash'=>'0','sortBy'=>'FirstContactIteration','sortDirection'=>'DESC'));
		$page_content='<table class="table table-striped table-condensed" style="width:50%" id="contacts_lists_table">'."\n";
		$page_content.="<thead>"."\n";
		$page_content.="<tr><th>Name</th><th data-sortable=\"true\">Email</th><th data-sortable=\"true\">Client</th><th data-sortable=\"true\">Since</th>"."\n";
		$page_content.="</thead>"."\n";
		$page_content.="<tfoot>"."\n";
		$page_content.="<tr><th>Name</th><th data-sortable=\"true\">Client</th><th data-sortable=\"true\">Since</th>"."\n";
		$page_content.="</tfoot>"."\n";		
		$page_content.="<tbody>"."\n";
		foreach ($ContactTableRaw as $row){
			//$page_content.= "<tr><td><a href=\"".site_url()."contacts/contacts_profile/".$row->ID."\">".$row->FirstName. " ". $row->LastName."</a></td><td>".$row->CompanyName."</td><td>".date('Y', strtotime($row->FirstContactIteration))."</td></tr>" ."\n";
			$page_content.= "<tr><td>".$row->FirstName. " ". $row->LastName."</td><td>".$row->Email1."</td><td>".$row->CompanyName."</td><td>".date('Y', strtotime($row->FirstContactIteration))."</td></tr>" ."\n";
					}
		$page_content.="</tbody>"."\n";
		$page_content.="</table>"."\n";
		
		$page_header='<div class="panel panel-info"><div class="panel-heading">'."\n"; 
		$page_header.='<h3 class="panel-title">'.count ($ContactTableRaw)." contacts (all time)</h3>";
		$page_header.="</div><!--panel-heading-->\n";
		$page_header.='<div class="panel-body">'."\n";
		
		$page_content=$page_header.$page_content."</div><!--panel body-->\n</div><!--panel-->\n";
		
		
		return 	$page_content;
	}

	

}

/* End of file members.php */
/* Location: ./application/controllers/team/members.php */