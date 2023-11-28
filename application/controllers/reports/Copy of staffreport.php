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
		
		if ( isset($_POST['staffmember']))
		{
			$staffmember =$_POST['staffmember'];
		}
		else
		{
			$stafftemp=explode(".",$templateVars['ZOWuser']);
			$staffmember=strtolower($stafftemp[0]);
		}			
		
		$ClientList= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));
		
		

		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		$templateVars['pageOutput'] .=$this->_getTopBar($StartDate,$staffmember,$templateVars['ZOWuser']);
		$templateVars['pageOutput'] .=$this->_getstaffsplit($options=array('staffmember'=>$staffmember,'StartDate'=>$StartDate,'EndDate'=>$EndDate));
		$templateVars['pageOutput'] .=$this->_getstafforiginators($options=array('staffmember'=>$staffmember,'StartDate'=>$StartDate,'EndDate'=>$EndDate));
		
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
			if ($user=="miguel" || $user=="sunil") {
				$TopBar .="</h1>";
			} else {
				$TopBar .=ucfirst($staffmember)."</h1>";
			} 
			
			$TopBar .=$this->_dateform($StartDate,$staffmember, $user);
			//Clients report  button
			$TopBar .="<a href=\"".site_url()."reports\">Clients</a>";
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
			$staffmember=ucfirst($options['staffmember']);

			//$now =date( 'Y-m-15', strtotime('10 September 2012'));
			//$StartDate = date( 'Y-m-1', strtotime('10 September 20$options['12'));
			//$EndDate = date( 'Y-m-t', strtotime('10 September 2012'));
			

			$splittables=array("ScheduledBy","WorkedBy","ProofedBy","CompletedBy");

			foreach ($splittables as $step) {
				$totalnew="";
				$totaledits="";
				$totalhours="";
				$totalcalctotals="";
				$temptable="";
				$this->db->select('Client');		
				$this->db->select_sum('NewSlides','BilledNewSlides');
				$this->db->select_sum('EditedSlides','BilledEditedSlides');
				$this->db->select_sum('Hours','BilledHours');
				$this->db->where('DateOut >=', $options['StartDate']);
				$this->db->where('DateOut <= ', $options['EndDate']);
				$this->db->where('Trash =',0);
				$this->db->where($step,$staffmember);
				$this->db->group_by('Client');
				$billedquery=$this->db->get('zowtrakentries');
				$tabledata=$billedquery->result_array();

				$temptable="<table><thead><tr><th>Client</th><th>New</th><th>Edits</th><th>Hours</th><th>Total Hours Billed</th><tr></thead>\n<tbody>";
				foreach ($tabledata as $row) {
					
					$rowtotal=$row['BilledNewSlides']/5;
					$rowtotal+=$row['BilledEditedSlides']/10;
					$rowtotal+=$row['BilledHours'];
					$temptable.= "<tr>";
					$temptable.= "<td>".$row['Client']."</td>";
					$temptable.= "<td>".$row['BilledNewSlides']."</td>";
					$temptable.= "<td>".$row['BilledEditedSlides']."</td>";
					$temptable.= "<td>".$row['BilledHours']."</td>";
					$temptable.= "<td>".$rowtotal."</td>";
					$temptable.= "</tr>";
					$totalnew+=$row['BilledNewSlides'];
					$totaledits+=$row['BilledEditedSlides'];
					$totalhours+=$row['BilledHours'];
					$totalcalctotals+=$rowtotal;
	 			}
				$temptable.="</tbody></table>";
 				$staffdata.="<div class=\"clientreportlayout\">";
			    $staffdata.="<h3 class=\"title\">".str_replace("By", "", $step)." ";
				if (empty($tabledata)) {
				    $staffdata .="No Entries</h3>";
				} else {
				    $staffdata.= $totalcalctotals." billed hours <span>(";
					$staffdata.= $totalnew."  new, ";
					$staffdata.= $totaledits." edits, ";
					$staffdata.= $totalhours." hours)</span>"; 
				    $staffdata.="</h3>";
					$staffdata.=$temptable;
				}
 				$staffdata.="</div>";
			

			}
		return $staffdata;

	}

	// ################## Display Data ##################
	function  _getstafforiginators($options =  array())
	{
			$staffdata="";
			$staffmember=ucfirst($options['staffmember']);

			
			$this->db->select('Originator');		
			$this->db->group_by('Originator');
			$this->db->where('DateOut >=', $options['StartDate']);
			$this->db->where('DateOut <= ', $options['EndDate']);
			$this->db->where('Trash =',0);
			$where = "( `ScheduledBy` = '".$staffmember."' OR `WorkedBy` = '".$staffmember."' OR `ProofedBy` = '".$staffmember."' OR `CompletedBy` = '".$staffmember."' )";
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

		if ($user=="miguel" || $user=="sunil.singal") {
			$staff= array('sunil'=>'Sunil','tarun'=>'Tarun','ganesh'=>'Ganesh','miguel'=>'Miguel');	
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