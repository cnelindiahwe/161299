<?php

class Tracking extends MY_Controller {

	
	public function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 

		$this->load->library('session');
	
		
		$this->load->helper(array('tracking','general','url','form','userpermissions'));	


		//$CalendarYear='';
		$CalendarMonth = date( 'm', strtotime('now'));
		$CalendarYear=date( 'Y', strtotime('now'));
		// Get user timezone
		$utz = $this->session->userdata('timezone');
		$templateVars['userTimeData'] =  _getusertimedata($utz);
		//Get user name
	 $templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		
		$templateVars['pageOutput'] .=  $this-> _listOngoingJobs( $templateVars['ZOWuser']);
		$templateVars['pageInput'] = $this-> _pastjobscontrol();
		$templateVars['pageInput'] = $this-> _listPastJobs();

		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Tracking";
		$templateVars['pageType'] = "trackingnew";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));

 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}
	


	
	
	// ################## list Ongoing Jobs  ##################	
	function  _listOngoingJobs($ZOWuser)
	{


		$query = "SELECT * FROM (`zowtrakentries`) WHERE `Status`!='COMPLETED' AND `Invoice` = 'NOT BILLED' AND `Trash` = '0' ORDER BY FIELD(`Status`, 'IN PROOFING','IN PROGRESS','SCHEDULED','TENTATIVE')";
		$rawentries =$this->db->query($query);
		$getentries=$rawentries->result();

		//echo $this->db->last_query();
		if($getentries)
		{
			$OngoingJobsdata=_getOngoingJobs($getentries,$ZOWuser);
		} 
		else {
			$OngoingJobsdata=_displayOngoingJobs(0,$ZOWuser);
		}	
		return $OngoingJobsdata;
	
	}
	
	// ################## new job buttons ##################	
	function  _newjobbuttons()
	{
		$newjobbuttons="<a href=\"trackingnew\" class=\"newjob\">Create New Job</a></h3>\n";
		return $newjobbuttons;
	}


	// ################## _list Past Jobs ##################	
	function  _listPastJobs()
	{
		$this->load->model('trakentries', '', TRUE);
		
		$getentries = $this->trakentries->GetEntry($options = array('Status' => 'COMPLETED','Trash' => '0', 'sortBy'=> 'DateOut','sortDirection'=> 'desc', 'limit'=> '20'));
	
		if($getentries)
		{
			//$entries ="<div id='pageOutput'>\n";
			$entries ="<div id='pastJobs'>\n";
			$entries .="<div class='zowtrakui-topbar'><h2>Completed Jobs</h2>";
			$entries .=	$this-> _pastjobscontrol();
			$entries .="</div>\n";
			$entries.= _entrydatatable($getentries);
			$entries .="\n</div><!--past jobs-->\n";
			//$entries .="\n</div><!--past jobs-->\n";
		}
		return $entries;
	}

	// ################## past jobs control ##################	
	function   _pastjobscontrol()
	{
		$attributes['id'] = 'pastjobscontrol';
		$pastjobscontrol= form_open(site_url()."trackingnew\n",$attributes);

			//View type
			$options = array('list' => 'List', 'month'=>'Month');
			$more = 'id="viewtype" class="viewtype"';	
			$selected='list';
			$pastjobscontrol .=form_label('View:','viewtypes');
			$pastjobscontrol .=form_dropdown('viewtype', $options,$selected,$more);

			//jobs listed
			$options = array('10' => '10', '20'=>'20', '50'=>'50', '100'=>'100');
			$more = 'id="numberjobs" class="numberjobs"';	
			$selected='20';
			$pastjobscontrol .=form_label('Jobs listed:','numberjobs');
			$pastjobscontrol .=form_dropdown('numberjobs', $options,$selected,$more);
			
			//Clients
				$this->load->model('trakclients', '', TRUE);
				$Clientlist = $this->trakclients->GetEntry();
				$options=array();
				foreach($Clientlist as $client)
				{
				$options[$client->CompanyName]=$client->CompanyName;
				}
				asort($options);
				$options=array(''=>"All")+$options;		
				$more = 'id="pastjobsclient" class="pastjobsclient"';			
				$selected='all';
				$pastjobscontrol .=form_label('Client:','pastclientlist');
				$pastjobscontrol .=form_dropdown('pastclientlist', $options,$selected ,$more);

		$pastjobscontrol .= form_close()."\n";

		return $pastjobscontrol;
	
	}

}
/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>