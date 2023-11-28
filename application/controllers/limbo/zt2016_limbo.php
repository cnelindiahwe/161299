<?php

class Zt2016_limbo extends MY_Controller {
	
	function index()
	{
		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 

		$this->load->helper(array('zowtrakui','url','zt2016_limbo','userpermissions','form'));

		$templateData['title'] = 'Limbo';
		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['base_limbo_dir'] = dirname(dirname(dirname(__dir__)))."/zowtempa/etc/limbo/";
		
		$templateData['main_content'] =$this-> _display_limbo_page(); 		
			
		$this->load->view('admin_temp/main_temp',$templateData);	
		
	}
	// ################## top bar ##################	
	function  _display_limbo_page()
	{
		
		$pageOutput='';
		
		######### Display success message
		if($this->session->flashdata('SuccessMessage')){		
			
			$pageOutput.='<div class="alert alert-success" role="alert" style="margin-top:.5em;>'."\n";
			$pageOutput.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			//$page_content.='  <span class="sr-only">Error:</span>'."\n";
			$pageOutput.=$this->session->flashdata('SuccessMessage');
			$pageOutput.='</div>'."\n";
		}

		######### Display error message
		if($this->session->flashdata('ErrorMessage')){		
			
			$pageOutput.='<div class="alert alert-danger" role="alert" style="margin-top:.5em;>'."\n";
			$pageOutput.='  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'."\n";
			$pageOutput.='  <span class="sr-only">Error:</span>'."\n";
			$pageOutput.=$this->session->flashdata('ErrorMessage');
			$pageOutput.='</div>'."\n";
		}
	
		
		############## panel header	
		$pageOutput.='<div class="panel panel-default"><div class="panel-heading">'."\n"; 
		
		
		$pageOutput.='<h4 class="col-lg-12">Limbo</h4>'."\n";
		$pageOutdput .='
 <div class="content container-fluid p-0">
        
            <div class="row">
			<div class="col-sm-12">';
		 

                    $pageOutdput .=  $this-> _getdircontent();
                    $pageOutdput .=  '</div></div></div></div>
       
		';
// echo 
		
		$pageOutput.="</div><!--panel-heading-->\n"."\n\n";

		############## panel body	
		$pageOutput.='<div class="panel-body">'."\n";
		
		// $pageOutput.=$this-> _getdircontent();
		
		#### end panel		
		$pageOutput.="</div><!--panel body-->\n</div><!--panel-->\n";
		return $pageOutdput;/**/		

	}
	// ################## ftp content ##################
	function _getdircontent()
	{
			$filelist=array();
			$dirlist=array();
			$finallist=array();
			//$dir = $_SERVER['NFSN_SITE_ROOT'] . "protected/limbo/";
			$dir = dirname(dirname(dirname(__dir__)))."/zowtempa/etc/limbo/";
		
				// Open a known directory, and proceed to read its contents
				if (is_dir($dir)) {
					if ($dh = opendir($dir)) {
						while (($file = readdir($dh)) !== false) {
							if ($file!='.' && $file!='..') {
								if(is_dir($dir.'/'.$file)){
									$dirlist[]= $file;
									} else {								
									$filelist[]= $file;
								}
							}
						}
						closedir($dh);
					
						# sort and add dir and file names arrays
						natcasesort ($dirlist);
						natcasesort ($filelist);
						
						foreach ($dirlist as $xfile){
							$finallist[]=$xfile;
							$filesizes[]="";
						}
						foreach ($filelist as $xfile){
							$finallist[]=$xfile;
							$filesizes[]="";

						}						
						
												
					}
					else {echo "No files found".$clientcode." ".$dir;}
				}
		
		 return  get_list_of_folder($finallist);
		 
	/**/
	}
	public function generate_key(){
		
		if($this->input->post('file_remove') == 1 ){
$option=array(
	'G_key' => $this->input->post('file_key')
);
$delete_data = $this->db->delete('limbo_folder_management',$option);
			if($delete_data){
			    $key = $this->input->post('file_key');
			    $d_file_name = dirname(dirname(dirname(__dir__))).'/zowtempa/etc/limbo_zip/'.$key.'.zip';
			    if(file_exists($d_file_name)){
					
				unlink($d_file_name);
				
				}
				
				echo 1;
			}else{
				echo 0;
			}
		}else{
			$rand_gen =  mt_rand().time();
			$random = substr(md5($rand_gen), 0, 21);
			$file_name =  $this->input->post('file_name');
			$file_path = $this->input->post('file_path');
			$filetype = $this->input->post('filetype');
			$option=array(
				'G_key' => $random,
				'path' => $file_path,
				'file_name' => $file_name,
				'file_type' => $filetype,
			);
			$insert_data = $this->db->insert('limbo_folder_management',$option);
			if($insert_data){
				echo $this->db->insert_id();
				
			}else{
				echo 0;
			}
		}
		
	}
	public function set_key_pass(){
	
		if(!empty($this->input->post('key_id')) && !empty($this->input->post('password')) ){
			$option=array(
				'password' => $this->input->post('password'),
				'status' => 1
			);
			$this->db->where('id', $this->input->post('key_id'));
			$update_data = $this->db->update('limbo_folder_management',$option);
			if($update_data){
			$query = $this->db->select("path,file_type,G_key");
	        $query = $this->db->where('id', $this->input->post('key_id'));
            $this->db->from('limbo_folder_management');
            $query=$this->db->get();
			$get_path = $query->row(0);
			if($get_path->file_type == 'folder'){
			    $key = $get_path->G_key;
			   $file_dir_path =  str_replace('limbo/zt2016_limbodir/','',$get_path->path);
			    $d_file_name = dirname(dirname(dirname(__dir__))).'/zowtempa/etc/limbo_zip/'.$key.'.zip';
			    $this->Zip(dirname(dirname(dirname(__dir__))).'/zowtempa/etc/limbo/'.$file_dir_path,$d_file_name,true);
				echo 1;
			}else{
			    echo 1;
			    
			}
				
			}else{
				echo 0;
			}
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

	public function store_files(){

		// (A) HELPER FUNCTION - SERVER RESPONSE
		function verbose ($ok=1, $info="") {
		  if ($ok==0) { http_response_code(400); }
		  exit(json_encode(["ok"=>$ok, "info"=>$info]));
		}
		
		// (B) INVALID UPLOAD
		if (empty($_FILES) || $_FILES["file"]["error"]) {
		  verbose(0, "Failed to move uploaded file.");
		}
		
		// (C) UPLOAD DESTINATION - CHANGE FOLDER IF REQUIRED!
		if($this->uri->segment('2') == 'limbo'){
			$filePath = dirname(dirname(dirname(__dir__)))."/zowtempa/etc/limbo/";
		}else{
			$path = str_replace('_Q_','/',$this->uri->segment('2'));
			$new_path = str_replace('%20',' ',$path);
		$filePath = dirname(dirname(dirname(__dir__)))."/zowtempa/etc/limbo/".$new_path;

		}
	
		//$filePath = __DIR__ . DIRECTORY_SEPARATOR . "uploads";
if (!file_exists($filePath)) { if (!mkdir($filePath, 0777, true)) {
		  verbose(0, "Failed to create $filePath");
		}}
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : $_FILES["file"]["name"];
		$filePath = $filePath . DIRECTORY_SEPARATOR . $fileName;
		
		// (D) DEAL WITH CHUNKS
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
		$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
		if ($out) {
		  $in = @fopen($_FILES["file"]["tmp_name"], "rb");
		  if ($in) { while ($buff = fread($in, 4096)) { fwrite($out, $buff); } }
		  else { verbose(0, "Failed to open input stream"); }
		  @fclose($in);
		  @fclose($out);
		  @unlink($_FILES["file"]["tmp_name"]);
		} else { verbose(0, "Failed to open output stream"); }
		
		// (E) CHECK IF FILE HAS BEEN UPLOADED
		if (!$chunks || $chunk == $chunks - 1) { rename("{$filePath}.part", $filePath); }
		verbose(1, "Upload OK");
	}
  
}	
/* End of file Zt2016_limbo.php */
/* Location: ./system/application/controllers/limbo/zt2016_limbo.php */
?>