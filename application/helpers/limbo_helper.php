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
	function  _createlistlimbolist($list,$filesizes,$finaldir=""){
		$contentlist="<div id='limbodir'>";
		$contentlist.="<p><br/>";
		$contentlist.="This is a shared internal file repository. ";
		$contentlist.="You can upload and download files that cannot be easily sent over email.<br/> ";
		$contentlist.="You can also create upto 3 levels of subdirectories (folders). ";
		$contentlist.="You can delete files but you cannot rename them.";
		$contentlist.="<p>";
		$contentlist.="<h3>";
		$contentlist.="<img src='".base_url()."/web/img/folder.gif'>";
		if 	($finaldir=="") {
			$contentlist.="Limbo";
		} else {
			$contentlist.= "<a href='".base_url()."limbo'>Limbo/</a>";
			$contentlist.=$finaldir;
		}
		$contentlist.="/</h3>";
				$listcount=-1;
		foreach ($list as $key){
			$listcount++;
			$finalkey= $key;		
			if ($finaldir!="") {
				$sortkey= str_replace($finaldir."/", "", $finalkey);
			} else {
				$sortkey=$finalkey;
			}
				
			
			if ($sortkey!=$finaldir."/." && $sortkey!="." ){
					$contentlist.="<div class='diritem'>";	
					if (pathinfo($finalkey, PATHINFO_EXTENSION)!=""){
						$classsortkey =str_replace (".","_",$sortkey);
							$contentlist.= "<a href='".base_url()."limbo/downloadlimbofile/".$finalkey."' class='".$classsortkey."'><img src='".base_url()."/web/img/file.gif'>".$sortkey;
							$contentlist.= " (".number_format($filesizes[$listcount]/1000000,2). " mb) ";
							$contentlist.="</a>";
							$contentlist.= "<a href='".base_url()."limbo/deletelimbofile/".$finalkey."' class='deletefile'><img src='".base_url()."/web/img/trash.gif'></a>";
							$filedate = date ('F d Y ', filemtime($_SERVER['DOCUMENT_ROOT']."/zowtempa/etc/limbo/" .$finaldir."/".$sortkey));
							$contentlist.= "(".$filedate.")";
					} else {
						if ($sortkey==".."  && $finaldir!="") {
							$contentlist.= "<a href='".base_url()."limbo/limbodir/".$finalkey."'><img src='".base_url()."/web/img/arrowup.gif'>Parent folder</a>";
						}						
						else if ($sortkey!="..") {
							$contentlist.= "<a href='".base_url()."limbo/limbodir/";
							$contentlist.=$finalkey."'><img src='".base_url()."/web/img/folder.gif'>".$sortkey."</a>";
							// Scans the path for directories and if there are more than 2
							// directories i.e. "." and ".." then the directory is not empty
							//http://mattgeri.com/blog/2009/01/php-code-to-check-if-a-directory-is-empty/
							
							if (( $filesinlimbodir = @scandir($_SERVER['NFSN_SITE_ROOT'] . 'protected/limbo/'.$finalkey)) && (count($filesinlimbodir) > 2)) 
							{
								$finalnumberfiles =	count($filesinlimbodir)-2;
									$contentlist.= "(".$finalnumberfiles." item";
									if ($finalnumberfiles>1) {
										$contentlist.="s";
									}
									$contentlist.=")";
							} else {
								$contentlist.= "<a href='".base_url()."limbo/deletelimbodir/".$finalkey."' class='deletedir'><img src='".base_url()."/web/img/trash.gif'></a>";
							}
						}					
					}
					$contentlist.= "</div>";

			}
		}
		$contentlist.="</div>";
		return $contentlist;
	}
}

// ------------------------------------------------------------------------

/**
 * limboToolset
 *
 * add limbo toolbox
 *
 * @access	public
 * @return	string
 */
if ( ! function_exists('_limboToolset'))
{
	function _limboToolset($finaldir=""){
		$contentlist="";	
		//upload file
			$formattributes = array('id' => 'uploadform');
			$contentlist.= form_open_multipart('limbo/uploadlimbofile', $formattributes);	
			$data = array(
              'name'        => 'fileuploadname',
              'id'          => 'fileuploadname',
              'maxlength'   => '100',
              'size'        => '25',
              'type' 		=> 'file'
            );
			$contentlist.= form_input($data);
			$contentlist.=form_hidden('currentdir', $finaldir);
			$contentlist.= form_submit('fileuploasubmit', 'Upload file');
			$contentlist.= form_close();
			
			// ###### create directory
			//check that there are no more than 3 levels of nesting folders
			$howmanyfolders=explode("/",$finaldir);
			if (count($howmanyfolders)<3) {
				//create form
				$formattributes = array('id' => 'createdirform');
				$contentlist.= form_open('limbo/ajax_createdir', $formattributes);	
				$data = array(
	              'name'        => 'createdirname',
	              'id'          => 'createdirname',
	              'value'       => '',
	              'maxlength'   => '100',
	              'size'        => '25',
	            );
				$contentlist.=form_hidden('currentdir', $finaldir);
				$contentlist.= form_input($data);
				$contentlist.= form_submit('createdirsubmit', 'Create directory');
				$contentlist.= form_close();
			} else {
				$contentlist.="<h1>Only 3 levels of nesting folders allowed</h1>";
			}
		return $contentlist;
	}
}
/* End of file limbo_helper.php */
/* Location: ./system/application/helpers/limbo_helper.php  */
 ?>
