<?php

//Problem online is uri segment number - please read hidden client input 

class PostogoneZOWsite extends MY_Controller {


	function index()
	{
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header("Expires: " . gmdate( "D, j M Y H:i:s", time() ) . " GMT"); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		//$this->load->helper(array('clients','general','form','userpermissions', 'url','invoice','reports','financials','security'));

		$this->load->helper(array('userpermissions', 'url', 'file'));

		
		$zowuser=_superuseronly(); 

		#Determine whether form was submitted
		if ($this->input->post('ORDERID'))
		{
			$invoiceinfo['invoicenumber']=$this->input->post('ORDERID');
			$invoiceinfo['invoicetotal']=(float)$this->input->post('AMOUNT')/100;
			$invoiceinfo['invoicehtml']=$this->input->post('INVOICEHTML');

			
			$status=$this-> _create_html_invoice($invoiceinfo['invoicenumber'],$invoiceinfo['invoicehtml']);
			echo $status;
		} 
		else {
			
			echo "uhoh";
			/*$invoicenumber=$this->uri->segment(3);
			if ($invoicenumber=="") {
				redirect('invoicing');
			}
		
		$this->load->model('trakclients', '', TRUE);
		$ClientList= $this->trakclients->GetEntry($options = array('Trash' => '0', 'sortBy'=> 'CompanyName','sortDirection'=> 'asc'));
		
		$clientName=$this-> _getclient($invoicenumber);
		$invoiceinfo=InvoiceTotalsByNumber($invoicenumber,$clientName);
			 * */
		}
		
		
		
		#Create page
	 	/* $templateVars['ZOWuser']=_getCurrentUser();
		$templateVars['pageOutput'] =  _getmanagerbar($templateVars['ZOWuser']);
		
		$templateVars['pageOutput'] .=  $this->_gettopmenu($invoicenumber,$invoiceinfo['invoicetotal'],$clientName);

		$templateVars['pageOutput'] .= "<div class=\"content\">";
		$templateVars['pageOutput'] .=$this->_getinvoiceinfo($invoicenumber,$invoiceinfo['invoicetotal']);
		
		$templateVars['pageOutput'] .= "</div><!-- content -->";

		
		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "Ogone";
		$templateVars['pageType'] = "invoice";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);	
					
		$this->load->view('zowtrak2012template');
		*/	
		
	}

	// ################## top ##################	
	function _gettopmenu($invoicenumber,$invoicetotal,$clientName)
	{
			$topmenu ="<div id='newjobbuttons' class='zowtrakui-topbar'>\n";
			$topmenu .="<h1>Ogone form for invoice ".$invoicenumber."</h1>";
			$topmenu .="<a href=\"".site_url()."invoicing/viewinvoice/".$invoicenumber."\" >View Details</a>";
			$topmenu .= $this->_getogoneform($invoicenumber,$invoicetotal,$clientName);

			//Add logout button
			$topmenu .="<a href=\"".site_url()."main/logout\" class=\"logout\">Logout</a>";
			$topmenu .="</div>";
		
			
			return $topmenu;

	}

	// ################## upload  ##################	
	function _create_html_invoice ($invoicenumber,$invoicehtml)
	{
			
			// ##### Create invoice file and save it in protected/temp
			if ( ! write_file($_SERVER['NFSN_SITE_ROOT']  . 'protected/temp/'.$invoicenumber.'.html', $invoicehtml))
			{
			     echo 'Unable to write the file';
				 return;
			}
			else
			{
			     echo 'File written!<br/>';
			}

			// ##### Upload invoice file to ZOW site
			
			$this->load->library('ftp');

			$config['hostname'] = 'ftp.zebraonwheels.com';
			$config['username'] = 'zebraonwheels.com';
			$config['password'] = 'c9qEbRu6';
			$config['debug']	= TRUE;
			
			$this->ftp->connect($config);
			
			$this->ftp->upload($_SERVER['NFSN_SITE_ROOT']  . 'protected/temp/'.$invoicenumber.'.html', '/www/payments/'.$invoicenumber.'.html', 'ascii');
			
			$this->ftp->close();
			
			echo 'File uploaded <br/> ';
			echo 'Check it: <a href="http://www.zebraonwheels.com/payments/'.$invoicenumber.'.html" target="_blank">http://www.zebraonwheels.com/payments/'.$invoicenumber.'.html</a>';			
			
			// ##### Delete file from  protected/temp

			unlink($_SERVER['NFSN_SITE_ROOT']  . 'protected/temp/'.$invoicenumber.'.html');
			
	}


}

/* End of file viewinvoice.php */
/* Location: ./system/application/controllers/billing/viewinvoice.php */
?>