<?php
	define("INI_FILE_NAME","fxCalc.ini");

	include_once('ErrorDataModel.php');

	class FxDataModel{
		// key name for csv file name in fxCalcIni
		const INI_ARRAY_KEY = 'fx.rates.file';

		// constants for names of inputs for fxCalc.php
		const DST_AMT_KEY = 'dst.amt';
		const DST_CUCY_KEY = 'dst.cucy';
		const SRC_AMT_KEY = 'src.amt';
		const SRC_CUCY_KEY = 'src.cucy';
		const FX_RATE = 'fx.rate';

		const FX_SESSION_KEY = 'fxDataModelInstance';

		const PHP_FILENAME = 'fxCalc.php';

		const EMAIL_FIELD_NAME = 'emailFieldName';

		const DSN_KEY = 'dsn';
		const DB_USERNAME_KEY = 'dbUsername';
		const DB_PASSWORD_KEY = 'dbPassword';
		const DB_PREP_STMT = 'fxPrepStatement';

		private $currencyCodes = array();
		private $fxRates = array();

		private $iniArray;
		private $csvFileName;

		private $array;

		public function __construct(){
			$this->iniArray = parse_ini_file(INI_FILE_NAME);

			try{

				$pdoObject = new PDO(
					$this->iniArray[self::DSN_KEY],
					$this->iniArray[self::DB_USERNAME_KEY],
					$this->iniArray[self::DB_PASSWORD_KEY]
				);

				$prepareStatement = $pdoObject->prepare($this->iniArray[self::DB_PREP_STMT]);

				$prepareStatement->execute();

				$data = $prepareStatement->fetchAll(PDO::FETCH_ASSOC);
				$this->array = $data;

				$len = count($data);
				for($i = 0; $i < $len; $i++){
					if(in_array($data[$i][$this->iniArray[self::SRC_CUCY_KEY]], $this->currencyCodes)){
						// echo $data[$i]['srcCucy'] . ' already exists ' . '<br/>';
					}else{
						array_push($this->currencyCodes, $data[$i][$this->iniArray[self::SRC_CUCY_KEY]]);
					}

					if(in_array($data[$i][$this->iniArray[self::DST_CUCY_KEY]], $this->currencyCodes)){
						// echo $data[$i]['srcCucy'] . ' already exists ' . '<br/>';
					}else{
						array_push($this->currencyCodes, $data[$i][$this->iniArray[self::DST_CUCY_KEY]]);
					}

					$this->fxRates[$data[$i][$this->iniArray[self::SRC_CUCY_KEY]] . $data[$i][$this->iniArray[self::DST_CUCY_KEY]]]  = $data[$i][$this->iniArray[self::FX_RATE]];
				}
				
				$prepareStatement->closeCursor();
				$pdoObject = null;

			}catch(PDOException $e){
				header(ErrorDataModel::getErrorUrl($e->getMessage()));
				exit;
			}
		}

		public function getFxCurrencies(){
			return $this->currencyCodes;
		}

		public function getFxRate($inCurrency, $outCurrency){
			$key1 = $this->currencyCodes[$inCurrency];
			$key2 = $this->currencyCodes[$outCurrency];

			if($key1 === $key2){
				return 1.0;
			}else{
				if(array_key_exists($key1 . $key2, $this->fxRates)){
					return $this->fxRates[$key1 . $key2];
				}else{
					return (1.0/$this->fxRates[$key2 . $key1]);
				}
			}
		}

		public function getOutput($inputCurrency, $in, $out){
			return $inputCurrency * (double)$this->getFxRate($in,$out);
		}

		public function getIniArray(){
			return $this->iniArray;
		}

		public function getData(){
			return $this->array;
		}

	}
?>