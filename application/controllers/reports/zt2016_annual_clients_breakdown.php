<?php

class Zt2016_annual_clients_breakdown extends MY_Controller {

	
	function index()
	{

		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 

		$this->load->model('zt2016_clients_model', '', TRUE);
		$this->load->model('zt2016_reports_breakdown_model', '', TRUE);
		
		$this->load->library(array('session')); #flashdata
		
		$this->load->helper(array('userpermissions','form','zt2016_reports_helper'));
		
		//$this->load->helper(array('form','url','clients','general','userpermissions','zt2016_clients','zt2016_timezone'));
		
		//$zowuser=_superuseronly(); 
		
		$report_year=$this->uri->segment(3);

		if (empty ($report_year)) {
			
		 	if ($this->input->post('selected_year')){
				$report_year=$this->input->post('selected_year');
			}
			
			else if ($this->session->flashdata('selected_year')){
		 		$report_year=$this->session->flashdata('selected_year');
	 		}
			
			else{
				$report_year= date('Y');
		 	}
		}

		$this->session->set_flashdata('selected_year', $report_year);
		
		
		$templateData['title'] = $report_year.' Annual Clients Breakdown - ZOWTrak';
		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _create_page($report_year,$templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData);

	}
	

	// ################## display clients info ##################	
	function _create_page($report_year,$ZOWuser)
	{

		$page_content ='<div class="row">'."\n".'<div class="page_content col-sm-12">'."\n";
		

		######### Display success message
		if($this->session->flashdata('SuccessMessage')){		
			
			$page_content.='<div class="alert alert-success" role="alert" style="margin-top:2em;>'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			//$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('SuccessMessage');
			$page_content.='</div>'."\n";
		}

		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			
			$page_content.='<div class="alert alert-danger" role="alert" style="margin-top:2em;>'."\n";
			$page_content.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$page_content.=$this->session->flashdata('ErrorMessage');
			$page_content.='</div>'."\n";
		}

		########## panel head
		$page_content.='<div id="client_info_panel" class="panel panel-success">'."\n";
		$page_content.='<div class="panel-heading">'."\n";
		
			### Month Tile and selector
			
				# Query lowest date from db
				$this->db->select_min('DateOut');
				$start_month = $this->db->get('zowtrakentries');		

				# year selector options
				$year_selector_info['FormURL']=site_url()."reports/zt2016_annual_clients_breakdown";
				$year_selector_info['labeltext']= 'Year';
				$year_selector_info['id'] = 'date_dropdown_form';
				$year_selector_info['style'] = 'display:inline-block';

				$page_content.='<h4 >'."\n";
				$page_content.=	'<div class="pull-left" style="margin-right:1rem; padding-top:.7rem;">Annual Clients Breakdown for</div>'."\n";
				$page_content.=year_selector($report_year,$year_selector_info)."\n";

		
				########## left buttons
				##### monthly client breakdown button
				$page_content.='<a href="'.site_url().'reports/zt2016_annual_originators_breakdown/'.$report_year.'" class="btn btn-primary btn-sm">Originators Data</a>';		
				### mopnthly clients breakdown button
				$page_content.='<a href="'.site_url().'reports/zt2016_monthly_clients_breakdown/Jan%20'.$report_year.'" class="btn btn-info btn-sm">Monthly Data</a>';		

					### old reports page
				$page_content.='<a href="'.site_url().'reports" class="btn btn-danger btn-sm">Old Reports Page</a>';		
		
				
				########## right buttons
				##### momentum button
			//	$page_content.='<a href="'.site_url().'reports/zt2016_monthly_momentum_report/Jan%20'.$report_year.'" class="btn btn-info btn-sm pull-right">Momentum</a>';		

			$page_content.= '</h4>'."\n";


		$page_content.= '</div>'."\n";

		########## panel body
		$page_content.='<div class="panel-body">'."\n";

			$page_content.='	<div class="row">'."\n";
			//$page_content.='		<div class="col-sm-12" style="margin-bottom:2em;">'."\n";
		
			$now = strtotime(date('Y-m-15'));
			//$options['StartDate'] = date( 'Y-01-01', strtotime($report_year.'-01-01'));
			$options['StartDate'] = date( $report_year.'-01-01');
			$options['EndDate']= date( $report_year.'-12-31');		

		
		$page_content.=$this->_yearData($options);

			//$page_content.= '		</div>'."\n";		
			$page_content.= '	</div>'."\n";
				
		$page_content.= '	</div>'."\n";		
		$page_content.= '</div>'."\n";		
		
		
		
	
		
		return $page_content;
	
	}	
	
// ################## General month numbers	
	
	
	function  _yearData($options=array())
	{
			######### Extract month data from db ################## 
	
		
			$StartDate=$options['StartDate'];
			$EndDate=$options['EndDate'];
					
			if (date( 'Y', strtotime($StartDate))==date( 'Y', strtotime('now'))){
				$thismonth=1;
			}


			################## Billed data ################## 
			unset($bookeddata );
		
			$bookeddata = $this->zt2016_reports_breakdown_model->_getAllClientsBilledTotalsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
	
		
		
			if (isset($bookeddata)){
	
				$grandtotal=0;
				$newtotal=0;
				$editstotal=0;
				$hourstotal=0;
				foreach ($bookeddata as $row) {
						foreach ($row as $key => $value) {
							$monthtotal[$row['Client']][$key]= $value;
						}
						$bookedtotal=number_format($row['InvoiceTime'],2);
						$bookedtotal=floatval(str_replace(",","",$bookedtotal));
						
					
						############ originator total
						if (isset($monthtotal[$row['Client']]['Total'])){
							$monthtotal[$row['Client']]['Total']=$monthtotal[$row['Client']]['Total']+ number_format($bookedtotal,2);
						} else {
							$monthtotal[$row['Client']]['Total']= number_format($bookedtotal,2);
						}					


						############ group total					
						$clientdata= $this->zt2016_clients_model->GetClient($options = array('CompanyName' => $row['Client']));
						$monthtotal[$row['Client']]['ClientCode']=$clientdata->ClientCode;
						
						if ($clientdata->Group!=""){
							$monthtotal[$row['Client']]['Group']=$clientdata->Group;
						} else{
							$monthtotal[$row['Client']]['Group']="Other";
						}
						
						if (isset($grouptotal[$monthtotal[$row['Client']]['Group']])){
								$grouptotal[$monthtotal[$row['Client']]['Group']]=$grouptotal[$monthtotal[$row['Client']]['Group']]+$bookedtotal;
						} else{
								$grouptotal[$monthtotal[$row['Client']]['Group']]=$bookedtotal;
						}
					
						############ client total
						if (isset($clienttotal[$monthtotal[$row['Client']]['Client']])){
								$clienttotal[$monthtotal[$row['Client']]['Client']]=$clienttotal[$monthtotal[$row['Client']]['Client']]+$bookedtotal;
						} else{
								$clienttotal[$monthtotal[$row['Client']]['Client']]=$bookedtotal;
						}					
					
						$grandtotal=$grandtotal+$bookedtotal;
						$newtotal=$newtotal+$row['NewSlides'];
						$editstotal=$editstotal+$row['EditedSlides'];
						$hourstotal=number_format($hourstotal+$row['Hours'],2);
						

				}
			 
			}

			##################  Complete but not billed data ################## 
			unset($bookeddata );

		
			$bookeddata = $this->zt2016_reports_breakdown_model->_getAllClientsCompletedTotalsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));

		/*
		echo "<br/><br/><br/><br/><br>";
		var_dump($bookeddata);	
		echo "<br/><br/><br/><br/><br>";
		*/
			if (isset($bookeddata)){
		
				foreach ($bookeddata as $row) {
						foreach ($row as $key => $value) {
							if (isset ($monthtotal[$row['Client']][$key])) {
									if ($key!="Client" && $key!="Originator") {
									$monthtotal[$row['Client']][$key]= $monthtotal[$row['Client']][$key]+ $value;
								}
							} else {
								$monthtotal[$row['Client']][$key]= $value;
							}
						
						}
						$clientdata= $this->zt2016_clients_model->GetClient($options = array('CompanyName' => $row['Client']));
	
						$monthtotal[$row['Client']]['ClientCode']=$clientdata->ClientCode;
					
						if ($clientdata->Group!=""){
							$monthtotal[$row['Client']]['Group']=$clientdata->Group;
						}else{
							$monthtotal[$row['Client']]['Group']="Other";
						}
							
						//Apply edit price
						$subtotalbooked= $row['EditedSlides']*$clientdata->PriceEdits/5;
						//Add slides and divide by slides per hour
						//$subtotalbooked= $row['NewSlides']+$subtotalbooked;
						//$subtotalbooked= $subtotalbooked/5;
					
						$subtotalbooked= $subtotalbooked+$row['NewSlides']/5;
					
					
						//Add hours to get the total
						$bookedtotal= number_format ($subtotalbooked+$row['Hours'],2);						
						
					
						############ originator total
						if (isset($monthtotal[$row['Client']]['Total'])){
							$monthtotal[$row['Client']]['Total']=$monthtotal[$row['Client']]['Total']+ number_format($bookedtotal,2);
						} else {
							$monthtotal[$row['Client']]['Total']= number_format($bookedtotal,2);
						}
					
						############ group total
						if (isset($grouptotal[$monthtotal[$row['Client']]['Group']])){
								$grouptotal[$monthtotal[$row['Client']]['Group']]=$grouptotal[$monthtotal[$row['Client']]['Group']]+$bookedtotal;
						} else{
								$grouptotal[$monthtotal[$row['Client']]['Group']]=$bookedtotal;
						}
					
						############ client total
						if (isset($clienttotal[$monthtotal[$row['Client']]['Client']])){
								$clienttotal[$monthtotal[$row['Client']]['Client']]=$clienttotal[$monthtotal[$row['Client']]['Client']]+$bookedtotal;
						} else{
								$clienttotal[$monthtotal[$row['Client']]['Client']]=$bookedtotal;
						}						
					
					
						$grandtotal=$grandtotal+$bookedtotal;
						$newtotal=$newtotal+$row['NewSlides'];
						$editstotal=$editstotal+$row['EditedSlides'];
						$hourstotal=number_format($hourstotal+$row['Hours'],2);

				}// ($bookeddata as $row) loop
				
				if(isset($grouptotal)){ $monthgroupstotal=  count($grouptotal);} else {$monthgroupstotal=0;} 
				if(isset($clienttotal)){$monthclientstotal = count($clienttotal);} else {$monthclientstotal=0;}
				
				if(isset($grouptotal)){
					
					$monthcontacts=$this->zt2016_reports_breakdown_model->_OriginatorsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
					$monthcontactstotal = count($monthcontacts);
					unset($monthcontacts);
				} else{$monthcontactstotal=0;}
			}

	
			if (isset($thismonth)){
			
				################## Booked but not completed jobs ################## 
				
				unset($bookeddata );
				$bookeddata = $this->zt2016_reports_breakdown_model->_getAllClientsOngoingTotalsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));

				
				if (isset($bookeddata)){
	
					$bookedgrandtotal=$grandtotal;
					$bookednewtotal=$newtotal;
					$bookededitstotal=$editstotal;
					$bookedhourstotal=$hourstotal;
					
					foreach ($bookeddata as $row) {
						foreach ($row as $key => $value) {
							if (isset ($monthtotal[$row['Client']][$key])) {
								if ($key!="Client" && $key!="Originator") {
									$monthtotal[$row['Client']][$key]= $monthtotal[$row['Client']][$key]+ $value;
								}
							} else {
								$monthtotal[$row['Client']][$key]= $value;
							}
						}

						$clientdata= $this->zt2016_clients_model->GetClient($options = array('CompanyName' => $row['Client']));

						$monthtotal[$row['Client']]['ClientCode']=$clientdata->ClientCode;
						
						if ($clientdata->Group!=""){
							$monthtotal[$row['Client']]['Group']=$clientdata->Group;
						}else{
							$monthtotal[$row['Client']]['Group']="Other";
						}
						
						//Apply edit price
						$subtotalbooked= $row['EditedSlides']*$clientdata->PriceEdits/5;
						//$subtotalbooked= $row['EditedSlides']*$clientdata->PriceEdits;
						//Add slides and divide by slides per hour
						//$subtotalbooked= $row['NewSlides']+$subtotalbooked;
						//$subtotalbooked= $subtotalbooked/5;
						$subtotalbooked= $subtotalbooked+$row['NewSlides']/5;
						
						//Add hours to get the total
						$bookedtotal= number_format ($subtotalbooked+$row['Hours'],2);						
					
						############ originator total
						if (isset($monthtotal[$row['Client']]['Total'])){
							$monthtotal[$row['Client']]['Total']=$monthtotal[$row['Client']]['Total']+ number_format($bookedtotal,2);
						} else {
							$monthtotal[$row['Client']]['Total']= number_format($bookedtotal,2);

						}
						
						############ group total
						if (isset($grouptotal[$monthtotal[$row['Client']]['Group']])){
								$grouptotal[$monthtotal[$row['Client']]['Group']]=$grouptotal[$monthtotal[$row['Client']]['Group']]+$bookedtotal;
						} else{
								$grouptotal[$monthtotal[$row['Client']]['Group']]=$bookedtotal;
						}
						############ client total
						if (isset($clienttotal[$monthtotal[$row['Client']]['Client']])){
								$clienttotal[$monthtotal[$row['Client']]['Client']]=$clienttotal[$monthtotal[$row['Client']]['Client']]+$bookedtotal;
						} else{
								$clienttotal[$monthtotal[$row['Client']]['Client']]=$bookedtotal;
						}						
						
						
						
						$bookedgrandtotal=$bookedgrandtotal+$bookedtotal;
						$bookednewtotal=$bookednewtotal+$row['NewSlides'];
						$bookededitstotal=$bookededitstotal+$row['EditedSlides'];
						$bookedhourstotal=number_format($bookedhourstotal+$row['Hours'],2);

						

						

					}// ($bookeddata as $row) loop
					$monthbookedgroupstotal=  count($grouptotal);
					$monthnookedclientstotal = count($clienttotal);
					
					
					$monthbookedcontactstotal=count($monthtotal);
					$monthBookedcontacts=$this->zt2016_reports_breakdown_model->_BookedOriginatorsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
					$monthbookedcontactstotal = count($monthBookedcontacts);
					unset($monthBookedcontacts);
				}
			}		
		
/**/
		
			//https://stackoverflow.com/questions/2359652/how-do-i-move-an-array-element-with-a-known-key-to-the-end-of-an-array-in-php
		 	if (isset($grouptotal)) { arsort($grouptotal); };
			
			# Place "Other" group last in sorted array
			if (isset($grouptotal['Other'])){
				$v = $grouptotal['Other'];
				unset($grouptotal['Other']);
				$grouptotal['Other'] = $v;				
			}

			if (!isset($monthtotal)) {$monthtotal=0;}
			if (!isset($grouptotal)) {$grouptotal=0;}
			if (!isset($clienttotal)) {$clienttotal=0;}
		
			if (!isset($thismonth)){
				$monthdatatable=$this->_create_month_table($options2=array('StartDate'=>$StartDate,'EndDate'=>$EndDate, 'monthtotal'=>$monthtotal,'grandtotal'=>$grandtotal,'newtotal'=>$newtotal,'editstotal'=>$editstotal,'hourstotal'=>$hourstotal, 'grouptotal'=>$grouptotal, 'clienttotal'=>$clienttotal, 'monthgroupstotal'=>$monthgroupstotal, 'monthclientstotal'=>$monthclientstotal,'monthcontactstotal'=>$monthcontactstotal));
			}
			else {
				$monthdatatable=$this->_create_month_table($options2=array('StartDate'=>$StartDate,'EndDate'=>$EndDate, 'monthtotal'=>$monthtotal,'grandtotal'=>$grandtotal,'newtotal'=>$newtotal,'editstotal'=>$editstotal,'hourstotal'=>$hourstotal, 'grouptotal'=>$grouptotal, 'clienttotal'=>$clienttotal, 'monthgroupstotal'=>$monthgroupstotal, 'monthclientstotal'=>$monthclientstotal,'monthcontactstotal'=>$monthcontactstotal, 'bookedgrandtotal'=>$bookedgrandtotal, 'bookednewtotal'=>$bookednewtotal, 'bookededitstotal'=>$bookededitstotal, 'bookedhourstotal'=>$bookedhourstotal,'monthbookedgroupstotal'=>$monthbookedgroupstotal, 'monthbookedclientstotal'=>$monthnookedclientstotal,'monthbookedcontactstotal'=>$monthbookedcontactstotal));

			}

			if (!isset($monthdatatable)) {
				$monthdatatable="<div class=\"monthtotal\"><h3>No entries.<h3></div>";
			}
		
		 return $monthdatatable;
	}
	
	
	
	// ################## create month info ##################	
	function _create_month_table($options)
	{
	
				if (date( 'M Y', strtotime($options['StartDate']))==date( 'M Y', strtotime('now'))){
					$thismonth=1;
					$numjobsbooked=$this->zt2016_reports_breakdown_model->_NumJobsByDate($options2 = array('StartDate'=>$options['StartDate'],'EndDate'=>$options['EndDate']));
					$options['EndDate'] = date( 'Y-m-d', strtotime('now' ));
				}
			
				$numjobs=$this->zt2016_reports_breakdown_model->_NumJobsByDate($options);
				
				## Year main figures
	
				$dailyAverage = number_format($options['grandtotal']/365, 2);
				//$split="<div class=\"monthtotal\">";
		
			
				# Year totals
				$split="	<div class=\"col-sm-12\">";
					$split.="	<h3><Month Totals</h3>";
				$split.="</div>";
		
		
		
				# Hours per day, total hours, jobs
				$split.="	<div class=\"col-sm-4\">";
		
					$split.="	<h4><strong>";
					$split.=$dailyAverage."</strong>  hours billed per day";
					$split.="<small> (average 365 days)</small>" ;
					$split.="	</h4>";


					$split.="		<h4><strong>".number_format($options['grandtotal'],2)."</strong> total hours";
					if (date( 'M Y', strtotime($options['StartDate']))==date( 'M Y', strtotime('now'))){
						$split.="<small> (Booked: ".number_format($options['bookedgrandtotal'],2).")</small>";
					}

					$split.="		</h4>";

					$split.="	<h4><strong>".$numjobs."</strong> jobs ";
					if (isset($numjobsbooked)){
						$split.=" <small>( Booked: ".$numjobsbooked." )</small>";
					}
					$split.="	</h4>";		
				
				$split.="</div>";

		
				# Complex slides, simple slides, additional hours
				$split.="	<div class=\"col-sm-4\">";
			
					$split.="	<h4><strong>".number_format($options['newtotal'])."</strong> Complex slides | ".number_format($options['newtotal']/5,2)." hours";
					if (isset($thismonth)){
						$split.=" <small> (Booked: ".$options['bookednewtotal']." | ".number_format($options['bookednewtotal']/5,2)." hours)</small>";
					}
					$split.="	</h4>";

					$split.=" 	<h4><strong>".number_format($options['editstotal'])."</strong> Simple slides | ".number_format($options['newtotal']/10,2)." hours";
					if (isset($thismonth)){
						$split.=" <small> (Booked: ".$options['bookededitstotal']." | ".number_format($options['bookededitstotal']/10,2)." hours)</small>";
					}
					$split.="	</h4>";

					$split.=" 	<h4><strong>".number_format($options['hourstotal'],2)."</strong> Additional hours";
					if (isset($thismonth)){
						$split.=" <small> (Booked: ".$options['bookedhourstotal'].")</small>";
					}
					$split.="	</h4>";	
		
				$split.="</div>";

				# Clients Originators
				$split.="	<div class=\"col-sm-4\">";

					
					$split.="	<h4><strong>".$options['monthgroupstotal']."</strong> Groups";
					if (isset($thismonth)){
						$split.=" <small> (Booked: ".$options['monthbookedgroupstotal'].")</small>";
					}
					$split.="	</h4>";
			
					$split.="	<h4><strong>".$options['monthclientstotal']."</strong> Clients";
					if (isset($thismonth)){
						$split.=" <small>(Booked: ".$options['monthbookedclientstotal'].")</small>";
					}
					$split.="	</h4>";
		
		
					$split.="	<h4><strong>".$options['monthcontactstotal']."</strong> Originators";
					if (isset($thismonth)){
						$split.=" <small>(Booked: ".$options['monthbookedcontactstotal'].")</small>";
					}
					$split.="	</h4>";
					
				$split.="</div>";

				## Breakdown Table

		
				$split.="	<div class=\"col-sm-12\">";
				$split.="<hr />";
				if ($numjobs >0) {
					
					$split.="	<div id='breakdown-table-title'>";		

						if (isset($thismonth)){
							$split.="	<h4><strong>".$options['monthbookedcontactstotal']."</strong> booked clients";
						} else{
							$split.="	<h4><strong>".$options['monthcontactstotal']."</strong> booked clients";
						}

					$split.="	</div>";
					
					
					$split.="<div id='client-totals-div'>";
					
					$split.='<table id="breakdown-table" class="table table-striped table-condensed display compact">'."\n<thead><tr><th class=\"header_hwe\" data-sortable=\"false\">Group</th><th class=\"header_hwe\" data-sortable=\"false\">Client</th><th class=\"header_hwe\" data-sortable=\"true\">Booked Hours</th><th class=\"header_hwe\" data-sortable=\"true\">Jobs</th><th class=\"header_hwe\" data-sortable=\"true\">New</th><th class=\"header_hwe\" data-sortable=\"true\">Edits</th><th class=\"header_hwe\" data-sortable=\"true\">Hours</th></tr></thead>";
					
					$counter=array('Jobs'=>0,'NewSlides'=>0,'EditedSlides'=>0,'Hours'=>0,'BilledHours'=>0);
					
					$tabledata="";
					foreach ($options['monthtotal'] as $row) {

											
						$SafeClientName=str_replace(" ", "_", $row['Client']);
						$SafeClientName=str_replace("&", "~", $SafeClientName);
						
						$tabledata.="<tr>";

						
						$tabledata.="<td data-order=\"".$options['grouptotal'][$row['Group']]."\">".$row['Group']."</td>";	
						
						$tabledata.="<td data-order=\"".$options['clienttotal'][$row['Client']]."\" data-clientcode=\"".$row['ClientCode']."\"><a href=\"".site_url()."reports/zt2016_annual_client_figures/".$SafeClientName."\">".$row['Client']."</a></td>";
						
						
						$tabledata.="<td>".$row['Total']."</td>";	
						
						$row['Jobs']=$this->zt2016_reports_breakdown_model->_NumJobsByDate($options2 = array('StartDate'=>$options['StartDate'],'EndDate'=>$options['EndDate'],'Client'=>$row['Client']));
						
						$tabledata.="<td >".$row['Jobs']."</td>";
						$tabledata.="<td>".$row['NewSlides']."</td>";
						$tabledata.="<td>".$row['EditedSlides']."</td>";
						$tabledata.="<td>".number_format($row['Hours'],2)."</td>";

						$tabledata.="</tr>\n";
						
						$counter['BilledHours']=$counter['BilledHours']+$row['Total'];
						$counter['Jobs']=$counter['Jobs']+$row['Jobs'];
						$counter['NewSlides']=$counter['NewSlides']+$row['NewSlides'];
						$counter['EditedSlides']=$counter['EditedSlides']+$row['EditedSlides'];
						$counter['Hours']=$counter['Hours']+$row['Hours'];

					}
					$counter['Hours']=number_format($counter['Hours'],2,'.',',');
					$counter['BilledHours']=number_format($counter['BilledHours'],2,'.',',');
					
					$split.="<tfoot><tr><th></th><th>Monthly Totals</th><th>".$counter['BilledHours']."</th><th>".number_format($counter['Jobs'])."</th><th>".number_format($counter['NewSlides'])."</th><th>".number_format($counter['EditedSlides'])."</th><th>".number_format($counter['Hours'],2)."</th></tr></tfoot>";	
					$split.=$tabledata;
					$split.="</table>\n";
					$split.="</div>";
				}
				
				$split.="</div>";
			
			return $split;
		}	
	
}


/* End of file zt2016_monthly_breakdown.php */
/* Location: ./system/application/controllers/reports/zt2016_monthly_breakdown */
?>