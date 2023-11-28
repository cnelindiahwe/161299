<?php class Zowindia_expenses_model extends CI_Model {

################ Get client

function GetExpensesEntrie($options = array())
{
    

    $qualificationArray = array('item','Reference','purchaseDate','Category','amount','paidBy','status','attch','currency','Remark','paymentDate','paymentAmount');
    $startDate = $options['startDate'];
    $endDate = $options['endDate'];
    foreach($qualificationArray as $qualifier)
	{
		if(isset($options[$qualifier]) && !empty($options[$qualifier])) $this->db->where($qualifier, $options[$qualifier]);
	}
    if (!empty($startDate) && !empty($endDate)) {
        $this->db->where('purchaseDate >=', $startDate);
        $this->db->where('purchaseDate <=', $endDate);
    }
    $query = $this->db->get('zowindiaexpenses');
    // echo $this->db->last_query();
    // die;
    if($query->num_rows() == 0) return false;
    // print_r($query->result());
    // die;

    return $query->result();
}



################ Update client

function UpdateExpensesEntrie($options = array())
{
   
// required values
    if(!$this->_required(array('id'), $options)) return false;

    // qualification (make sure that we're not allowing the site to insert data that it shouldn't)
    $qualificationArray = array('item','Reference','purchaseDate','Category','amount','paidBy','status','currency','Remark','paymentDate','attch','paymentAmount');

    foreach($qualificationArray as $qualifier)
    {

        
        if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
    }

   // Execute the query
    $this->db->where('id', $options['id']);
    $this->db->update('zowindiaexpenses');

   

    return $this->db->affected_rows();
    }

################ add client

function addexpenses($options =  array())
{
    
    // qualification (make sure that we're not allowing the site to insert data that it shouldn't)
    $qualificationArray = array('item','Reference','purchaseDate','Category','amount','paidBy','status','attch','Remark','currency','paymentDate','paymentAmount');

    foreach($qualificationArray as $qualifier)
    {
        if(isset($options[$qualifier])) $this->db->set($qualifier, $options[$qualifier]);
    }
    
    // Execute the query
    $this->db->insert('zowindiaexpenses');
    
     // Return the ID of the inserted row, or false if the row could not be inserted
    return $this->db->insert_id();

}

function  DeleteExpensesEntrie($options =  array())
{
	if(!$this->_required(array('id'), $options)) return false;

	$this->db->where('id',$options['id']);
	$this->db->delete('zowindiaexpenses');
	// Return the number of rows updated, or false if the row could not be inserted
	return $this->db->affected_rows();


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