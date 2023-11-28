<?php class Trakreports extends CI_Model
{

	/**
	 * ClientHistorical
	 *
	 * @param array $required
	 * @param array $data
	 * @return bool
	 */

	function _ClientHistorical($options =  array())
	{
		if (!$this->_required(array('Client', 'CurrentMonth'), $options)) return false;

		$StartDate = date('Y-m-d', strtotime('first day of ' . $options['CurrentMonth']));
		$EndDate = date('Y-m-d', strtotime('last day of ' . $options['CurrentMonth']));
		//echo $StartDate." - ".$EndDate. "<br/>";



		$historical['month'] = date('M Y', strtotime($StartDate));
		//Get billed totals from db
		$this->db->select_sum('Hours', 'Hours');
		$this->db->select_sum('NewSlides', 'NewSlides');
		$this->db->select_sum('EditedSlides', 'EditedSlides');
		$this->db->select_sum('InvoiceTime', 'InvoiceTime');
		$this->db->from('zowtrakentries');
		$this->db->where('Client', $options['Client']->CompanyName);
		$this->db->where('DateOut >=', $StartDate);
		$this->db->where('DateOut <= ', $EndDate);
		$this->db->where('Trash =', 0);
		$this->db->where('Invoice !=', 'NOT BILLED');
		$query = $this->db->get();

		$historical['total'] = $query->row()->InvoiceTime;
		$historical['newslides'] = $query->row()->NewSlides;
		$historical['editslides'] = $query->row()->EditedSlides;
		$historical['hours'] = $query->row()->Hours;


		$this->db->from('zowtrakentries');
		$this->db->where('Client', $options['Client']->CompanyName);
		$this->db->where('DateOut >=', $StartDate);
		$this->db->where('DateOut <= ', $EndDate);
		$this->db->where('Trash =', 0);
		$query2 = $this->db->get();

		$historical['jobs'] = $query2->num_rows();;

		return $historical;
	}

	// ------------------------------------------------------------------------

	/**
	 * ClientsByDate gives number of clients between dates
	 *
	 * @param array $required
	 * @param array $data
	 * @return bool
	 */
	function ClientsByDate($options =  array())
	{

		if (!isset($options['Booked'])) {
			$options['Booked'] = 0;
		}

		$this->db->distinct();
		$this->db->select('Client');



		$this->db->where('DateOut >=', $options['StartDate']);
		$this->db->where('DateOut <= ', $options['EndDate']);
		$this->db->where('Trash =', 0);
		if ($options['Booked'] == 1) {
			$where = "(Status = 'COMPLETED' OR Status = 'BILLED' OR Status = 'PAID' OR Status='DISPUTED' OR Status = 'WAIVED' OR Status = 'MARKETING')";
			$this->db->where($where);
		}
		if (isset($options['WorkType'])) {
			$this->db->where('WorkType',  $options['WorkType']);
		}
		if (isset($options['Currency'])) {
			$this->db->where('Currency',  $options['Currency']);
		}
		$this->db->from('zowtrakentries');
		$this->db->order_by('Client', 'Asc');
		$query = $this->db->get();

		return $query->result();
	}

	// ------------------------------------------------------------------------

	/**
	 * OriginatorsByDate gives number of clients between dates
	 *
	 * @param array $required
	 * @param array $data
	 * @return bool
	 */
	function OriginatorsByDate($options =  array())
	{

		if (!isset($options['Booked'])) {
			$options['Booked'] = 0;
		}

		$this->db->distinct();
		$this->db->select('Originator');



		$this->db->where('DateOut >=', $options['StartDate']);
		$this->db->where('DateOut <= ', $options['EndDate']);
		$this->db->where('Trash =', 0);
		if ($options['Booked'] == 1) {
			$where = "(Status = 'COMPLETED' OR Status = 'BILLED' OR Status = 'PAID' OR Status='DISPUTED' OR Status = 'WAIVED' OR Status = 'MARKETING')";
			$this->db->where($where);
		}
		if (isset($options['WorkType'])) {
			$this->db->where('WorkType',  $options['WorkType']);
		}
		if (isset($options['Currency'])) {
			$this->db->where('Currency',  $options['Currency']);
		}
		$this->db->from('zowtrakentries');
		$this->db->order_by('Originator', 'Asc');
		$query = $this->db->get();

		return $query->result();
	}

	// ------------------------------------------------------------------------

	/**
	 * _NumJobsByDate gives number of jobs between dates
	 *
	 * @param array $required
	 * @param array $data
	 * @return bool
	 */
	function  _NumJobsByDate($options = array())
	{

		//Get BOOKED totals from db
		$this->db->where('DateOut >=', $options['StartDate']);
		$this->db->where('DateOut <= ', $options['EndDate']);
		$this->db->where('Trash =', 0);
		if (isset($options['Client'])) {
			$this->db->where('Client',  $options['Client']);
		}
		if (isset($options['WorkType'])) {
			if ($options['WorkType'] != "") {
				$this->db->where('WorkType',  $options['WorkType']);
			}
		}
		$this->db->from('zowtrakentries');
		return $this->db->count_all_results();
	}

	/**
	 * _getAllClientTotalsByDate gives totals for all clients between datess
	 *
	 * @param array $required
	 * @param array $data
	 * @return bool
	 */

	function  _getAllClientTotalsByDate($options = array())
	{

		if (!isset($options['Booked'])) {
			$options['Booked'] = 0;
		}

		//Get BOOKED totals from db
		$this->db->select('Client');
		$this->db->select_sum('Hours', 'Hours');
		$this->db->select_sum('NewSlides', 'NewSlides');
		$this->db->select_sum('EditedSlides', 'EditedSlides');
		$this->db->group_by('Client');

		if ($options['Booked'] == 1) {
			$where = "(Status = 'COMPLETED' OR Status = 'BILLED' OR Status = 'PAID' OR Status='DISPUTED' OR Status = 'WAIVED' OR Status = 'MARKETING')";
			$this->db->where($where);
		} elseif ($options['Booked'] == 2) {
			$this->db->where('Invoice', 'NOT BILLED');
			$this->db->where('Status', 'COMPLETED');
		} elseif ($options['Booked'] == 3) {
			$this->db->where('Invoice', 'NOT BILLED');
			$this->db->where("Status != 'TENTATIVE'");
		}

		if (isset($options['WorkType'])) {
			$this->db->where('WorkType',  $options['WorkType']);
		}
		$this->db->where('DateOut >=',  $options['StartDate']);
		$this->db->where('DateOut <= ',  $options['EndDate']);
		$this->db->where('Trash =', 0);
		$this->db->from('zowtrakentries');
		$querybooked = $this->db->get();
		// echo $this->db->last_query();
		//return $querybooked->result();
		return $querybooked->result_array();
	}


	/**
	 * _getAllClientBilledTotalsByDate gives billed totals for all clients between datess
	 *
	 * @param array $required
	 * @param array $data
	 * @return bool
	 */

	function  _getAllClientBilledTotalsByDate($options = array())
	{

		if (!isset($options['Booked'])) {
			$options['Booked'] = 0;
		}

		//Get BOOKED totals from db
		$this->db->select('Client');
		$this->db->select_sum('InvoiceTime', 'InvoiceTime');
		$this->db->select_sum('InvoiceEntryTotal', 'InvoiceEntryTotal');
		$this->db->select_sum('Hours', 'Hours');
		$this->db->select_sum('NewSlides', 'NewSlides');
		$this->db->select_sum('EditedSlides', 'EditedSlides');
		$this->db->where('Invoice !=', 'NOT BILLED');
		$this->db->where('DateOut >=',  $options['StartDate']);
		$this->db->where('DateOut <= ',  $options['EndDate']);
		$this->db->where('Trash =', 0);
		if (isset($options['WorkType'])) {
			if ($options['WorkType'] != "") {
				$this->db->where('WorkType',  $options['WorkType']);
			}
		}
		$this->db->group_by('Client');
		$this->db->from('zowtrakentries');
		$querybooked = $this->db->get();
		//echo $this->db->last_query();
		//return $querybooked->result();
		return $querybooked->result_array();
	}

	/**
	 * _getAllClientCompletedTotalsByDate gives Completed totals for all clients between datess
	 *
	 * @param array $required
	 * @param array $data
	 * @return bool
	 */

	function  _getAllClientCompletedTotalsByDate($options = array())
	{

		if (!isset($options['Booked'])) {
			$options['Booked'] = 0;
		}

		//Get BOOKED totals from db
		$this->db->select('Client');
		$this->db->select_sum('Hours', 'Hours');
		$this->db->select_sum('NewSlides', 'NewSlides');
		$this->db->select_sum('EditedSlides', 'EditedSlides');
		$this->db->where('Invoice', 'NOT BILLED');
		$this->db->where('Status', 'COMPLETED');
		$this->db->where('DateOut >=',  $options['StartDate']);
		$this->db->where('DateOut <= ',  $options['EndDate']);
		$this->db->where('Trash =', 0);
		if (isset($options['WorkType'])) {
			if ($options['WorkType'] != "") {
				$this->db->where('WorkType',  $options['WorkType']);
			}
		}
		$this->db->group_by('Client');
		$this->db->from('zowtrakentries');
		$querybooked = $this->db->get();
		//echo $this->db->last_query();
		//return $querybooked->result();
		return $querybooked->result_array();
	}
	/**
	 * _getAllClientEllapsedTotalsByDate gives booked totals for all clients between datess
	 *
	 * @param array $required
	 * @param array $data
	 * @return bool
	 */

	function  _getAllClientEllapsedTotalsByDate($options = array())
	{

		if (!isset($options['Booked'])) {
			$options['Booked'] = 0;
		}

		//Get BOOKED totals from db
		$this->db->select('Client');
		$this->db->select_sum('Hours', 'Hours');
		$this->db->select_sum('NewSlides', 'NewSlides');
		$this->db->select_sum('EditedSlides', 'EditedSlides');
		$this->db->where('Invoice', 'NOT BILLED');
		$where = "( status = 'SCHEDULED' OR status = 'IN PROGRESS' OR status = 'IN PROOFING' )";
		$this->db->where($where);
		$this->db->where('DateOut >=',  $options['StartDate']);
		$this->db->where('DateOut <= ',  $options['EndDate']);
		$this->db->where('Trash =', 0);
		if (isset($options['WorkType'])) {
			if ($options['WorkType'] != "") {
				$this->db->where('WorkType',  $options['WorkType']);
			}
		}
		$this->db->group_by('Client');
		$this->db->from('zowtrakentries');
		$querybooked = $this->db->get();
		// echo $this->db->last_query();
		//return $querybooked->result();
		return $querybooked->result_array();
	}
	/**
	 * _getClientTotalsByDate gives totals for a single client and status between datess
	 *
	 * @param array $required
	 * @param array $data
	 * @return bool
	 */
	function  _getClientTotalsByDate($StartDate, $EndMonth, $clientb, $Status, $Booked)
	{
		//Get BOOKED totals from db
		$this->db->select($Status);
		$this->db->select_sum('Hours', 'Hours');
		$this->db->select_sum('NewSlides', 'NewSlides');
		$this->db->select_sum('EditedSlides', 'EditedSlides');
		$this->db->group_by($Status);
		$this->db->where('Client', $clientb->Client);
		$this->db->where('DateOut >=', $StartDate);
		$this->db->where('DateOut <= ', $EndMonth);
		if ($Booked == 1) {
			$where = "(Status = 'COMPLETED' OR Status = 'BILLED' OR Status = 'PAID' OR Status='DISPUTED' OR Status = 'WAIVED' OR Status = 'MARKETING')";
			$this->db->where($where);
		} else if ($Booked == 2) {
			$this->db->where('Invoice', 'NOT BILLED');
			$this->db->where('Status', 'COMPLETED');
		}
		$this->db->where('Trash =', 0);
		$this->db->from('zowtrakentries');
		$querybooked = $this->db->get();
		return $querybooked->result_array();
	}

	function  _getClientTotalsByDate_new($StartDate, $EndMonth, $clientb, $Status, $Booked)
	{
		//Get BOOKED totals from db
		$this->db->select($Status);
		$this->db->select_sum('Hours', 'Hours');
		$this->db->select_sum('Hours_2', 'Hours_2_main');
		$this->db->select_sum('Hours_3', 'Hours_3_main');
		$this->db->select_sum('NewSlides', 'NewSlides');
		$this->db->select_sum('NewSlides_2', 'NewSlides_2_main');
		$this->db->select_sum('NewSlides_3', 'NewSlides_3_main');
		$this->db->select_sum('EditedSlides', 'EditedSlides');
		$this->db->select_sum('EditedSlides_2', 'EditedSlides_2_main');
		$this->db->select_sum('EditedSlides_3', 'EditedSlides_3_main');
		//   $this->db->select_sum('Hours_2','Hours_2');
		//   $this->db->select_sum('NewSlides_2','NewSlides_2');
		//   $this->db->select_sum('EditedSlides_2','EditedSlides_2');
		//   $this->db->select_sum('Hours_3','Hours_3');
		//   $this->db->select_sum('NewSlides_3','NewSlides_3');
		//   $this->db->select_sum('EditedSlides_3','EditedSlides_3');
		$this->db->group_by($Status);
		$this->db->where('Client', $clientb->Client);
		$this->db->where('DateOut >=', $StartDate);
		$this->db->where('DateOut <= ', $EndMonth);
		if ($Booked == 1) {
			$where = "(Status = 'COMPLETED' OR Status = 'BILLED' OR Status = 'PAID' OR Status='DISPUTED' OR Status = 'WAIVED' OR Status = 'MARKETING')";
			$this->db->where($where);
		} else if ($Booked == 2) {
			$this->db->where('Invoice', 'NOT BILLED');
			$this->db->where('Status', 'COMPLETED');
		}
		$this->db->where('Trash =', 0);
		$this->db->from('zowtrakentries');
		$querybooked = $this->db->get();


		if ($Status == 'WorkedBy'  ) {
			//Get BOOKED totals from db
			$this->db->select('workedby_2');
			$this->db->select_sum('Hours_2', 'Hours_2');
			$this->db->select_sum('NewSlides_2', 'NewSlides_2');
			$this->db->select_sum('EditedSlides_2', 'EditedSlides_2');
			//   $this->db->select_sum('Hours_3','Hours_3');
			//   $this->db->select_sum('NewSlides_3','NewSlides_3');
			//   $this->db->select_sum('EditedSlides_3','EditedSlides_3');
			$this->db->group_by('workedby_2');
			$this->db->where('Client', $clientb->Client);
			$this->db->where('has_multi_worked', 1);
			$this->db->where('DateOut >=', $StartDate);
			$this->db->where('DateOut <= ', $EndMonth);
			if ($Booked == 1) {
				$where = "(Status = 'COMPLETED' OR Status = 'BILLED' OR Status = 'PAID' OR Status='DISPUTED' OR Status = 'WAIVED' OR Status = 'MARKETING')";
				$this->db->where($where);
			} else if ($Booked == 2) {
				$this->db->where('Invoice', 'NOT BILLED');
				$this->db->where('Status', 'COMPLETED');
			}
			$this->db->where('Trash =', 0);
			$this->db->from('zowtrakentries');

			$querybooked_2 = $this->db->get();
			// print_r($querybooked_2->result_array());
			$this->db->select('workedby_3');
			$this->db->select_sum('Hours_3', 'Hours_3');
			$this->db->select_sum('NewSlides_3', 'NewSlides_3');
			$this->db->select_sum('EditedSlides_3', 'EditedSlides_3');
			$this->db->group_by('workedby_3');
			$this->db->where('Client', $clientb->Client);
			$this->db->where('has_multi_worked', 1);
			$this->db->where('DateOut >=', $StartDate);
			$this->db->where('DateOut <= ', $EndMonth);

			if ($Booked == 1) {
				$where = "(Status = 'COMPLETED' OR Status = 'BILLED' OR Status = 'PAID' OR Status='DISPUTED' OR Status = 'WAIVED' OR Status = 'MARKETING')";
				$this->db->where($where);
			} else if ($Booked == 2) {
				$this->db->where('Invoice', 'NOT BILLED');
				$this->db->where('Status', 'COMPLETED');
			}
			$this->db->where('Trash =', 0);
			$this->db->from('zowtrakentries');
			$querybooked_3 = $this->db->get();

			$make_new = array();
			$check_array = array();
			$array_data = array();
			// if ($clientb->Client == 'Philips CSA' && $Status == $Status) {
				foreach($querybooked->result_array() as $key => $newData){
					// print_r($newData);
					if (  is_numeric($newData[$Status]) && $newData[$Status] !=0) {
						$CI = get_instance();
						//$CI->load->model('Zt2016_limbo_model');
						$CI->load->model('Zt2016_users_model');
						$name= $CI->Zt2016_users_model->getsuer_name_by_id($newData[$Status]);
						$name = $name->fname;
					}else{
						$name = $newData[$Status];
					}
					// echo ucfirst($name);
					if(!in_array(ucfirst($name),$check_array)){
						$check_array[] = ucfirst($name);
						$newData[$Status] = ucfirst($name);;
						$array_data[] = $newData;
					}else{
						foreach($array_data as $key1 => $data){
							if(ucfirst($name) == $data[$Status]){
								$array_data[$key1]['Hours'] = $newData['Hours']+$data['Hours'];
								$array_data[$key1]['Hours_2_main'] = $newData['Hours_2_main']+$data['Hours_2_main'];
								$array_data[$key1]['Hours_3_main'] = $newData['Hours_3_main']+$data['Hours_3_main'];
								$array_data[$key1]['NewSlides'] = $newData['NewSlides']+$data['NewSlides'];
								$array_data[$key1]['NewSlides_2_main'] = $newData['NewSlides_2_main']+$data['NewSlides_2_main'];
								$array_data[$key1]['NewSlides_3_main'] = $newData['NewSlides_3_main']+$data['NewSlides_3_main'];
								$array_data[$key1]['EditedSlides'] = $newData['EditedSlides']+$data['EditedSlides'];
								$array_data[$key1]['EditedSlides_2_main'] = $newData['EditedSlides_2_main']+$data['EditedSlides_2_main'];
								$array_data[$key1]['EditedSlides_3_main'] = $newData['EditedSlides_3_main']+$data['EditedSlides_3_main'];
							}
						}
					}	
				}
			// print_r($array_data);

			// die;
			foreach ($array_data as $key => $arr_val) {
				$make_new[$key]=$arr_val;
				$worked_2_arr = json_decode(json_encode($querybooked_2->result_array()), true);
				if (!empty($worked_2_arr) ) {
				

					// if (array_key_exists($key, $worked_2_arr)) {
						// echo $key.'<br>';;
						foreach($worked_2_arr as $w_2key=>$w_2val){
							
							if (  is_numeric($w_2val['workedby_2']) && $w_2val['workedby_2'] !=0) {
								$CI = get_instance();
								//$CI->load->model('Zt2016_limbo_model');
								$CI->load->model('Zt2016_users_model');
								$name= $CI->Zt2016_users_model->getsuer_name_by_id($w_2val['workedby_2']);
								$name = $name->fname;
							}else{
								$name = $w_2val['workedby_2'];
							}
							if (ucfirst($arr_val['WorkedBy']) == ucfirst($name)) {
								$make_new[$key] = array_merge($arr_val, $w_2val);
							}else{
								  if($name != 0){
								$w_2id =ucfirst($name);
								$have_row =0;
								foreach ($querybooked->result_array() as $temp_key => $temp_arr_val) {
									if($w_2id == ucfirst($temp_arr_val['WorkedBy'])){
										$have_row = 1;
									}
								}
								if($have_row == 0){
								$custom_arr = array(
								'WorkedBy' => $w_2id,
								'Hours' => 0,
								'Hours_2_main' => 0,
								'Hours_3_main' => 0,
								'NewSlides_2_main' => 0,
								'NewSlides_3_main' => 0,
								'NewSlides_2_main' => 0,
								'EditedSlides' => 0,
								'EditedSlides_2_main' => 0,
								'EditedSlides_3_main' => 0,

							);
							$worked_arr_hwe_cus = array_merge($custom_arr, $w_2val);
							$make_new[] = $worked_arr_hwe_cus;
								}
							}
							}
						}
					}
					$worked_3_arr = json_decode(json_encode($querybooked_3->result_array()), true);
					if (!empty($worked_3_arr) ) {
							foreach($worked_3_arr as $w_3key=>$w_3val){
								if (  is_numeric($w_3val['workedby_3']) && $w_3val['workedby_3'] != 0) {
									$CI = get_instance();
									$CI->load->model('Zt2016_users_model');
									$name= $CI->Zt2016_users_model->getsuer_name_by_id($w_3val['workedby_3']);
									$name = $name->fname;
								}else{
									$name = $w_3val['workedby_3'];
								}
								if (ucfirst($arr_val['WorkedBy']) == ucfirst($name)) {
									$make_new[$key] = array_merge($arr_val, $w_3val);
								}else{
								    if($name != 0){
									$w_3id =ucfirst($name);
									$have_row =0;
									foreach ($querybooked->result_array() as $temp_key => $temp_arr_val) {
										if($w_3id == ucfirst($temp_arr_val['WorkedBy'])){
											$have_row = 1;
										}
									}
									if($have_row == 0){
									$custom_arr = array(
									'WorkedBy' => $w_3id,
									'Hours' => 0,
									'Hours_2_main' => 0,
									'Hours_3_main' => 0,
									'NewSlides_2_main' => 0,
									'NewSlides_3_main' => 0,
									'NewSlides_2_main' => 0,
									'EditedSlides' => 0,
									'EditedSlides_2_main' => 0,
									'EditedSlides_3_main' => 0,
	
								);
								$worked_arr_hwe_cus = array_merge($custom_arr, $w_3val);
								$make_new[] = $worked_arr_hwe_cus;
									}
								}
								}
							}
						}
						// if ($arr_val['WorkedBy'] == $worked_2_arr[$key]['workedby_2']) {
						// 	$make_new[$key] = array_merge($arr_val, $worked_2_arr[$key]);
						// } else {
						// 	if(!empty($worked_2_arr[$key]['workedby_2'])){
						// 	$make_new[$key] = $arr_val;
						// 	$custom_arr = array(
						// 		'WorkedBy' => $worked_2_arr[$key]['workedby_2'],
						// 		'Hours' => 0,
						// 		'Hours_2_main' => 0,
						// 		'Hours_3_main' => 0,
						// 		'NewSlides_2_main' => 0,
						// 		'NewSlides_3_main' => 0,
						// 		'NewSlides_2_main' => 0,
						// 		'EditedSlides' => 0,
						// 		'EditedSlides_2_main' => 0,
						// 		'EditedSlides_3_main' => 0,

						// 	);
						// 	$worked_arr_hwe_cus = array_merge($custom_arr, $worked_2_arr[$key]);
						// 	$make_new[] = $worked_arr_hwe_cus;
						// }
						// }
					
				// } else {
				// 	$make_new[] = $arr_val;
				// }
			

			// foreach ($querybooked->result_array() as $key => $arr_val) {
			// 	if (!empty($worked_3_arr)) {
			// 		if (array_key_exists($key, $worked_3_arr)) {
			// 			if ($arr_val['WorkedBy'] == $worked_3_arr[$key]['workedby_2']) {
			// 				$make_new[$key] = array_merge($arr_val, $worked_3_arr[$key]);
			// 			} else {
			// 				if(!empty($worked_3_arr[$key]['workedby_3'])){
			// 				$make_new[$key] = $arr_val;
			// 				$custom_arr = array(
			// 					'WorkedBy' => $worked_3_arr[$key]['workedby_3'],
			// 					'Hours' => 0,
			// 					'Hours_2_main' => 0,
			// 					'Hours_3_main' => 0,
			// 					'NewSlides_2_main' => 0,
			// 					'NewSlides_3_main' => 0,
			// 					'NewSlides_2_main' => 0,
			// 					'EditedSlides' => 0,
			// 					'EditedSlides_2_main' => 0,
			// 					'EditedSlides_3_main' => 0,

			// 				);
			// 				$worked_arr_hwe_cus = array_merge($custom_arr, $worked_3_arr[$key]);
			// 				$make_new[] = $worked_arr_hwe_cus;
			// 			}
			// 		}
			// 		}
			// 	} else {
			// 		$make_new[] = $arr_val;
			// 	}
			// }
			}
			$check_array = array();
			$array_data = array();
			// if ($clientb->Client == 'Philips CSA' && $Status == $Status) {
				foreach($make_new as $key => $newData){
					// print_r($newData);
					if (  is_numeric($newData[$Status]) && $newData[$Status] !=0) {
						$CI = get_instance();
						//$CI->load->model('Zt2016_limbo_model');
						$CI->load->model('Zt2016_users_model');
						$name= $CI->Zt2016_users_model->getsuer_name_by_id($newData[$Status]);
						$name = $name->fname;
					}else{
						$name = $newData[$Status];
					}
					// echo ucfirst($name);
					if(!in_array(ucfirst($name),$check_array)){
						$check_array[] = ucfirst($name);
						$newData[$Status] = ucfirst($name);;
						$array_data[] = $newData;
					}else{
						foreach($array_data as $key1 => $data){
							if(ucfirst($name) == $data[$Status]){
								$array_data[$key1]['Hours'] = $newData['Hours']+$data['Hours'];
								$array_data[$key1]['Hours_2_main'] = $newData['Hours_2_main']+$data['Hours_2_main'];
								$array_data[$key1]['Hours_3_main'] = $newData['Hours_3_main']+$data['Hours_3_main'];
								$array_data[$key1]['NewSlides'] = $newData['NewSlides']+$data['NewSlides'];
								$array_data[$key1]['NewSlides_2_main'] = $newData['NewSlides_2_main']+$data['NewSlides_2_main'];
								$array_data[$key1]['NewSlides_3_main'] = $newData['NewSlides_3_main']+$data['NewSlides_3_main'];
								$array_data[$key1]['EditedSlides'] = $newData['EditedSlides']+$data['EditedSlides'];
								$array_data[$key1]['EditedSlides_2_main'] = $newData['EditedSlides_2_main']+$data['EditedSlides_2_main'];
								$array_data[$key1]['EditedSlides_3_main'] = $newData['EditedSlides_3_main']+$data['EditedSlides_3_main'];
							}
						}
					}	
				}

			// 	echo "<pre>";
			// print_r($array_data);
			// die('testtttttttttttttttttttttttttttttttt');
			return json_decode(json_encode($array_data));
			// return json_decode(json_encode($array_data));
		} else {
			$check_array = array();
			$array_data = array();
			// if ($clientb->Client == 'Philips CSA' && $Status == $Status) {
				foreach($querybooked->result_array() as $key => $newData){
					// print_r($newData);
					if (  is_numeric($newData[$Status]) && $newData[$Status] !=0) {
						$CI = get_instance();
						//$CI->load->model('Zt2016_limbo_model');
						$CI->load->model('Zt2016_users_model');
						$name= $CI->Zt2016_users_model->getsuer_name_by_id($newData[$Status]);
						$name = $name->fname;
					}else{
						$name = $newData[$Status];
					}
					// echo ucfirst($name);
					if(!in_array(ucfirst($name),$check_array)){
						$check_array[] = ucfirst($name);
						$newData[$Status] = ucfirst($name);;
						$array_data[] = $newData;
					}else{
						foreach($array_data as $key1 => $data){
							if(ucfirst($name) == $data[$Status]){
								$array_data[$key1]['Hours'] = $newData['Hours']+$data['Hours'];
								$array_data[$key1]['Hours_2_main'] = $newData['Hours_2_main']+$data['Hours_2_main'];
								$array_data[$key1]['Hours_3_main'] = $newData['Hours_3_main']+$data['Hours_3_main'];
								$array_data[$key1]['NewSlides'] = $newData['NewSlides']+$data['NewSlides'];
								$array_data[$key1]['NewSlides_2_main'] = $newData['NewSlides_2_main']+$data['NewSlides_2_main'];
								$array_data[$key1]['NewSlides_3_main'] = $newData['NewSlides_3_main']+$data['NewSlides_3_main'];
								$array_data[$key1]['EditedSlides'] = $newData['EditedSlides']+$data['EditedSlides'];
								$array_data[$key1]['EditedSlides_2_main'] = $newData['EditedSlides_2_main']+$data['EditedSlides_2_main'];
								$array_data[$key1]['EditedSlides_3_main'] = $newData['EditedSlides_3_main']+$data['EditedSlides_3_main'];
							}
						}
					}	
				}
				// if($Status == 'ProofedBy'){
				// 	echo "<pre>";
				// print_r($array_data);
				// }
				
				
			// }

			// if(empty($array_data)){
				// return $querybooked->result_array();
			// }else{
				return $array_data;

			// }
// print_r($querybooked->result_array());
// echo '--------------start---------------';
// print_r($array_data);
// echo '--------------end---------------';
		}
	}
	/**
	 * _getClientTotalsByDate gives billed totals for a single client and status between datess
	 *
	 * @param array $required
	 * @param array $data
	 * @return bool
	 */
	function  _getClientBilledTotalsByDate($StartDate, $EndMonth, $clientb, $Status)
	{
		//Get BOOKED totals from db
		$this->db->select($Status);
		$this->db->select_sum('Hours', 'Hours');
		$this->db->select_sum('NewSlides', 'NewSlides');
		$this->db->select_sum('EditedSlides', 'EditedSlides');
		$this->db->select_sum('InvoiceEntryTotal', 'InvoiceEntryTotal');
		$this->db->group_by($Status);
		$this->db->where('Client', $clientb->Client);
		$this->db->where('DateOut >=', $StartDate);
		$this->db->where('DateOut <= ', $EndMonth);
		$this->db->where('Invoice!=', 'NOT BILLED');
		$this->db->where('Trash =', 0);
		$this->db->from('zowtrakentries');
		$querybooked = $this->db->get();
		return $querybooked->result_array();
	}
	/**
	 * _getPartnersByDate gives totals for partners between dates
	 *
	 * @param array $required
	 * @param array $data
	 * @return bool
	 */
	function  _getPartnersByDate($StartDate, $EndMonth)
	{
		//Get BOOKED totals from db


		$P1 = 'SELECT ScheduledBy FROM zowtrakentries WHERE DateOut >="' . $StartDate . '" AND DateOut <="' . $EndMonth . '" AND Trash="0" AND (Status="COMPLETED" OR Status="BILLED" OR Status="PAID" OR Status="DISPUTED")';
		$P1 .= ' UNION SELECT WorkedBy FROM zowtrakentries WHERE DateOut >= "' . $StartDate . '" AND DateOut <="' . $EndMonth . '" AND Trash="0" AND (Status="COMPLETED" OR Status="BILLED" OR Status="PAID" OR Status="DISPUTED")';
		$P1 .= ' UNION SELECT WorkedBy_2 FROM zowtrakentries WHERE DateOut >= "' . $StartDate . '" AND DateOut <="' . $EndMonth . '" AND Trash="0" AND has_multi_worked=1 AND (Status="COMPLETED" OR Status="BILLED" OR Status="PAID" OR Status="DISPUTED")';
		$P1 .= ' UNION SELECT WorkedBy_3 FROM zowtrakentries WHERE DateOut >= "' . $StartDate . '" AND DateOut <="' . $EndMonth . '" AND Trash="0" AND has_multi_worked=1 AND (Status="COMPLETED" OR Status="BILLED" OR Status="PAID" OR Status="DISPUTED")';
		$P1 .= ' UNION SELECT ProofedBy FROM zowtrakentries WHERE DateOut >= "' . $StartDate . '" AND DateOut <="' . $EndMonth . '" AND Trash="0" AND (Status="COMPLETED" OR Status="BILLED" OR Status="PAID" OR Status="DISPUTED")';
		$P1 .= ' UNION SELECT CompletedBy FROM zowtrakentries WHERE DateOut >= "' . $StartDate . '" AND DateOut <="' . $EndMonth . '" AND Trash="0" AND (Status="COMPLETED" OR Status="BILLED" OR Status="PAID" OR Status="DISPUTED")';

		$querybooked = $this->db->query($P1);
		//echo  $querybooked->num_rows();
		// echo  $this->db->last_query();
		return $querybooked->result_array();
	}


	/**
	 * _required method returns false if the $data array does not contain all of the keys assigned by the $required array.
	 *
	 * @param array $required
	 * @param array $data
	 * @return bool
	 */
	function _required($required, $data)
	{
		foreach ($required as $field) if (!isset($data[$field])) return false;
		return true;
	}
}
