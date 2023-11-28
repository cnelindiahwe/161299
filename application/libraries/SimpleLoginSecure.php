<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('phpass-0.1/PasswordHash.php');

define('PHPASS_HASH_STRENGTH', 8);
define('PHPASS_HASH_PORTABLE', false);

/**
 * SimpleLoginSecure Class
 *
 * Makes authentication simple and secure.
 *
 * Simplelogin expects the following database setup. If you are not using 
 * this setup you may need to do some tweaking.
 *   
 * 
 *   CREATE TABLE `users` (
 *     `user_id` int(10) unsigned NOT NULL auto_increment,
 *     `user_email` varchar(255) NOT NULL default '',
 *     `user_pass` varchar(60) NOT NULL default '',
 *     `user_date` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Creation date',
 *     `user_modified` datetime NOT NULL default '0000-00-00 00:00:00',
 *     `user_last_login` datetime NULL default NULL,
 *     PRIMARY KEY  (`user_id`),
 *     UNIQUE KEY `user_email` (`user_email`),
 *   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 * 
 * @package   SimpleLoginSecure
 * @version   2.0
 * @author    Stéphane Bourzeix, Pixelmio <stephane[at]bourzeix.com>
 * @copyright Copyright (c) 2012, Stéphane Bourzeix
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 * @link      https://github.com/DaBourz/SimpleLoginSecure
 */
class SimpleLoginSecure
{
	var $CI;
	var $user_table = 'users';

	/**
	 * Create a user account
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function create( $dp='',$name='',$lname='', $user_email = '', $user_pass = '', $auto_login = true,$user_type=1,$visibility=1) 
	{
		$this->CI =& get_instance();
		


		//Make sure account info was sent
		if($user_email == '' OR $user_pass == '') {
			return 11;
		}
		
		//Check against user table
		$this->CI->db->where('user_email', $user_email); 
		$this->CI->db->where('status', 0); 
		$query = $this->CI->db->get_where($this->user_table);
		
		if ($query->num_rows() > 0) //user_email already exists
			return 12;

		//Hash user_pass using phpass
		// $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
		// $user_pass_hashed = $hasher->HashPassword($user_pass);
		$user_pass_hashed = md5($user_pass);

		//Insert account into the database
		$data = array(
					'fname' => $name,
					'lname' => $lname,
					'dp' => $dp,
					'user_email' => $user_email,
					'user_pass' => $user_pass_hashed,
					'user_type' => $user_type,
					'visibility' => $visibility,
					'user_date' => date('c'),
					'user_modified' => date('c'),
				);

		$this->CI->db->set($data); 

		if(!$this->CI->db->insert($this->user_table)) //There was a problem! 
			return 13;						
				
// 		if($auto_login)
// 			$this->login($user_email, $user_pass);
		
		return 20;
	}
	function update($option=array()) 
	{
		$this->CI =& get_instance();
		


		
		//Check against user table
		$this->CI->db->where('user_email', $option['user_email']); 
		$query = $this->CI->db->get_where($this->user_table);
		
		if ($query->num_rows() > 0){
		redirect('user/edit/'.$option['user_id']);
	}

		//Hash user_pass using phpass
		$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
		$user_pass_hashed = $hasher->HashPassword($option['user_pass']);
		//Insert account into the database
		$data = array(
					'user_email' => $option['user_email'],
					'user_pass' => $user_pass_hashed,
					'user_date' => date('c'),
					'user_modified' => date('c'),
				);

		$this->CI->db->set($data); 
		$this->CI->db->where('user_id',$option['user_id']);	
		if(!$this->CI->db->update($this->user_table)){
			return false;						
			
		}else{

			return true;
		}
				
		
		
	}
	/**
	 * Login and sets session variables
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	function login($user_email, $user_pass) 
	{
		$this->CI =& get_instance();
		// $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
		// $user_pass_hashed = $hasher->HashPassword('user_pass_hashed_12345');
		// print_r($user_pass_hashed);
		// die;
		if($user_email == '' OR $user_pass == '')
			return false;


		//Check if already logged in
		if($this->CI->session->userdata('user_email') == $user_email)
			return true;
		
		
		//Check against user table
		$this->CI->db->where('user_email', $user_email); 
		$this->CI->db->where('status', 0); 

		$query = $this->CI->db->get_where($this->user_table);

		
		if ($query->num_rows() > 0) 
		{
			$user_data = $query->row_array(); 

			$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
            
			if(!$hasher->CheckPassword($user_pass, $user_data['user_pass']))
				{
				// 	echo "no pass";
				die('sdfs');
					return false;
				}
			//Destroy old session
			$this->CI->session->sess_destroy();
			
			//Create a fresh, brand new session
			$this->CI->session->sess_create();

			$this->CI->db->simple_query('UPDATE ' . $this->user_table  . ' SET user_last_login = NOW() WHERE user_id = ' . $user_data['user_id']);

			//Set session data
			unset($user_data['user_pass']);
			$user_data['user_id'] = $user_data['user_id']; // for compatibility with Simplelogin
			$user_data['user'] = $user_data['user_email']; // for compatibility with Simplelogin
			$user_data['logged_in'] = true;
			$this->CI->session->set_userdata($user_data);
			
			return true;
		} 
		else 
		{
			return false;
		}	

	}

	/**
	 * Logout user
	 *
	 * @access	public
	 * @return	void
	 */
	function logout() {
		$this->CI =& get_instance();		

		$this->CI->session->sess_destroy();
	
	}

	/**
	 * Delete user
	 *
	 * @access	public
	 * @param integer
	 * @return	bool
	 */
	function delete() 
	{
		$this->CI =& get_instance();
		
		if(!is_numeric($user_id))
			return false;			
			
		return $this->CI->db->delete($this->user_table, array('user_id' => $user_id));
	}
	
}
?>