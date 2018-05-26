<!DOCTYPE html>
<html lang="en">
<head>
	<title>Data Usage Stats Viewer</title>
	<base href="http://<?php echo $_SERVER['SERVER_NAME'] ?><?php echo dirname($_SERVER['PHP_SELF']) ?>/" />
	<link rel="stylesheet" href="css/normalize.css" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
	<link rel="stylesheet" href="css/styles.css" />
</head>
<body<?php if (!empty($this->page_id)) echo ' id="' . $this->page_id . '"'; ?>>
	<?php echo $this->body ?>

	<script src="js/jquery-3.3.1.min.js"></script>
	<script src="js/highcharts.js"></script>
	<script src="js/scripts.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>