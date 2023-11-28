<?php

class Zt2016_annual_originator_figures extends MY_Controller {

	
	function index()
	{

		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session',)); #flashdata
		$this->load->helper(array('form','userpermissions','zt2016_clients','zt2016_contacts'));
							 
		//$this->load->helper(array('form','url','clients','general','userpermissions','zt2016_clients','zt2016_timezone'));
		
		$this->load->model('zt2016_clients_model', '', TRUE);
		$this->load->model('zt2016_contacts_model', '', TRUE);
		$this->load->model('zt2016_reports_model', '', TRUE);
		$this->load->model('zt2016_contacts_figures_model', '', TRUE);
		
		$templateData['title'] = 'Annual Originator Figures';
		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _create_page($templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData);

	}
	

	// ################## display clients info ##################	
	function _create_page($ZOWuser)
	{
		
		# retrieve all clients from db		
		
		$ClientsTable = $this->zt2016_clients_model->GetClient($options = array('sortBy' => 'CompanyName'));
		
		
		# retrieve selected client from url
		$CurrentOriginatorID=$this->uri->segment(3);
		
		
		
		#if no originator in url
		if (empty ($CurrentOriginatorID)) {
			# retrieve selected client from form (post) values
		 	if ($this->input->post('Current_Contact')){
		 		$CurrentOriginatorID=$this->input->post('Current_Contact');
			# retrieve selected client from flashdata	
		 	} else if ($this->session->flashdata('CurrentOriginatorID')){
		 		$CurrentOriginatorID=$this->session->flashdata('CurrentOriginatorID');
	 		}
			# use the first client on the client's table as selected client
			else{
				if ($this->input->post('Current_Client')){
					$CurrentClient=$this->input->post('Current_Client');
				} else {
					$CurrentClient=$ClientsTable[0]->CompanyName;
					
				}
				$CurrentClient=str_replace("~", "&",$CurrentClient);
			    $CurrentClient=str_replace("_", " ", $CurrentClient);

				$ClientInfo =$this->zt2016_clients_model->GetClient($options = array('CompanyName' => $CurrentClient));
				$ContactsTable = $this->zt2016_contacts_model->GetContact($options = array('CompanyName' => $CurrentClient, 'sortBy' => 'FirstName'));
				$CurrentOriginatorID= $ContactsTable[0]->ID;
			}
		 }	

		$OriginatorInfo = $this->zt2016_contacts_model->GetContact($options = array('ID' => $CurrentOriginatorID));

		if (empty($OriginatorInfo )){
			die("No originator provided");
		}
		
		$OriginatorInfo->FullName = $OriginatorInfo->FirstName." ".$OriginatorInfo->LastName;

		if (empty ($ClientInfo)) {
			 $ClientInfo =$this->zt2016_clients_model->GetClient($options = array('CompanyName' => $OriginatorInfo->CompanyName));
		}
		
		
		# retrieve all current contact's company contacts from db		
		if (empty($ContactsTable)){
			$ContactsTable = $this->zt2016_contacts_model->GetContact($options = array('CompanyName' => $OriginatorInfo->CompanyName));
		}
		
		if (empty($ContactsTable)){
			redirect('contacts/zt2016_contacts_search', 'refresh');
		}
		
		//var_dump($ClientInfo);
		


		$page_content=$this->_display_page($ClientsTable,$ClientInfo,$ContactsTable,$OriginatorInfo,$ZOWuser);
		
		
		return $page_content;
	
	}	


// ################## create page ##################	


//function   _display_page ($ClientsTable,$ClientInfo,$ZOWuser,$SafeclientName,$CountriesList,$TimezonesList)
function   _display_page ($ClientsTable,$ClientInfo,$ContactsTable,$OriginatorInfo,$ZOWuser)
{

		$page_content ='<style>
		#client_info_panel .panel-heading .btn {
			margin-right: .5em;
			margin-top: -3px;
		  }
		</style><div class="page_content">'."\n";


		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			
			$page_content.='<div class="alert alert-danger" role="alert" style="margin-top:2em;>'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('ErrorMessage');
			$page_content.='</div>			'."\n";
		}


	######### client dropdown
		$page_content.=$this->_display_originators_control($ClientsTable,$ClientInfo,$ContactsTable,$OriginatorInfo);
	
	    
	
		/*
		########## panel head
						//Invoice status form
				//$attributes='class="form-inline" id="invoice-status-form"';
				$attributes='id="client-information-form"';
				$formurl=site_url().'clients/zt2016_client_update/'.$ClientInfo->ID;

				$page_content.=form_open($formurl,$attributes )."\n";

				*/		
		$page_content.='<div id="client_info_panel" class="panel panel-default"  style="margin-top:2em;">'."\n";
		$page_content.='<div class="panel-heading">'."\n";

		#### Title 
			$page_content.=' <h3 class="panel-title">Annual Figures for ';
				$page_content.=$OriginatorInfo->FullName;
				$page_content.=" from ";
				$page_content.=$OriginatorInfo->CompanyName;
			$page_content.=': </h3>';
						
			### buttons
			$page_content.= "<p style='margin-top:-1em;'>";

	
				# Client Data button
	
				$ClientInfo->SafeclientName=str_replace(" ", "_", $ClientInfo->CompanyName);
				$ClientInfo->SafeclientName=str_replace("&", "~", $ClientInfo->SafeclientName);
	
				$page_content.='<a href="'.site_url().'reports/zt2016_annual_client_figures/'.$ClientInfo->SafeclientName.'" class="btn btn-warning btn-sm pull-right">Client Data</a>';	
	
				# Client Info button	
				$page_content.='<a href="'.site_url().'contacts/zt2016_contact_info/'.$OriginatorInfo->ID.'" class="btn btn-info btn-sm pull-right">Originator Info</a>';
	
	
	$page_content.= '</p>'."\n";### buttons
	
	
		$page_content.= '<div class="clearfix"></div>'."\n";
						
		$page_content.= '</div>'."\n";#### panel-heading 

		########## panel body
		$page_content.='<div class="panel-body">'."\n";
				

				/*
				$page_content .=zt2016_getClientForm($TimezonesList, $CountriesList, $ClientInfo);
				*/
			$page_content.='<div id="chart-area" class="col-md-6"></div>'."\n";
	
			 $page_content.=$this->_display_originator_data($OriginatorInfo);
	

		$page_content.='</div>'.'<!-- // class="panel-body" -->'."\n";#### panel body 
		$page_content .='</div><!-- // class="page_content" -->'."\n";;#### panel


		return $page_content;
		
	}

// ################## client data  ##################	
	function   _display_originator_data($OriginatorInfo)
	{
		
		# retrieve all clients from db		

		
		//$start = new DateTime($ClientInfo->FirstClientIteration);
		$start =new DateTime('01-01-2011');
		
		$end = new DateTime('today');
		$interval = new DateInterval('P1Y');
		$period = new DatePeriod($start, $interval, $end);

		$page_content='<div id="datatable" class="table-responsive col-sm-6">'."\n";
		
		$page_content.='<table id="annualdatatable" class="table table-striped table-condensed display compact datatable">';	
		$page_content.= "<thead><tr><th>Year</th><th>Billed Hours</th><th>Jobs</th><th>Avg. Hours per Job</th></tr></thead>";	

			foreach ($period as $dt) {
				//$row= $this->zt2016_reports_model->_Originator_Annual_Figures_data($options = array('OriginatorInfo' => $OriginatorsInfo, 'CurrentYear'=>$dt->format('Y')));
				$row= $this->_get_row_data($options = array('OriginatorInfo' => $OriginatorInfo, 'CurrentYear'=>$dt->format('Y')));
				$page_content.= "<tr>";
				$page_content.= "<td>".$row['Year']."</td>";
				$page_content.= "<td>".number_format($row['BilledHours'], 1)." </td>";
				$page_content.= "<td>".number_format($row['Jobs'], 0)." </td>";
				
				if ($row['BilledHours']>0){
					$page_content.= "<td>".number_format($row['BilledHours']/$row['Jobs'],1)." </td>";
				} else{
					$page_content.= "<td>0</td>";
				}
				
				$page_content.= "</tr>";
				
			}
		
		$page_content.="</table>\n";

	
		$page_content.="</div><!--table-responsive-->\n";		
		
		
		
		return $page_content;
	
	}

// ################## row data  ##################	
	function   _get_row_data($options =  array())
	{
		

		if ($options['CurrentYear'] <  date("Y")-2){			
			$row= $this->zt2016_contacts_figures_model->GetOriginatorAnnualData($options2 = array('CompanyName' => $options['OriginatorInfo']->CompanyName,'Originator' => $options['OriginatorInfo']->FullName,  'Year'=>$options['CurrentYear']));
			
			
			#if no data exists, insert data in dbd
			if(empty($row)){

				$row= $this->zt2016_reports_model->_Originator_Annual_Figures_data($options2 = array('OriginatorInfo' => $options['OriginatorInfo'], 'CurrentYear'=>$options['CurrentYear']));
				
				$insertedrow = $this->zt2016_contacts_figures_model->AddOriginatorAnnualData($options = array("Originator"=>$options['OriginatorInfo']->FullName, 'CompanyName' =>  $options['OriginatorInfo']->CompanyName, 'Year'=>$row['Year'], "BilledHours"=>$row['BilledHours'], "Jobs"=>$row['Jobs'],  "NewSlides"=>$row['NewSlides'], "EditedSlides"=>$row['EditedSlides'], "AdditionalHours"=>$row['AdditionalHours']));
				
			}
			
			//die();
		# data for the last 2 years is calculated and (re)inserted in existing table
		} else {
			$row= $this->zt2016_reports_model->_Originator_Annual_Figures_data($options2 = array('OriginatorInfo' => $options['OriginatorInfo'], 'CurrentYear'=>$options['CurrentYear']));

			$CheckAnnualOriginator= $this->zt2016_contacts_figures_model->GetOriginatorAnnualData($options2 = array('CompanyName' => $options['OriginatorInfo']->CompanyName,'Originator' => $options['OriginatorInfo']->FullName,  'Year'=>$options['CurrentYear']));
			
			if($CheckAnnualOriginator) {
				$insertedrow = $this->zt2016_contacts_figures_model->UpdateOriginatorAnnualData($options = array('ID' => $CheckAnnualOriginator->ID, "Originator"=>$options['OriginatorInfo']->FullName, 'CompanyName' =>  $options['OriginatorInfo']->CompanyName, 'Year'=>$row['Year'], "BilledHours"=>$row['BilledHours'], "Jobs"=>$row['Jobs'],  "NewSlides"=>$row['NewSlides'], "EditedSlides"=>$row['EditedSlides'], "AdditionalHours"=>$row['AdditionalHours']));
			} else{	
				$insertedrow = $this->zt2016_contacts_figures_model->AddOriginatorAnnualData($options = array("Originator"=>$options['OriginatorInfo']->FullName, 'CompanyName' =>  $options['OriginatorInfo']->CompanyName, 'Year'=>$row['Year'], "BilledHours"=>$row['BilledHours'], "Jobs"=>$row['Jobs'],  "NewSlides"=>$row['NewSlides'], "EditedSlides"=>$row['EditedSlides'], "AdditionalHours"=>$row['AdditionalHours']));
			}

		}

		return  (array) $row;
	
	}



// ################## client dropdown ##################	
	function   _display_originators_control($ClientsTable,$ClientInfo,$ContactsTable,$ContactInfo)
	{
		
		#top client dropdown
		$FormInfo['FormURL']="reports/zt2016_annual_originator_figures";
		$FormInfo['labeltext']= 'Show data for';
		$FormInfo['id'] = 'client_dropdown_form';
		$FormInfo['class'] = 'form-inline';
		
	
		$clients_top_dropdown=zt2016_create_clientselector($ClientsTable,$ClientInfo,$FormInfo)."\n";

		$clients_top_dropdown.=_display_contacts_control($ContactsTable,$ContactInfo,$FormInfo['FormURL']);		

		return $clients_top_dropdown;
	
	}


}



// ------------------------------------------------------------------------

/**
 * _dateform
 *
 * Creates date selector dropdown
 *
 * @access	public
 * @return	string
 */
	function  _dateform($StartDate)
	{
	//get lowest date from db
	$this->db->select_min('DateOut');
	$query = $this->db->get('zowtrakentries');
	
	//echo $query->row(0)->DateOut;
	$initial=date( 'M Y', strtotime($query->row(0)->DateOut));
	
	$selecteddate =date( 'M Y', strtotime($StartDate));
	
	$EndDate = date( 'M Y', strtotime('now'));
	$i=0;
						
	
	do {
		$i++;
		$running =date( 'M Y', strtotime($initial.'+'.$i.'months'));
		$options[$running]=$running;
		//echo $running."<br/>";;
	} while ($running != $EndDate);
	
	$options = array_reverse($options);
	
	 $attributes['id'] = 'financialsmonthform';
	 $entryForm = form_open(site_url().'financials', $attributes)."\n";
	$entryForm .="<fieldset>";
		$more = 'id="financialsmonthpicker"';
		$selected=$selecteddate;
		$entryForm .=form_dropdown('financialsdate', $options,$selected,$more);
		$ndata = array('name' => 'submitbutton','value' => 'View', 'id'=>'financialsmonthsubmit');
		$entryForm .= form_submit($ndata)."\n";
	  $entryForm .="</fieldset>";  
	  $entryForm .= form_close()."\n";
	 return $entryForm ;
	}



/* End of file zt2016_monthly_report.php */
/* Location: ./system/application/controllers/reports/zt2016_monthly_report */
?>