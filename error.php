<html>
	<head>
		<title>Error</title>
	</head>
	<body style="text-align:center">
		<h1>Money Banks Error</h1>
		<p>Sorry an exception has occurred.</p>
		<p>To continue, click the back button.</p>
		<h2>Details</h2>
		<p> Message: <?php require_once('ErrorDataModel.php'); echo $_GET[ErrorDataModel::ERROR_KEY]; ?></p>
	</body>
</html>