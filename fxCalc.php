<?php
	// import contents of FxDatamodel php
	require_once('FxDataModel.php');

	require_once('LoginDataModel.php');
	require_once('EmailModel.php');

	if(!isset($_SESSION)){
		session_start();
	}

	if(!isset($_SESSION[LoginDataModel::USERNAME_SESSION_KEY])){
		require_once LoginDataModel::PHP_FILENAME;
		exit;
	}

	if(!isset($_SESSION[FxDataModel::FX_SESSION_KEY])){
		// fx Data model class object creation
		$fxDataModel = new FxDataModel();
		$_SESSION[FxDataModel::FX_SESSION_KEY] = serialize($fxDataModel);
	}else{
		$fxDataModel = unserialize($_SESSION[FxDataModel::FX_SESSION_KEY]);
	}

	// getting the array of fxCurrencyCodes
	$fxCurrencies = $fxDataModel->getFxCurrencies();
	// ini file for fxData
	$fxIniArray = $fxDataModel->getIniArray();

	$inputCurrency = '';
	$outputCurrency = '';

	$email = '';
	$emailBody = '';

	$isValid = false;
	$success = '';

	$error = '';

	$emailModel = new EmailModel();

	if(array_key_exists($fxIniArray[FxDatamodel::DST_AMT_KEY], $_POST) && 
		array_key_exists($fxIniArray[FxDatamodel::DST_CUCY_KEY], $_POST) &&
		array_key_exists($fxIniArray[FxDatamodel::SRC_CUCY_KEY], $_POST)){

		// returns index of currency array w/ matching value 
		$in = array_search($_POST[$fxIniArray[FxDatamodel::SRC_CUCY_KEY]], $fxCurrencies); 
		$out = array_search($_POST[$fxIniArray[FxDatamodel::DST_CUCY_KEY]], $fxCurrencies);

		// value to be converted, entered by user
		$inputCurrency = $_POST[$fxIniArray[FxDatamodel::SRC_AMT_KEY]];
		$outputCurrency = $fxDataModel->getOutput($inputCurrency,$in,$out);

		$email = $_POST[$fxIniArray[FxDataModel::EMAIL_FIELD_NAME]];
		$isValid = EmailModel::validateEmailAddr($email);

		if($isValid){
			$emailBody = $inputCurrency . ' ' . $_POST[$fxIniArray[FxDataModel::SRC_CUCY_KEY]] . ' = ' . $outputCurrency . ' ' . $_POST[$fxIniArray[FxDataModel::DST_CUCY_KEY]];
			//$success = 'Email sent successfully to ' . $email;
			$success = $emailModel->sendMail($email,$emailBody);
		}

		$isNumeric = is_numeric($inputCurrency);

		if(!$isNumeric){
			$inputCurrency = '';
			$outputCurrency = '';
		}
	}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Php Project 8</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
    	<h1 style="text-align:center; border-bottom:1px solid gray; padding-bottom:15px;">Money Banks F/X Calculator</h1>

		<form name="fxCalc" action="fxCalc.php" method="post" style="text-align:center">

			<h3>
				<?php 
					echo 'Welcome ' . $_SESSION[LoginDataModel::USERNAME_SESSION_KEY];
				?>
			</h3>

				<select name="<?php echo $fxIniArray[FxDatamodel::SRC_CUCY_KEY]; ?>">
		        	<?php
		        		for($i = 0; $i < count($fxCurrencies); $i++){
		        			if($_POST[$fxIniArray[FxDatamodel::SRC_CUCY_KEY]] === $fxCurrencies[$i] && $isNumeric){
		        				echo '<option value="'.$fxCurrencies[$i].'" selected>'.$fxCurrencies[$i].'</option>';
		        			}else{
		        				echo '<option value="'.$fxCurrencies[$i].'">'.$fxCurrencies[$i].'</option>';
		        			}
		        		}
		        	?>
		        </select>

		        <input type="text" name="<?php echo $fxIniArray[FxDatamodel::SRC_AMT_KEY]; ?>" value="<?php echo $inputCurrency;  ?>">

		         <select name="<?php echo $fxIniArray[FxDatamodel::DST_CUCY_KEY]; ?>">
		        	 <?php
		        		for($i = 0; $i < count($fxCurrencies); $i++){
		        			if($_POST[$fxIniArray[FxDatamodel::DST_CUCY_KEY]] === $fxCurrencies[$i] && $isNumeric){
		        				echo '<option value="'.$fxCurrencies[$i].'" selected>'.$fxCurrencies[$i].'</option>';
		        			}else{
		        				echo '<option value="'.$fxCurrencies[$i].'">'.$fxCurrencies[$i].'</option>';
		        			}
		        		}
		        	?>
		        </select>

		        <input type="text" name="<?php echo $fxIniArray[FxDatamodel::DST_AMT_KEY]; ?>" value="<?php echo $outputCurrency; ?>"readonly>
				
				<p></p>

				<label for="<?php echo $fxIniArray[FxDataModel::EMAIL_FIELD_NAME]; ?>">Email Address</label>
				<input type="text" name="<?php echo $fxIniArray[FxDataModel::EMAIL_FIELD_NAME]; ?>" value="<?php echo $email; ?>">

		       <div id="buttons" style="margin-top:15px;">
		       	 <input type="submit" value="Convert">
		         <input type="reset" value="Reset">
		       </div>
		</form>
		<?php
			if(!$isValid && strlen($email) > 0){
				echo '<p style="text-align:center;">'. $email . ' is an <strong>invalid</strong> e-mail address results could not be emailed.</p>';
			}

			else if(strlen($success) === 0 && $isValid){
				echo '<p style="text-align:center;">' .'Results are being e-mailed to ' . $email . '</p>';
			}

			else if(strlen($success) > 0){
				echo '<p style="text-align:center;">' . 'The following error occured when trying to email results to ' . $email . '</p>';
				echo htmlspecialchars($success);
			}
			
		?>
    </body>
</html>
