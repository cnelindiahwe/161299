<?php

class Zt2016_monthly_originators_breakdown extends MY_Controller {

	
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
		
		$report_month=$this->uri->segment(3);
		$report_month=str_replace ("%20"," ",$report_month);
		

		if (empty ($report_month)) {
			
		 	if ($this->input->post('selected_month')){
				$report_month=$this->input->post('selected_month');
			}
			
			else if ($this->session->flashdata('selected_month')){
		 		$report_month=$this->session->flashdata('selected_month');
	 		}
			
			else{
				$report_month= date('M Y');
		 	}
		}

		$this->session->set_flashdata('selected_month', $report_month);
		
		
		$templateData['title'] = $report_month.' Originators Breakdown - ZOWTrak';
		$templateData['sub_title'] ='report_page_hsd';
		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _create_page($report_month,$templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData);

	}
	

	// ################## display clients info ##################	
	function _create_page($report_month,$ZOWuser)
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

				# month selector options
				$month_selector_info['FormURL']=site_url()."reports/zt2016_monthly_originators_breakdown";
				$month_selector_info['labeltext']= 'Month';
				$month_selector_info['id'] = 'date_dropdown_form';
				$month_selector_info['style'] = 'display:inline-block';

				$page_content.='<h4 >'."\n";
				$page_content.=	'<div class="pull-left" style="margin-right:1rem; padding-top:.7rem;">Monthly Originator Breakdown for</div>'."\n";
				$page_content.=month_selector($report_month,$start_month,$month_selector_info)."\n";
		
		
				########## left buttons
				### monthly client breakdown button
				$page_content.='<a href="'.site_url().'reports/zt2016_monthly_clients_breakdown/'.$report_month.'" class="btn btn-info btn-sm">Client Data</a>';		
				### annual originators breakdown button
				$page_content.='<a href="'.site_url().'reports/zt2016_annual_originators_breakdown/'.date('Y',strtotime($report_month)).'" class="btn btn-primary btn-sm">Annual Data</a>';		
		
				### old reports page
				$page_content.='<a href="'.site_url().'reports" class="btn btn-danger btn-sm">Old Reports Page</a>';		
		
		
		
				########## right buttons
				##### momentum button
			//	$page_content.='<a href="'.site_url().'reports/zt2016_monthly_momentum_report/'.$report_month.'" class="btn btn-info btn-sm pull-right">Momentum</a>';		
		
		
		
		
		

			$page_content.= '</h4>'."\n";


		$page_content.= '</div>'."\n";

		########## panel body
		$page_content.='<div class="panel-body">'."\n";

			$page_content.='	<div class="row">'."\n";
			//$page_content.='		<div class="col-sm-12" style="margin-bottom:2em;">'."\n";
		
			$now = strtotime(date('Y-m-15'));
			$options['StartDate'] = date( 'Y-m-1', strtotime($report_month));
			$options['EndDate']= date( 'Y-m-t', strtotime($report_month));		
			$page_content.=$this->_monthData($options);

			//$page_content.= '		</div>'."\n";		
			$page_content.= '	</div>'."\n";
				
		$page_content.= '	</div>'."\n";		
		$page_content.= '</div>'."\n";		
		
		return $page_content;
	
	}	
	
// ################## General month numbers	
	
	
	function  _monthData($options=array())
	{
			######### Extract month data from db ################## 
			$grandtotal=0;
			$newtotal=0;
			$editstotal=0;
			$hourstotal=0;		
		
			$StartDate=$options['StartDate'];
			$EndDate=$options['EndDate'];
					
			if (date( 'M Y', strtotime($StartDate))==date( 'M Y', strtotime('now'))){
				$thismonth=1;
			}


			################## Billed data ################## 
			unset($bookeddata );
		
			$bookeddata = $this->zt2016_reports_breakdown_model->_getAllOriginatorsBilledTotalsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
	
			/*
			echo "<br/><br/><br/><br/><br>";
			var_dump($bookeddata);	
			echo "<br/><br/><br/><br/><br>";	
			*/
		
			if (isset($bookeddata)){
	

				foreach ($bookeddata as $row) {
						foreach ($row as $key => $value) {
							$monthtotal[$row['Originator']][$key]= $value;
						}
						$bookedtotal=number_format($row['InvoiceTime'],2);
					
						############ originator total
						if (isset($monthtotal[$row['Originator']]['Total'])){
							$monthtotal[$row['Originator']]['Total']=$monthtotal[$row['Originator']]['Total']+ number_format($bookedtotal,2);
						} else {
							$monthtotal[$row['Originator']]['Total']= number_format($bookedtotal,2);
						}					


						############ group total					
						$clientdata= $this->zt2016_clients_model->GetClient($options = array('CompanyName' => $row['Client']));
					
						$monthtotal[$row['Originator']]['ClientCode']=$clientdata->ClientCode;
					
						if ($clientdata->Group!=""){
							$monthtotal[$row['Originator']]['Group']=$clientdata->Group;
						} else{
							$monthtotal[$row['Originator']]['Group']="Other";
						}
						
						if (isset($grouptotal[$monthtotal[$row['Originator']]['Group']])){
								$grouptotal[$monthtotal[$row['Originator']]['Group']]=$grouptotal[$monthtotal[$row['Originator']]['Group']]+$bookedtotal;
						} else{
								$grouptotal[$monthtotal[$row['Originator']]['Group']]=$bookedtotal;
						}
					
						############ client total
						if (isset($clienttotal[$monthtotal[$row['Originator']]['Client']])){
								$clienttotal[$monthtotal[$row['Originator']]['Client']]=$clienttotal[$monthtotal[$row['Originator']]['Client']]+$bookedtotal;
						} else{
								$clienttotal[$monthtotal[$row['Originator']]['Client']]=$bookedtotal;
						}					
					
						$grandtotal=$grandtotal+$bookedtotal;
						$newtotal=$newtotal+$row['NewSlides'];
						$editstotal=$editstotal+$row['EditedSlides'];
						$hourstotal=number_format($hourstotal+$row['Hours'],2);
						// echo  number_format($bookedtotal,2)." - ".$bookedtotal." - ".$grandtotal."<br/>";

				}
			}

			##################  Complete but not billed data ################## 
			unset($bookeddata );

		
			$bookeddata = $this->zt2016_reports_breakdown_model->_getAllOriginatorsCompletedTotalsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));

		/*
		echo "<br/><br/><br/><br/><br>";
		var_dump($bookeddata);	
		echo "<br/><br/><br/><br/><br>";
		*/
			if (isset($bookeddata)){
		
				foreach ($bookeddata as $row) {
						foreach ($row as $key => $value) {
							if (isset ($monthtotal[$row['Originator']][$key])) {
									if ($key!="Client" && $key!="Originator") {
									$monthtotal[$row['Originator']][$key]= $monthtotal[$row['Originator']][$key]+ $value;
								}
							} else {
								$monthtotal[$row['Originator']][$key]= $value;
							}
						
						}
						$clientdata= $this->zt2016_clients_model->GetClient($options = array('CompanyName' => $row['Client']));
	
						$monthtotal[$row['Originator']]['ClientCode']=$clientdata->ClientCode;
					
						if ($clientdata->Group!=""){
							$monthtotal[$row['Originator']]['Group']=$clientdata->Group;
						}else{
							$monthtotal[$row['Originator']]['Group']="Other";
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
						if (isset($monthtotal[$row['Originator']]['Total'])){
							$monthtotal[$row['Originator']]['Total']=$monthtotal[$row['Originator']]['Total']+ number_format($bookedtotal,2);
						} else {
							$monthtotal[$row['Originator']]['Total']= number_format($bookedtotal,2);
						}
					
						############ group total
						if (isset($grouptotal[$monthtotal[$row['Originator']]['Group']])){
								$grouptotal[$monthtotal[$row['Originator']]['Group']]=$grouptotal[$monthtotal[$row['Originator']]['Group']]+$bookedtotal;
						} else{
								$grouptotal[$monthtotal[$row['Originator']]['Group']]=$bookedtotal;
						}
						############ client total
						if (isset($clienttotal[$monthtotal[$row['Originator']]['Client']])){
								$clienttotal[$monthtotal[$row['Originator']]['Client']]=$clienttotal[$monthtotal[$row['Originator']]['Client']]+$bookedtotal;
						} else{
								$clienttotal[$monthtotal[$row['Originator']]['Client']]=$bookedtotal;
						}						
					
					
						$grandtotal=$grandtotal+$bookedtotal;
						$newtotal=$newtotal+$row['NewSlides'];
						$editstotal=$editstotal+$row['EditedSlides'];
						$hourstotal=number_format($hourstotal+$row['Hours'],2);

				}// ($bookeddata as $row) loop
				
				if(isset($grouptotal)){ $monthgroupstotal=  count($grouptotal);} else {$monthgroupstotal=0;} 
				if(isset($clienttotal)){$monthclientstotal = count($clienttotal);} else {$monthclientstotal=0;}
				if(isset($grouptotal)){$monthcontactstotal = count($monthtotal);} else{$monthcontactstotal=0;}
			}

	
			if (isset($thismonth)){
			
				################## Booked but not completed jobs ################## 
				
				unset($bookeddata );
				$bookeddata = $this->zt2016_reports_breakdown_model->_getAllOriginatorsOngoingTotalsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));

				
				if (isset($bookeddata) && !empty($bookeddata)){
	
					$bookedgrandtotal=$grandtotal;
					$bookednewtotal=$newtotal;
					$bookededitstotal=$editstotal;
					$bookedhourstotal=$hourstotal;
					
					foreach ($bookeddata as $row) {
						foreach ($row as $key => $value) {
							if (isset ($monthtotal[$row['Originator']][$key])) {
								if ($key!="Client" && $key!="Originator") {
									$monthtotal[$row['Originator']][$key]= $monthtotal[$row['Originator']][$key]+ $value;
								}
							} else {
								$monthtotal[$row['Originator']][$key]= $value;
							}
						}
						 
						$clientdata= $this->zt2016_clients_model->GetClient($options = array('CompanyName' => $row['Client']));

						$monthtotal[$row['Originator']]['ClientCode']=$clientdata->ClientCode;
						
						if ($clientdata->Group!=""){
							$monthtotal[$row['Originator']]['Group']=$clientdata->Group;
						}else{
							$monthtotal[$row['Originator']]['Group']="Other";
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
						if (isset($monthtotal[$row['Originator']]['Total'])){
							$monthtotal[$row['Originator']]['Total']=$monthtotal[$row['Originator']]['Total']+ number_format($bookedtotal,2);
						} else {
							$monthtotal[$row['Originator']]['Total']= number_format($bookedtotal,2);

						}
						
						############ group total
						if (isset($grouptotal[$monthtotal[$row['Originator']]['Group']])){
								$grouptotal[$monthtotal[$row['Originator']]['Group']]=$grouptotal[$monthtotal[$row['Originator']]['Group']]+$bookedtotal;
						} else{
								$grouptotal[$monthtotal[$row['Originator']]['Group']]=$bookedtotal;
						}
						############ client total
						if (isset($clienttotal[$monthtotal[$row['Originator']]['Client']])){
								$clienttotal[$monthtotal[$row['Originator']]['Client']]=$clienttotal[$monthtotal[$row['Originator']]['Client']]+$bookedtotal;
						} else{
								$clienttotal[$monthtotal[$row['Originator']]['Client']]=$bookedtotal;
						}						
						
						
						
						$bookedgrandtotal=$bookedgrandtotal+$bookedtotal;
						$bookednewtotal=$bookednewtotal+$row['NewSlides'];
						$bookededitstotal=$bookededitstotal+$row['EditedSlides'];
						$bookedhourstotal=number_format($bookedhourstotal+$row['Hours'],2);

						

						

					}// ($bookeddata as $row) loop
					
					$monthbookedgroupstotal=  count($grouptotal);
					$monthnookedclientstotal = count($clienttotal);
					$monthbookedcontactstotal=count($monthtotal);
				}
			}		
		
/**/
		
			//https://stackoverflow.com/questions/2359652/how-do-i-move-an-array-element-with-a-known-key-to-the-end-of-an-array-in-php
		
		
		 	if (isset($grouptotal)) { arsort($grouptotal); };
			
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
				
				## Month main figures
		
				$monthvar = date( 'm', strtotime($options['StartDate']));
				$yearvar = date( 'Y', strtotime($options['StartDate']));
				if (!isset($thismonth)){
					$daysEllapsed = cal_days_in_month(CAL_GREGORIAN, $monthvar, $yearvar);
				}
				else {
					$daysEllapsed= date( 'd', strtotime('now'));
				}
				$dailyAverage = number_format($options['grandtotal']/$daysEllapsed, 2);
				//$split="<div class=\"monthtotal\">";
		
			
				# Month totals
				$split="	<div class=\"col-sm-12\">";
					$split.="	<h3><Month Totals</h3>";
				$split.="</div>";
		
				# Hours per day, total hours, jobs
				$split.="	<div class=\"col-sm-4\">";
		
					$split.="	<h4><strong>";
					$split.=$dailyAverage."</strong>  hours billed per day";
					$split.="<small> (average ".$daysEllapsed." days)</small>" ;
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
							$split.="	<h4><strong>".$options['monthbookedcontactstotal']."</strong> booked originators";
						} else{
							$split.="	<h4><strong>".$options['monthcontactstotal']."</strong> booked originators";
						}

					$split.="	</div>";
					
					
					$split.="<div id='client-totals-div'>";
					
					$split.='<table id="breakdown-table" class="table table-striped table-condensed display compact">'."\n<thead><tr><th class=\"\" data-sortable=\"false\">Group</th><th class=\"\" data-sortable=\"false\">Client</th><th class=\"\" data-sortable=\"false\">Originator</th><th class=\"\" data-sortable=\"true\">Booked Hours</th><th class=\"\" data-sortable=\"true\">Jobs</th><th class=\"\" data-sortable=\"true\">New</th><th class=\"\" data-sortable=\"true\">Edits</th><th class=\"\" data-sortable=\"true\">Hours</th></tr></thead>";
					
					$counter=array('Jobs'=>0,'NewSlides'=>0,'EditedSlides'=>0,'Hours'=>0,'BilledHours'=>0);
					
					$tabledata="";
					foreach ($options['monthtotal'] as $row) {

											
						$SafeClientName=str_replace(" ", "_", $row['Client']);
						$SafeClientName=str_replace("&", "~", $SafeClientName);
						
						$tabledata.="<tr>";

						//$split.="<td data-order=\"".$options['grouptotal'[$row['Group']]]."\">".$row['Group']."</td>";
						
						$tabledata.="<td data-order=\"".$options['grouptotal'][$row['Group']]."\">".$row['Group']."</td>";	
						
						$tabledata.="<td data-order=\"".$options['clienttotal'][$row['Client']]."\" data-clientcode=\"".$row['ClientCode']."\"><a href=\"".site_url()."reports/zt2016_annual_client_figures/".$SafeClientName."\">".$row['Client']."</a></td>";
						

						$this->db->like("CONCAT(`FirstName`,' ',`LastName`)", $row['Originator']);
						$this->db->where('CompanyName', $row['Client']);
						$query = $this->db->get('zowtrakcontacts');
		  			    $OriginatorData= $query->row(0);
						

						
						$tabledata.="<td data-order=\"".$row['Total']."\"><a href=\"".site_url()."reports/zt2016_annual_originator_figures/".$OriginatorData->ID."\">".$row['Originator']."</a></td>";
						
						$tabledata.="<td>".$row['Total']."</td>";	
						
						$row['Jobs']=$this->zt2016_reports_breakdown_model->_NumJobsByDate($options2 = array('StartDate'=>$options['StartDate'],'EndDate'=>$options['EndDate'],'Originator'=>$row['Originator']));
						
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
					
					$split.="<tfoot><tr><th></th><th></th><th>Monthly Totals</th><th>".$counter['BilledHours']."</th><th>".number_format($counter['Jobs'])."</th><th>".number_format($counter['NewSlides'])."</th><th>".number_format($counter['EditedSlides'])."</th><th>".number_format($counter['Hours'],2)."</th></tr></tfoot>";	
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