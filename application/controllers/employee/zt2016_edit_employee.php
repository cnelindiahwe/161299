<?php

class Zt2016_edit_employee extends MY_Controller {

	
	function index()
	{

		 
		$this->output->set_header("Last-Modified: " . gmdate( "D, j M Y H:i:s" ) . " GMT"); // Date in the past 
		$this->output->set_header('Expires: Thu, 01-Jan-70 00:00:01 GMT'); // always modified 
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1 
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0", FALSE); 
		$this->output->set_header("Pragma: no-cache"); 
		
		$this->load->library(array('session')); #flashdata
		$this->load->helper(array('form','url','general','userpermissions','zt2016_clients','zt2016_timezone'));
		
		$zowuser=_superuseronly(); 
		
		$templateData['ZOWuser']= _getCurrentUser();
		

		$employeeid=$this->uri->segment(3);

        $this->load->model('zt2016_employee_model', '', TRUE);
		//$GroupsData = $this->zt2016_groups_model->GetUser($options = array('Trash'=>'0','sortBy'=>'GroupName','sortDirection'=>'ASC	'));
		$employeeData = $this->zt2016_employee_model->getemployeedata($options = array('employeeid'=> $employeeid));
        
        $this->load->model('zt2016_users_model', '', TRUE);
		//$GroupsData = $this->zt2016_groups_model->GetUser($options = array('Trash'=>'0','sortBy'=>'GroupName','sortDirection'=>'ASC	'));
		$UsersData = $this->zt2016_users_model->GetUser($options = array('user_id'=> $employeeid));

        $BasicCountriesList = array("","Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China, People's Republic of", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");
		
		$CountriesList= array();
		foreach($BasicCountriesList  as $country){
		           $CountriesList [$country]=$country;
		}		

		$templateData['title'] = 'Edit Employee Information';
		$templateData['ZOWuser']=_getCurrentUser();
		$templateData['sidebar_content']='sidebar';
		$templateData['main_content'] =$this-> _create_client_page($UsersData,$CountriesList,$employeeData,$templateData['ZOWuser']); 

		$this->load->view('admin_temp/main_temp',$templateData);

	}
	

	// ################## display clients info ##################	
	function _create_client_page($UserDetails,$CountriesList,$employeeData,$ZOWuser)
	{
					
			// print_r($employeeData);
            // die;	
        $basicData = $employeeData['basicData'];
        $familydata = $employeeData['familydata'];
        $education = $employeeData['education'];
        $experience = $employeeData['experience'];
		$page_content = '<div class="page_content">
        <form action="'.base_url().'employee/zt2016_employee_data" id="client-information-form" method="post" accept-charset="utf-8">
            <div id="client_info_panel" class="panel panel-default" style="margin-top: 2em;">
                <div class="panel-heading">
                    <h3 class="panel-title">Edit information for  '.$UserDetails->fname.' '.$UserDetails->lname.'<small> ( ID '.$UserDetails->user_id.' )</small></h3>
                    <p class="top-buffer-10">
                        <input type="submit" name="submit" value="Update Data" class="submitButton btn btn-success btn-sm" />
                        <a href="'.base_url().'employee/zt2016_employee_profile/'.$UserDetails->user_id.'" class="btn btn-info btn-sm">Cancel</a>
                      
                    </p>
                </div>
                <div class="panel-body">
                    <div class="col-sm-4 col-sm-12">
                        <div class="item-group">
                            <div class="col-sm-12"><h5 class="text-uppercase text-primary basic">Personal Informations</h5></div>
                            <div class="col-sm-12">
                            <label>Birth Date</label>
                                <div class="cal-icon">
                                <input class="form-control datetimepicker" type="text" name="dob" value="'.$basicData->dob.'">
                                <input class="form-control" type="hidden" name="employeeid" value="'.$UserDetails->user_id.'">

                                </div>
                            </div>
                            <div class="col-sm-12">
                            <label>Phone:</label>
                                <div class="cal-icon">
                                <input class="form-control" type="text" name="phone" value="'.$UserDetails->phone.'">

                                </div>
                            </div>
                            <div class="col-sm-12">
                                <label>Marital status</label>
                            
                                <select name="married" class="select form-control">';
                                $maleselect = '';
                                $femaleselect = '';
                                if($basicData->gender== 'male'){
                                    $maleselect = 'selected';
                                }
                                else if($basicData->gender== 'female'){
                                    $femaleselect = 'selected';
                                }
                                $page_content.='
                                    <option value="married" '.$maleselect.'>Married</option>
                                    <option value="unmarried" '.$femaleselect.'>Unmarried</option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-sm-12">
                                <label>Gender</label>
                                <select name="gender" class="select form-control">';
                                $maleselect = '';
                                $femaleselect = '';
                                if($basicData->gender== 'male'){
                                    $maleselect = 'selected';
                                }
                                else if($basicData->gender== 'female'){
                                    $femaleselect = 'selected';
                                }
                                $page_content.='
                                    <option value="male" '.$maleselect.'>Male</option>
                                    <option value="female" '.$femaleselect.'>Female</option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-sm-12">
                                <div class="form-group">
                                    <label>State</label>
                                    <input type="text" class="form-control" value="New York" name="state" value="'.$basicData->state.'">
                                </div>
                            </div>
                            <div class="col-sm-12">
                            <div class="form-group">
                                <label>Address</label>
                                <textarea type="text" class="form-control" name="address">'.$basicData->address.'</textarea>
                            </div>
                            </div>
                        
                            <div class="col-sm-12">
                                <label for="Country">Country:</label>
                                <select name="Country" class="form-control" required="yes">';
                                foreach($CountriesList as $key => $country){
                                    if($country == $basicData->Country){
                                        $page_content.='
                                        <option value="'.$key.'" selected>'.$country.'</option>';
                                    }
                                    else{
                                        $page_content.='
                                        <option value="'.$key.'">'.$country.'</option>';
                                    }
                                   
                                }
                                $page_content.='
                                </select>
                            </div>
                        </div>
                        <div class="item-group">
                            <div class="col-sm-12">
                                <label for="Religion">Religion</label>
                                <input type="text" name="Religion" id="Religion" size="25" class="form-control" value="'.$basicData->Religion.'"/>
                            </div>

        
                            <div class="col-sm-12">
                                <label for="ZIPCode">ZipCode:</label>
                                <input type="text" name="ZIPCode" value="499641" id="ZIPCode" size="25" class="form-control" value="'.$basicData->ZIPCode.'" />
                            </div>
                            <div class="col-sm-12">
                                <label for="City">City:</label>
                                <input type="text" name="City" value="Singapore" id="City" size="25" class="form-control" value="'.$basicData->City.'"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-4 col-sm-12">
                        <div class="item-group">
                            <div class="col-sm-12"><h5 class="text-uppercase text-primary pricing">Primary Contact</h5></div>
                            <div class="col-sm-6 col-sm-12">
                                <label for="PrimaryName">Name:</label>
                                <input type="text" name="PrimaryName"  id="PrimaryName" size="3" class="form-control" required="true" value="'.$basicData->PrimaryName.'"/>

                            </div>
                            <div class="col-sm-6 col-sm-12">
                                <label for="PrimaryRelationship">Relationship:</label>
                                <input type="text" name="PrimaryRelationship"  id="PrimaryRelationship" size="3" class="form-control" required="true" value="'.$basicData->PrimaryRelationship.'"/>

                            </div>
                            <div class="col-sm-6 col-sm-12">
                                <label for="PrimaryPhone">Phone:</label>
                                <input type="text" name="PrimaryPhone"  id="PrimaryPhone" size="3" class="form-control" required="true" value="'.$basicData->PrimaryPhone.'"/>

                            </div>
                        </div>
                        <div class="item-group">
                            <div class="col-sm-12"><h5 class="text-uppercase text-primary pricing">Secondry Contact</h5></div>
                            <div class="col-sm-6 col-sm-12">
                                <label for="SecondryName">Name:</label>
                                <input type="text" name="SecondryName" id="SecondryName" size="3" class="form-control" required="true" value="'.$basicData->SecondryName.'"/>

                            </div>
                            <div class="col-sm-6 col-sm-12">
                                <label for="SecondryRelationship">Relationship:</label>
                                <input type="text" name="SecondryRelationship"  id="SecondryRelationship" size="3" class="form-control" required="true" value="'.$basicData->SecondryRelationship.'"/>

                            </div>
                            <div class="col-sm-6 col-sm-12">
                                <label for="SecondryPhone">Phone:</label>
                                <input type="text" name="SecondryPhone" id="SecondryPhone" size="3" class="form-control" required="true" value="'.$basicData->SecondryPhone.'"/>

                            </div>
                        </div>
                        
                       
                    </div>
                    <div class="col-sm-4 col-sm-12">
                        <div class="item-group">
                            <div class="col-sm-12"><h5 class="text-uppercase text-primary discounts">Bank information</h5></div>
                            <div class="col-sm-6 col-sm-12">
                                <label for="BankName">Bank Name</label>
                                <input type="text" name="BankName" id="BankName" size="3" class="form-control" value="'.$basicData->BankName.'"/>
                            </div>
                            <div class="col-sm-6 col-sm-12">
                                <label for="AccountNumber">Account Number</label>
                                <input type="text" name="AccountNumber" id="AccountNumber" size="3" class="form-control" value="'.$basicData->AccountNumber.'" />
                            </div>
                            <div class="col-sm-6 col-sm-12">
                                <label for="IFSCCode">IFSC Code:</label>
                                <input type="text" name="IFSCCode" id="IFSCCode" size="3" class="form-control" value="'.$basicData->IFSCCode.'" />
                            </div>
                            <div class="col-sm-6 col-sm-12">
                                <label for="PANNO">PAN NO:</label>
                                <input type="text" name="PANNO" id="PANNO" size="3" class="form-control" value="'.$basicData->PANNO.'" />
                            </div>
                        
                        </div>
                    </div>
                    <div class="col-sm-12 col-sm-12">
                        <div class="item-group family">
                    
                            <div class="col-sm-12"><h5 class="text-uppercase text-primary pricing">Family Informations</h5></div>';
                            if($familydata){
                                $index = 1;
                                foreach($familydata as $data){
                                    // print_r($data);
                                    // die;
                                    $page_content.='
                                    <div class="family-field">
                                        <div class="col-sm-3 col-sm-12">
                                            <label for="familyName">Name:</label>
                                            <input type="text" name="familyName[]"  id=familyName" size="3" class="form-control" value="'.$data->familyName.'" />
                                            <input type="hidden" name="id[]"  id=familyName" size="3" class="form-control" value="'.$data->id.'" />
        
                                        </div>
                                        <div class="col-sm-3 col-sm-12">
                                            <label for="familyRelationship">Relationship:</label>
                                            <input type="text" name="familyRelationship[]"  id="familyRelationship" size="3" class="form-control" value="'.$data->familyRelationship.'" />
        
                                        </div>
                                        <div class="col-sm-2 col-sm-12">
                                            <label for="familydob">Date of Birth :</label>
                                            <input type="text" name="familydob[]"  id="familydob" size="3" class="form-control floating datetimepicker" value="'.$data->familydob.'" />
        
                                        </div>
                                        <div class="col-sm-2 col-sm-12">
                                            <label for="familyPhone">Phone:</label>
                                            <input type="text" name="familyPhone[]"  id="familyPhone" size="3" class="form-control" value="'.$data->familyPhone.'" />
        
                                        </div>
                                        <div class="col-sm-2 col-sm-12 item-group d-flex" style="height:60px;">
                                            
                                            <div class="add-more mt-auto">';
                                            if($index == 1){
                                                $page_content.='
                                                <a href="javascript:void(0);"><i class="fa fa-plus-circle"></i> Add More</a>';
                                            }
                                            else{
                                                $page_content.='<a href="javascript:void(0)" class="text-danger font-18 remove-felid" title="Remove"><i class="fa fa-trash-o"></i></a>';
                                            }
                                            $index++;
                                

                                            $page_content.='
                                                
                                            </div>
        
                                        </div></div>';
                                }
                            }
                            else{

                            $page_content.='
                            <div class="family-field">
                                <div class="col-sm-3 col-sm-12">
                                    <label for="familyName">Name:</label>
                                    <input type="text" name="familyName[]"  id=familyName" size="3" class="form-control" />

                                </div>
                                <div class="col-sm-3 col-sm-12">
                                    <label for="familyRelationship">Relationship:</label>
                                    <input type="text" name="familyRelationship[]"  id="familyRelationship" size="3" class="form-control" />

                                </div>
                                <div class="col-sm-2 col-sm-12">
                                    <label for="familydob">Date of Birth :</label>
                                    <input type="text" name="familydob[]"  id="familydob" size="3" class="form-control floating datetimepicker" />

                                </div>
                                <div class="col-sm-2 col-sm-12">
                                    <label for="familyPhone">Phone:</label>
                                    <input type="text" name="familyPhone[]"  id="familyPhone" size="3" class="form-control" />

                                </div>
                                <div class="col-sm-2 col-sm-12 item-group d-flex" style="height:60px;">
                                    
                                    <div class="add-more mt-auto">
                                        <a href="javascript:void(0);"><i class="fa fa-plus-circle"></i> Add More</a>
                                    </div>

                                </div></div>';
                            }
                                $page_content.='
                            
                        </div>
                    </div>
                    <div class="col-sm-12 col-sm-12">
                        <div class="item-group education">
                            <div class="col-sm-12"><h5 class="text-uppercase text-primary production">Education Informations</h5></div>';
                           if($education){
                                foreach($education as $data){
                                $page_content.='    
                                <div class="education-field">
                                    <div class="col-sm-2 col-sm-12">
                                        <label for="Institution">Institution:</label>
                                        <input type="text" name="Institution[]"  id=Institution" size="3" class="form-control" value="'.$data->Institution.'" />
                                        <input type="hidden" name="id[]"  id=Institution" size="3" class="form-control" value="'.$data->id.'" />

                                    </div>
                                    <div class="col-sm-2 col-sm-12">
                                        <label for="Subject">Subject:</label>
                                        <input type="text" name="Subject[]"  id="Subject" size="3" class="form-control" value="'.$data->Subject.'" />

                                    </div>
                                    <div class="col-sm-2 col-sm-12">
                                        <label for="familydob">Year :</label>
                                        <input type="text" name="year[]"  id="year" size="3" class="form-control" value="'.$data->year.'" />

                                    </div>
                                    <div class="col-sm-2 col-sm-12">
                                        <label for="Degree">Degree :</label>
                                        <input type="text" name="Degree[]"  id="Degree" size="3" class="form-control" value="'.$data->Degree.'"  />

                                    </div>
                                    <div class="col-sm-2 col-sm-12">
                                        <label for="Grade">Grade :</label>
                                        <input type="text" name="Grade[]"  id="Grade" size="3" class="form-control" value="'.$data->Grade.'" />

                                    </div>
                                
                                    <div class="col-sm-2 col-sm-12 d-flex" style="height:61px;">
                                
                                        <div class="add-more float-right mt-auto">';
                                    
                                        if($index == 1){
                                            $page_content.='
                                            <a href="javascript:void(0);"><i class="fa fa-plus-circle"></i> Add More</a>';
                                        }
                                        else{
                                            $page_content.='<a href="javascript:void(0)" class="text-danger font-18 remove-felid" title="Remove"><i class="fa fa-trash-o"></i></a>';
                                        }
                                        $index++;
                                        $page_content.='
                                        </div>

                                    </div>
                                
                                </div>';
                               
                                }
                           }
                           else{
                            $page_content.='
                            
                            <div class="education-field">
                                <div class="col-sm-2 col-sm-12">
                                    <label for="Institution">Institution:</label>
                                    <input type="text" name="Institution[]"  id=Institution" size="3" class="form-control" />

                                </div>
                                <div class="col-sm-2 col-sm-12">
                                    <label for="Subject">Subject:</label>
                                    <input type="text" name="Subject[]"  id="Subject" size="3" class="form-control" />

                                </div>
                                <div class="col-sm-2 col-sm-12">
                                    <label for="familydob">Year :</label>
                                    <input type="text" name="year[]"  id="year" size="3" class="form-control" />

                                </div>
                                <div class="col-sm-2 col-sm-12">
                                    <label for="Degree">Degree :</label>
                                    <input type="text" name="Degree[]"  id="Degree" size="3" class="form-control"  />

                                </div>
                                <div class="col-sm-2 col-sm-12">
                                    <label for="Grade">Grade :</label>
                                    <input type="text" name="Grade[]"  id="Grade" size="3" class="form-control"  />

                                </div>
                            
                                <div class="col-sm-2 col-sm-12 d-flex" style="height:61px;">
                            
                                    <div class="add-more float-right mt-auto">
                                        <a href="javascript:void(0);"><i class="fa fa-plus-circle"></i> Add More</a>
                                    </div>

                                </div>
                              
                            </div>';
                           }
                            $page_content.='

                        </div>
                    </div>
                    <div class="col-sm-12 col-sm-12">
                        <div class="item-group experience">
                            <div class="col-sm-12"><h5 class="text-uppercase text-primary other">Experience</h5></div>';
                            if($experience){
                                foreach($experience as $data){
                                    $page_content.='
                                <div class="experience-field">
                                    <div class="col-sm-2 col-sm-12">
                                        <label for="CompanyName">Company Name:</label>
                                        <input type="text" name="CompanyName[]"  id=CompanyName" size="3" class="form-control" value="'.$data->CompanyName.'" />
                                        <input type="hidden" name="id[]"  id=id" size="3" class="form-control" value="'.$data->id.'" />

                                    </div>
                                    <div class="col-sm-2 col-sm-12">
                                        <label for="Location">Location:</label>
                                        <input type="text" name="Location[]"  id="Location" size="3" class="form-control" value="'.$data->Location.'" />

                                    </div>
                                    <div class="col-sm-2 col-sm-12">
                                        <label for="JobPosition">Job Position :</label>
                                        <input type="text" name="JobPosition[]"  id="JobPosition" size="3" class="form-control" value="'.$data->JobPosition.'" />

                                    </div>
                                    <div class="col-sm-2 col-sm-12">
                                        <label for="PeriodFrom">Period From :</label>
                                        <input type="text" name="PeriodFrom[]"  id="PeriodFrom" size="3" class="form-control floating datetimepicker" value="'.$data->PeriodFrom.'" />

                                    </div>
                                    <div class="col-sm-2 col-sm-12">
                                        <label for="PeriodTo">Period To :</label>
                                        <input type="datetime" name="PeriodTo[]"  id="PeriodTo" size="3" class="form-control floating datetimepicker" value="'.$data->PeriodTo.'" />

                                    </div>
                            
                                    <div class="col-sm-2 col-sm-12 d-flex" style="height:61px;">
                                
                                        <div class="add-more float-right mt-auto">';

                                        if($index == 1){
                                            $page_content.='
                                            <a href="javascript:void(0);"><i class="fa fa-plus-circle"></i> Add More</a>';
                                        }
                                        else{
                                            $page_content.='<a href="javascript:void(0)" class="text-danger font-18 remove-felid" title="Remove"><i class="fa fa-trash-o"></i></a>';
                                        }
                                        $index++;
                                        $page_content.='
                                        </div>

                                    </div>
                                </div>';
                                }
                            }
                            else{
                            $page_content.='
                            <div class="experience-field">
                            <div class="col-sm-2 col-sm-12">
                                <label for="CompanyName">Company Name:</label>
                                <input type="text" name="CompanyName[]"  id=CompanyName" size="3" class="form-control" />

                            </div>
                            <div class="col-sm-2 col-sm-12">
                                <label for="Location">Location:</label>
                                <input type="text" name="Location[]"  id="Location" size="3" class="form-control" />

                            </div>
                            <div class="col-sm-2 col-sm-12">
                                <label for="JobPosition">Job Position :</label>
                                <input type="text" name="JobPosition[]"  id="JobPosition" size="3" class="form-control"  />

                            </div>
                            <div class="col-sm-2 col-sm-12">
                                <label for="PeriodFrom">Period From :</label>
                                <input type="text" name="PeriodFrom[]"  id="PeriodFrom" size="3" class="form-control floating datetimepicker"  />

                            </div>
                            <div class="col-sm-2 col-sm-12">
                                <label for="PeriodTo">Period To :</label>
                                <input type="datetime" name="PeriodTo[]"  id="PeriodTo" size="3" class="form-control floating datetimepicker"  />

                            </div>
                    
                            <div class="col-sm-2 col-sm-12 d-flex" style="height:61px;">
                         
                                <div class="add-more float-right mt-auto">
                                    <a href="javascript:void(0);"><i class="fa fa-plus-circle"></i> Add More</a>
                                </div>

                            </div>
                            </div>';
                            }
                            $page_content.='
                        </div>
                    </div>
                    <!-- // class="panel-body" -->
                </div>
                <!-- // class="page_content" -->
            </div>
        </form>
        <!-- /Page Content -->
    </div>
    ';

		return $page_content;


	
	}	



}

/* End of file editclient.php */
/* Location: ./system/application/controllers/clients/editclient.php */
?>