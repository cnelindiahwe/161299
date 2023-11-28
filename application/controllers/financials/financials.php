<?php

class Financials extends MY_Controller {


	function index()
	{
	  
// 		ini_set('display_startup_errors', 1);
// ini_set('display_errors', 1);
// error_reporting(-1);
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('userpermissions','financialsnew','form','reports'));
		$templateVars['ZOWuser']=_superuseronly(); 
		
		$this->load->helper(array());

		$this->load->model('trakreports', '', TRUE);
		$this->load->model('trakclients', '', TRUE);
		
		$query = $this->db->select("*");
		$query = $this->db->where('MONTH(DateIn)', date('m')); //For current month
		$query = $this->db->where('YEAR(DateIn)', date('Y'));
        $this->db->from('zowtrakentries');
        // $query=$this->db->limit(1,0);
        // $query=$this->db->order_by('id','desc');
        $query=$this->db->get();
			// print_r($query->result());
			// die;

		foreach($query->result() as $key=>$val){

			// print_r($val);
			// echo $val->WorkedBy;
			// if(is_string($val->TentativeBy) == 1){
			// 	$update_name = $this->db->select("user_id");
			// 	$update_name = $this->db->where("fname LIKE '%".$val->TentativeBy."%'");
			// 	$update_name = $this->db->from('users');
			// 	$querupdate_namey=$this->db->limit(1);
			// 	$update_name=$this->db->get();
			// 	$user_id =  $update_name->row(0)->user_id;
				
			// 	if(!empty($user_id )){
			// 		$data=array(
			// 			'TentativeBy'=>$user_id
			// 		);
			// 		$this->db->where('id', $val->id);
			// 		$this->db->update('zowtrakentries', $data);
			// 	}
				
			// }
			// if(is_numeric($val->CompletedBy)){
				
			// 	$update_name = $this->db->select("fname");
			// 	$update_name = $this->db->where('user_id',$val->CompletedBy);
			// 	$update_name = $this->db->from('users');
			// 	$update_name=$this->db->get();
			// 	$user_id =  ucfirst($update_name->row(0)->fname);
			// if(!empty($user_id )){
			// 	$data=array(
			// 		'CompletedBy'=>$user_id
			// 	);
			// 	$this->db->where('id', $val->id);
			// 	$this->db->update('zowtrakentries', $data);
			// }
			// }
			// if(is_string($val->WorkedBy) == 1){
			// 	// echo $val->WorkedBy.'<br>';
			// 	$update_name = $this->db->select("user_id");
			// 	$update_name =$this->db->where("fname LIKE '%".$val->WorkedBy."%'");
			// 	$update_name = $this->db->from('users');
			// 	$querupdate_namey=$this->db->limit(1);
			// 	$update_name=$this->db->get();
			// 	$user_id =  $update_name->row(0)->user_id;
			// if(!empty($user_id )){
			// 	$data=array(
			// 		'WorkedBy'=>$user_id
			// 	);
			// 	$this->db->where('id', $val->id);
			// 	$this->db->update('zowtrakentries', $data);
			// }
			// }
			// if(is_string($val->ProofedBy) == 1){
			// 	$update_name = $this->db->select("user_id");
			// 	$update_name =$this->db->where("fname LIKE '%".$val->ProofedBy."%'");
			// 	$update_name = $this->db->from('users');
			// 	$querupdate_namey=$this->db->limit(1);
			// 	$update_name=$this->db->get();
			// 	$user_id =  $update_name->row(0)->user_id;
			// if(!empty($user_id )){
			// 	$data=array(
			// 		'ProofedBy'=>$user_id
			// 	);
			// 	$this->db->where('id', $val->id);
			// 	$this->db->update('zowtrakentries', $data);
			// }
			// }
			// if(is_string($val->CompletedBy) == 1){
			// 	$update_name = $this->db->select("user_id");
			// 	$update_name =$this->db->where("fname LIKE '%".$val->CompletedBy."%'");
			// 	$update_name = $this->db->from('users');
			// 	$querupdate_namey=$this->db->limit(1);
			// 	$update_name=$this->db->get();
			// 	$user_id =  $update_name->row(0)->user_id;
			// if(!empty($user_id )){
			// 	$data=array(
			// 		'CompletedBy'=>$user_id
			// 	);
			// 	$this->db->where('id', $val->id);
			// 	$this->db->update('zowtrakentries', $data);
			// }
			// }
			// if(is_string($val->BilledBy) == 1){
			// 	$update_name = $this->db->select("user_id");
			// 	$update_name =$this->db->where("fname LIKE '%".$val->BilledBy."%'");
			// 	$update_name = $this->db->from('users');
			// 	$querupdate_namey=$this->db->limit(1);
			// 	$update_name=$this->db->get();
			// 	$user_id =  $update_name->row(0)->user_id;
			// if(!empty($user_id )){
			// 	$data=array(
			// 		'BilledBy'=>$user_id
			// 	);
			// 	$this->db->where('id', $val->id);
			// 	$this->db->update('zowtrakentries', $data);
			// }
			// }
			// if(is_string($val->PaidBy) == 1){
			// 	$update_name = $this->db->select("user_id");
			// 	$update_name =$this->db->where("fname LIKE '%".$val->PaidBy."%'");
			// 	$update_name = $this->db->from('users');
			// 	$querupdate_namey=$this->db->limit(1);
			// 	$update_name=$this->db->get();
			// 	$user_id =  $update_name->row(0)->user_id;
			// if(!empty($user_id )){
			// 	$data=array(
			// 		'PaidBy'=>$user_id
			// 	);
			// 	$this->db->where('id', $val->id);
			// 	$this->db->update('zowtrakentries', $data);
			// }
			// }
		
		}

		// die;
		if ( isset($_POST['financialsdate']))
		{
			$currentmonth =$_POST['financialsdate'];
			$now =date( 'Y-m-15', strtotime($currentmonth));		
			$StartDate = date( 'Y-m-1', strtotime($currentmonth));
			$EndDate = date( 'Y-m-t', strtotime($currentmonth));
		}
		else
		{
			//$now = strtotime(date('Y-m-15'));
			$now =date( 'Y-m-15', strtotime('now'));		
			$StartDate = date( 'Y-m-1', strtotime('now'));
			$EndDate = date( 'Y-m-t', strtotime('now'));
		}		
		//Month totals

		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		
		$Worktype="";

		$templateVars['pageOutput'] .= $this->_gettopmenu($StartDate,$Worktype);


		  $monthtotals = $this->_calculateMonthPrices($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
		$templateVars['pageOutput'] .= _MonthPricestable($monthtotals,$StartDate);
		
		// splits
		$ClientList= $this->trakreports->ClientsByDate($options =  array('StartDate'=> $StartDate,'EndDate'=> $EndDate,'Booked'=>1,'SortBy'=> 'Client', 'sortDirection'=> 'Desc'));

		$templateVars['pageOutput'].= $this->_monthSplitnew($StartDate,$EndDate,$ClientList,$monthtotals);

		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "financials";
		$templateVars['pageType'] = "financials";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}
	// ################## top ##################	

	function  _gettopmenu($StartDate,$Worktype)

	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$entries .="<h1>Financials - Monthly Splits</h1>";
			//$entries .=$this->_getWorktypeDropDown($Worktype);
			$entries .=$this->_dateform($StartDate);

			//Add trends button
			$entries .="<a href=\"".site_url()."financials/fin_trends\">Trends</a>";		
			//Add totals button

			$entries .="<a href=\"".site_url()."financials/fin_totals\" >Totals</a>";
			//Add breakdown button
			$entries .="<a href=\"".site_url()."financials/fin_breakdown\">Breakdown</a>";
			//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			$entries .="</div>";
		
			
			return $entries;

	}


	
// ------------------------------------------------------------------------

/**
 * _calculateMonthPrices
 *
 * Provides monthly prices per client
 *
 * @access	public
 * @return	string
 */
 
 	function _calculateMonthPrices($options=array()){
			$StartDate=$options['StartDate'];
			$EndDate=$options['EndDate'];
			unset ($options);
			$bookeddata = $this->trakreports->_getAllClientTotalsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate,'Booked'=>2));
			if (isset($bookeddata)){
				foreach ($bookeddata as $row) {
					//$clientdata= $this->trakclients->GetEntry($options = array('CompanyName' => $row->Client));
						
						foreach ($row as $key => $value) {
							$monthtotal[$row['Client']][$key]= $value;
						}
						$clientdata= $this->trakclients->GetEntry($options2 = array('CompanyName' => $row['Client']));
						//Apply edit price
						$subtotalbooked= $row['EditedSlides']*$clientdata->PriceEdits;
						//Add slides and divide by slides per hour
						$subtotalbooked= $row['NewSlides']+$subtotalbooked;
						$subtotalbooked= $subtotalbooked/5;
						//Add hours to get the total
						$bookedtotal= number_format( $subtotalbooked+$row['Hours'],2);
						$monthtotal[$row['Client']]['Total']= $bookedtotal;
						$monthtotal[$row['Client']]['Price']= _fetchClientMonthPrice($clientdata,$bookedtotal);
						$monthtotal[$row['Client']]['Ammount']= $monthtotal[$row['Client']]['Total']*	$monthtotal[$row['Client']]['Price'];
						$monthtotal[$row['Client']]['Currency']=$clientdata->Currency;
					
				}
			}
			$bookeddata = $this->trakreports->_getAllClientBilledTotalsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
			
			if (isset($bookeddata)){
				foreach ($bookeddata as $row) {
							$this->db->where('Client', $row['Client']);
							$this->db->where('DateOut >=', $options['StartDate']);
							$this->db->where('DateOut <= ', $options['EndDate']);
							$this->db->where('Trash =',0);
							$this->db->where('Invoice !=','NOT BILLED');
							$this->db->limit(1);
							$this->db->from('zowtrakentries');
							$invoicedata=$this->db->get();
							$invoiceprice=$invoicedata->row()->InvoicePrice;
							
						if (isset($monthtotal[$row['Client']])){
							
							$monthtotal[$row['Client']]['Total']= number_format($monthtotal[$row['Client']]['Total']+$row['InvoiceTime'],2);
							$monthtotal[$row['Client']]['Ammount']= $monthtotal[$row['Client']]['Ammount']+$row['InvoiceEntryTotal'];
							$monthtotal[$row['Client']]['Price']= $monthtotal[$row['Client']]['Price']." / ".$invoiceprice;
						}
						else {
							$monthtotal[$row['Client']]['Price']= 0;
							foreach ($row as $key => $value) {
								$monthtotal[$row['Client']][$key]= $value;
							}
							$clientdata= $this->trakclients->GetEntry($options2 = array('CompanyName' => $row['Client']));
							$monthtotal[$row['Client']]['Total']= number_format($row['InvoiceTime'],2);
							$monthtotal[$row['Client']]['Ammount']= $row['InvoiceEntryTotal'];							
							$monthtotal[$row['Client']]['Price']= $invoiceprice;
							$monthtotal[$row['Client']]['Currency']=$clientdata->Currency;
						}
					}

			}


		if (!isset($monthtotal)) {$monthtotal="";	}
		 return $monthtotal;

	}
	


// ------------------------------------------------------------------------

/**
 * _monthSplitNew
 *
 * Provides monthly splits per client
 *
 * @access	public
 * @return	string
 */
	function  _monthSplitNew($StartDate,$EndDate,$ClientList,$monthtotals)
	{
		
// 	   error_reporting(E_ALL);
// ini_set('display_errors', '1');

			if ($monthtotals=="") {
				$output="";
				return $output;

			}
			
			
			$AllTotals=array();
			$SummaryTotals=array();
			$split="";
			
			
			$StatusType=array('ScheduledBy','WorkedBy','ProofedBy','CompletedBy'); 
			foreach($StatusType as $Status) {
				
				foreach ($ClientList as $clientb) {
					// if($clientb->Clinnt == 'Philips CSA' ){
					$t=0;
					$clientdata= $this->trakclients->GetEntry($options = array('CompanyName' => $clientb->Client));
					//non-billed data
					
					if($t == 1){
						$Output = $this->trakreports->_getClientTotalsByDate($StartDate,$EndDate,$clientb,$Status,2);

					}else{
						$Output = $this->trakreports->_getClientTotalsByDate_new($StartDate,$EndDate,$clientb,$Status,2);
					}
					if($clientb->Client == 'Johnson and Johnson China' && $Status == 'WorkedBy'){
					// print_r($Output);
					}
					// echo $clientb->Client;
					// echo "<br>";
					// print_r($Output);
					$row = '';
					foreach ($Output as $row) {
						$this->load->model('zt2016_users_model');
						// $name= $this->zt2016_users_model->getsuer_name_by_id($row[$Status]);
					
						if($t == 1){
							$num = (int) $row[$Status];

						}else{
							if($Status != 'WorkedBy'){
								$num = $row[$Status];

							}else{
								$num = $row->$Status;

							}

						}
						if (  is_numeric($num) && $num !=0) {
							$name= $this->zt2016_users_model->getsuer_name_by_id($num);
							$name = $name->fname;
						}else{
							if($Status != 'WorkedBy'){
								// $name= $this->zt2016_users_model->getsuer_name_by_string($row[$Status]);
								$name= $row[$Status];
							}else{
							//  $name= $this->zt2016_users_model->getsuer_name_by_string($row->$Status);
							$name= $row->$Status;
							}
							// $name= $this->zt2016_users_model->getsuer_name_by_string($row->$Status);
						}
						//echo $name->fname;
						//Apply edit price
						if($t == 1){
								//Apply edit price
								$subtotalbooked= $row['EditedSlides']*$clientdata->PriceEdits;
								//Add slides and divide by slides per hour
								$subtotalbooked= $row['NewSlides']+$subtotalbooked;
								$subtotalbooked= $subtotalbooked/5;
								//Add hours to get the total
								$bookedtotal= $subtotalbooked+$row['Hours'];
								$AllTotals[$clientb->Client][$Status][ucfirst($name)]['cashtotal']=$bookedtotal*$monthtotals[$clientb->Client]['Price'];
								$AllTotals[$clientb->Client][$Status][ucfirst($name)]['total']=$bookedtotal;
								$AllTotals[$clientb->Client][$Status][ucfirst($name)]['span']="(".$row['NewSlides']." N, ".$row['EditedSlides']." E, ".$row['Hours']." H)";
							

						}else{
							if($Status == 'WorkedBy'){
								$row_edit_hwe_less = $row->EditedSlides_2_main + $row->EditedSlides_3_main;
								$row_edit_hwe = ($row->EditedSlides + $row->EditedSlides_2 + $row->EditedSlides_3)-$row_edit_hwe_less;
								$subtotalbooked= $row_edit_hwe*$clientdata->PriceEdits;
								//Add slides and divide by slides per hour
								$row_new_hwe_less = $row->NewSlides_2_main + $row->NewSlides_3_main;
								$row_new_hwe = ($row->NewSlides + $row->NewSlides_2 + $row->NewSlides_3)-$row_new_hwe_less;
								$row_Hours_hwe_less = $row->Hours_2_main + $row->Hours_3_main;

								$row_Hours_hwe = ($row->Hours + $row->Hours_2 + $row->Hours_3)-$row_Hours_hwe_less;
								$subtotalbooked= $row_new_hwe+$subtotalbooked;
								$subtotalbooked= $subtotalbooked/5;
								$bookedtotal= $subtotalbooked+$row_Hours_hwe;
								$AllTotals[$clientb->Client][$Status][ucfirst($name)]['cashtotal']=$bookedtotal*$monthtotals[$clientb->Client]['Price'];
								$AllTotals[$clientb->Client][$Status][ucfirst($name)]['total']=$bookedtotal;
								$AllTotals[$clientb->Client][$Status][ucfirst($name)]['span']="(".$row_new_hwe." N, ".$row_edit_hwe." E, ".$row_Hours_hwe." H)";
								
							}else{
								$subtotalbooked= $row['EditedSlides']*$clientdata->PriceEdits;
								//Add slides and divide by slides per hour
								$subtotalbooked= $row['NewSlides']+$subtotalbooked;
								$subtotalbooked= $subtotalbooked/5;
								//Add hours to get the total
								$bookedtotal= $subtotalbooked+$row['Hours'];
								$AllTotals[$clientb->Client][$Status][ucfirst($name)]['cashtotal']=$bookedtotal*$monthtotals[$clientb->Client]['Price'];
								$AllTotals[$clientb->Client][$Status][ucfirst($name)]['total']=$bookedtotal;
								$AllTotals[$clientb->Client][$Status][ucfirst($name)]['span']="(".$row['NewSlides']." N, ".$row['EditedSlides']." E, ".$row['Hours']." H)";
							
							}


						}
				
					}
					
					//billed data
					$this->db->select($Status);
					$this->db->select_sum('InvoiceEntryTotal','PartnerTotal');
					$this->db->select_sum('InvoiceTime','InvoiceTime');
					$this->db->select_sum('Hours','BilledHours');
					$this->db->select_sum('NewSlides','BilledNewSlides');
					$this->db->select_sum('EditedSlides','BilledEditedSlides');
					$this->db->where('Client', $clientb->Client);
					$this->db->where('DateOut >=', $StartDate);
					$this->db->where('DateOut <= ', $EndDate);
					$this->db->where('Trash =',0);
					$this->db->where("Invoice != 'NOT BILLED'");
					$this->db->group_by($Status);
					$billedquery=$this->db->get('zowtrakentries');
					$billed=$billedquery->result_array();
					// print_r($billed);
					// if($Status == 'ProofedBy'){
					// 	echo $Status;
					// 	print_r($row);
					// }
					// echo "<pre>";
					foreach ($billed as $partner) {
						// print_r($AllTotals[$clientb->Client][$Status]);
						
						if (!isset($AllTotals[$clientb->Client][$Status][$partner[$Status]]['total'])) {
							// print_r($billed);
							$billed_user_id = $partner[$Status];
							if (  is_numeric($billed_user_id) && $billed_user_id !=0) {
								$name= $this->zt2016_users_model->getsuer_name_by_id($billed_user_id);
								$name = $name->fname;
							}else{
								if($Status != 'WorkedBy'){
									// $name= $this->zt2016_users_model->getsuer_name_by_string($row[$Status]);
									 $name= $partner[$Status];
								}else{
								//  $name= $this->zt2016_users_model->getsuer_name_by_string($row->$Status);
								 $name= $partner[$Status];
								}
								// $name= $this->zt2016_users_model->getsuer_name_by_string($row->$Status);
							}
							$AllTotals[$clientb->Client][$Status][$name]['cashtotal']=($AllTotals[$clientb->Client][$Status][$name]['cashtotal']+$partner['PartnerTotal']);
							// $AllTotals[$clientb->Client][$Status][$name]['total']=number_format($partner['InvoiceTime'],1);
							// $AllTotals[$clientb->Client][$Status][$name]['span']="(".($row['NewSlides']+$partner['BilledNewSlides'])." N, ".$partner['BilledEditedSlides']." E, ".$partner['BilledHours']." H)";
							if($Status == 'WorkedBy'){
								$AllTotals[$clientb->Client][$Status][$name]['total']=$AllTotals[$clientb->Client][$Status][$row->$Status]['total']+number_format($partner['InvoiceTime'],1);
								 $AllTotals[$clientb->Client][$Status][$name]['span']="(".($row->NewSlides+$partner['BilledNewSlides'])." N, ".($row->EditedSlides+$partner['BilledEditedSlides'])." E, ".($row->Hours+$partner['BilledHours'])." H)";
							 }else{
							 //   print_r();
							    	$AllTotals[$clientb->Client][$Status][$name]['total']=$AllTotals[$clientb->Client][$Status][$row->$Status]['total']+number_format($partner['InvoiceTime'],1);
								 $AllTotals[$clientb->Client][$Status][$name]['span']="(".($row->NewSlides+$partner['BilledNewSlides'])." N, ".($row->EditedSlides+$partner['BilledEditedSlides'])." E, ".($row->Hours+$partner['BilledHours'])." H)";
						
								//  $AllTotals[$clientb->Client][$Status][$name]['total']=$AllTotals[$clientb->Client][$Status][$row[$Status]]['total']+number_format($partner['InvoiceTime'],1);
							 //$AllTotals[$clientb->Client][$Status][$name]['span']="(".($row['NewSlides']+$partner['BilledNewSlides'])." N, ".($row['EditedSlides']+$partner['BilledEditedSlides'])." E, ".($row['Hours']+$partner['BilledHours'])." H)";
							 }
						}
						else
						{
							
								// print_r($row);
							$AllTotals[$clientb->Client][$Status][$name]['cashtotal']=$AllTotals[$clientb->Client][$Status][$name]['cashtotal']+$partner['PartnerTotal'];

						    if($Status == 'WorkedBy'){
						       $AllTotals[$clientb->Client][$Status][$name]['total']=$AllTotals[$clientb->Client][$Status][$row->$Status]['total']+number_format($partner['InvoiceTime'],1);
							    $AllTotals[$clientb->Client][$Status][$name]['span']="(".($row->NewSlides+$partner['BilledNewSlides'])." N, ".($row->EditedSlides+$partner['BilledEditedSlides'])." E, ".($row->Hours+$partner['BilledHours'])." H)";
						    }else{

								// print_r($row);
						        $AllTotals[$clientb->Client][$Status][$name]['total']=$AllTotals[$clientb->Client][$Status][$row[$Status]]['total']+number_format($partner['InvoiceTime'],1);
								$AllTotals[$clientb->Client][$Status][$name]['span']="(".($row['NewSlides']+$partner['BilledNewSlides'])." N, ".($row['EditedSlides']+$partner['BilledEditedSlides'])." E, ".($row['Hours']+$partner['BilledHours'])." H)";
						    }
							}	
					}
							
				// }

				}									
				
		}
		$array=$this->trakreports-> _getPartnersByDate($StartDate,$EndDate);
		

		$i = 0;
		$Partners =array();
		foreach ($array as $row) {
			//$CI = get_instance();
			// You may need to load the model if it hasn't been pre-loaded
			$this->load->model('zt2016_users_model');
			$num = (int) $row['ScheduledBy'];
							if (  is_numeric($num) && $num !=0) {
								$name= $this->zt2016_users_model->getsuer_name_by_id($num);
								
							}else{
								$name= $this->zt2016_users_model->getsuer_name_by_string($row['ScheduledBy']);
							}
				// 			if($i ==12){
				// 			echo $row['ScheduledBy'];

				// 				print_r($name);
				// 			}

							if(!in_array(ucfirst($name->fname),$Partners)){
								$Partners[$i]=rtrim(ucfirst($name->fname),' ');
								$i++;
							}
			
		} 

		
		if (!in_array("Miguel",$Partners)){
			$Partners[$i]="Miguel";
		}
		
//   print_r($AllTotals);
		
		$split.=_buildSplitTables($AllTotals,$StartDate,$EndDate,$Status,$ClientList,$monthtotals,$Partners);
		return $split;
	}

// ------------------------------------------------------------------------

/**
 * _monthSplit
 *
 * Provides monthly splits per client
 *
 * @access	public
 * @return	string
 */
	function  _monthSplit($StartDate,$EndDate,$ClientList,$monthtotals)
	{
			if ($monthtotals=="") {
				$output="";
				return $output;
			}
			
			
			$AllTotals=array();
			$SummaryTotals=array();
			$split="";
			
			
			$StatusType=array('ScheduledBy','WorkedBy','ProofedBy','CompletedBy'); 
			foreach($StatusType as $Status) {

				foreach ($ClientList as $clientb) {
					$clientdata= $this->trakclients->GetEntry($options = array('CompanyName' => $clientb->Client));
					$Output = $this->trakreports->_getClientTotalsByDate($StartDate,$EndDate,$clientb,$Status,1);
					foreach ($Output as $row) {
						//Apply edit price
						$subtotalbooked= $row['EditedSlides']*$clientdata->PriceEdits;
						//Add slides and divide by slides per hour
						$subtotalbooked= $row['NewSlides']+$subtotalbooked;
						$subtotalbooked= $subtotalbooked/5;
						//Add hours to get the total
						$bookedtotal= $subtotalbooked+$row['Hours'];

						$AllTotals[$clientb->Client][$Status][$row[$Status]]['total']=$bookedtotal;
						$AllTotals[$clientb->Client][$Status][$row[$Status]]['span']="(".$row['NewSlides']." N, ".$row['EditedSlides']." E, ".$row['Hours']." H)";
					}
				}
				
				
		}
		$array=$this->trakreports-> _getPartnersByDate($StartDate,$EndDate);
		$i = 0;
		foreach ($array as $row) {
			$Partners[$i]=$row['ScheduledBy'];
			$i++;
			 
		} 
		$split.=_buildSplitTables($AllTotals,$StartDate,$EndDate,$Status,$ClientList,$monthtotals,$Partners);
		return $split;
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
	
	// echo $query->row(0)->DateOut;
	
	 $initial=date( 'M Y', strtotime($query->row(0)->DateOut));
	
	$selecteddate =date( 'M Y', strtotime($StartDate));
	
	 $EndDate = date( 'M Y', strtotime('now'));
	$i=0;
	
	do {
		$i++;
		$running =date( 'M Y', strtotime($initial.'+'.$i.'months'));
		$options[$running]=$running;
		// echo $running."<br/>";;
		// die;
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


// ------------------------------------------------------------------------

/**
 * _getWorktypeDropDown
 *
 * Creates worktype selector dropdown
 *
 * @access	public
 * @return	string
 */


	function  _getWorktypeDropDown($Worktype="")
	{
		$attributes['id'] = 'worktypecontrol';
			$WorktypeDropDown= form_open(site_url()."financials",$attributes);

			
			//dropdown
				$options=array(''=>"All",'Office'=>"Office",'Non-Office'=>"Non-Office");
				$more = 'id="WorkType" class="WorkType"';
				$WorktypeDropDown .=form_label('Work type:','WorkType');
				$WorktypeDropDown .=form_dropdown('WorkType', $options,$Worktype,$more);
				$more = 'id="worktypesubmit" class="worktypesubmit"';			
				$WorktypeDropDown .=form_submit('worktypesubmit', 'View',$more);
				$WorktypeDropDown .= form_close()."\n";

		return $WorktypeDropDown;	


	}

/* End of file financials.php */
/* Location: ./system/application/controllers/billing/financials.php */
}

?>