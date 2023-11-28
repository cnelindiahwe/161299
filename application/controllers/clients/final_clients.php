<?php

class Final_clients extends MY_Controller {

	
	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->helper(array('clients','general','form','userpermissions', 'url'));
		
		$templateVars['ZOWuser']=_superuseronly(); 
		

		$this->load->model('trakclients', '', TRUE);
		$ClientList = $this->trakclients->GetEntry($options = array('Trash'=>'0','sortBy'=>'CompanyName','sortDirection'=>'ASC	'));
	
	 	$templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		$templateVars['pageOutput'] .= $this->_get_top_menu($ClientList);
		$templateVars['pageOutput'] .=$this->_non_repeating_clients ($ClientList);	
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
	
		// ################## clients control ##################	
	function   _non_repeating_clients ($ClientList)
	{

		//$query = "SELECT  Client, YEAR(DateOut) AS Firstdate  FROM zowtrakentries WHERE Trash = 0 GROUP BY Client  ORDER BY Firstdate DESC ";
		$query = "SELECT Client,  MIN(YEAR(DateOut)) AS Firstdate,  MAX(YEAR(DateOut)) AS Lastdate  FROM zowtrakentries WHERE Trash = 0 GROUP BY Client ORDER BY Client ASC ";		
		$rawentries =$this->db->query($query);
		//print_r($rawentries);
		//$rawentries2 =$this->db->query($query2);
		$getentries=$rawentries->result_array();
		$Prevyear=date( "Y",strtotime('now'));
		$clientlist_byage="";
		$yearcount=0;
		$clientlist_byage="";
		$currentyear=date('Y');
		$clientlist_running="";


        $maxyear = $getentries[0]['Lastdate'];
        foreach($getentries as $a) {
            if($a['Lastdate'] > $maxyear) {
                $maxyear = $a['Lastdate'];
            }
        }
	
        $minyear = $getentries[0]['Firstdate'];
        foreach($getentries as $a) {
            if($a['Firstdate'] < $minyear) {
                $minyear = $a['Firstdate'];
            }
        }
       for ($i=$minyear; $i<=$maxyear; $i++){      				
       			
 				foreach($getentries as $client)
				{
					if ($i >= $client['Firstdate'] && $i <= $client['Lastdate'] ){
						$safeclient=str_replace("&","~",$client['Client']);
						$clientlist_running.="<strong><a href=\"clients/editclient/".$safeclient."\">".$client['Client']."</strong></a><br/>";	
						$yearcount++;
					}
					
       			}		
				$clientlist_running="<div class=\"yearpile\"><h3>".$i.":<br/><span class=\"subheader\">".$yearcount." active clients</span></h3>".$clientlist_running;
	   			$clientlist_running.="</div>";
	   			$clientlist_byage=$clientlist_running.$clientlist_byage;
				$clientlist_running="";
				$yearcount="0";
	   }
				//foreach($getentries as $client)
				//{
					//$Firstyear=$client->Firstdate;
					//$Lastyear=$client->Lastdate;
					//if ($Prevyear>=Firstdate && $Prevyear>=Lastdate
									
					 
					/*
					  
					$Firstyear=$client->Firstdate;
					$Lastyear=$client->Lastdate;
					$yearcount++;					
					if ($Firstyear!=$Prevyear) {
						if ($Prevyear!=$currentyear){
							$qualifier="non-repeating";
						}else {
							$qualifier="active";
							$currentyearcount=$yearcount;
						}
						$clientlist_byage.="<div class=\"yearpile\"><h3>".$Prevyear.":<br/><span class=\"subheader\">".$yearcount." ".$qualifier." clients</span></h3>";						
						$clientlist_byage.=$clientlist_running;
						$clientlist_byage.="</div><div class=\"yearpile\">";
						$yearcount=0;
						$clientlist_running="";
					 
					}
					$safeclient=str_replace("&","~",$client->Client);
					$clientlist_running.="<strong><a href=\"clients/editclient/".$safeclient."\">".$client->Client."</strong></a> (".$Firstyear.")<br/>";
					$Prevyear=$Firstyear;
				*/
				//}
				/*
				 * $clientlist_byage.="<div class=\"yearpile\"><h3>".$Prevyear.":<br/><span class=\"subheader\">".$yearcount." non-repeating clients</span></h3>";						
				$clientlist_byage.=$clientlist_running;
				$clientlist_byage.="</div><div class=\"yearpile\">";
				$yearcount=0;
				$clientlist_running="";
				$clientlist_byage.="</div>";
				$clientlist_byage.="</div><!--content-->";
				*/

				/*
				 * $clientlist_header="<div class=\"content\">";
				$clientlist_header.="<h3>".$currentyearcount." Clients in ".$currentyear;
				$clientlist_header.="<br/><span class=\"non-bold subheader\">(".count($ClientList)." Total Clients)</span></h3>";				
				$clientlist_byage=$clientlist_header.$clientlist_byage;
				 */
		return $clientlist_byage;
	
	}

    function _max_with_key($array, $key) {
        if (!is_array($array) || count($array) == 0) return false;
        $max = $array[0][$key];
        foreach($array as $a) {
            if($a[$key] > $max) {
                $max = $a[$key];
            }
        }
        return $max;
    }	
	
	
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>