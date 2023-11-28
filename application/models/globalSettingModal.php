<?php class globalSettingModal extends CI_Model {

################ Get client

function GetGlobalSetting($options = array())
{
   
    $query = $this->db->get('zowtrakglobalsetting');
    
    if($query->num_rows() == 0) return false;

    return $query->result();
}



################ Update client

function UpdateGlobalSetting($options = array())
{

// required values
    if(!$this->_required(array('ID'), $options)) return false;

    // qualification (make sure that we're not allowing the site to insert data that it shouldn't)
    $qualificationArray = array('fromAddress','contactName','mobNumber','email','bankAccount','footer','emailBody','StandardEmail','ReminderEmail','SecondReminderEmail','subject','ccMailto','reminderSubject','secondReminderSubject','zowIndiafromAddress','zowIndiaBank','zowIndiaMobile','zowIndiafooter','toAddress');
    
    $query = $this->db->get('zowtrakglobalsetting');

    
    if($query->num_rows() == 0) return $this->AddGlobalSetting($options);
  
    foreach($qualificationArray as $qualifier)
    {
        if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
    }



   // Execute the query
    $this->db->where('ID', $options['ID']);
    $this->db->update('zowtrakglobalsetting');
    // Return the number of rows updated, or false if the row could not be inserted
    return $this->db->affected_rows();
    }

################ add client

function AddGlobalSetting($options =  array())
{
    
    // qualification (make sure that we're not allowing the site to insert data that it shouldn't)
    $qualificationArray = array('fromAddress','contactName','mobNumber','email','bankAccount','footer','emailBody','StandardEmail','ReminderEmail','SecondReminderEmail','subject','ccMailto','reminderSubject','secondReminderSubject','zowIndiafromAddress','zowIndiaBank','zowIndiaMobile','zowIndiafooter','toAddress');
    
    foreach($qualificationArray as $qualifier)
    {
        if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
    }
    
    // Execute the query
    $this->db->insert('zowtrakglobalsetting');
    
     // Return the ID of the inserted row, or false if the row could not be inserted
    return $this->db->insert_id();

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
    foreach($required as $field) if(!isset($data[$field])) return false;
    return true;
}


}

?>