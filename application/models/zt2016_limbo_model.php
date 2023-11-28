<?php class Zt2016_limbo_model extends CI_Model {

	################ Get client
	
public function match_key($path){

	// echo $path;
		
	$query = $this->db->select("G_key");
	$query = $this->db->where('path',$path);
	$query = $this->db->where('status',1);
            $this->db->from('limbo_folder_management');
            $query=$this->db->get();
			
			return $query->row(0);
}
public function match_key_by_gkey($gkey){

	// echo $path;
 		
	$query = $this->db->select("path,file_type");
	$query = $this->db->where('G_key',$gkey);
            $this->db->from('limbo_folder_management');
            $query=$this->db->get();
			
			return $query->row(0);
}
public function verify_password($pass){

	// echo $path;
 		
	$query = $this->db->select("id");
	$query = $this->db->where('password',$pass['pass']);
	$query = $this->db->where('G_key',$pass['G_key']);
	$query = $this->db->where('status',1);
            $this->db->from('limbo_folder_management');
            $query=$this->db->get();
			
			return $query->num_rows();
}
public function g_key_verify($gkey){

	// echo $path;
 		
	$query = $this->db->select("id");
	$query = $this->db->where('G_key',$gkey);
            $this->db->from('limbo_folder_management');
            $query=$this->db->get();
			
				return $query->num_rows();
}
}
?>