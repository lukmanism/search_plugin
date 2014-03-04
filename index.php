<!DOCTYPE html>
<html>
<head>
<title></title>

		<!-- Start all needed script to run -->
		<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.min.js"></script>
		<script src='includes/js/jquery.easyMark.js'></script>
		<!-- End all needed script to run -->

		<!-- CSS's are optional -->
		<link rel="stylesheet" type="text/css" href="includes/css/styles.css" />
</head>
<body>

		<!-- Start Search Plugin -->
		<div class="filter">
			<input type="text" id="search" value="" class="field-search"/><span class="btn-search" id="searchbtn"></span>
			<ul class="keywords"></ul>
		</div>
		<div class="" id="results" ></div>
		<!-- Include JS file after #results element -->
		<script src='includes/js/search.js'></script>
		<!-- End Search Plugin -->

</body>
</html>