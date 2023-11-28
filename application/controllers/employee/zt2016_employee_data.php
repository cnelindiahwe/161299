<?php

class Zt2016_employee_data extends MY_Controller {

	
	function index()
	{
        $this->load->model('zt2016_employee_model');

	    $data =$this->input->post();

        if(isset($data['submit'])){
            $employeeData = $this->zt2016_employee_model->getemployeedata($options = array('employeeid'=> $data['employeeid']));
            if($employeeData){
                $res=$this->zt2016_employee_model->updateemployeedata($data);
                if($res){
                    $Message="Updated employee details";  
                    $this->session->set_flashdata('SuccessMessage',$Message);
                }
                redirect('employee/zt2016_employee_profile/'.$data['employeeid']);
            }else{
                $res=$this->zt2016_employee_model->addemployeedata($data);
                if($res){
                    $Message="Updated employee details";  
                    $this->session->set_flashdata('SuccessMessage',$Message);
                }
                redirect('employee/zt2016_employee_profile/'.$data['employeeid']);
            }
           

        }

    }
}