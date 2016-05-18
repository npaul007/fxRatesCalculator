<?php
	define ("LOGIN_INI", "login.ini");

	class LoginDataModel{

		private $loginIniArray;

		const USERNAME_KEY = 'username';
		const PASSWORD_KEY = 'password';

		const DSN_KEY = 'dsn';
		const DB_USERNAME_KEY = 'dbUsername';
		const DB_PASSWORD_KEY = 'dbPassword';
		const DB_PREP_STMT = 'loginPreparedStatement';
  
		const USERNAME_SESSION_KEY = 'usernameSession';

		const PHP_FILENAME = 'login.php';

		private $prepareStatement;
		private $returnedStatementData;

		public function __construct(){

			$this->loginIniArray = parse_ini_file(LOGIN_INI);

			try{
				$pdoObject = new PDO(
					$this->loginIniArray[self::DSN_KEY],
					$this->loginIniArray[self::DB_USERNAME_KEY],
					$this->loginIniArray[self::DB_PASSWORD_KEY]
				);

				$this->prepareStatement = $pdoObject->prepare($this->loginIniArray[self::DB_PREP_STMT]);

			}catch(PDOException $e){
				header(ErrorDataModel::getErrorUrl($e->getMessage()));
				exit;
			}
		}

		public function __destruct(){
			$pdoObject = null;
		}

		public function validateUser($username, $password){
			try{
				$this->prepareStatement->bindParam(':username',$username);
				$this->prepareStatement->bindParam(':password',$password);

				$this->prepareStatement->execute();

				if($this->prepareStatement->rowCount()){
					return true;
				}else{
					return false;
				}

				$this->prepareStatement->closeCursor();

			}catch(PDOException $e){
				header(ErrorDataModel::getErrorUrl($e->getMessage()));
				exit;
			}
		}

		public function getLoginIniArray(){
			return $this->loginIniArray;
		}
	}
?>