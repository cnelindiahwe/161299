<?php

class Manageclientmaterials extends MY_Controller {

	
	function index()
	{

		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('form','url','clients','general','userpermissions'));

		$templateVars['ZOWuser']=_superuseronly(); 
	
		$templateVars['current'] =$this->input->post('ClientCode');
		
		//echo $templateVars['current'];
		
		if($templateVars['current']) {
			redirect('clients/manageclientmaterials/'.$templateVars['current'], 'refresh');
		}
		elseif(!$templateVars['current'])
		{
			if 	($this->uri->segment(3)!=""){
				$templateVars['current'] =$this->uri->segment(3);
			}				
			else {
				redirect(Base_Url().'clients');
			} 
				
		}
		$this->load->model('trakclients', '', TRUE);
		$ClientTable  = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
		foreach($ClientTable as $client)
		{
			if ($client->ClientCode==$templateVars['current'] ){
				$CurrentClient=$client;
			}
		}	
		
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		$templateVars['pageOutput'].=$this->_gettopbar($ClientTable ,$CurrentClient);
		$templateVars['pageOutput'] .=$this->_listclientmaterials($CurrentClient);

		$templateVars['baseurl'] = site_url();
		$templateVars['pageType'] = "manage_client_materials";
		$templateVars['pageName'] = "Manage Client Materials";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));

 	  $this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}
	



	// ################## top bar ##################	
	function  _gettopbar($ClientTable ,$CurrentClient)
	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			//$entries .="<h1>Edit client: ".$CurrentClient->CompanyName."</h1>";

			$entries .=$this->_clientscontrol($ClientTable ,$CurrentClient);


			$entries .=$this->_clientmaterialsuploadform ($CurrentClient);


			//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";
			return $entries;

	}
	// ################## clients control ##################	
	function   _clientscontrol($ClientTable ,$CurrentClient)
	{
		$attributes['id'] = 'clientcontrol';
		$clientscontrol= form_open(site_url()."clients/manageclientmaterials\n",$attributes);

			
			//Clients

				$options=array();
				foreach($ClientTable  as $client)
				{
				$options[$client->ClientCode]=$client->CompanyName;
				}
				asort($options);
				$options=array('all'=>"All")+$options;		
				$more = 'id="clientselector" class="selector"';			
				$selected=$CurrentClient->ClientCode;
				$clientscontrol .=form_label('Manage client materials:','client');
				$clientscontrol .=form_dropdown('ClientCode', $options,$selected ,$more);
				$more = 'id="clientcontrolsubmit" class="clientcontrolsubmit"';			
				$clientscontrol .=form_submit('clientcontrolsubmit', 'Go',$more);
		$clientscontrol .= form_close()."\n";

		return $clientscontrol;
	
	}



	// ################## list clients materials ##################	
	function   _listclientmaterials ($CurrentClient)
	{
		$this->load->model('trakcontacts', '', TRUE);
		$clientmaterials='<div id="clientmaterialsmanager" style="margin-left:1em;">';
		$clientmaterials.='<h4 class="contactlist">Client Materials for '.$CurrentClient->CompanyName.'</h4>';
		$clientcode = $this->uri->segment(3);			
		$clientmaterials.= getClientMaterials($CurrentClient->ClientCode);	
		$clientmaterials.='</div>';
		return $clientmaterials;
	
	}	
	
	
	// ################## list clients materials ##################	
	function   _clientmaterialsuploadform ($CurrentClient)
	{
			
		$formattributes = array('id' => 'uploadform');
		$myuploadform= form_open_multipart('clients/uploadclientmaterials/',$formattributes);
		$data = array(
              'name'        => 'fileuploadname',
              'id'          => 'fileuploadname',
              'maxlength'   => '100',
              'size'        => '25',
              'type' 		=> 'file'
            );
		
		$myuploadform.=form_input($data);
		$myuploadform.=form_hidden('clientdir', $CurrentClient->ClientCode);
		$myuploadform.="<input type=\"submit\" value=\"upload\" />\n";

		$myuploadform.="</form>\n";
		return $myuploadform;
	
	}	
		
		// ################## clients form ##################	
	function   _getclientformdetails($CurrentClient)
	{
		 $clienformdetails="<div class=\"clearfix\"></div>";
		 $clienformdetails .=_getClientForm($CurrentClient);	

		return $clienformdetails;
	
	}
	
}

/* End of file editclient.php */
/* Location: ./system/application/controllers/clients/editclient.php */
?>