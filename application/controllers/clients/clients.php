<?php

class Clients extends MY_Controller {

	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('clients','general','form','userpermissions', 'url','financialsnew','reports'));
		$templateVars['ZOWuser']=_superuseronly(); 
		

		$this->load->model('trakclients', '', TRUE);
		$ClientList = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
	
	 	$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		$templateVars['pageOutput'] .= $this->_get_top_menu($ClientList);
		
		$templateVars['pageOutput'] .= "<div class=\"content\">";
		$templateVars['pageOutput'] .= $this->_active_clients ($ClientList);
		$templateVars['pageOutput'] .= "<br/>";		
		$templateVars['pageOutput'] .= $this->_non_repeating_clients ($ClientList);	
		$templateVars['pageOutput'] .= "</div><!-- content -->";		
		

		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Clients";
		$templateVars['pageType'] = "clients";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));

 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtrak2012template');


	}

	
	// ################## top bar ##################	
	function  _get_top_menu($ClientList)
	{
			$entries ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			
			$entries .="<h2>".Count($ClientList)." Clients</h2>";
			$entries .="<a href=\"".site_url()."clients/newclient\" class=\"newclient\">Create New Client</a></h3>\n";

			//Add logout button
			$entries .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";

			
			$entries .=$this->_clientscontrol($ClientList);
			$entries .="</div>";
			
			return $entries;

	}
	
	
	// ################## clients control ##################	
	function   _clientscontrol($Clientlist)
	{
		$attributes['id'] = 'clientcontrol';
		$clientscontrol= form_open(site_url()."clients/editclient\n",$attributes);

			
			//Clients

				$options=array();
				foreach($Clientlist as $client)
				{
				$options[$client->CompanyName]=$client->CompanyName;
				}
				asort($options);
				$options=array('all'=>"All")+$options;		
				$more = 'id="clientselector" class="selector"';			
				$selected='all';
				$clientscontrol .=form_label('View / edit client details:','client');
				$clientscontrol .=form_dropdown('client', $options,$selected ,$more);
				$more = 'id="clientcontrolsubmit" class="clientcontrolsubmit"';			
				$clientscontrol .=form_submit('clientcontrolsubmit', 'Edit',$more);
				$clientscontrol .= form_close()."\n";

		return $clientscontrol;
	
	}
// ------------------------------------------------------------------------

/**
 * _active_clients
 *
 * Shows active clients per year
*/
function _active_clients ($ClientList){
	
		$active_clients=""; 		
		$running_clients="";
	 	$years = array();
		$current_year = date('Y');
		for ($i=$current_year; $i>2009; $i--){
		
			$thisyear=$i;
			if ($current_year== $thisyear) {
				$yeartotals =$this->_calculateyeartotal($thisyear);
			}
			
			$StartDate=$thisyear.'-1-1';
			$this->db->select();
			$this->db->where('Year',$StartDate);
			$rawentries = $this->db->get('zowtrakyearsummaries');
			if ($rawentries) {
				foreach ($rawentries->result() as $row){
					$monthclients =explode(",", $row->ClientList);
					if ($current_year== $thisyear) {$thisyearcount=count($monthclients);}					
					$running_clients .="<div class=\"yearpile\"><h3>".Date("Y",strtotime($row->Year))."<br/>";
					$running_clients .="<span class=\"subheader\">".count($monthclients)." Active Clients</span></h3>";
					$running_clients .="<ul>";
					foreach ($monthclients as $client){
						$safeclient=str_replace("&","~",$client);	
						$running_clients.="<li><strong><a href=\"clients/editclient/".$safeclient."\">".$client."</strong></a></li>";				
					}	
					$running_clients .="</ul></div>";
				}/**/
			}
			$active_clients.=$running_clients;
			$running_clients="";

		}
		
		$active_clients="<h3>".$thisyearcount." Active Clients in ".$current_year."</h3>".$active_clients;
		$active_clients.="<div class=\"clearfix\"></div>";
		return $active_clients;				
		


	}	

// ------------------------------------------------------------------------

/**
 * _calculateyeartotal
 *
 * Calculates totals for the year
 *
 * @access	public
 * @return	string
 */

	function   _non_repeating_clients ($ClientList)
	{

		$query = "SELECT Client,  MAX(DateOut) AS Firstdate  FROM zowtrakentries WHERE Trash = 0 GROUP BY Client ORDER BY Firstdate DESC ";
		$rawentries =$this->db->query($query);
		$getentries=$rawentries->result();
		$Prevyear=date( "Y",strtotime('now'));
		$clientlist_byage="";
		$yearcount=0;
		$clientlist_byage="";
		$currentyear=date('Y');
		$clientlist_running="";
				foreach($getentries as $client)
				{
					$Firstyear=date( "Y",strtotime($client->Firstdate));
					$Firstdate=date( "F Y",strtotime($client->Firstdate));
					$yearcount++;					
					if ($Firstyear!=$Prevyear) {
						if ($Prevyear!=$currentyear){
							$qualifier="non-repeating";
						}else {
							$qualifier="active";
							//$currentyearcount=$yearcount;
						}
						$clientlist_byage.="<div class=\"yearpile\"><h3>".$Prevyear.": ".($yearcount+0-1)." ".$qualifier." clients</h3><ul>";						
						$clientlist_byage.=$clientlist_running;
						$clientlist_byage.="</ul></div>";
						$yearcount=0;
						$clientlist_running="";
					}
					$safeclient=str_replace("&","~",$client->Client);
					$clientlist_running.="<li><strong><a href=\"clients/editclient/".$safeclient."\">".$client->Client."</a></strong>(".$Firstdate.")</li>";
					$Prevyear=$Firstyear;
				}
				$yearcount++;
				$clientlist_byage.="<div class=\"yearpile\"><h3>".$Prevyear.": ".$yearcount." non-repeating clients</h3>";						
				$clientlist_byage.=$clientlist_running;

				$yearcount=0;
				$clientlist_running="";
				$clientlist_byage.="</div>";
				$clientlist_byage.="<div class=\"clearfix\"></div>";
				
				
				$clientlist_header="<div><h3>Non-Repeating Clients</h3>";				
				$clientlist_byage=$clientlist_header.$clientlist_byage;
		return $clientlist_byage;
	
	}

	
// ------------------------------------------------------------------------

/**
 * _calculateyeartotal
 *
 * Calculates totals for the year
 *
 * @access	public
 * @return	string
 */
function _calculateyeartotal($thisyear) {
				$current_year = date('Y');
				$StartDate=$thisyear.'-1-1';
				$EndDate=$thisyear.'-12-31';
				$this->db->select('Client');
				$this->db->select_min('DateOut','StartDate') ;
				$this->db->select('count(id) as Jobs', FALSE);
				//$this->db->select_count('id','Jobs');
				$this->db->select_sum('InvoiceEntryTotal','Revenues');
				$this->db->select_sum('InvoiceTime','Hours');
				//if (isset($options['StartDate']) && $options['StartDate']!="") {
					$this->db->where('DateOut >=',  $StartDate);
				  	$this->db->where('DateOut <= ',  $EndDate);
				//}
				$this->db->where('Trash',0);
				$where = "(Status = 'COMPLETED' OR Status = 'BILLED' OR Status = 'PAID')";
				$this->db->where($where );
				$this->db->group_by('Client');
				$rawentries = $this->db->get('zowtrakentries');
	
				
				if ($rawentries) {
					//get client list
					$ClientTableRaw  = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
					
					
					foreach ($ClientTableRaw as $row){
						 $ClientCurrency[$row->CompanyName] =$row->Currency;
					}
					$eurtotal=0;
					 $usdtotal=0;
					$jobs=0;
					$clientlist="";
					 $clientjobs="";	
					 $clientrevenue="";	
					foreach ($rawentries->result() as $row){

						//echo $row->Client." ".$row->Jobs." ".$jobs."<br/>";
						 if ($ClientCurrency[$row->Client]=="EUR") { $eurtotal+=$row->Revenues;}
						 else if ($ClientCurrency[$row->Client]=="USD") { $usdtotal+=$row->Revenues;}
						 $jobs+=$row->Jobs;
						 $clientlist.=",".$row->Client;	
						 $clientjobs.=",".$row->Jobs;	
						 $clientrevenue.=",".$row->Revenues;					
					}
				
					$unbilled=$this->_getunbilledTotals();
					$eurtotal+=$unbilled['eurtotal'];
					$usdtotal+=$unbilled['usdtotal'];
					//############ Check if current year
					$timestamp_year_est="";
					$timestamp_year = date('Y');
	
					$timestamp_year_est= $timestamp_year;//. " Est.";
					$timestamp_year= $timestamp_year. " YTD";				

				}

				$start = strtotime($thisyear.'-01-01');
				$end = strtotime('now');
				$daysellaspsed = ceil(abs($end - $start) / 86400);


				$est_euro= ($eurtotal/$daysellaspsed)*365;
				$est_usd= ($usdtotal/$daysellaspsed)*365;


				
				$clientlist=$this->_deleteinitialcomma ($clientlist);
				$clientjobs=$this->_deleteinitialcomma ($clientjobs);
				$clientrevenue=$this->_deleteinitialcomma ($clientrevenue);				

				$this->db->select();
				$this->db->where('Year', $StartDate);
				$query = $this->db->get('zowtrakyearsummaries');
				if ($query->num_rows() != 0) {$existflag=1;} else {$existflag=2;} ;
	
				$this->db->set("TotalEUR", $eurtotal);
				$this->db->set("TotalUSD", $usdtotal);
				$this->db->set("Jobs", $jobs);
				$this->db->set("ClientList", $clientlist);			
				$this->db->set("ClientJobs",$clientjobs);	
				$this->db->set("ClientRevenue", $clientrevenue);					
								
				if ($existflag==1) {
					$this->db->where('Year', $StartDate);
					$this->db->update('zowtrakyearsummaries');
				} 
				else if($existflag==2) {
					$this->db->set("Year", $StartDate);
					$this->db->insert('zowtrakyearsummaries');	
				}


}
// ------------------------------------------------------------------------

/**
 * _originatorTotals
 *
 * Provides monthly prices per originator
 *
 * @access	public
 * @return	string
 */
 
 	function _originatorTotals($options=array()){
 		//run db query	
 		$this->db->select('Client');
		$this->db->select('Originator');
		$this->db->select_min('DateOut','StartDate') ;
		$this->db->select('count(id) as Jobs', FALSE);
		//$this->db->select_count('id','Jobs');
		$this->db->select_sum('InvoiceEntryTotal','Revenues');
		$this->db->select_sum('InvoiceTime','Hours');
		if (isset($options['StartDate']) && $options['StartDate']!="") {
			$this->db->where('DateOut >=',  $options['StartDate']);
		  	$this->db->where('DateOut <= ',  $options['EndDate']);
		}
		$this->db->where('Trash',0);
		$this->db->where("Invoice != 'NOT BILLED'");
		$this->db->group_by('Originator');
		$rawentries = $this->db->get('zowtrakentries');
		//if results exist, list them
		if ($rawentries) {
			//get client list
			$ClientTableRaw  = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
			
			foreach ($ClientTableRaw as $row){
				 $ClientCurrency[$row->CompanyName] =$row->Currency;
			}
			$financial_totals ="<div class=\"breakdown\"><h3>By Originator</h3>";
			$financial_totals .="<table class=\"originatorsdata\"><thead><tr><th>Originator</th><th>Client</th><th>Revenues</th><th>Avg. Price</th><th>Currency</th><th>Jobs</th><th>Hours</th></tr></thead><tbody>";	
			foreach ($rawentries->result() as $row){
				 $financial_totals .="<tr><th scope=\"row\">".$row->Originator."</th>";
				 $financial_totals .="<td>".$row->Client."</td>";
				 $financial_totals .="<td>".number_format($row->Revenues,2)."</td>";
				 $financial_totals .="<td>".number_format($row->Revenues/$row->Hours,2)."</td>";
				 $financial_totals .="<td>".$ClientCurrency[$row->Client]."</td>";
				 $financial_totals .="<td>".$row->Jobs."</td>";				 	
				 $financial_totals .="<td>".number_format($row->Hours,1)."</td></tr>";
				
			}
			$financial_totals .="</tbody></table></div>";
		}
		 return $financial_totals;

	}
// ------------------------------------------------------------------------

/**
 * _getunbilledTotals
 *
 * Provides monthly prices per client
 *
 * @access	public
 * @return	string
 */
 
	// ################## Generate client totals ##################	
	function  _getunbilledTotals()
	{
		$this->load->model('trakclients', '', TRUE);
		$Clientlist= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));

		$totalusd=0;
		$totaleuro=0;

		foreach($Clientlist as $client)
		{


		$StartDate = date( 'Y-m-1', strtotime('2013-1-1'));
		$EndDate = date( 'Y-m-d', strtotime('now'));
		  
		  //Get entry totals from db
		  
		  $this->db->select_sum('Hours','Hours');
		  $this->db->select_sum('NewSlides','NewSlides');
		  $this->db->select_sum('EditedSlides','EditedSlides');
		  $this->db->from('zowtrakentries');
		  $this->db->where('Client',$client->CompanyName);
			$this->db->where('Status','COMPLETED');
			$this->db->where('Invoice','NOT BILLED');
		 
		  $this->db->where('DateOut >=', $StartDate);
		  $this->db->where('DateOut <= ', $EndDate);
		   $this->db->where('Trash =',0);

		  $query = $this->db->get();
		  //var_dump($query);
		  

			
		  //Get client details from db
		  $this->load->model('trakclients', '', TRUE);
		  $query2 = $this->trakclients->GetEntry($options = array('CompanyName' => $client->CompanyName));
 
	  
		  //Apply edit price
		  $subtotal=$query->row()->EditedSlides*$query2->PriceEdits;
		  //Add slides and divide by slides per hour
		  $subtotal=$subtotal+$query->row()->NewSlides;
		  $subtotal=$subtotal/5;
		  //Add hours to get the total
		  $htotal=$subtotal+$query->row()->Hours;


		  if ($htotal!=0){
		  		 	
//echo $client->CompanyName."   ".$query->row()->EditedSlides."<br/>";	
			 
			 $tprice =_fetchClientMonthPrice($query2,$htotal);
			
			 $invoicerevenue=$tprice*$htotal;

			 
			 //add to totals

			 
			 if ($query2->Currency=="EUR"){
			 	
				$totaleuro=$totaleuro+$invoicerevenue;
			 }
			 else if ($query2->Currency=="USD"){
			 	
				$totalusd=$totalusd+$invoicerevenue;
				
			 }
			 
		}	
	}

	$total['eurtotal']=$totaleuro;
	$total['usdtotal']=$totalusd;
	return $total;
		

	}
	
// ------------------------------------------------------------------------

/**
 * _deleteinitialcomma
 *
 * Deletes initial comma from series strings
 *
 * @access	public
 * @return	string
 */
 	
 function _deleteinitialcomma ($mystring){
	return substr($mystring,1,strlen($mystring)-1);
}		
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>