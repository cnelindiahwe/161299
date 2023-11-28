<?php

class Ajax_addtravellernew extends MY_Controller {


	function index()
	{
		
	//get entry #
	/**/
	$this->load->helper(array('tracking','general','url','form'));	

				$this->load->model('trakclients', '', TRUE);
				$getclients = $this->trakclients->GetEntry();
		
				//$traveller= "<div class=\"traveller\" style=\"position:absolute;top:5em;left:12em; width:50%;background-color:#cdc;padding:1em 2em;-moz-box-shadow: 0px 4px 4px  #555;-webkit-box-shadow: 0px 4px 4px #555;box-shadow: 0px 4px 4px #555;-moz-border-radius:10px;-webkit-border-radius: 10px;\">";
				$traveller= "<div class=\"traveller\">";
				$traveller.=_getEntryForm($getclients);
				$traveller.="</div>";
				echo $traveller;
		

	}
}

/* End of file newentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>