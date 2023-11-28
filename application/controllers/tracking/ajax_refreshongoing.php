<?php

class Ajax_refreshongoing extends MY_Controller {
	
	function index()
	{

		$this->load->helper(array('zowtrakui','tracking','general','userpermissions'));	
		//print_r($_POST);
		$sortype= $this->input->post('filtersort');
		echo $this-> _listOngoingJobs($sortype);

	}
		// ################## _list Past Jobs ##################	
	function  _listOngoingJobs($sortype="phase")
	{
			
		$query = "SELECT * FROM (`zowtrakentries`) ";
		$query .= "WHERE `Status`!='COMPLETED' AND `Invoice` = 'NOT BILLED' AND `Trash` = '0' ";
		if ($sortype=="phase"){
			$query .= "ORDER BY FIELD(`Status`, 'IN PROOFING','IN PROGRESS','SCHEDULED','TENTATIVE')";	
		} else if ($sortype=="deadline") {
			//$query .= "ORDER BY `DateOut`,`TimeOut`, FIELD(`TimeZoneOut`, 'Pacific/Chatham','Etc/GMT-12','Pacific/Auckland','Asia/Anadyr','Pacific/Norfolk','Asia/Magadan','Etc/GMT-11','Australia/Lord_Howe','Asia/Vladivostok','Australia/Hobart','Australia/Brisbane','Australia/Darwin','Australia/Adelaide','Asia/Yakutsk','Asia/Seoul','Asia/Tokyo','Australia/Eucla','Australia/Perth','Asia/Irkutsk','Asia/Hong_Kong','Asia/Krasnoyarsk','Asia/Bangkok','Asia/Rangoon','Asia/Novosibirsk','Asia/Dhaka','Asia/Katmandu','Asia/Kolkata','Asia/Tashkent','Asia/Yekaterinburg','Asia/Kabul','Asia/Yerevan','Asia/Dubai','Asia/Tehran','Africa/Addis_Ababa','Europe/Moscow','Asia/Damascus','Europe/Minsk','Asia/Jerusalem','Africa/Blantyre','Asia/Gaza','Africa/Cairo','Asia/Beirut','Africa/Windhoek','Africa/Algiers','Europe/Brussels','Europe/Belgrade','Europe/Amsterdam','Africa/Abidjan','Europe/London','Europe/Lisbon','Europe/Dublin','Europe/Belfast','Atlantic/Azores','Atlantic/Cape_Verde','America/Noronha','America/Sao_Paulo','America/Argentina/Buenos_Aires','America/Godthab','America/Miquelon','America/Montevideo','America/Araguaina','America/St_Johns','America/Glace_Bay','America/Goose_Bay','America/Campo_Grande','Atlantic/Stanley','America/La_Paz','America/Santiago','America/Caracas','America/Bogota','America/Havana','America/New_York','America/Chicago','Chile/EasterIsland','America/Cancun','America/Belize','America/Dawson_Creek','America/Chihuahua','America/Denver','America/Los_Angeles','Etc/GMT+8','America/Ensenada','America/Anchorage','Pacific/Gambier','Pacific/Marquesas','Etc/GMT+10','America/Adak','Pacific/Midway')";	
			//$query .= "ORDER BY `DateOut`,`TimeOut`";
		}
		
		$rawentries =$this->db->query($query);
		$getentries=$rawentries->result();	

	 	$ZOWuser=_getCurrentUser();
		$entries =  _getmanagerbar($ZOWuser);
		if($getentries)
		{
			foreach($getentries as $project)
			{
				if	(empty($project->TimeZoneOut))
				{
					$tzquery = "SELECT * FROM (`zowtrakclients`) "; 
					$tzquery .= "WHERE `CompanyName`='".$project->Client."' AND `Trash` = '0' LIMIT 1";
					$gettzentries =$this->db->query($tzquery);
					$clientinfo=$gettzentries->row();	
					$project->TimeZoneOut =$clientinfo->TimeZone;
					if	(empty($project->TimeZoneIn)){
						$project->TimeZoneIn =$project->TimeZoneOut;	
					}
				}
				if (empty($project->TimeZoneIn))
				{
					$tzquery = "SELECT * FROM (`zowtrakclients`) "; 
					$tzquery .= "WHERE `CompanyName`='".$project->Client."' AND `Trash` = '0' LIMIT 1";
					$gettzentries =$this->db->query($tzquery);
					$clientinfo=$gettzentries->row();	
					$project->TimeZoneIn =$clientinfo->TimeZone;
				}				
				$pendinghours=_getpendinghours($project->DateOut,$project->TimeOut,$project->TimeZoneOut);	
				$project->PendingHours =$pendinghours;
			}
			
			if ($sortype=="deadline") {
				//http://stackoverflow.com/questions/1462503/sort-array-by-object-property-in-php
				function cmp($a, $b)
					{

						return $a->PendingHours['raw'] == $b->PendingHours['raw'] ? 0 : ( $a->PendingHours['raw'] > $b->PendingHours['raw'] ) ? 1 : -1;
					}
					
				usort($getentries, "cmp");
			}
						
			
			$entries .= _getOngoingJobs($getentries,$ZOWuser);
		}
		else
		{
			$entries.=_displayOngoingJobs(0,$ZOWuser);
		}
		return $entries;
	}

}


/* End of file newentry.php */
/* Location: ./system/application/controllers/updateentry.php */
?>