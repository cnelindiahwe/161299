<?php
class Ajax_viewtraveller extends MY_Controller {

	
	function index()
	{
	$this->load->helper(array('tracking','general','url'));
	
	//get entry #
		die ("hhiiiii");
	$fields=$_POST;
	$urlstring=explode("/",$fields['entry']);
	$currentid=$urlstring[count($urlstring)-1];
	
	$this->load->model('trakentries', '', TRUE);
	$currententry = $this->trakentries->GetEntry($options = array('id' => $currentid));
	
	
			
			if($currententry)
				{
					
				if	($currententry->TimeZoneOut=""))
				{
					die ("hoohah!");
					$tzquery = "SELECT * FROM (`zowtrakclients`) "; 
					$tzquery .= "WHERE `CompanyName`='".$currententry->Client."' AND `Trash` = '0' LIMIT 1";
					$gettzentries =$this->db->query($tzquery);
					$clientinfo=$gettzentries->row();	
					$currententry->TimeZoneOut =$clientinfo->TimeZone;
					if	(empty($project->TimeZoneIn)){
						$currententry->TimeZoneIn =$currententry->TimeZoneOut;	
					}
				}
				if (empty($currententry->TimeZoneIn))
				{
					$tzquery = "SELECT * FROM (`zowtrakclients`) "; 
					$tzquery .= "WHERE `CompanyName`='".$currententry->Client."' AND `Trash` = '0' LIMIT 1";
					$gettzentries =$this->db->query($tzquery);
					$clientinfo=$gettzentries->row();	
					$currententry->TimeZoneIn =$clientinfo->TimeZone;
				}
					
				$this->load->model('trakclients', '', TRUE);
				$getclients = $this->trakclients->GetEntry();
				$traveller= "<div class=\"traveller\">";
				$traveller.='<h1>Traveler - Job ID '.$currentid."</h1>\n";
				$traveller.=_getEntryForm($getclients,$currententry);
				$traveller.="</div>";
				echo $traveller;
				}
			else {
				echo 'There was a problem retrieving the current entry';
			}
	}

}

/* End of file newentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>	
