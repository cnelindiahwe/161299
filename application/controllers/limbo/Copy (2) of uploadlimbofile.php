<?php

class Uploadlimbofile extends MY_Controller {
	
	function index()
	{
		$currentdir=$this->input->post('currentdir');
		
		if ($_FILES["fileuploadname"]["error"] > 0)
		  {
		  echo "Error: " . $_FILES["fileuploadname"]["error"] . "<br />";
		  }
		else
		  {
			if (file_exists("/f5/zowtest/protected/limbo/".$currentdir."/" .$currentdir."/". $_FILES["fileuploadname"]["name"]))
		      {
		      echo "limbo/".$currentdir."/".$_FILES["fileuploadname"]["name"] . " already exists. Delete it first.";
		      }
		    else
		      {
		      move_uploaded_file($_FILES["fileuploadname"]["tmp_name"],
		      "/f5/zowtest/protected/limbo/" . $currentdir."/".$_FILES["fileuploadname"]["name"]);
		      }
			  if ($currentdir=="" ){
			  	redirect(Base_Url().'limbo/'); 
			  } else {
		   	  	redirect(Base_Url().'limbo/limbodir/'.$currentdir);
		 		}
		  }


	 }

	
}

/* End of file uploadlimbofile.php */
/* Location: ./system/application/controllers/limbo/uploadlimbofile.php */
?>