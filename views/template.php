<!DOCTYPE html>
<html lang="en">
<head>
	<title>Data Usage Stats Viewer</title>
	<base href="http://<?php echo $_SERVER['SERVER_NAME'] ?><?php echo dirname($_SERVER['PHP_SELF']) ?>/" />
	<link rel="stylesheet" href="css/normalize.css" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/bootstrap-theme.min.css" />
	<link rel="stylesheet" href="css/styles.css" />
</head>
<body<?php if (!empty($this->page_id)) echo ' id="' . $this->page_id . '"'; ?>>

<!-- Fixed navbar -->
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">

            <a class="navbar-brand" href="#">Pmacct Frontend</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="./">Home</a></li>
                <li><a href="#about">Month</a></li>
                <li><a href="#contact">Hosts</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>


	<?php echo $this->body ?>

	<script src="js/jquery-3.3.1.min.js"></script>
	<script src="js/highcharts.js"></script>
	<script src="js/scripts.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>