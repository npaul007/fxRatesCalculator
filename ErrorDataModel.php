<?php
	require_once('IError.php');

	class ErrorDataModel implements IError{
		public function getErrorUrl($errorMessage){
			$url = ('Location:' . self::ERROR_URL . '?' . self::ERROR_KEY . '=' . urlencode($errorMessage));
			return $url;
		}
	}
?>