<?php
	include_once 'LoginDataModel.php';
	include_once 'FxDataModel.php';

	if(!isset($_SESSION)){
		session_start();
	}

	if(!isset($_SESSION[LoginDataModel::USERNAME_SESSION_KEY])){
		include_once LoginDataModel::PHP_FILENAME;
	}

	$loginDataModel = new LoginDataModel();
	
	$loginIniArray = $loginDataModel->getLoginIniArray();

	if(array_key_exists($loginIniArray[LoginDataModel::USERNAME_KEY], $_POST) && 
		isset($loginIniArray[LoginDataModel::USERNAME_KEY]) &&
		$loginDataModel->validateUser(
				$_POST[$loginIniArray[LoginDataModel::USERNAME_KEY]],
				$_POST[$loginIniArray[LoginDataModel::PASSWORD_KEY]]
			)
		)
	{

		$_SESSION[LoginDataModel::USERNAME_SESSION_KEY] = $_POST[$loginIniArray[LoginDataModel::USERNAME_KEY]];

		include(FxDataModel::PHP_FILENAME);
		exit;
		
	}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Php Project 6</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
    	<h1 style="text-align:center; border-bottom:1px solid gray; padding-bottom:15px;">Money Banks Login</h1>
    	
		<form name="login" action="login.php" method="post" style="text-align:center;">
			<label for="username">Username</label>
			<input type="text" name="<?php echo $loginIniArray[LoginDataModel::USERNAME_KEY]; ?>" style="margin:10px;">

			<br/>

			<label for="password">Password</label>
			<input type="password" name="<?php echo $loginIniArray[LoginDataModel::PASSWORD_KEY]; ?>" style="margin:10px;">
			
			<br/>

			<input type="submit" value="Login" style="margin:8px;">
			<input type="reset" value="Reset" style="margin:8px;">

		</form>
    </body>
</html>
