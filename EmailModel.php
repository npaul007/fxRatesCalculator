<?php 
	define("INI_FILE_NAME_KEY", "email.ini");
	require_once('Mail.php');

	class EmailModel{

		const FROM_KEY = 'from';
		const HOST_KEY = 'host';
		const PORT_KEY = 'port';
		const SUBJECT_KEY = 'subject';
		const USER_KEY = 'username';
		const PASS_KEY = 'password';
		const AUTH_KEY = 'auth';

		private $iniArray;

		public function __construct(){
			$this->iniArray = parse_ini_file(INI_FILE_NAME_KEY);

			if( array_key_exists( self::AUTH_KEY, $this->iniArray ) && $this->iniArray[ self::AUTH_KEY ] === true){
			  $this->iniArray[ self::AUTH_KEY ] = true;
			}  

			if(array_key_exists( self::AUTH_KEY, $this->iniArray ) )6{
			  $this->iniArray[ self::AUTH_KEY ] = ( bool ) $this->iniArray[ self::AUTH_KEY ];
			}

		}

		public static function validateEmailAddr($email){
			$len = strlen($email);
			if($len > 0){
			    if(!preg_match("/^[^@]{1,64}@[^@]{1,255}$/", $email)) {
			        return false;
			    }
		
			    $email_array = explode("@", $email);
			    $local_array = explode(".", $email_array[0]);

			    for($i = 0; $i < sizeof($local_array); $i++) {
			        if(!preg_match("/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", 
			        	$local_array[$i])) {
			            return false;
			        }
			    }

			    if(!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])){ 
			        $domain_array = explode(".", $email_array[1]);
			        if(sizeof($domain_array) < 2){
			            return false; 
			        }

			        for($i = 0; $i < sizeof($domain_array); $i++){
			            if(!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", 
			            	$domain_array[$i])) {
			                return false;
			            }
			        }
			    }

			    return true;
			}
		}

		public function sendMail($email, $body){

			$to = 'Recipient' . '<' . $email . '>';

			$headers = array('From' => $this->iniArray[self::FROM_KEY],   
				'To' => $to,   
				'Subject' => $this->iniArray[self::SUBJECT_KEY]); 

			$smtp = Mail::factory('smtp',   
				array('host' => $this->iniArray[self::HOST_KEY],     
				'port' => $this->iniArray[self::PORT_KEY],     
				'auth' => $this->iniArray[self::AUTH_KEY],     
				'username' => $this->iniArray[self::USER_KEY],     
				'password' => $this->iniArray[self::PASS_KEY]));

			$mail = $smtp->send($to, $headers, $body);  

			if(PEAR::isError($mail)) {  
			 	return $mail->getMessage();  
			}

			else{   
				return '';
			}
		}

	}
?>
￼￼￼