<?php

class Editclient extends MY_Controller {

	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('form','url','clients','general','userpermissions'));

		$templateVars['ZOWuser']=_superuseronly(); 
	
		$templateVars['current'] =$this->input->post('client');
		
		if($templateVars['current']) {
			
			$safeclient=str_replace("&","~",$templateVars['current']);
			redirect('clients/editclient/'.$safeclient, 'refresh');
		}
		elseif(!$templateVars['current'])
		{
			$templateVars['current'] =$this->uri->segment(3);
			$templateVars['current'] =str_replace("%20"," ",$templateVars['current']);
			$templateVars['current'] =str_replace("~","&",$templateVars['current']);
		}
		$this->load->model('trakclients', '', TRUE);
		$ClientTable  = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
		foreach($ClientTable as $client)
		{
			$ClientList[]=$client->CompanyName;
			if ($client->CompanyName==$templateVars['current'] ){
				$CurrentClient=$client;
			}
		}	

		if (!isset($CurrentClient)) {
					redirect('clients', 'refresh');
		}

		$templateVars['pageInput'] =  _getmanagerbar($templateVars['ZOWuser']);
				
		$templateVars['pageInput'].=$this->_gettopbar($ClientTable ,$CurrentClient);
		$templateVars['pageInput'] .=$this->_clienttotal($CurrentClient);
		$templateVars['pageInput'] .=$this->_getclientcontacts($CurrentClient);
		$templateVars['pageInput'] .=$this->_listclientmaterials($CurrentClient);
		$templateVars['pageInput'] .=$this->_getclientformdetails($CurrentClient);

		$templateVars['baseurl'] = site_url();
		$templateVars['pageType'] = "edit clients";
		$templateVars['pageName'] = "Edit Client";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));

 	  $this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}
	
	// ################## main page ##################	

	function  _getCurrent()
	{
			$this->load->model('trakclients', '', TRUE);
			$currententry = $this->trakclients->GetEntry($options = array('ID' => $this->uri->segment(3)));
			if($currententry)
				{
				return $currententry;
				}
			else {
			 	echo 'There was a problem retrieving the current entry';
			}
	}


	// ################## top bar ##################	
	function  _gettopbar($ClientTable ,$CurrentClient)
	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			//$entries .="<h1>Edit client: ".$CurrentClient->CompanyName."</h1>";

			$entries .=$this->_clientscontrol($ClientTable ,$CurrentClient);
			if (isset($CurrentClient->ID)) {
				$entries .="<a href=\"clients\" class=\"clients canceledits\">Cancel Edit</a></h3>\n";
				$entries .="<a href=\"".site_url()."clients/newclient\" class=\"newclient\">Create New Client</a></h3>\n";
				
			}		

			//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";
			$entries .= "<a href=\"".site_url()."clients/trashentry/".$CurrentClient->ID."\" class=\"logout\">Trash Client</a>\n";

			$entries .="</div>";
		
			return $entries;

	}
	// ################## clients control ##################	
	function   _clientscontrol($ClientTable ,$CurrentClient)
	{
		$attributes['id'] = 'clientcontrol';
		$clientscontrol= form_open(site_url()."clients/editclient\n",$attributes);

			
			//Clients

				$options=array();
				foreach($ClientTable  as $client)
				{
				$options[$client->CompanyName]=$client->CompanyName;
				}
				asort($options);
				$options=array('all'=>"All")+$options;		
				$more = 'id="clientselector" class="selector"';			
				$selected=$CurrentClient->CompanyName;
				$clientscontrol .=form_label('Edit client:','client');
				$clientscontrol .=form_dropdown('client', $options,$selected ,$more);
				$more = 'id="clientcontrolsubmit" class="clientcontrolsubmit"';			
				$clientscontrol .=form_submit('clientcontrolsubmit', 'Edit',$more);
		$clientscontrol .= form_close()."\n";

		return $clientscontrol;
	
	}

	// ################## clients control ##################	
	function   _clienttotal($CurrentClient)
	{
		$clienttotals="<div id=\"basicdata\"><h4 class=\"basicdata\">Basic Data</h4><p class=\"basicdata\">";
		
		$this->db->select_sum('InvoiceEntryTotal','TotalBilled');
		$this->db->select_sum('InvoiceTime','TotalHours');
		$this->db->from('zowtrakentries');
		$this->db->where('Client', $CurrentClient->CompanyName);
		$this->db->where('Trash =',0);
		$this->db->where('Invoice !=','NOT BILLED');
		$query = $this->db->get();
		$averageprice=0;
		if ($query->row()->TotalBilled!=0 && $query->row()->TotalHours!=0) {
			$averageprice=number_format($query->row()->TotalBilled/$query->row()->TotalHours,1);
		}


		// ############# basic info

		//last date
		$agequery = "SELECT MAX(DateOut) AS Firstdate  FROM zowtrakentries WHERE Trash = 0 AND Client='".$CurrentClient->CompanyName."'";
		$lastactive =$this->db->query($agequery);	
		$clienttotals .= "Last iteration: ".date(' F Y',strtotime($lastactive->row()->Firstdate))." ";

		//Number of months inactive
		$date1 = new DateTime(date(' F Y',strtotime($lastactive->row()->Firstdate)));
		$date2 = new DateTime("now");
		$interval = $date1->diff($date2);
		$monthsinactive= $interval->y*12;
		$monthsinactive= $monthsinactive+$interval->m;
		$clienttotals .= "(".$monthsinactive." months)<br/>";		
		
		//start date
		$agequery = "SELECT MIN(DateOut) AS Firstdate  FROM zowtrakentries WHERE Trash = 0 AND Client='".$CurrentClient->CompanyName."'";
		$monthsage =$this->db->query($agequery);
		
		$clienttotals .= "Since: ".date(' F Y',strtotime($monthsage->row()->Firstdate))." ";
		
		//Number of months active
		$date1 = new DateTime(date(' F Y',strtotime($monthsage->row()->Firstdate)));
		$date2 = new DateTime(date(' F Y',strtotime($lastactive->row()->Firstdate)));
		//$date2 = new DateTime("now");
		$interval = $date1->diff($date2);
		$monthsactive= $interval->y*12;
		$monthsactive= $monthsactive+$interval->m;
		$monthsactive= $monthsactive+1;
		$clienttotals .= "(".$monthsactive." months)<br/>";		

		//Total jobs
		$clienttotals .= "<br/>";	
		$this->db->from('zowtrakentries');
		$this->db->where('Client', $CurrentClient->CompanyName);
		$this->db->where('Trash =',0);
		$this->db->where('Invoice !=','NOT BILLED');
		$totaljobs=$this->db->count_all_results();
		$clienttotals .= "Total Jobs to date = ".$totaljobs."<br/>";
		
	
		//Average jobs per month
		$averagejobsmonth=0;
		if ($monthsactive>0) {
			$averagejobsmonth=number_format($totaljobs/$monthsactive,1);
		}
		$clienttotals .= "Average jobs per month = ".$averagejobsmonth."<br/>";

		//Total hours
		$clienttotals .= "Total hours to date = ".number_format($query->row()->TotalHours,1)."<br/>";
		
		//Average hours per month
		$averagehoursmonth=0;
		if ($monthsactive>0) {
			$averagehoursmonth=number_format($query->row()->TotalHours/$monthsactive,1);
		}
		$clienttotals .= "Average hours per month = ".$averagehoursmonth."<br/>";		
		
		//Average hours per job
		$averagehours=0;
		if ($query->row()->TotalBilled!=0 && $query->row()->TotalHours!=0) {
			$averagehours=number_format($query->row()->TotalHours/$totaljobs,1);
		}
		$clienttotals .= "Average hours per job = ".$averagehours."<br/>";


		
		//Billed to date
		$clienttotals .= "<br/>";	
		$clienttotals .= "Total billed to date = ".number_format($query->row()->TotalBilled,2)." ".$CurrentClient->Currency."<br/>";


		//Average revenue per month
		$revpermonth= $query->row()->TotalBilled/$monthsactive;
		$clienttotals .= "Average revenues per month = ".number_format($revpermonth,2)." ".$CurrentClient->Currency."<br/>";

		//Average revenue per job
		if ($totaljobs==0) {$totaljobs=1;}
		$revperjob= $query->row()->TotalBilled/$totaljobs;
		$clienttotals .= "Average revenues per job = ".number_format($revperjob,2)." ".$CurrentClient->Currency."<br/>";

		//Link to historical report
		$clienttotals .= "<a href=\"".base_url()."reports/clientreport/".$CurrentClient->ID."\">View historical report</a><br/>";

		$clienttotals .= "</p></div>";
		
		return $clienttotals;
	
	}

	// ################## clients contacts ##################	
	function   _getclientcontacts($CurrentClient)
	{
		$this->load->model('trakcontacts', '', TRUE);
		$ContactsList  = $this->trakcontacts->GetEntry($options = array('CompanyName'=>$CurrentClient->CompanyName,'Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
		$clientcontacts='<div id="clientcontacts">';
		if ($ContactsList) {
			$clientcontacts.='<h4 class="contactlist">'.count($ContactsList).' Client Contacts</h4>';
			$clientcontacts.='<div  class="contactlist">';
			foreach ($ContactsList as $contact) {
				$clientcontacts.=' <a href="'.site_url().'/contacts/editcontact/'.$contact->ID.'">'.$contact->FirstName." ".$contact->LastName.'</a><br/>';
			}
			$clientcontacts.='</div>';
		} else {
			$clientcontacts.="No client contacts";
		}
		$clientcontacts.='</div>';
		return $clientcontacts;
	
	}

	// ################## clients materials ##################	
	function   _listclientmaterials ($CurrentClient)
	{
		$this->load->model('trakcontacts', '', TRUE);
		$clientmaterials='<div id="clientmaterials">';
		$clientmaterials.='<h4 class="contactlist">Client Materials</h4>';
		$clientcode = $this->uri->segment(3);			
		$clientmaterials.= getClientMaterials($CurrentClient->ClientCode);	
		$clientmaterials.= '<br/><a href="'.site_url().'clients/zt2016_manageclientmaterials/'.$CurrentClient->ClientCode.'">Manage client materials</a><br>';		
		$clientmaterials.='</div>';
		return $clientmaterials;
	
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