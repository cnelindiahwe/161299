<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ZOWTRAK
 *
 * @package		ZOWTRAK
 * @author		Zebra On WHeels
 * @copyright	Copyright (c) 2010 - 2009, Zebra On WHeels
 * @since		Version 1.0
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Client Helpers
 *
 * @package		ZOWTRAK
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Zebra On WHeels

 */

// ------------------------------------------------------------------------

/**
 * _createlistlimbolist
 *
 * Remote FTP server dir & file list
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('createlistlimbolist'))
{
	function  _createlistlimbolist($list,$finaldir=""){
		
		$contentlist="<div id='limbodir'>";

		$contentlist.='<ul class="list-group">'."\n";
		
		$contentlist.='<li class="list-group-item">'."\n";
		$contentlist.="<span class='glyphicon glyphicon-folder-open' aria-hidden='true'></span>";
		
		// add limbo top directory
		
		if 	($finaldir=="") {
			$contentlist.="Limbo /";
		} 
		
		// or subfolers directories
		else
		{
			
			$contentlist.= " <a href='".base_url()."limbo/zt2016_limbo'>Limbo /</a>";
			
			if (strpos ($finaldir,"/")){
				$urls = explode("/", $finaldir);
				$cumulativeurl="";
				$counturl=1;
				foreach ($urls as $url){
					if ($counturl < count($urls)) {
						$cumulativeurl.=$url."/";
						$contentlist.= " <a href='".base_url()."limbo/zt2016_limbodir/".$cumulativeurl."'>$url /  ";
					} else {
						$contentlist.=" ".$url; 
					}
					$counturl++;
				}
			} else{
				$contentlist.=" ".$finaldir;
			}
			// <input type='text' value='fsdfsd' readonly' class='form-control'>
		}
		$contentlist.="</li>";
		
		$listcount=-1;		
		
		foreach ($list as $key){
			
			$listcount++;
			
			// create string with final path
			if ($finaldir!="") {
				$finalkey=$finaldir."/".$key;
			} else {
				$finalkey=$key;
			}						

			if ($key!=$finaldir."/." && $key!="." ){
				
				$contentlist.='<li class="list-group-item">'."\n";	
					
					// ####### file
					if (pathinfo($key, PATHINFO_EXTENSION)!=""){
						
						// create string with directory's url
						if ($finaldir!="") {
							$dirurl=$finaldir."/".$key;
						} else {
							$dirurl=$key;
						}	
						
						$classsortkey =str_replace (".","_",$key);
						
						$download_path = "limbo/zt2016_downloadlimbofile/".$finalkey;
						$CI = get_instance();
						//$CI->load->model('Zt2016_limbo_model');
						$CI->load->model('Zt2016_limbo_model');
						// Call a function of the model
						$data = $CI->Zt2016_limbo_model->match_key($download_path);
						
						$contentlist.= "<span class='glyphicon glyphicon-list-alt' aria-hidden='true'></span> <a href='javascript:void(0);' class='".$classsortkey."'>".$key;
						$contentlist.="</a>";
						
						$filesize=filesize(dirname(dirname(__dir__))."/zowtempa/etc/limbo/".$finalkey);	
						
						$contentlist.= " | ".number_format($filesize/1000000,2). "mb";

						$filedate = date ('F d Y ', filemtime(dirname(dirname(__dir__))."/zowtempa/etc/limbo/".$finalkey));
						$contentlist.= " | ".$filedate." ";
						$contentlist.= "<a href='".base_url()."limbo/zt2016_deletelimbofile/".$finalkey."' class='deletefile  btn btn-danger btn-xs'><span class='glyphicon glyphicon glyphicon-trash' aria-hidden='true'></span></a>";
						if(!empty($data) && !empty($data->G_key)){
							
						 $contentlist.='<span style="width: 153px;float: right;margin-top: -6px;display: flex;"><button class="btn-success copy_text_btn" data-value="'.base_url().'x/'.$data->G_key.'"  style="padding: 0 5px;margin: 0 3px;">Copy Link</button> <button class="btn-danger remove_generate_key" data-key="'.$data->G_key.'"  style="padding: 0 5px;margin: 0 3px;">Remove</button></span>';
						

						}else{

							
							$contentlist.='<span style="width: 103px;float:right;margin-top: -3px;display: flex;"> <button id="generate_key" data-path="'.$download_path.'" data-name="'.$key.'" data-filetype="file" class="btn-info generate_key">Generate Link</button></span>';
						}
					} 
					// ####### directory
					else
					{
						// create string with directory's url
						if ($finaldir!="") {
							$dirurl=$finaldir."/".$key;
						} else {
							$dirurl=$key;
						}
						
						if ($key==".."  && $finaldir!="") {
							$contentlist.= "<span class='glyphicon glyphicon-level-up' aria-hidden='true'></span> <a href='".base_url()."limbo/zt2016_limbodir/".$key."'>Parent folder</a>";
						}						
						else if ($key!="..") {
							$download_folder_path = "limbo/zt2016_limbodir/".$finalkey;
						$CI = get_instance();
						//$CI->load->model('Zt2016_limbo_model');
						$CI->load->model('Zt2016_limbo_model');
						// Call a function of the model
						$data = $CI->Zt2016_limbo_model->match_key($download_folder_path);
							$contentlist.= "<span class='glyphicon glyphicon-folder-close' aria-hidden='true'></span> <a href='".base_url()."limbo/zt2016_limbodir/".$finalkey."'>".$key."</a>";
							if(!empty($data) && !empty($data->G_key)){
							
								$contentlist.='<span style="width: 153px;float: right;margin-top: -6px;display: flex;"><button class="btn-success copy_text_btn" data-value="'.base_url().'x/'.$data->G_key.'"  style="padding: 0 5px;margin: 0 3px;">Copy Link</button> <button class="btn-danger remove_generate_key" data-key="'.$data->G_key.'"  style="padding: 0 5px;margin: 0 3px;">Remove</button></span>';
							   
	   
							   }else{
	   
								   
								   $contentlist.='<span style="width: 123px;float:right;margin-top: -3px;display: flex;"> <button id="generate_key" data-path="'.$download_folder_path.'" data-name="'.$key.'" data-filetype="folder" class="btn-info generate_key">Generate Link</button></span>';
							   }
							// Scans the path for directories and if there are more than 2
							// directories i.e. "." and ".." then the directory is not empty
							//http://mattgeri.com/blog/2009/01/php-code-to-check-if-a-directory-is-empty/
							if (( $filesinlimbodir = @scandir(dirname(dirname(__dir__)).$_SERVER['DOCUMENT_ROOT']."/zowtempa/etc/limbo/".$dirurl)) && (count($filesinlimbodir) > 2)) 
							{
								$finalnumberfiles =	count($filesinlimbodir)-2;
									$contentlist.= "<span class=\"badge\">".$finalnumberfiles." item";
									if ($finalnumberfiles>1) {
										$contentlist.="s";
									}
									$contentlist.="</span>";
							}
							else {

								$contentlist.= "<a href='".base_url()."limbo/zt2016_deletelimbodir/".$finalkey."' class='deletedir btn btn-danger btn-xs'><span class='glyphicon glyphicon glyphicon-trash' aria-hidden='true'></span></a>";

							}
						}					
					}
					$contentlist.= "</li>";

			}
		}
		
		$contentlist.="</ul>";
		return $contentlist;
		
	}
}
if ( ! function_exists('get_list_of_folder'))
{
	function  get_list_of_folder($list,$finaldir=""){
		$final_data='<div class="col-sm-12 " id="" style="padding: 10px 28px;background: linear-gradient(to bottom,rgb(217, 237, 247) 0,rgb(196, 227, 243) 100%);display:block;">';
			$final_data.=limboToolset($finaldir);
			$final_data.= '</div>
			<div class="file-wrap">
			';
               
                    
		$contentlist.='
		<div class="file-pro-list">
		<div class="file-scroll">
			<ul class="file-menu">';
		
		// // add limbo top directory
		$back_list ='';
		if 	($finaldir=="") {
		//	$back_list.="Limbo /";
		} 
		
		// // or subfolers directories
		// else
		// {
			
		 	$back_list.= " <a href='".base_url()."limbo/zt2016_limbo' style=\"font-size: 16px;font-weight: 600;\">Limbo </a><span>></span>";
		$contentlist_new = '<a class="btn btn-dark text-light" href="'.base_url().'limbo/zt2016_limbo"> Back</a>';

			if (strpos ($finaldir,"/")){
				$urls = explode("/", $finaldir);
				$cumulativeurl="";
				$backlink_gen='';
				$counturl=1;
				foreach ($urls as $url){
					$backlink_gen.=$url."/";
					$back_list.= " <a href='".base_url()."limbo/zt2016_limbodir/".$backlink_gen."' style=\"font-size: 16px;font-weight: 600;\">".$url." </a><span>></span> ";
					if ($counturl < count($urls)) {
						$cumulativeurl.=$url."/";
						$contentlist_new = " <a class='btn btn-dark text-light' href='".base_url()."limbo/zt2016_limbodir/".$cumulativeurl."'>Back</a>";
					} else {
						$contentlist.=" ".$url; 
					}
					$counturl++;
				}
			} else{
				$contentlist.=" ".$finaldir;
				if(!empty($finaldir)){
				$back_list.= " <a href='".base_url()."limbo/zt2016_limbodir/".$finaldir."' style=\"font-size: 16px;font-weight: 600;\">$finaldir </a><span>></span>";
				}
				$contentlist_new = " <a class='btn btn-dark text-light' href='".base_url()."limbo'>Back</a>";
			}
			// <input type='text' value='fsdfsd' readonly' class='form-control'>
		// }
		// $contentlist.="</li>";
		
		// $listcount=-1;		
		$contentlist_file ='<div class="file-cont-wrap d">
		<div class="file-cont-inner">
			<div class="file-cont-header">
				<div class="file-options">
					<a href="javascript:void(0)" id="file_sidebar_toggle" class="file-sidebar-toggle">
						<i class="fa fa-bars"></i>
					</a>
					
				</div>
				<span>File Manager</span>
				<div class="file-options">

				<input type="hidden" name="currentdir" id="currentdir"  value="'.$finaldir.'">

				<div class="btn" role="group" aria-label="Basic example">
				<button type="button" class="btn btn-info" id="showtoolsbutton" >More</button>
				<button type="button" class="btn btn-secondary"  id="hidetoolsbutton" style="display:none;">Less</button>
				<button type="button" class="btn btn-success d-none" id="upload_limbo_modal_open"><span class="btn-file"  ><i class="fa fa-upload"></i></span></button>
				</div>
				<div class="row d-none">
				<div class="col-sm-6">
				<button type="button" id="showtoolsbutton" class="btn btn-info"></button>
			<button type="button " id="hidetoolsbutton" class="btn btn-secondary" style="display:none;">Less</button>
			</div>
				<div class="col-sm-6">
				<input type="hidden" name="currentdir" id="currentdir"  value="'.$finaldir.'">
			
					<span class="btn-file" id="upload_limbo_modal_open" ><i class="fa fa-upload"></i></span></div>
				</div>
			
				</div>
				
			</div>
			<div class="row" style="width: 99%;">
				<div class="col-sm-12" style="padding:10px 23px;">'.$back_list.'</div></div>
			<div class="file-content">
				<form class="file-search d-none">
					<div class="input-group">
						<div class="input-group-prepend">
							<i class="fa fa-search"></i>
						</div>
						<input type="text" class="form-control rounded-pill" placeholder="Search">
					</div>
				</form>
				<div class="file-body">
					<div class="file-scroll">
						<div class="file-content-inner">
							<div class="row row-sm">';
							
		$contentlist_folder='<div class="file-sidebar">
		<div class="file-header justify-content-center">
			<span>Projects</span>
			<a href="javascript:void(0);" class="file-side-close"><i class="fa fa-times"></i></a>
			
		</div>
		<div class="col-sm-12 bg-light text-dark">
				
				</div>
		<form class="file-search d-none">
			<div class="input-group">
				<div class="input-group-prepend">
					<i class="fa fa-search"></i>
				</div>
				<input type="text" class="form-control rounded-pill" placeholder="Search">
			</div>
		</form>
		<div class="row">
				</div>
		<div class="file-pro-list">
			<div class="file-scroll"><ul class="file-menu">
			
			<li>'.$contentlist_new.'</li>';
			
		foreach ($list as $key){
			
			$listcount++;
			
			// create string with final path
			if ($finaldir!="") {
				$finalkey=$finaldir."/".$key;
			} else {
				$finalkey=$key;
			}						
		
			if ($key!=$finaldir."/." && $key!="." ){
				
				// $contentlist_file.='<div class="file-cont-wrap">
				// <div class="file-cont-inner">'."\n";	
					
					// ####### file
					if (pathinfo($key, PATHINFO_EXTENSION)!=""){
						
						// create string with directory's url
						if ($finaldir!="") {
							$dirurl=$finaldir."/".$key;
						} else {
							$dirurl=$key;
						}	
						
						$classsortkey =str_replace (".","_",$key);
						
						$download_path = "limbo/zt2016_downloadlimbofile/".$finalkey;
						$CI = get_instance();
						//$CI->load->model('Zt2016_limbo_model');
						$CI->load->model('Zt2016_limbo_model');
						// Call a function of the model
						$data = $CI->Zt2016_limbo_model->match_key($download_path);
						
						// $contentlist.= "<span class='glyphicon glyphicon-list-alt' aria-hidden='true'></span> <a href='javascript:void(0);' class='".$classsortkey."'>".$key;
						// $contentlist_file .=' <div class="file-cont-header">
						// <div class="file-options">
						// 	dsff
						// 	</div>
						// 	<span>File Manager</span>
						// 	<div class="file-options">
						// 		<span class="btn-file"><input type="file" class="upload"><i class="fa fa-upload"></i></span>
						// 	</div>
						// </div>';
						$filesize=filesize(dirname(dirname(__dir__))."/zowtempa/etc/limbo/".$finalkey);	
						
						$contentlist_file .=' <div class="col-6 col-sm-4 col-md-3 col-lg-4 col-xl-3">
						<div class="card card-file">
							<div class="dropdown-file">
								<a href="" class="dropdown-link" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></a>
								<div class="dropdown-menu dropdown-menu-right">';
								$filedate = date ('F d Y ', filemtime(dirname(dirname(__dir__))."/zowtempa/etc/limbo/".$finalkey));
						if(!empty($data) && !empty($data->G_key)){
							
							// $contentlist_file .=' <span style="width: 153px;float: right;margin-top: -6px;display: flex;"><button class="btn-success copy_text_btn" data-value="'.base_url().'x/'.$data->G_key.'"  style="padding: 0 5px;margin: 0 3px;">Copy Link</button>
							//  <button class="btn-danger remove_generate_key" data-key="'.$data->G_key.'"  style="padding: 0 5px;margin: 0 3px;">Remove</button></span>';
							$contentlist_file .='<a href="javascript:void(0);" class="dropdown-item  copy_text_btn" data-value="'.base_url().'x/'.$data->G_key.'" >Copy Link</a>';
							$contentlist_file .='<a href="javascript:void(0);" class="dropdown-item  remove_generate_key " data-key="'.$data->G_key.'" >Remove Link</a>';
						

						}else{
							$contentlist_file .='<a href="javascript:void(0);"  data-path="'.$download_path.'" data-name="'.$key.'" data-filetype="file" class="dropdown-item generate_key">Generate Link</a>';

							
							// $contentlist_file .=' <span style="width: 103px;float:right;margin-top: -3px;display: flex;"> <button id="generate_key" data-path="'.$download_path.'" data-name="'.$key.'" data-filetype="file" class="btn-info generate_key">Generate Link</button></span>';
						}
						// <iframe src="'.base_url().'zowtempa/etc/limbo/'.$finalkey.'"></iframe>
						$extensions = array('jpg', 'JPG', 'png' ,'PNG' ,'jpeg' ,'JPEG');
						$extension = pathinfo($key);
						$vid = array('mp4', 'mov', 'mpg', 'flv');
						if(in_array($extension['extension'],$extensions))
						{
							  $html_print ='<img src="'.base_url().'/zowtempa/etc/limbo/'.$finalkey.'" style="max-width: 251px;max-height: 120px;" />';
						}
						elseif(in_array($extension['extension'],$vid))
						{
							  $html_print ='<video width="100%" height="auto" controls>
							  <source src="'.base_url().'/zowtempa/etc/limbo/'.$finalkey.'" type="video/mp4" />
						 </video>';
						}else{
							$html_print ='<i class="fa fa-file-o"></i>';

						}
								
						$contentlist_file .='	<a id="generate_key" href="'.base_url().$download_path.'" class="dropdown-item">Download</a>
									<a href="'.base_url()."limbo/zt2016_deletelimbofile/".str_replace(' ','%20',$finalkey).'" class="dropdown-item limbo_file_remove_popup">Delete</a>
								</div>
							</div>
							<div class="card-file-thumb" style="overflow: hidden;">';
							$contentlist_file .=$html_print;
							$contentlist_file .='
								
							</div>
							<div class="card-body">
								<h6><a href="" class="'.$classsortkey.'">'.$key.'</a></h6>
								<span>'.number_format($filesize/1000000,2).'mb</span>
							</div>
							<div class="card-footer">
								<span class="d-none d-sm-inline">Modified: </span>'.$filedate.'
							</div>
						</div>
					</div>';
						
						
						// $contentlist.= " | ".number_format($filesize/1000000,2). "mb";

						// $contentlist.= " | ".$filedate." ";
						// $contentlist.= "<a href='".base_url()."limbo/zt2016_deletelimbofile/".$finalkey."' class='deletefile  btn btn-danger btn-xs'><span class='glyphicon glyphicon glyphicon-trash' aria-hidden='true'></span></a>";
						// if(!empty($data) && !empty($data->G_key)){
							
						//  $contentlist.='<span style="width: 153px;float: right;margin-top: -6px;display: flex;"><button class="btn-success copy_text_btn" data-value="'.base_url().'x/'.$data->G_key.'"  style="padding: 0 5px;margin: 0 3px;">Copy Link</button> <button class="btn-danger remove_generate_key" data-key="'.$data->G_key.'"  style="padding: 0 5px;margin: 0 3px;">Remove</button></span>';
						

						// }else{

							
						// 	$contentlist.='<span style="width: 103px;float:right;margin-top: -3px;display: flex;"> <button id="generate_key" data-path="'.$download_path.'" data-name="'.$key.'" data-filetype="file" class="btn-info generate_key">Generate Link</button></span>';
						// }
					}
					// ####### directory
					else
					{
						// create string with directory's url
						if ($finaldir!="") {
							$dirurl=$finaldir."/".$key;
						} else {
							$dirurl=$key;
						}
						
						if ($key==".."  && $finaldir!="") {
						$contentlist_folder .=$listcount;

							$contentlist_folder .= '<li class="active"><div class="dropdown-file">
							<a href="" class="dropdown-link" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-v"></i></a>
							<div class="dropdown-menu dropdown-menu-right">';
							$filedate = date ('F d Y ', filemtime(dirname(dirname(__dir__))."/zowtempa/etc/limbo/".$finalkey));
					if(!empty($data) && !empty($data->G_key)){
						
						// $contentlist_file .=' <span style="width: 153px;float: right;margin-top: -6px;display: flex;"><button class="btn-success copy_text_btn" data-value="'.base_url().'x/'.$data->G_key.'"  style="padding: 0 5px;margin: 0 3px;">Copy Link</button>
						//  <button class="btn-danger remove_generate_key" data-key="'.$data->G_key.'"  style="padding: 0 5px;margin: 0 3px;">Remove</button></span>';
						$contentlist_folder .='<a href="javascript:void(0);" class="dropdown-item  copy_text_btn" data-value="'.base_url().'x/'.$data->G_key.'" >Copy Link</a>';
						$contentlist_folder .='<a href="javascript:void(0);" class="dropdown-item  remove_generate_key " data-key="'.$data->G_key.'" >Remove Link</a>';
					

					}else{
						$contentlist_folder .='<a href="javascript:void(0);"  data-path="'.$download_path.'" data-name="'.$key.'" data-filetype="file" class="dropdown-item generate_key">Generate Link</a>';

						
						// $contentlist_file .=' <span style="width: 103px;float:right;margin-top: -3px;display: flex;"> <button id="generate_key" data-path="'.$download_path.'" data-name="'.$key.'" data-filetype="file" class="btn-info generate_key">Generate Link</button></span>';
					}
							
					$contentlist_folder .='	<a id="generate_key" href="'.base_url().$download_path.'" class="dropdown-item">Download</a>
								<a href="'.base_url()."limbo/zt2016_deletelimbofile/".$finalkey.'" class="dropdown-item">Delete</a>
							</div>
						</div><a href="'.base_url().'limbo/zt2016_limbodir/"'.$key.'">Parent folder</a>';
						}						
						else if ($key!="..") {

							$download_folder_path = "limbo/zt2016_limbodir/".$finalkey;
						$CI = get_instance();
						//$CI->load->model('Zt2016_limbo_model');
						$CI->load->model('Zt2016_limbo_model');
						// Call a function of the model
						$data = $CI->Zt2016_limbo_model->match_key($download_folder_path);
							// $contentlist.= "<span class='glyphicon glyphicon-folder-close' aria-hidden='true'></span> <a href='".base_url()."limbo/zt2016_limbodir/""'>".$key."</a>" ;
							$contentlist_folder .= '<li class="active mb-2 mt-1"><a href="'.base_url().'limbo/zt2016_limbodir/'.$finalkey.'">'.$key.'</a></li>';
							if(!empty($data) && !empty($data->G_key)){
							
								// $contentlist.='<span style="width: 153px;float: right;margin-top: -6px;display: flex;"><button class="btn-success copy_text_btn" data-value="'.base_url().'x/'.$data->G_key.'"  style="padding: 0 5px;margin: 0 3px;">Copy Link</button> <button class="btn-danger remove_generate_key" data-key="'.$data->G_key.'"  style="padding: 0 5px;margin: 0 3px;">Remove</button></span>';
								// $contentlist_folder .= '<li class="active"><a href="'.base_url().'limbo/zt2016_limbodir/>'.$finalkey.'</a>';

	   
							   }else{
	   
								   
								   $contentlist.='<span style="width: 123px;float:right;margin-top: -3px;display: flex;"> <button id="generate_key" data-path="'.$download_folder_path.'" data-name="'.$key.'" data-filetype="folder" class="btn-info generate_key">Generate Link</button></span>';
							   }
							// Scans the path for directories and if there are more than 2
							// directories i.e. "." and ".." then the directory is not empty
							//http://mattgeri.com/blog/2009/01/php-code-to-check-if-a-directory-is-empty/
							// if (( $filesinlimbodir = @scandir(dirname(dirname(__dir__))."/home/zowtempa/etc/limbo/".$dirurl)) && (count($filesinlimbodir) > 2)) 
							// {
							// 	$finalnumberfiles =	count($filesinlimbodir)-2;
							// 		$contentlist.= "<span class=\"badge\">".$finalnumberfiles." item";
							// 		if ($finalnumberfiles>1) {
							// 			$contentlist.="s";
							// 		}
							// 		$contentlist.="</span>";
							// }
							// else {

							// 	$contentlist.= "<a href='".base_url()."limbo/zt2016_deletelimbodir/".$finalkey."' class='deletedir btn btn-danger btn-xs'><span class='glyphicon glyphicon glyphicon-trash' aria-hidden='true'></span></a>";

							// }
						}					
					}
					
					// $contentlist.= "</li>";

			}
		}
		$contentlist_file .='</div>
		</div>
	</div>
</div>';
		$contentlist_folder.='
		</ul>
		
	</div>
</div>
</div>';

$final_data .= $contentlist_folder;
$final_data .= $contentlist_file;
		return $final_data;
		
	}
}
/* */
// ------------------------------------------------------------------------

/**
 * limboToolset
 *
 * add limbo toolbox
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('limboToolset'))
{
	 //function _limboToolset($finaldir=""){
	function limboToolset($finaldir=""){
		
			$howmanyfolders=explode("/",$finaldir);
		 	$contentlist="";
		 	$contentlist.="<div class='row' id='dtoolset'>"."\n";
			
			//######  upload file form
			$contentlist.="<div class='col-lg-6'>";
		
			$formattributes = array('id' => 'uploadform', 'class' => "form-inline", 'style' =>'display:none;');
			$contentlist.= form_open_multipart('limbo/zt2016_uploadlimbofile', $formattributes)."\n";	

			$contentlist.='<input type="hidden" name="currentdir" id="currentdir"  value="'.$finaldir.'">'."\n";
		
			$data = 'type="file" value="" id="fileuploadname" maxlength="100" size="25" class="form-control" required';
			// $contentlist.=	form_upload('fileuploadname','',$data)."\n";
			$f_path = $finaldir ? str_replace('/','_Q_',$finaldir) : 'limbo';
			$contentlist.= '<button type="button" id="upload_limbo_modal_open"class="btn btn-primary" data-path="'.$f_path.'" >Upload</button>';
			//$contentlist.= form_submit('fileuploasubmit', 'Upload file', "class='form-control btn btn-primary'")."\n";
		
			$contentlist.= form_close()."\n";
		
			$contentlist.=	'</div>'."\n";	
		
			// ###### create directory
			//check that there are no more than 3 levels of nesting folders
			$contentlist.="<div class='col-lg-6'>"."\n";
			
			if (count($howmanyfolders)<3) {
				//create form
				$formattributes = array('id' => 'createdirform', 'class' => "form-inline pull-right", 'style' =>'display:none;');
				$contentlist.= form_open('limbo/zt2016_createlimbodir', $formattributes)."\n";	

				$contentlist.=form_hidden('currentdir', $finaldir)."\n";
				$data = 'type="text" name="createdirname" value="" id="createdirname" maxlength="100" size="25" class="form-control" required';
				$contentlist.= form_input('createdirname','',$data)."\n";
				$contentlist.= form_submit('createdirsubmit', 'Create directory', "id='createdirsubmit' class='form-control btn btn-info'")."\n";
	
				$contentlist.= form_close()."\n";
			} else {
				$contentlist.="<p style='text-align:right;padding-top:1rem;' id='createdirform'>Only 3 levels of nesting folders allowed</p>"."\n";
			}
			$contentlist.=	'</div>'."\n";
			$contentlist.=	'</div>'."\n";	
		/**/
		//$contentlist="hey";
		return $contentlist;
		
	}
}

 function limboToolset_create($finaldir=''){
	$contentlist='';
	
	
		//create form
		$formattributes = array('id' => 'screatedirform', 'class' => "form-inline pull-right",);
		$contentlist.= form_open('limbo/zt2016_createlimbodir', $formattributes)."\n";	

		$contentlist.=form_hidden('currentdir', $finaldir)."\n";
		$data = 'type="text" name="createdirname" value="" id="createdirname" maxlength="100" size="25" class="form-control" required';
		$contentlist.= form_input('createdirname','',$data)."\n";
		$contentlist.= form_submit('createdirsubmit', 'Create directory', "id='createdirsubmit' class='form-control btn btn-info'")."\n";

		$contentlist.= form_close()."\n";
	
	return $contentlist;
		
}
/* End of file zt2016_limbo_helper.php */
/* Location: ./system/application/helpers/zt2016_limbo_helper.php  */
 ?>
