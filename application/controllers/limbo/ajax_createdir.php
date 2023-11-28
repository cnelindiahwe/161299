<?php

class Ajax_createdir extends MY_Controller {
	
	function index()
	{

		$this->load->helper(array('url'));	
		$currentdir = $this->input->post('currentdir');
		$createdirname = $this->input->post('createdirname');
		if ($createdirname!="") {
			$createdirname=str_replace(".","-" ,$createdirname);
			$createdirname=str_replace("/","-" ,$createdirname);
			$createdirname=str_replace("\\","-" ,$createdirname);
			
			if ($currentdir=="") {
				$finalcreatedirname=$_SERVER['DOCUMENT_ROOT']."/zowtempa/etc/limbo/".$createdirname;
			} else{
				$finalcreatedirname=$_SERVER['DOCUMENT_ROOT']."/zowtempa/etc/limbo/".$currentdir."/".$createdirname;
			}
			if (!is_dir($finalcreatedirname)) {
				mkdir($finalcreatedirname,0770);
			} else {
				if ($currentdir=="") {	
					echo "Directory 'limbo/".$createdirname."' exists.";
				} else {
					echo "Directory 'limbo/".$currentdir."/".$createdirname."' exists.";
				}
				die;
			}
		}
		if ($currentdir=="") {	
			redirect(Base_Url().'/limbo');
		}
		else  {	
			redirect(Base_Url().'limbo/limbodir/'.$currentdir);
		}
	}



	
}

/* End of file ajax_createdir */
/* Location: ./system/application/controllers/limbo/ajax_createdir */
?>