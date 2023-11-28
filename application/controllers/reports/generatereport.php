<?php

class Generatereport extends MY_Controller {


	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('userpermissions', 'url'));
		
		$ZOWuser=_getCurrentUser();
			if ($ZOWuser!="miguel" &&	$ZOWuser!="sunil.singal") {
			redirect('trackingnew');
		}		
		$this->load->helper(array('form','url', 'invoice','reports'));
		
		//Read form input values
		$Client =$this->input->post('Client', TRUE);
		$StartDate =$this->input->post('ReportStartDate', TRUE);
		$EndDate =$this->input->post('ReportEndDate', TRUE);

	
		if ($Client!="" and $StartDate!="" and $EndDate!="") {
			$StartDate = date( 'Y-m-d',strtotime(str_replace("/","-",$StartDate)));
			$EndDate = date( 'Y-m-d',strtotime(str_replace("/","-",$EndDate)));
			$templateVars['pageOutput'] = $this->_getOutput(array('Client'=>$Client),$StartDate,$EndDate);
			$templateVars['pageInput'] = $this-> _getInputForm($Client,$StartDate,$EndDate);
		}
		else 
		{
			if($this->session->flashdata('Client')){ 
				$Client =$this->session->flashdata('Client');
				$Client= str_replace('_', ' ', $Client);
				$StartDate = date( 'Y-m-d',strtotime(str_replace("/","-",$this->session->flashdata('ReportStartDate'))));
				$EndDate = date( 'Y-m-d',strtotime(str_replace("/","-",$this->session->flashdata('ReportEndDate'))));
				$templateVars['pageOutput'] = $this->_getOutput(array('Client'=>$Client),$StartDate,$EndDate);
				$templateVars['pageInput'] = $this-> _getInputForm($Client,$StartDate,$EndDate);
				//$templateVars['pageInput'] = '';
			}
			else
			{
			$templateVars['pageOutput'] = "Please filter report data below";
			$templateVars['pageInput'] = $this-> _getInputForm($Client,$StartDate,$EndDate);
			}
		}
			$this->load->model('trakclients', '', TRUE);
			$ClientList= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));
	
		if ($Client){
			$templateVars['pageSidebar'] = _getClientReportList($ClientList,$Client);
		
		}
		else{
			$templateVars['pageSidebar'] = _getClientReportList($ClientList);
		}
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Report";
		$templateVars['pageType'] = "edit billing";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtraktemplate');


	}
	

	// ################## Top Form ##################	
	function  _getInputForm ($Loadedclient,$StartDate,$EndDate)
	{
		$attributes = array( 'id' => 'ReportFilter');
		
		$FilterForm = form_open(site_url().'reports/generatereport',$attributes)."\n";
		
		$FilterForm .="<fieldset>";
		$FilterForm.= form_label('Client:','Client');
		
		$options = array(''  => '');
		$Clientlist=$this->_getClients();
		foreach($Clientlist as $client)
		{
			$options[$client->CompanyName]=$client->CompanyName;
		}		

		$more = 'id="Client"';
					
		if ($Loadedclient!="") {
			$FilterForm .=form_dropdown('Client', $options, $Loadedclient,$more);
		}
		else {
			$FilterForm .=form_dropdown('Client', $options, '',$more);
		}
		$FilterForm .="</fieldset>";


		$FilterForm .="<fieldset>";
		$FilterForm .= form_label('Start Date:','ReportStartDate')."\n";
		//If date comes from db, format it for human display
		if ($StartDate!=""){$StartDate = date( 'd/M/Y',strtotime($StartDate));}
		$ndata = array('name' => 'ReportStartDate', 'id' => 'ReportStartDate', 'size' => '15', 'class'=>'StartDate', 'value'=>$StartDate);
		$FilterForm .= "\n".form_input($ndata)."\n";
		$FilterForm .="</fieldset>";
		
		$FilterForm .="<fieldset>";
		$FilterForm .= form_label('End Date:','ReportEndDate')."\n";
		//If date comes from db, format it for human display
		if ($EndDate!=""){$EndDate = date( 'd/M/Y',strtotime($EndDate));}
		$ndata = array('name' => 'ReportEndDate', 'id' => 'ReportEndDate', 'size' => '15', 'class'=>'EndDate', 'value'=>$EndDate);
		$FilterForm .= "\n".form_input($ndata)."\n";
		$FilterForm .="</fieldset>";

		$FilterForm .="<fieldset class=\"formbuttons\">";
		$ndata = array('name' => 'submit','value' => 'Report','class' => 'submitButton');
		$FilterForm .= form_submit($ndata)."\n";
		$FilterForm .="</fieldset>";
		$FilterForm .= form_close()."\n";
		return $FilterForm;
	}



	// ################## Entry list ##################	
	function  _getOutput($client,$StartDate,$EndDate)
	{
		if ($client==""){
			return false;
		}
		else
		{
			$this->load->model('trakentries', '', TRUE);
			$getentries = $this->trakentries->GetEntryRange($client,$StartDate,$EndDate);
			if($getentries)
			{
				
				$entries=$this->_getTotals($client['Client'],$StartDate,$EndDate);

				
				$entries.= "<table id=\"currententries\">\n";
				$entries .= "<thead>\n";
				$entries .= "<tr><th class=\"header\">Client</th><th class=\"header\">Date</th><th class=\"header\">Originator</th><th class=\"header\"># New Slides</th><th class=\"header\"># Edited Slides</th><th class=\"header\"># Hours</th><th>File Name</th><th class=\"button\"></th><th class=\"button\"></th></tr>\n";
				$entries .= "</thead>\n";
				
				$entries .= "<tbody>\n";
				foreach($getentries as $project)
				{
					$entries .= "<tr>";
					$entries .= "<td>".$project->Client . "</td>";
					//Converts MySQL date
					$mysqldate = date( 'd/M/Y',strtotime($project->DateOut));
					$entries .= "<td class=\"date\">".$mysqldate. "</td>";
					$entries .= "<td>".$project->Originator . "</td>";
					if ($project->NewSlides > 0) {
						$entries .= "<td class=\"slides\">".$project->NewSlides . "</td>";
					}
					else
					{
						$entries .= "<td class=\"slides\"></td>";
					}
					if ($project->EditedSlides > 0) {
						$entries .= "<td class=\"slides\">".$project->EditedSlides . "</td>";
					}
					else
					{
						$entries .= "<td class=\"slides\"></td>";
					}
					if ($project->Hours > 0) {
						$entries .= "<td class=\"slides\">".$project->Hours . "</td>";
					}
					else
					{
						$entries .= "<td class=\"slides\"></td>";
					}
					$entries .= "<td>".$project->FileName . "</td>";
					$entries .= "<td class=\"button edit\"><a href=\"".site_url()."editentry/".$project->id . "\" class=\"edit\">Edit</a></td>";
					$entries .= "<td class=\"button delete\"><a href=\"".site_url()."trashentry/".$project->id . "\" class=\"delete\">Trash</a></td>";

					$entries .= "</tr>\n";
				}
				$entries .= "</tbody>\n";
				$entries .= "</table>\n";
			}
			else
			{
				$entries = "No entries since last invoice.\n";
				
				$entries .= getPastInvoices($client['Client']);

			}
		return $entries;
		}
	}
	
	// ################## Calculate and display totals ##################	
	function  _getTotals($client,$StartDate,$EndDate)
	{
	
		  //Get entry totals from db
		  $this->db->select_sum('Hours','Hours');
		  $this->db->select_sum('NewSlides','NewSlides');
		  $this->db->select_sum('EditedSlides','EditedSlides');
		  $this->db->from('zowtrakentries');
		  $this->db->where('Client',$client);
		  $this->db->where('DateOut >=', $StartDate);
		  $this->db->where('DateOut <= ', $EndDate);
		  $this->db->where('Trash =',0);
		  $query = $this->db->get();

		  //Get client details from db
		  $this->load->model('trakclients', '', TRUE);
		  $query2 = $this->trakclients->GetEntry($options = array('CompanyName' => $client));

 		  //Convert dates from db to human format
		  $StartDate = date( 'd/M/Y',strtotime($StartDate));
		  $EndDate = date( 'd/M/Y',strtotime($EndDate));
		  
		  //Apply edit price
		  $subtotal=$query->row()->EditedSlides*$query2->PriceEdits;
		  //Add slides and divide by slides per hour
		  $subtotal=$subtotal+$query->row()->NewSlides;
		  $subtotal=$subtotal/5;
		  //Add hours to get the total
		  $htotal=$subtotal+$query->row()->Hours;

		  //$total = "<p>".$this->db->last_query()."</p>";

		  $total ="<div id=\"reportTotals\">\n";
		   $total .="<div id=\"reportMain\">\n";
		  $total .="<h3>\n";
		  $total .="Report for ".$client;
		  $total .= "<br/> from  ".$StartDate." to  ".$EndDate.". ";
		  $total .= "</h3>\n" ;
		  $total .= "<h2>Total billable hours: ".$htotal;
		  if ($query2->RetainerHours!=0){
				if (date("m",strtotime($StartDate))==date("m",strtotime($EndDate))){
					$Retainerleft=$query2->RetainerHours-$htotal;
					$total .="<br />Hours left in retainer: ".$Retainerleft;
				}
		  }
		  $total .= "</h2>\n" ;
		  $total .= "</div>\n" ;

		  $total .="<div id=\"totals\"><p>\n";
		  $total .="Total Hours: ".$query->row()->Hours."<br/>\n";
		  $total .="Total New Slides: ".$query->row()->NewSlides."<br/>\n";
		  $total .="Total Edited Slides: ".$query->row()->EditedSlides."<br/>\n";
		  $gtotal=$query->row()->NewSlides+$query->row()->EditedSlides;
		  
		  $total .="<strong>Total Slides: ".$gtotal."</strong><br/>\n";

		  //Get client numbers from db

		  //$total .= "<p>".$this->db->last_query()."</p>";



		  $total .="<strong><span>Total billable hours: ".$htotal."</span></strong></p>\n";
		  //If client has retainer, show numbers
		  if ($query2->RetainerHours!=0){
			  $total .="<p id=\"retainer\">Retainer: ".$query2->RetainerHours."<br/>\n";
		  }
		$total .="</div>";
		$total .=getPastInvoices($client);		
		$total .="</div>";
		return $total;

	}


	// ################## Load client list ##################	
	function  _getClients()
	{
	
		$this->load->model('trakclients', '', TRUE);
		$getentries = $this->trakclients->GetEntry();
		return $getentries;

	}



}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>