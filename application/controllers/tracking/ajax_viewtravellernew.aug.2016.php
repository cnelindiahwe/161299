<?php
class Ajax_viewtravellernew extends CI_Controller  {


	
	function index()
	{
	$this->load->helper(array('tracking','general','url','form'));
	
	//get entry #
	$fields=$_POST;
	$urlstring=explode("/",$fields['entry']);
	$currentid=$urlstring[count($urlstring)-1];
	
	$this->load->model('trakentries', '', TRUE);
	$currententry = $this->trakentries->GetEntry($options = array('id' => $currentid));
	
	
			
			if($currententry)
				{
				$this->load->model('trakclients', '', TRUE);
				$getclients = $this->trakclients->GetEntry();
				$getClientInfo = $this->trakclients->GetEntry($options =array('CompanyName' => $currententry->Client));


				$this->load->model('trakcontacts', '', TRUE);
				$getContacts = $this->trakcontacts->GetEntry($options =array('CompanyName' => $currententry->Client));
				if($getContacts) {
					foreach($getContacts as $project)
					{
						if ($project->FirstName." ".$project->LastName==$currententry->Originator){
							$contactinfo=$project;
						}
					}
				}

				$this->load->model('trakclients', '', TRUE);
				$getclients = $this->trakclients->GetEntry();
				//$traveller= "<div class=\"traveller\">";
				//$traveller.='<h1>Job ID '.$currentid."</h1>\n";
				$traveller=_getEntryForm($getclients,$currententry,$options = array('contactinfo' => $contactinfo,'clientinfo' => $getClientInfo));
				//$traveller.="</div>";
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
