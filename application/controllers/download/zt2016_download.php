<?php

class Zt2016_download extends CI_Controller {

	
	function index($path_regenerate ='')
	{
		
		// $this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		// $this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		// $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		// $this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		// $this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		//$this->load->helper(array('form','url','general','userpermissions'));
		
		$this->load->model('Zt2016_limbo_model');
		$this->load->helper(array('form','url','general'));
		if(!empty( $this->input->post('G_security'))){
		
			if($this->Zt2016_limbo_model->verify_password(['pass'=> $this->input->post('G_security'),'G_key'=>$this->uri->segment('2')])){

			if(!empty( $this->input->post('G_security'))){
				$key = $this->uri->segment('2');
			}else{
				$key = 	$this->uri->segment('2');
			}
			
			$get_path = $this->Zt2016_limbo_model->match_key_by_gkey($key);
			//	if(!empty($get_path->file_type)){
			if($get_path->file_type == 'folder'){
				  $file_dir_path =  str_replace('limbo/zt2016_limbodir/','',$get_path->path);
		
				 $d_file_name = dirname(dirname(dirname(__dir__))).'/zowtempa/etc/limbo_zip/'.$key.'.zip';
			//	$this->Zip(dirname(dirname(dirname(__dir__))).'/zowtempa/etc/limbo/'.$file_dir_path,$d_file_name,true);
				if(file_exists($d_file_name)){
			 $link =base_url().'/zowtempa/etc/limbo_zip/'.$key.'.zip';	
   	redirect($link);
				}
					redirect($this->uri->uri_string());
			}else{
			    	
			    	 $file_dir_path =  str_replace('limbo/zt2016_downloadlimbofile/','',$get_path->path);
			    	 $make_path = dirname(dirname(dirname(__dir__))).'/zowtempa/etc/limbo/'.$file_dir_path;
			    
			
  if (file_exists($make_path)) {
   
    $ext = pathinfo(basename($make_path), PATHINFO_EXTENSION);
   
     if($ext == 'pdf'){
          	define('CHUNK_SIZE', 2048*2048);
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($make_path).'"'); 
			header('Content-Transfer-Encoding: binary');
			header("Pragma: public");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header('Content-Length: ' . filesize($make_path));
			$f = fopen($make_path, 'r');
			while(!feof($f)){
			    print fgets($f, 5000);
			}
			fclose($f);
     }else{
           	$make_path = base_url().'zowtempa/etc/limbo/'.$file_dir_path;
				redirect($make_path);
     }
     
  }
   
			}
		}else{
			$this->session->set_flashdata('ErrorMessage', 'Invalid password');
			redirect($this->uri->uri_string());
		}
	}
	
		if($this->Zt2016_limbo_model->g_key_verify($this->uri->segment('2'))){
		     $this->load->view('download/download');
		}else{
		   redirect('404');
		}
       
	}
	public function make_zip($option){
		if(is_file($option['zip_add'])){
			echo 'file';
		}else if(is_dir($option['zip_add'])){
			$this->index($option['zip_add'].'/');
		}

	}
	function Zip($source, $destination, $include_dir = false)
{ 
 
  
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }


    if (file_exists($destination)) {
        unlink ($destination);
    }
  
    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }
    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true)
    {

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        if ($include_dir) {

            $arr = explode("/",$source);
            $maindir = $arr[count($arr)- 1];

            $source = "";
            for ($i=0; $i < count($arr) - 1; $i++) { 
                $source .= '/' . $arr[$i];
            }

            $source = substr($source, 1);

            $zip->addEmptyDir($maindir);

        }

        foreach ($files as $file)
        {
            $file = str_replace('\\', '/', $file);

            // Ignore "." and ".." folders
            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                continue;

             $file = str_replace("\\","/",realpath($file));

            if (is_dir($file) === true)
            {
				// echo $source . '/';
				// echo '<br>';
				// echo $file . '/';
				
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            }
            else if (is_file($file) === true)
            {
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    }
    else if (is_file($source) === true)
    {
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
}

	// ################## display clients info ##################	

		

}

/* End of file editclient.php */
/* Location: ./system/application/controllers/clients/editclient.php */
?>