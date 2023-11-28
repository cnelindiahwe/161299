<?php

class Invoicesreport extends MY_Controller {


	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('userpermissions', 'url','form', 'invoice','reports'));
		$this->load->library('input');
		
		_superuseronly(); 

		//Read form input values
		$Client=$this->input->post('Client');
		$StartDate =$this->input->post('ReportStartDate');
		$EndDate =$this->input->post('ReportEndDate');
		$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		$this->load->model('trakclients', '', TRUE);
			$ClientList= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));

	
		if ($Client!="" and $StartDate!="" and $EndDate!="") {
			$templateVars['pageOutput'].= $this->_getinvoicingpage($ClientList,$Client);
			$StartDate = date( 'Y-m-d',strtotime(str_replace("/","-",$StartDate)));
			$EndDate = date( 'Y-m-d',strtotime(str_replace("/","-",$EndDate)));
			$templateVars['pageOutput'] .= "<div class=\"content\">";
			$templateVars['pageOutput'] .= $this->_getOutput(array('Client'=>$Client),$StartDate,$EndDate);
		
		}
		else 
		{
			if($this->session->flashdata('Client')){ 
				$Client =$this->session->flashdata('Client');
				$Client= str_replace('_', ' ', $Client);
				$StartDate = date( 'Y-m-d',strtotime(str_replace("/","-",$this->session->flashdata('ReportStartDate'))));
				$EndDate = date( 'Y-m-d',strtotime(str_replace("/","-",$this->session->flashdata('ReportEndDate'))));
				$templateVars['pageOutput'].= $this->_gettopmenu($ClientList,$Client);
				$templateVars['pageOutput'] .= "<div class=\"content\">";
				$templateVars['pageOutput'].= $this->_getOutput(array('Client'=>$Client),$StartDate,$EndDate);
			}
			else
			{
				redirect('invoicing');
			}
		}
		$templateVars['pageOutput'] .= "</div><!-- content -->";
		
		
		$this->load->model('trakclients', '', TRUE);
		$ClientList= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));
	

		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Invoicing Report";
		$templateVars['pageType'] = "invoicingreport";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}
	
	// ################## top ##################	
	function  _gettopmenu($ClientList,$Client="")
	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>Invoices</h1>";
			$entries .=_clientscontrol($ClientList,$Client);

			//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";
		
			
			return $entries;

	}	
	
	

	// ################## Entry list ##################	
	function  _getOutput($client,$StartDate,$EndDate)
	{
		if ($client==""){
			return false;
		}
		else
		{



		  $entries ="<h3>\n";
		 $entries .="Invoicing Report for ".$client['Client'];
		 $entries .= "</h3>\n" ;


			$this->load->model('trakentries', '', TRUE);
			$getentries = $this->trakentries->GetEntryRange($client,$StartDate,$EndDate);
			if($getentries)
			{
				
				$entries.=$this->_getTotals($client['Client'],$StartDate,$EndDate);

				
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
				
				$entries .= "No entries since last invoice.\n";

			}
		$entries .= getPastInvoices($client['Client']);
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

		 
		 $total .= "<h3>Pending hours: ".$htotal;
		  if ($query2->RetainerHours!=0){
				if (date("m",strtotime($StartDate))==date("m",strtotime($EndDate))){
					$Retainerleft=$query2->RetainerHours-$htotal;
					$total .="<br />Hours left in retainer: ".$Retainerleft;
				}
		  }
		  $total .= " (<em>Last invoice:".$StartDate."</em>)\n" ;
		  
		  $total .= "<a href=\"".site_url()."invoicing/newinvoice/".$client. "\">New Invoice</a>";
		  $total .= "</h3>\n" ;
		  $total .= "</div>\n" ;

		  $total .="<div id=\"totals\"><p>\n";
		  $total .=$query->row()->NewSlides." New | ";
		  $total .=$query->row()->EditedSlides." Edits | ";
		  $total .=$query->row()->Hours." Hours</p>\n";
		  $gtotal=$query->row()->NewSlides+$query->row()->EditedSlides;
		  
		  //$total .="<strong><span>Total billable hours: ".$htotal."</span></strong></p>\n";
		 

		  //Get client numbers from db

		  //$total .= "<p>".$this->db->last_query()."</p>";

		  //If client has retainer, show numbers
		  if ($query2->RetainerHours!=0){
			  $total .="<p id=\"retainer\">Retainer: ".$query2->RetainerHours."<br/>\n";
		  }
		//$total .="</div>";
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

}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>