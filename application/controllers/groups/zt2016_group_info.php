<?php

class zt2016_group_info extends MY_Controller {

	
	function index()
	{
		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		$this->load->helper(array('form','url','zt2016_groups','general','userpermissions'));

		$zowuser=_superuseronly(); 
		
		$templateData['ZOWuser']= _getCurrentUser();
		
		
		$templateData['GroupName']=strtoupper($this->uri->segment(3));


		 if (empty ($templateData['GroupName'])) {
		 	if ($this->input->post('Current_Group')){
		 		$templateData['GroupName']=strtoupper($this->input->post('Current_Group'));
			}
			
		 }
		
		if (empty ($templateData['GroupName'])) {			 
			$this->session->set_flashdata('ErrorMessage','Failed to show group info: group name was not provided.');
			redirect('groups/zt2016_groups', 'refresh');
		 } 
		
		$templateData['title'] =$templateData['GroupName'].' Group Info';
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this->_group_info_page($templateData); 

		$this->load->view('admin_temp/main_temp',$templateData); 

	}
	

	// ################## display clients info ##################	
	function  _group_info_page($templateData)
	{
		$GroupName=$templateData['GroupName'];	
		$ZOWuser=$templateData['ZOWuser'];	

		#load group info	
		$this->load->model('zt2016_groups_model', '', TRUE);
		$GroupData = $this->zt2016_groups_model->GetGroup($options = array('GroupName'=>$GroupName));
		
		#load groups info	
		$AllGroupsData = $this->zt2016_groups_model->GetGroup($options = array());
		
		
		#load clients info	
		$this->load->model('zt2016_clients_model', '', TRUE);
		if ($GroupName=="DEFAULT")
		{$QueryGroupName='';}
		else {$QueryGroupName=$GroupName;}
		
		$GroupClientsData = $this->zt2016_clients_model->Getclient($options = array('Group'=>$QueryGroupName,'Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC'));
		
		$this->load->model('zt2016_contacts_model', '', TRUE);

		#create clients table & associated variables
		$Groupinfopage = $this->_group_clients_table($GroupClientsData,$GroupData);
		
		$FormValues = (array) $GroupData;
		
		$FormValues['Group']=$GroupName;
		
		$GetGroupFirstLastDates=$this->zt2016_groups_model->GetGroupFirstLastDates(array('GroupName'=>$GroupName));
		$FormValues['FirstJob']=strtotime ($GetGroupFirstLastDates[0]->FirstRequest);
		$FormValues['LastJob']=strtotime ($GetGroupFirstLastDates[0]->LastRequest);			
		
		$this->session->set_flashdata('FormValues',$FormValues);

		
		#Create page
		$page_content ="<div class=\"page_content\">\n";

		
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
		
		######### group dropdown
		$FormURL='groups/zt2016_group_info';
		$page_content.=_display_groups_control($AllGroupsData,$GroupData,$FormURL);				
		######### panel header
		if ($GroupName=="DEFAULT")
		{$PanelName='DEFAULT / NONE';}
		else {$PanelName=$GroupName;}
		
		$page_content.='<div class="panel panel-info" style="margin-top:2em;"><div class="panel-heading" >'."\n"; 
		$page_content.='<h4>'.$PanelName." group";
		
		if ($GroupName=="DEFAULT")
		{
			$page_content.="<br/>"."\n"; 
			$page_content.="<small>DEFAULT group's settings default price and default currency are used for new clients that do not belong to an existing group.</small>";
		}		

		########## Edit group button (form)

		$formurl="groups/zt2016_group_edit";
		$attributes='id="group-edit-form"  style="display:inline; float:right;"';
		$page_content.=form_open($formurl,$attributes )."\n";
		$page_content.=form_hidden('GroupName',$FormValues['GroupName'] );
		$page_content.=form_hidden('GroupClientsCount',$Groupinfopage['CountClients'] );
		$data = array(
		  'id' => 'NewGroupSubmit',
		  'name' => 'NewGroupSubmit',
		  'class' => 'btn btn-primary btn-sm',
		  'value' => 'Edit Group', 
		  'style' => 'margin-top:0;margin-bottom:0;',
		);
		$page_content.= form_submit($data);
		$page_content.= form_close();
		
		
		
		########## New group client button 			
		
	  // $GroupData=$GroupData;
		//$page_content.= '<a href="'.site_url().'clients/zt2016_client_new" class="btn btn-info btn-sm pull-right">New '.$PanelName.' Client</a>'."\n";
		########## Edit group button (form)

		$formurl="clients/zt2016_client_new";
		$attributes='id="new-client-form"  style="display:inline; float:right;"';
		$page_content.=form_open($formurl,$attributes )."\n";
		$page_content.=form_hidden('ClientFormValues', $FormValues );
		$data = array(
		  'id' => 'NewClientSubmit',
		  'name' => 'NewClientSubmit',
		  'class' => 'btn btn-success btn-sm',
		  'value' => 'New '.$PanelName.' Client', 
		  'style' => 'margin-top:0;margin-bottom:0;',
		);
		$page_content.= form_submit($data);
		$page_content.= form_close();
		
		
		
		$page_content.="</h4>\n";
		$page_content.="<div class='clearfix'></div>\n";
		$page_content.="</div><!--panel-heading-->\n";

		
		######### panel body
		$page_content.='<div class="panel-body">'."\n";
		

		
		
		############ Group Details
 		$page_content.='<div class="row">'."\n";	
 		
 		########## Price 
		$page_content.='<div class="col-sm-3">'."\n";			

		
			$page_content.='	<ul class="list-group">'."\n";
		
			$page_content.='		<li class="list-group-item">'."\n";
			$page_content.='		Def. Price'."\n";
			$page_content.='		<span class="pull-right">'.number_format($GroupData->DefaultPrice, 2, '.', '' )." ".$GroupData->DefaultCurrency.'</span> '."\n";
			$page_content.='		</li>'."\n";

			$page_content.='		<li class="list-group-item">'."\n";
			$page_content.='		Def. Pay. Days'."\n";
			$page_content.='		<span class="pull-right">'.$GroupData->DefaultPaymentDays.'</span> '."\n";
			$page_content.='		</li>'."\n";		

			$page_content.='		</li>'."\n";
			$page_content.='	</ul>'."\n";
		
			$page_content.='	</div><!--col-->'."\n";

		
 		########## country / timezone
		$page_content.='<div class="col-sm-3">'."\n";			
		
		
			$page_content.='	<ul class="list-group">'."\n";
		
			$page_content.='		<li class="list-group-item">'."\n";
			$page_content.='		Def. Country'."\n";
			$page_content.='		<span class="pull-right">'.$GroupData->DefaultCountry.'</span> '."\n";
			$page_content.='		</li>'."\n";

			$page_content.='		<li class="list-group-item">'."\n";
			$page_content.='		Def. Timezone'."\n";
			$page_content.='		<span class="pull-right">'.$GroupData->DefaultTimeZone.'</span> '."\n";
			$page_content.='		</li>'."\n";		

			$page_content.='		</li>'."\n";
			$page_content.='	</ul>'."\n";
		
		$page_content.='	</div><!--col-->'."\n";		
		

 		########## Clients & Contacts
		$page_content.='<div class="col-sm-3">'."\n";			
		
		
			$page_content.='	<ul class="list-group">'."\n";
		
			$page_content.='		<li class="list-group-item">'."\n";
			$page_content.='		Clients'."\n";
		

			$page_content.='		<span class="pull-right">'.$Groupinfopage['CountClients'].'</span> '."\n";

			$page_content.='		</li>'."\n";

			$page_content.='		<li class="list-group-item">'."\n";
			$page_content.='		Contacts'."\n";
			$page_content.='		<span class="pull-right">'.$Groupinfopage['CountActiveContacts']." <span class=\"graytext\">(".$Groupinfopage['CountAllContacts'].")</span></span>\n";
			$page_content.='		</li>'."\n";		

			$page_content.='		</li>'."\n";
			$page_content.='	</ul>'."\n";
		
		$page_content.='	</div><!--col-->'."\n";			
		

 		########## Dates
		$page_content.='<div class="col-sm-3">'."\n";			
		
		
			$page_content.='	<ul class="list-group">'."\n";
		
			$page_content.='		<li class="list-group-item">'."\n";
			$page_content.='		First Job'."\n";
		

				$page_content.='		<span class="pull-right">'.date("d-M-Y",strtotime($Groupinfopage['FirstJob'])).'</span> '."\n";

			$page_content.='		</li>'."\n";

			$page_content.='		<li class="list-group-item">'."\n";
			$page_content.='		Last Jobxx'."\n";
			
			$page_content.='		<span class="pull-right">'.date("d-M-Y",strtotime($Groupinfopage['LastJob'])).'</span> '."\n";

			$page_content.='		</li>'."\n";		

			$page_content.='		</li>'."\n";
			$page_content.='	</ul>'."\n";
		
		$page_content.='	</div><!--col-->'."\n";			
		
		$page_content.='</div><!--row-->'."\n";
		
		
		$page_content.='<div id="table_loading_message">Loading ... </div>'."\n";

		
	

		$page_content.=	$Groupinfopage['GroupClientsTable'];
		
		$page_content.="</div><!--panel body-->\n</div><!--panel-->\n";

		$page_content.="</div><!--page content-->";

  		if ($ZOWuser=="miguel" ||$ZOWuser=="sunil.singal" ||	$ZOWuser=="jirka.blom") {
  			
  		}

		return $page_content;

	}	


	// ################## create client table ##################	
	function _group_clients_table($GroupClientsData,$GroupData)
	{
	
		$CountActiveContacts=0;
		$CountTotalContacts=0;
		$GroupClientsTable ='';	
		$Groupinfopage['CountActiveContacts']=0;
		$Groupinfopage['CountAllContacts']=0;
		$Groupinfopage['FirstJob']=1;
		$Groupinfopage['LastJob']=0;
		
		if (!EMPTY($GroupClientsData)){	
			foreach ($GroupClientsData  as $ClientDetails) 
			{
				$GroupClientsTable .="<tr>\n";

				$GroupClientsTable .="<td>\n";

				$SafeClientName=str_replace(" ", "_", $ClientDetails->CompanyName);
				$SafeClientName=str_replace("&", "~", $SafeClientName);

				$GroupClientsTable .='<a href="'.site_url().'clients/zt2016_client_info/'.$SafeClientName.'">'.$ClientDetails->CompanyName.'</a>'."\n";
				$GroupClientsTable .="</td>\n";			

				if ($ClientDetails->BasePrice > $GroupData->DefaultPrice){
					$GroupClientsTable	.="<td class=\"alert-success\">\n";
				}
				else if ($ClientDetails->BasePrice < $GroupData->DefaultPrice){
					$GroupClientsTable	.="<td class=\"alert-danger\">\n";
				}
				else {
					$GroupClientsTable	.="<td>\n";
				}

				$GroupClientsTable .=number_format($ClientDetails->BasePrice, 2, '.', '' );
				$GroupClientsTable .="</td>\n";			

				$GroupClientsTable .="<td>\n";			
				$GroupClientsTable .=$ClientDetails->Currency ;
				$GroupClientsTable .="</td>\n";
				// echo $ClientDetails->CompanyName.''
				$ClientContacts = $this->zt2016_contacts_model->GetContact($options = array('CompanyName'=>$ClientDetails->CompanyName));						
				$ActiveClientContacts = $this->zt2016_contacts_model->GetContact($options = array('CompanyName'=>$ClientDetails->CompanyName, 'Active'=>1));
				
				if(!empty($ActiveClientContacts)){

					$Groupinfopage['CountActiveContacts']+=count($ActiveClientContacts);	
				}

				$GroupClientsTable.="<td data-order=\"".count($ClientContacts)."\">\n";
				if(!empty($ActiveClientContacts) && !empty($ClientContacts)){
					$GroupClientsTable .=count($ActiveClientContacts)." <span class=\"graytext\">(".count($ClientContacts).")</span>\n";			
				}
				if(!empty($ClientContacts)){
					$Groupinfopage['CountAllContacts']+=count($ClientContacts);			
				}
				$GroupClientsTable .="</td>\n";		


				$GroupClientsTable .="<td data-order=\"".$ClientDetails->FirstClientIteration."\">\n";			


				#Search for  group's first job date
				if($ClientDetails->FirstClientIteration!=0){
					$GroupClientsTable .=date("d-M-Y",strtotime($ClientDetails->FirstClientIteration));
					if ( $Groupinfopage['FirstJob']==1 || $Groupinfopage['FirstJob']>$ClientDetails->FirstClientIteration)
					{ 
						$Groupinfopage['FirstJob']=$ClientDetails->FirstClientIteration;
					}
				}
				else{
					$GroupClientsTable .="-";
				}
				$GroupClientsTable .="</td>\n";

				$GroupClientsTable .="<td>\n";			

					$this->db->select_max('DateOut');
					$this->db->where('Client',$ClientDetails->CompanyName);
					$LastClientDateQuery = $this->db->get('zowtrakentries');

					#last client data
					if ($LastClientDateQuery->num_rows() > 0)
					{
						$row = $LastClientDateQuery->row(); 

						$LastClientDate= strtotime($row->DateOut);

						#Search for group's last job date
						if ( $Groupinfopage['LastJob']==0 || $Groupinfopage['LastJob']<$row->DateOut)
						{$Groupinfopage['LastJob']=$row->DateOut;}

						if ($LastClientDate>0){
							$GroupClientsTable .=date("d-M-Y",$LastClientDate);
						} else{
							$GroupClientsTable .="-";
						}


					} else{
						$GroupClientsTable .="-";
					}						



				$GroupClientsTable .="</td>\n";



				$GroupClientsTable .="<td>\n";			
				$GroupClientsTable .=$ClientDetails->Country;
				$GroupClientsTable .="</td>\n";

				$GroupClientsTable .="<td>\n";			
				$GroupClientsTable .=$ClientDetails->TimeZone;
				$GroupClientsTable .="</td>\n";

				$GroupClientsTable .="</ tr>\n";

			}
		}
	
		if (!EMPTY($GroupClientsData)){
			$Groupinfopage['CountClients']=count($GroupClientsData);
		} else{
			$Groupinfopage['CountClients']=0;
		}
		$GroupClientsTableHeader ='<table class="table table-striped table-condensed responsive dataTable" style="width:100%;" id="group_clients_table">'."\n";
		$GroupClientsTableHeader .="<thead><tr><th data-sortable=\"true\">".$Groupinfopage['CountClients']." Clients</th><th data-sortable=\"true\">Price</th><th data-sortable=\"true\">Currency</th><th data-sortable=\"true\">".$Groupinfopage['CountActiveContacts']." A. Contacts <span class=\"graytext\">(".$Groupinfopage['CountAllContacts']." Total)</span></th><th data-sortable=\"true\">First I.</th><th data-sortable=\"true\">Last I.</th><th data-sortable=\"true\">Country</th><th data-sortable=\"true\">TimeZone</th></tr></thead>";
		$GroupClientsTableHeader .="<tbody>\n";
		
		
		$GroupClientsTable=$GroupClientsTableHeader.$GroupClientsTable;
		
		$GroupClientsTable .="<tbody>\n";
		$GroupClientsTable .="</table>\n";
		
		$Groupinfopage['GroupClientsTable']=$GroupClientsTable;		
		
		
		return $Groupinfopage;

	}

}

/* End of file editclient.php */
/* Location: ./system/application/controllers/groups/zt2016_group_info.php */
?>