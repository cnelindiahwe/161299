<?php 

//http://stackoverflow.com/questions/17454163/how-to-include-3rd-party-libraries-in-codeigniter

set_include_path(APPPATH . '/third_party/phpsec');
require_once APPPATH."/third_party/phpsec/Net/SFTP.php"; 

class Sftp {  }