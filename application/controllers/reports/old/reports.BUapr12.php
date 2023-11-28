<?php

class Reports extends MY_Controller {


	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 

		$this->load->helper(array('form','url','reports','userpermissions'));

		_superuseronly(); 

		$this->load->model('trakreports', '', TRUE);
		$this->load->model('trakclients', '', TRUE);
		$this->load->model('trakclients', '', TRUE);
		$ClientList= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));

	 	$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);

		//Get day 15 of current month

			 $templateVars['pageOutput'].=$this->_getreportspage($ClientList);

 		$now = strtotime(date('Y-m-15'));
		$reportresults="";
		for ($i = 0; $i <=5; $i++) {
		
			$StartDate = date( 'Y-m-1', strtotime('-'.$i.' month',$now ));
			$EndDate = date( 'Y-m-t', strtotime('-'.$i.' month',$now ));
			$reportresults .=$this-> _monthData($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
			//$templateVars['pageOutput'] =$this-> _monthData($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
		}	
			$templateVars['pageOutput'].= $reportresults;
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Reports";
		$templateVars['pageType'] = "reports";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		

		
			if ($this->input->is_ajax_request()) {
				echo $reportresults;
			}
			else {
				$this->load->vars($templateVars);		
				$this->load->view('zowtrak2012template');
			}				 
	}
	// ################## main page ##################	
	function  _getreportspage($ClientList)
	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>Reports</h1>";
			$entries .=_getClientReportDropDown($ClientList);

			//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";

			
			return $entries;

	}
	

		// ################## calculations ##################	

	function  _monthData($options=array())
	{
			//Extract month data
			$StartDate=$options['StartDate'];
			$EndDate=$options['EndDate'];
			//$WorkType=$options['WorkType'];
			unset ($options);	
					
			if (date( 'M Y', strtotime($StartDate))==date( 'M Y', strtotime('now'))){
				$thismonth=1;
			}
			 $monthtotal=array();
			 $grandtotal=0;
			 $newtotal=0;
			 $editstotal=0;
			 $hourstotal=0;
			 if (isset(	$thismonth)){
				 $bookedgrandtotal=0;
				 $bookednewtotal=0;
				 $bookededitstotal=0;
				 $bookedhourstotal=0;
				 $ellapsedtotal=0;
			}

			//Billed data
			$bookeddata = $this->trakreports->_getAllClientBilledTotalsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
			
			if (isset($bookeddata)){
				foreach ($bookeddata as $row) {
						foreach ($row as $key => $value) {
							$monthtotal[$row['Client']][$key]= $value;
						}
						$bookedtotal=number_format($row['InvoiceTime'],1);
						$monthtotal[$row['Client']]['Total']= number_format($bookedtotal,1);
	
						$grandtotal=$grandtotal+$bookedtotal;
						$newtotal=$newtotal+$row['NewSlides'];
						$editstotal=$editstotal+$row['EditedSlides'];
						$hourstotal=$hourstotal+$row['Hours'];

				}

			}
			
			//Complete but not billed data
			unset($bookeddata );
			$bookeddata = $this->trakreports->_getAllClientCompletedTotalsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
			
			if (isset($bookeddata)){
				foreach ($bookeddata as $row) {
						foreach ($row as $key => $value) {
							$monthtotal[$row['Client']][$key]= $value;
						}
						$clientdata= $this->trakclients->GetEntry($options = array('CompanyName' => $row['Client']));
						//Apply edit price
						$subtotalbooked= $row['EditedSlides']*$clientdata->PriceEdits;
						//Add slides and divide by slides per hour
						$subtotalbooked= $row['NewSlides']+$subtotalbooked;
						$subtotalbooked= $subtotalbooked/5;
						//Add hours to get the total
						$bookedtotal= number_format ($subtotalbooked+$row['Hours'],1);						
						
						if (isset($monthtotal[$row['Client']]['Total'])){
							$monthtotal[$row['Client']]['Total']=$monthtotal[$row['Client']]['Total']+ number_format($bookedtotal,1);
						} else {
							$monthtotal[$row['Client']]['Total']= number_format($bookedtotal,1);
						
						}
						$grandtotal=$grandtotal+$bookedtotal;
						$newtotal=$newtotal+$row['NewSlides'];
						$editstotal=$editstotal+$row['EditedSlides'];
						$hourstotal=$hourstotal+$row['Hours'];

				}// ($bookeddata as $row) loop

			}
			
			if (isset($thismonth)){
			//Booked not completed
				unset($bookeddata );
				$bookeddata = $this->trakreports->_getAllClientEllapsedTotalsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
				
				if (isset($bookeddata)){
	
					$bookedgrandtotal=$grandtotal;
					$bookednewtotal=$newtotal;
					$bookededitstotal=$editstotal;
					$bookedhourstotal=$hourstotal;
					foreach ($bookeddata as $row) {
							foreach ($row as $key => $value) {
								$monthtotal[$row['Client']][$key]= $value;
							}

							$clientdata= $this->trakclients->GetEntry($options = array('CompanyName' => $row['Client']));
							//Apply edit price
							$subtotalbooked= $row['EditedSlides']*$clientdata->PriceEdits;
							//Add slides and divide by slides per hour
							$subtotalbooked= $row['NewSlides']+$subtotalbooked;
							$subtotalbooked= $subtotalbooked/5;
							//Add hours to get the total
							$bookedtotal= number_format ($subtotalbooked+$row['Hours'],1);						
							
							if (isset($monthtotal[$row['Client']]['Total'])){
								$monthtotal[$row['Client']]['Total']=$monthtotal[$row['Client']]['Total']+ number_format($bookedtotal,1);
							} else {
								$monthtotal[$row['Client']]['Total']= number_format($bookedtotal,1);
							
							}
							$bookedgrandtotal=$bookedgrandtotal+$bookedtotal;
						$bookednewtotal=$bookednewtotal+$row['NewSlides'];
						$bookededitstotal=$bookededitstotal+$row['EditedSlides'];
						$bookedhourstotal=$bookedhourstotal+$row['Hours'];

					}// ($bookeddata as $row) loop
	
				}/**/
			}
			 	if (!isset($thismonth)){
					$monthdata=$this->_buildMonthTables($options2=array('StartDate'=>$StartDate,'EndDate'=>$EndDate, 'monthtotal'=>$monthtotal,'grandtotal'=>$grandtotal,'newtotal'=>$newtotal,'editstotal'=>$editstotal,'hourstotal'=>$hourstotal));
				}
				else {
					$monthdata=$this->_buildMonthTables($options2=array('StartDate'=>$StartDate,'EndDate'=>$EndDate, 'monthtotal'=>$monthtotal,'grandtotal'=>$grandtotal,'newtotal'=>$newtotal,'editstotal'=>$editstotal,'hourstotal'=>$hourstotal,'bookedgrandtotal'=>$bookedgrandtotal,'bookednewtotal'=>$bookednewtotal,'bookededitstotal'=>$bookededitstotal,'bookedhourstotal'=>$bookedhourstotal));
					//}
				}

				if (!isset($monthdata)) {
					$monthdata="<div class=\"monthtotal\"><h3>".date( 'M Y', strtotime($StartDate))." - No entries. <h3></div>";
				}

		 return $monthdata;
	}
			
	function _buildMonthTables($options=array())
	{
				if (date( 'M Y', strtotime($options['StartDate']))==date( 'M Y', strtotime('now'))){
					$thismonth=1;
					$numjobsbooked=$this->trakreports->_NumJobsByDate($options2=array('StartDate'=>$options['StartDate'],'EndDate'=>$options['EndDate']));
					$options['EndDate'] = date( 'Y-m-d', strtotime('now' ));
				}
			
				$numjobs=$this->trakreports->_NumJobsByDate($options2=array('StartDate'=>$options['StartDate'],'EndDate'=>$options['EndDate']));
				$monthvar = date( 'm', strtotime($options['StartDate']));
				$yearvar = date( 'Y', strtotime($options['StartDate']));
				if (!isset($thismonth)){
					$daysEllapsed = cal_days_in_month(CAL_GREGORIAN, $monthvar, $yearvar);
				}
				else {
					$daysEllapsed= date( 'd', strtotime('now'));
				}
				$dailyAverage = number_format($options['grandtotal']/$daysEllapsed, 2);
				$split="<div class=\"monthtotal\">";
				$split.="<h3>".Date('F Y', strtotime($options['StartDate']))." - ".number_format($options['grandtotal'],1)." Hours";
				if (date( 'M Y', strtotime($options['StartDate']))==date( 'M Y', strtotime('now'))){
					$split.=" (Booked:".number_format($options['bookedgrandtotal'],1).")";
				}
				$split.="</h3>";	
				$split.="<p>".$numjobs." jobs ";
				if (isset($numjobsbooked)){
					$split.=" ( Booked: ".$numjobsbooked." ) ";
				}
				
				$split.="| ".$dailyAverage." average hours billed per day (average last ".$daysEllapsed." days) </p>";	
				$split.="<p>".$options['newtotal']." new | ";
				$split.=$options['editstotal']." edits | ";	
				$split.=$options['hourstotal']." hours";
				if (isset($thismonth)){
					$split.=" (<em> booked ".$options['bookednewtotal']." new | ";
					$split.=$options['bookededitstotal']." edits | ";	
					$split.=$options['bookedhourstotal']." hours </em>)";
				
				}
				$split.="</p>";	
				$split.="<table>\n<thead><tr><th class=\"header\">Client</th><th class=\"header\">Total</th><th class=\"header\">New</th><th class=\"header\">Edits</th><th class=\"header\">Hours</th></tr></thead>";
				foreach ($options['monthtotal'] as $row) {
					$split.="<tr><th scope='row'>".$row['Client']."</th>";
					$split.="<td>".$row['Total']."</td>";
					$split.="<td>".$row['NewSlides']."</td>";
					$split.="<td>".$row['EditedSlides']."</td>";
					$split.="<td>".$row['Hours']."</td></tr>\n";			
				}

			
				$split.="</table>\n";
				$split.="</div>";
			
			return $split;
	}




	

}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>