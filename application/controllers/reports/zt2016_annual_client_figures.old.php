<?php

class Zt2016_annual_client_figures extends MY_Controller {

	
	function index()
	{

		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session',)); #flashdata
		$this->load->helper(array('form','userpermissions','zt2016_clients'));
							 
		//$this->load->helper(array('form','url','clients','general','userpermissions','zt2016_clients','zt2016_timezone'));
		


		$templateData['title'] = 'Annual Client Figures';
		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _create_page($templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData);

	}
	

	// ################## display clients info ##################	
	function _create_page($ZOWuser)
	{
		
		# retrieve all clients from db		
		$this->load->model('zt2016_clients_model', '', TRUE);
		$ClientsTable = $this->zt2016_clients_model->GetClient($options = array('sortBy' => 'CompanyName'));
		
		
		# retrieve selected client from url
		$SafeclientName=$this->uri->segment(3);
		
		
		
		#if no client in url
		if (empty ($SafeclientName)) {
			# retrieve selected client from form (post) values
		 	if ($this->input->post('Current_Client')){
		 		$SafeclientName=$this->input->post('Current_Client');
			# retrieve selected client from flashdata	
		 	} else if ($this->session->flashdata('Current_Client')){
		 		$SafeclientName=$this->session->flashdata('Current_Client');
	 		}
			# use the first client on the client's table as selected client
			else{
				$clientName=$ClientsTable[0]->CompanyName;
				$SafeclientName=str_replace(" ", "_", $clientName);
				$SafeclientName=str_replace("&", "~", $SafeclientName);	
			}
		 }	

		
		$clientName=str_replace("_", " ", $SafeclientName);
		$clientName=str_replace("~", "&", $clientName);		
		
		$ClientInfo = $this->zt2016_clients_model->GetClient($options = array('CompanyName' => $clientName));

		$ClientInfo->SafeclientName =  $SafeclientName;
		
		$SafeclientName=$this->session->set_flashdata('Current_Client', $clientName);


		$page_content=$this->_display_page($ClientsTable,$ClientInfo,$ZOWuser);
		
		
		return $page_content;
	
	}	


// ################## create page ##################	


//function   _display_page ($ClientsTable,$ClientInfo,$ZOWuser,$SafeclientName,$CountriesList,$TimezonesList)
function   _display_page ($ClientsTable,$ClientInfo,$ZOWuser)
{

		$page_content ='<div class="page_content">'."\n";


		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			
			$page_content.='<div class="alert alert-danger" role="alert" style="margin-top:2em;>'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('ErrorMessage');
			$page_content.='</div>'."\n";
		}


	######### client dropdown
		$page_content.=$this->_display_clients_control($ClientsTable,$ClientInfo);
	
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
				$page_content.=$ClientInfo->CompanyName;
			$page_content.=': </h3>';
						
			### buttons
			$page_content.= "<p style='margin-top:-1em;'>";

				# Client Info button	
				$page_content.='<a href="'.site_url().'clients/zt2016_client_info/'.$ClientInfo->SafeclientName.'" class="btn btn-info btn-sm pull-right">Client Info</a>';
				$page_content.= '</p>';
	
				# Originator Data button	
				$page_content.='<form action="'.site_url().'reports/zt2016_annual_originator_figures" style="display:inline; " method="post" ><input type="hidden" id="Current_Client" name="Current_Client" value="'.$ClientInfo->SafeclientName.'">
				<input type="submit" value="Originator Data"  style=" margin-top:-.8em;" class="btn btn-warning btn-sm pull-right"></form>';
				//<a href="'.site_url().'reports/zt2016_annual_originator_figures/'.$ClientInfo->SafeclientName.'" class="btn btn-danger btn-sm pull-right">Client Info</a>';	
	
			$page_content.= "\n";### buttons
	
	
		$page_content.= '<div class="clearfix"></div>'."\n";
						
		$page_content.= '</div>'."\n";#### panel-heading 

		########## panel body
		$page_content.='<div class="panel-body">'."\n";
				

				/*
				$page_content .=zt2016_getClientForm($TimezonesList, $CountriesList, $ClientInfo);
				*/
			$page_content.='<div id="chart-area" class="col-md-6"></div>'."\n";
	
			 $page_content.=$this->_display_client_data($ClientInfo);
	

		$page_content.='</div>'.'<!-- // class="panel-body" -->'."\n";#### panel body 
		$page_content .='</div><!-- // class="page_content" -->'."\n";;#### panel


		return $page_content;
		
	}

// ################## client data  ##################	
	function   _display_client_data($ClientInfo)
	{
		
		# retrieve all clients from db		
		//$this->load->model('zt2016_entries_model', '', TRUE);
	
		$this->load->model('zt2016_reports_model', '', TRUE);
		
		//$start = new DateTime($ClientInfo->FirstClientIteration);
		$start =new DateTime('01-01-2011');
		
		$end = new DateTime('today');
		$interval = new DateInterval('P1Y');
		$period = new DatePeriod($start, $interval, $end);

		$page_content='<div id="datatable" class="table-responsive col-sm-6">'."\n";
		
		$page_content.='<table id="annualdatatable" class="table table-striped table-condensed display compact datatable">';	
		$page_content.= "<thead><tr><th>Year</th><th>Billed Hours</th><th>Jobs</th><th>Originators</th><th>Avg. Hours per Job</th><th>Avg. Hours per Originator</th><th>Avg. Jobs per Originator</th></tr></thead>";	

			foreach ($period as $dt) {
				$row= $this->zt2016_reports_model->_Client_Annual_Figures_data($options = array('Client' => $ClientInfo, 'CurrentYear'=>$dt->format('Y')));
				
				$page_content.= "<tr>";
				$page_content.= "<td>".$row['month']."</td>";
				$page_content.= "<td>".number_format($row['total'], 1)." </td>";
				$page_content.= "<td>".number_format($row['jobs'], 0)." </td>";
				$page_content.= "<td>".number_format($row['originators'], 0)." </td>";
				
				if ($row['total']>0){
					$page_content.= "<td>".number_format($row['total']/$row['jobs'],1)." </td>";
					$page_content.= "<td>".number_format($row['total']/$row['originators'], 1)." </td>";
					$page_content.= "<td>".number_format($row['jobs']/$row['originators'], 1)." </td>";
				} else{
					$page_content.= "<td>0</td>";
					$page_content.= "<td>0</td>";
					$page_content.= "<td>0</td>";
				}
				
				$page_content.= "</tr>";
				
			}
		
		$page_content.="</table>\n";

	
		$page_content.="</div><!--table-responsive-->\n";		
		
		
		
		return $page_content;
	
	}





// ################## client dropdown ##################	
	function   _display_clients_control($ClientsTable,$ClientInfo)
	{
		
		#top client dropdown
		$FormInfo['FormURL']="reports/zt2016_annual_client_figures";
		$FormInfo['labeltext']= 'Show data for';
		$FormInfo['id'] = 'client_dropdown_form';
		$FormInfo['class'] = 'form-inline';
		
	
		$clients_top_dropdown=zt2016_create_clientselector($ClientsTable,$ClientInfo,$FormInfo)."\n";
		

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