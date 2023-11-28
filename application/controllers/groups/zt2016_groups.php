<?php

class Zt2016_groups extends MY_Controller {

	
	function index()
	{
		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		$this->load->helper(array('form','url','general','userpermissions'));

		$zowuser=_superuseronly(); 
		
		$templateData['ZOWuser']= _getCurrentUser();
		
		$templateData['title'] = 'Groups';
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this->_display_groups($templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData); 


	}
	

	// ################## display clients info ##################	
	function  _display_groups($ZOWuser)
	{
					

		#load groups info	
		$this->load->model('zt2016_groups_model', '', TRUE);
		$GroupsData = $this->zt2016_groups_model->GetGroup($options = array('Trash'=>'0','sortBy'=>'GroupName','sortDirection'=>'ASC	'));
		
		
		#load clients info	
		$this->load->model('zt2016_clients_model', '', TRUE);
		//$ClientsData = $this->zt2016_clients_model->Getclient($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));

		$this->load->model('zt2016_contacts_model', '', TRUE);
		#   $TrashClientsData = $this->trakclients->GetEntry($options = array('Trash'=>'1','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
		
		
		#Create page
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
		$page_content.='<div class="panel panel-info"><div class="panel-heading">'."\n"; 
		$page_content.='<h4>'.count($GroupsData)." existing groups";

		
		########## New group button
		$page_content.= '<a href="'.site_url().'groups/zt2016_group_new'.'" class="btn btn-success btn-sm pull-right">New Group</a>'."\n";


		$page_content.="</h4>\n";
		$page_content.="<div class='clearfix'></div>\n";
		$page_content.="</div><!--panel-heading-->\n";

		
		######### panel body
		$page_content.='<div class="panel-body">'."\n";
		$page_content.='<div id="table_loading_message">Loading ... </div>'."\n";

		
		#fetch groups table
		$page_content .= $this-> _groups_table($GroupsData)	;		


		$page_content.="</div><!--panel body-->\n</div><!--panel-->\n";


  		if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal" ||	$ZOWuser=="jirka.blom") {
  			
  		}

		return $page_content;

	}	


	// ################## create group table ##################	
	function   _groups_table($GroupsData)
	{

		$GroupsTable ='<table class="table table-striped table-condensed responsive" style="width:100%" id="groups_table">'."\n";
		$GroupsTable .="<thead><tr><th data-sortable=\"true\">Group</th><th data-sortable=\"true\">D. Price</th><th data-sortable=\"true\">D. Currency</th><th data-sortable=\"true\">Clients</th><th data-sortable=\"true\">A. Contacts <span class=\"graytext\">(Total)</span></th><th data-sortable=\"true\">First</th><th data-sortable=\"true\">Last</th><th data-sortable=\"true\">D. Country</th><th data-sortable=\"true\">D. Time Zone</th></tr></thead>\n";
		#$GroupsTable .="<tfoot><tr><th></th><th></th><th data-sortable=\"true\">First Year</th><th data-sortable=\"true\">Last Year</th><th data-sortable=\"true\">Group</th><th data-sortable=\"true\">Base price</th><th data-sortable=\"true\">Currency</th></tr></tfoot>\n";
		$GroupsTable .="<tbody>\n";
		
		
		foreach($GroupsData as $GroupDetails)
		{
			$GroupsTable .="<tr>\n";
			

			
			$GroupsTable .='<td> <a href="'.site_url().'groups/zt2016_group_info/'.$GroupDetails->GroupName.'">'.$GroupDetails->GroupName.'</a></td>'."\n";
			$GroupsTable .="<td>".number_format($GroupDetails->DefaultPrice, 2, '.', '' )."</td>\n";
			$GroupsTable .="<td>".$GroupDetails->DefaultCurrency."</td>\n";		


			
			# get group clients table from db
			if ($GroupDetails->GroupName=="DEFAULT"){
				$GroupName="";
			}
			else {
				$GroupName=$GroupDetails->GroupName;
			}
			
			$GroupClients = $this->zt2016_clients_model->GetClient($options = array('Group'=>$GroupName));
			
			# clients
			if (!EMPTY($GroupClients)){			
				$GroupsTable .="<td>".count($GroupClients)."</td>\n";
			} else{
				$GroupsTable .="<td>0</td>\n";
			}
			
			# contacts
			$ActiveGroupContacts=0;
			$GroupContacts=$this->zt2016_groups_model->GetGroupContacts($options = array('GroupName'=>$GroupName));
			$GroupActiveContacts=$this->zt2016_groups_model->GetGroupContacts($options = array('GroupName'=>$GroupName,'Active'=>1));
			
			if (!EMPTY($GroupContacts )){			
				$GroupsTable .="<td data-order=\"".count($GroupActiveContacts)."\">".count($GroupActiveContacts)." <span class=\"graytext\">(".count($GroupContacts).")</span></td>\n";
			} else{
				$GroupsTable .="<td>0</td>\n";
			}
			


			# first and last requests
			
			$GetGroupFirstLastDates=$this->zt2016_groups_model->GetGroupFirstLastDates(array('GroupName'=>$GroupName));
			$ActiveGroupFirstRequest=strtotime ($GetGroupFirstLastDates[0]->FirstRequest);
			$ActiveGroupLastRequest=strtotime ($GetGroupFirstLastDates[0]->LastRequest);			

			
			
			if (!EMPTY($GroupClients)){			
				$GroupsTable .="<td data-order=\"".$ActiveGroupFirstRequest."\">".date("d-M-Y",$ActiveGroupFirstRequest)."</td>\n";
				$GroupsTable .="<td data-order=\"".$ActiveGroupLastRequest."\">".date("d-M-Y",$ActiveGroupLastRequest)."</td>\n";
			} else{
				$GroupsTable .="<td data-order=\"0\">-</td>\n";
				$GroupsTable .="<td data-order=\"0\">-</td>\n";
			}

			# country and timezone
			$GroupsTable .="<td>".$GroupDetails->DefaultCountry."</td>\n";
			$GroupsTable .="<td>".$GroupDetails->DefaultTimeZone."</td>\n";
			
			$GroupsTable .="</tr>\n";
			
		}
		
		
		$GroupsTable .="</tbody>\n";
		$GroupsTable .="</table>\n";




		return $GroupsTable;
	
	}



		

}

/* End of file editclient.php */
/* Location: ./system/application/controllers/clients/editclient.php */
?>