<?php

class Staffreport extends MY_Controller {

	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('zowtrakui','form','url','reports','invoice','userpermissions'));
		$this->load->library('table');
		$this->load->model('trakclients', '', TRUE);
		$this->load->model('zt2016_users_model', '', TRUE);
		
		if ( isset($_POST['reportdate']))
		{
			$currentmonth =$_POST['reportdate'];
			$now =date( 'Y-m-15', strtotime($currentmonth));		
			$StartDate = date( 'Y-m-1', strtotime($currentmonth));
			$EndDate = date( 'Y-m-t', strtotime($currentmonth));
		}
		else
		{
			$now =date( 'Y-m-15', strtotime('now'));		
			$StartDate = date( 'Y-m-1', strtotime('now'));
			$EndDate = date( 'Y-m-t', strtotime('now'));
		}	
		
		$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['ZOWuser_id']=_getCurrentUser_id();
		if ( isset($_POST['staffmember']))
		{
			$staffmember_name =$this->zt2016_users_model->getsuer_name_by_id($_POST['staffmember'])->fname;

			$staffmember_id =$_POST['staffmember'];
		}
		else
		{
			$stafftemp=explode(".",$templateVars['ZOWuser']);
			if (strtolower($stafftemp[0])=="sunil" && strtolower($stafftemp[1])=="poojari") {
					$staffmember=strtolower($stafftemp[1]);	
					$staffmember_name =$stafftemp[1];
					$staffmember_id =$templateVars['ZOWuser_id'];
			}
			else {
				$staffmember=strtolower($stafftemp[0]);
				$staffmember_name =$stafftemp[0];
					$staffmember_id =$templateVars['ZOWuser_id'];
			}
			
			
		}		
		
		$ClientList= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		$templateVars['pageOutput'] .=$this->_getTopBar($StartDate,$staffmember_id,$templateVars);
		$templateVars['pageOutput'] .=$this->_getstaffsplit($options=array('staffmember_id'=>$staffmember_id,'staffmember_name'=>$staffmember_name,'StartDate'=>$StartDate,'EndDate'=>$EndDate));
		$templateVars['pageOutput'] .=$this->_getstafforiginators($options=array('staffmember_id'=>$staffmember_id,'staffmember_name'=>$staffmember_name,'StartDate'=>$StartDate,'EndDate'=>$EndDate));
		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Staff Report";
		$templateVars['pageType'] = "staffreport";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
		$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}


	// ################## top menu ##################	
	function  _getTopBar($StartDate,$staffmember, $user)
	{
			$TopBar ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$TopBar .="<h1>Staff Report - ";
			if ($user['ZOWuser_id']== 38 || $user['ZOWuser_id'] == 3) {
				$TopBar .="</h1>";
			} else {
				$TopBar .=ucfirst($staffmember)." test</h1>";
			} 
			
			$TopBar .=$this->_dateform($StartDate,$staffmember, $user['ZOWuser_id']);
			//Clients report  button
			$TopBar .="<a href=\"".site_url()."reports\">Clients</a>";
			//Historic data  button
			$TopBar .="<a href=\"".site_url()."reports/historicaldata\">Historical Data</a>\n";
			//Add logout button
			$TopBar .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";
			//page switcher
			$TopBar .=_createpageswitcher();
			//Tracking button
			$TopBar .="<a href=\"".site_url()."tracking\" class=\"logout\">Tracking</a>";
			$TopBar .="</div>";
			return $TopBar;

	}


	// ################## Display split Data ##################
	function  _getstaffsplit($options =  array())
	{
			$staffdata="";
			$staffmember_name=ucfirst($options['staffmember_name']?:'');
			$staffmember_id=ucfirst($options['staffmember_id']);

			//$now =date( 'Y-m-15', strtotime('10 September 2012'));
			//$StartDate = date( 'Y-m-1', strtotime('10 September 20$options['12'));
			//$EndDate = date( 'Y-m-t', strtotime('10 September 2012'));
			

			$splittables=array("ScheduledBy","WorkedBy","ProofedBy","CompletedBy");

			foreach ($splittables as $step) {
				$totalnew=0;
				$totaledits=0;
				$totalhours=0;
				$totalcalctotals=0;
				$temptable="";
				$this->db->select('Client');
				$this->db->select('Code');			
				$this->db->select_sum('NewSlides','BilledNewSlides');
				$this->db->select_sum('EditedSlides','BilledEditedSlides');
				$this->db->select_sum('Hours','BilledHours');
				$this->db->where('DateOut >=', $options['StartDate']);
				$this->db->where('DateOut <= ', $options['EndDate']);
				$this->db->where('Trash =',0);
				$where = "( ".$step." = '".$staffmember_name."' OR ".$step." = '".$staffmember_id."' )";
				 $this->db->where($where);
				// $this->db->or_where($step,$staffmember_name);
				// $this->db->or_where('WorkedBy_3',$staffmember);
				$this->db->group_by('Client');
				$billedquery=$this->db->get('zowtrakentries');
				$tabledata=$billedquery->result_array();
				
			

				$temptable="<table class=\"".$step."\"><thead><tr><th>Client</th><th>New</th><th>Edits</th><th>Hours</th><th>Total Hours Billed</th><th>Client Code</th><tr></thead>\n<tbody>";
				foreach ($tabledata as $row) {
					// if( $step == 'WorkedBy'){
					// 	$this->db->select('Client');
					// 	$this->db->select('Code');			
					// 	$this->db->select_sum('NewSlides_2','NewSlides_2');
					// 	$this->db->select_sum('EditedSlides_2','EditedSlides_2');
					// 	$this->db->select_sum('Hours_2','Hours_2');
					// 	$this->db->where('DateOut >=', $options['StartDate']);
					// 	$this->db->where('DateOut <= ', $options['EndDate']);
					// 	$this->db->where('Trash =',0);
					// 	$this->db->where('WorkedBy_2',$staffmember);
					// 	$this->db->where('Client',$row['Client']);
					// 	$worked_2_hwe=$this->db->get('zowtrakentries');
					// 	$worked_2 = $worked_2_hwe->result_array();
					// 	$this->db->select('Client');
					// 	$this->db->select('Code');			
					// 	$this->db->select_sum('NewSlides_3','NewSlides_3');
					// 	$this->db->select_sum('EditedSlides_3','EditedSlides_3');
					// 	$this->db->select_sum('Hours_3','Hours_3');
					// 	$this->db->where('DateOut >=', $options['StartDate']);
					// 	$this->db->where('DateOut <= ', $options['EndDate']);
					// 	$this->db->where('Trash =',0);
					// 	$this->db->where('WorkedBy_3',$staffmember);
					// 	$this->db->where('Client',$row['Client']);
					// 	$worked_3_hwe=$this->db->get('zowtrakentries');
					// 	$worked_3 = $worked_3_hwe->result_array();

					// 	if($row['BilledNewSlides'])
					// 	$BilledNewSlides = $row['BilledNewSlides']+$worked_2[0]['NewSlides_2']+$worked_3[0]['NewSlides_3'];
					// 	$BilledEditedSlides = $row['BilledEditedSlides']+$worked_2[0]['EditedSlides_2']+$worked_3[0]['EditedSlides_3'];
					// 	$BilledHours = $row['BilledHours']+$worked_2[0]['Hours_2']+$worked_3[0]['Hours_3'];

					// 	$rowtotal=$BilledNewSlides/5;
					// 	$rowtotal+=$BilledEditedSlides/10;
					// 	$rowtotal+=$BilledHours;
					// }else{
						$BilledNewSlides = $row['BilledNewSlides'];
						$BilledEditedSlides = $row['BilledEditedSlides'];
						$BilledHours = $row['BilledHours'];

						$rowtotal=$BilledNewSlides/5;
						$rowtotal+=$BilledEditedSlides/10;
						$rowtotal+=$BilledHours;
					// $rowtotal=$row['BilledNewSlides']/5;
					// $rowtotal+=$row['BilledEditedSlides']/10;
					// $rowtotal+=$row['BilledHours'];
					// }
					$temptable.= "<tr>";
					$temptable.= "<th span=\"row\">".$row['Client']."</th>";
					$temptable.= "<td>".$BilledNewSlides."</td>";
					$temptable.= "<td>".$BilledEditedSlides."</td>";
					$temptable.= "<td>".number_format($BilledHours,2)."</td>";
					$temptable.= "<td>".number_format($rowtotal,2)."</td>";
					$temptable.= "<td>".$row['Code']."</td>";
					$temptable.= "</tr>";
					$totalnew+=$BilledNewSlides;
					$totaledits+=$BilledEditedSlides;
					$totalhours+=$BilledHours;
					$totalcalctotals+=number_format($rowtotal,2);
	 			}
				$temptable.="</tbody></table>";
 				$staffdata.="
 				<div class=\"clientreportlayout\" class=\"".$step."\">";
			    $staffdata.="<h3 class=\"title\">".str_replace("By", "", $step)." ";
				if (empty($tabledata)) {
				    $staffdata .="No Entries</h3>";
				} else {
				    $staffdata.= number_format($totalcalctotals,2)." billed hours <span>(";;
					$staffdata.= $totalnew."  new, ";
					$staffdata.= $totaledits." edits, ";
					$staffdata.= number_format($totalhours,2)." hours)</span>"; 
				    $staffdata.="</h3>";
					$staffdata.=$temptable;
				}

				$staffdata.="</div>

								";

			}
		return $staffdata;

	}

	// ################## Display Data ##################
	function  _getstafforiginators($options =  array())
	{
			$staffdata="";
			// $staffmember=ucfirst($options['staffmember']);
			$staffmember_name=ucfirst($options['staffmember_name']?:'');
			$staffmember_id=ucfirst($options['staffmember_id']);
			
			$this->db->select('Originator');		
			$this->db->group_by('Originator');
			$this->db->where('DateOut >=', $options['StartDate']);
			$this->db->where('DateOut <= ', $options['EndDate']);
			$this->db->where('Trash =',0);
			$where = "( `ScheduledBy` = '".$staffmember_name."' OR `ScheduledBy` = '".$staffmember_id."' OR `WorkedBy` = '".$staffmember_name."' OR `WorkedBy` = '".$staffmember_id."' OR `ProofedBy` = '".$staffmember_name."' OR `ProofedBy` = '".$staffmember_id."' OR `CompletedBy` = '".$staffmember_name."' OR `CompletedBy` = '".$staffmember_id."' )";
			//$where = "( `ScheduledBy` = '".$staffmember."' OR `WorkedBy` = ".$staffmember."')";
			$this->db->where($where);

			$billedquery=$this->db->get('zowtrakentries');
			$tabledata=$billedquery->result_array();
			$numentries=count($tabledata);
			$staffdata= "<div  class=\"clientreportlayout\"><h3>";
			if ($numentries==0) {
				$staffdata .= "No Originators </h3>";
			}
			else {
				
				$staffdata.= "<h3 class=\"title\">".$numentries." Originator";
				if ($numentries>1) {$staffdata.="s";}
				$staffdata.="</h3>";
				if ($numentries>0) {
					$this->table->set_heading(array('Originator'));
					$staffdata .= $this->table->generate($tabledata);
					}
			}
			$staffdata.="</div>";

		return $staffdata;

	}

/**
 * _dateform
 *
 * Creates date selector dropdown
 *
 * @access	public
 * @return	string
 */
	function  _dateform($StartDate,$staffmember,$user)
	{
			
 		$attributes['id'] = 'reportmonthform';
	 	$entryForm = form_open(site_url().'reports/staffreport', $attributes)."\n";
		$entryForm .="<fieldset>";




		//################ user dropdown			

		if ($user== 38 || $user== 3) {
			
			$query = $this->db->select();
	        $query = $this->db->where("visibility",1);
	        $query = $this->db->where('status',0);
            $query = $this->db->from('users');
            $query=$this->db->get();
// 			print_r($query->result());
			$staff=array();
			
			foreach ($query->result() as $row)
				{
					$rawname=substr($row->user_email, 0, strrpos($row->user_email, '@'));
					$name=$row->fname;
					
				// 	if  (count($name) == 2 && $name[1]=="poojari"){
				// 		$finalname="poojari";
				// 	} else{
				// 		$finalname=$name[0];
				// 	}
					$staff[$row->user_id]=ucfirst ($name);
				}
				ksort($staff);
				// print_r($staff);
			//$staff= array('sunil'=>'Sunil','tarun'=>'Tarun','ganesh'=>'Ganesh','hussain'=>'Hussain','joseph'=>'Joseph','nainesh'=>'Nainesh','poojari'=>'Poojari','miguel'=>'Miguel');	
			$more = 'id="staffswitcherselect"';
			$entryForm .=form_dropdown('staffmember', $staff,$staffmember,$more);
			
		}

		//################ date dropdown
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
	
		$more = 'id="reportmonthpicker"';
		$selected=$selecteddate;
		$entryForm .=form_dropdown('reportdate', $options,$selected,$more);
		
		
		$ndata = array('name' => 'submitbutton','value' => 'View', 'id'=>'reportmonthsubmit');
		$entryForm .= form_submit($ndata)."\n";
	  $entryForm .="</fieldset>";  
	  $entryForm .= form_close()."\n";
	 return $entryForm ;
	}


}

/* End of file staffreport .php */
/* Location: ./system/application/controllers/reports/staffreport .php */
?>