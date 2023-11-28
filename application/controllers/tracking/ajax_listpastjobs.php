<?php

class Ajax_listpastjobs extends MY_Controller {

	function index()
	{
		
		$this->load->helper(array('tracking'));	
		$this->load->model('trakentries', '', TRUE);

		//clean up incoming data				
		//#jobs
		if($_POST['numjobs']) 
			{$numjobs=$_POST['numjobs']; }
		else
			{$numjobs=20; }
		
		//client name
		$client=trim(str_replace("'","", $_POST['client']));
		$client=str_replace('~','&',$client);
		
		//Originator
		if(isset($_POST['Originator']))  {
			if($_POST['Originator']!="'all'" && $_POST['Originator']!="''")  {
				$Originator=trim(str_replace("'","", $_POST['Originator']));
			}				
		}

		if($client!="all" && $client!="") 
		{
			if (isset($Originator)) {
				$getentries = $this->trakentries->GetCompletedEntries($options = array('Trash' => '0', 'sortByNew'=> '1', 'limit'=> $numjobs,'Client'=>$client,'Originator'=> $Originator));
			} else{
				$getentries = $this->trakentries->GetCompletedEntries($options = array('Trash' => '0', 'sortByNew'=> '1', 'limit'=> $numjobs,'Client'=>$client));
			}
			}
		else {
		$getentries = $this->trakentries->GetCompletedEntries($options = array('Trash' => '0', 'sortByNew'=> '1', 'limit'=> $numjobs));
		}
	
		if($getentries)
		{
			
			$entries= _entrydatatable($getentries);
		}
		else {
			$entries=  "<table><tr><td>No data found.";
			$entries.= " Query was:<br/><br/>".$this->db->last_query();
			$entries.="</td></tr></table>";
		}
		echo $entries;
	}		



}

/* End of file newentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>