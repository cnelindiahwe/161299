<?php

class Reportsnew extends MY_Controller {

	function Reportsnew()
	{
		parent::MY_Controller();	
	}
	
	function index()
	{
		

		$this->load->helper(array('form','url','reports'));

		$this->load->model('trakreports', '', TRUE);
		$this->load->model('trakentries', '', TRUE);

		$templateVars['pageSidebar'] ="";
		$templateVars['pageOutput'] =$this->_monthData();
		$templateVars['pageInput'] ="";

		$templateVars['baseurl'] = site_url();
		$templateVars['pageName'] = "reports";
		$templateVars['pageType'] = "reports";
		$templateVars['pageJavascript'] = str_replace(' ','', strtolower($templateVars['pageType']));
		
 	  	$this->load->vars($templateVars);		
		$this->load->view('zowtraktemplate');
				 
	}

	function  _monthData($options=array())
	{
	$data="";
	$now = strtotime(date('Y-m-15'));
		for ($i = -1; $i <=5; $i++) {
		
			$StartDate = date( 'Y-m-1', strtotime('-'.$i.' month',$now ));
			$EndDate = date( 'Y-m-t', strtotime('-'.$i.' month',$now ));
			$data.= "<br/>";
			$data.= date( 'M Y', strtotime($StartDate ));
			$data.= "<br/>";
			$clients=$this->trakreports->ClientsByDate($options=array('StartDate'=>$StartDate,'EndDate'=>$EndDate));
			foreach ($activeclients as $thisclient) {
						$this->db->select_sum('HoursBilled');
						$this->db->from('zowtrakentries');
						$this->db->where('Client', $thisclient->Client);
			
						$this->db->where('DateOut >=', $options['StartDate']);
						$this->db->where('DateOut <= ', $options['EndDate']);
						$this->db->where('Trash =',0);
						//if($options['Booked']==1) {
							$where = "(Status = 'COMPLETED' OR Status = 'BILLED' OR Status = 'PAID')";
						$this->db->where($where);
						//}
						$query = $this->db->get();
						$queryarray=$query->result_array();
						$data.= $thisclient->Client." ".number_format($queryarray[0]['HoursBilled'],2);
						$data.= "<br/>";
			}

		}
		return $data;
	}




	

}

/* End of file newentry.php */
/* Location: ./system/application/controllers/newentry.php */
?>