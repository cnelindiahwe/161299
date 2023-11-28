<?php

class Ajax_pjmonthview extends MY_Controller {

	function index()
	{
		if(isset($_POST['CalendarMonth']) && isset($_POST['CalendarYear']))  {
			$newdate=strtotime("1-".$_POST['CalendarMonth']."-".$_POST['CalendarYear']);
			$CalendarMonth = date( 'm', $newdate);
			$CalendarYear=date( 'Y', $newdate);
		}
		else {
			$CalendarMonth = date( 'm', strtotime('now'));
			$CalendarYear=date( 'Y', strtotime('now'));
		}
		
		//Originator
		if(isset($_POST['client']))  {
			if($_POST['client']!="'all'" && $_POST['client']!="''")  {
				$Client=trim(str_replace("'","", $_POST['client']));
			}				
		}
		//Originator
		if(isset($_POST['Originator']))  {
			if($_POST['Originator']!="'all'" && $_POST['Originator']!="''")  {
				$Originator=trim(str_replace("'","", $_POST['Originator']));
				
			}				
		}

		if (isset($Originator))  echo $this-> _getEntries($CalendarMonth,$CalendarYear,$Client,$Originator);
		else if (isset($Client)) echo $this-> _getEntries($CalendarMonth,$CalendarYear,$Client);
		else 	echo $this-> _getEntries($CalendarMonth,$CalendarYear);
	
	}
		
			// ################## Entry list ##################	
		function  _getEntries($CalendarMonth,$CalendarYear,$Client="",$Originator="")
		{
			$this->load->model('trakentries', '', TRUE);

				$StartDate = date('Y-m-d',strtotime($CalendarYear.'-'.$CalendarMonth));
				$EndDate = date('Y-m-t',strtotime($CalendarYear.'-'.$CalendarMonth));
				$options = array('Trash' => '0', 'sortBy'=> 'id', 'sortDirection'=> 'desc');
				if ($Client!="") {$options['Client']=$Client;}
				if ($Originator!="") {$options['Originator']=$Originator; }
			
			 $getentries = $this->trakentries->GetEntryRange($options,$StartDate,$EndDate);
			 
			 //echo $this->db->last_query();
			if($getentries)
			{
			$entries =$this->_getCalendar($getentries,$CalendarMonth,$CalendarYear);
	
			}
			else
			{
				//$entries = "No data available.";
				$entries =$this->_getCalendar("",$CalendarMonth,$CalendarYear);
			}
			return $entries;
		}

		// ################## Create calendar ##################	
	function  _getCalendar($getentries,$CalendarMonth,$CalendarYear)
	{
	
	$this->load->helper(array('zowcalendar'));
	
		$prefs = array (
               'show_next_prev'  => TRUE,
               'next_prev_url'   => site_url().'tracking/view/'
             );
			 	$prefs['template'] = '
	
	   {table_open}<table border="0" cellpadding="0" cellspacing="0" id="monthview">{/table_open}
	
	   {heading_row_start}<tr>{/heading_row_start}
	
	   {heading_previous_cell}<th><a href="{previous_url}" class="monthviewnav">&lt;&lt;</a></th>{/heading_previous_cell}
	   {heading_title_cell}<th colspan="{colspan}">{heading}</th>{/heading_title_cell}
	   {heading_next_cell}<th><a href="{next_url}"  class="monthviewnav">&gt;&gt;</a></th>{/heading_next_cell}
	
	   {heading_row_end}</tr>{/heading_row_end}
	
	   {week_row_start}<tr class="weekdays">{/week_row_start}
	   {week_day_cell}<td><div>{week_day}</div></td>{/week_day_cell}
	   {week_row_end}</tr>{/week_row_end}
	
	   {cal_row_start}<tr>{/cal_row_start}
	   {cal_cell_start}<td>{/cal_cell_start}
	
	   {cal_cell_content}<div class="CalendarDay"><strong>{day}</strong></div><div>{content}</div>{/cal_cell_content}
	   {cal_cell_content_today}<div class="CalendarDay"><strong>{day}</strong></div><div>{content}</div>{/cal_cell_content_today}
	
	   {cal_cell_no_content}<div class="CalendarDay">{day}</div>{/cal_cell_no_content}
	   {cal_cell_no_content_today}<div class="CalendarDay">{day}</div>{/cal_cell_no_content_today} 
	   
	   {cal_cell_start_today}<td class="today">{/cal_cell_start_today} 
	   {cal_cell_end_today}</td>{/cal_cell_end_today} 
	
	   {cal_cell_blank}&nbsp;{/cal_cell_blank}
	
	   {cal_cell_end}</td>{/cal_cell_end}
	   {cal_row_end}</tr>{/cal_row_end}
	
	   {table_close}</table>{/table_close}
	';
	
	$this->load->library('calendar', $prefs);
	
	
			if ($CalendarMonth=="") {
				$CalendarMonth=date( 'm');
			}
			if ($CalendarYear=="") {
				$CalendarYear=date( 'Y');
			}
			

			if (isset($CalendarData)) {
				unset($CalendarData);
			}
			$CalendarData=getCalendarData($getentries,$CalendarMonth,$CalendarYear);
	return $this->calendar->generate($CalendarYear,$CalendarMonth,$CalendarData);

	}
	



}

/* End of file newentry.php */
/* Location: ./system/application/controllers/deleteentry.php */
?>