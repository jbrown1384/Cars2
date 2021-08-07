<?php
	require_once('classes/InsuranceParser.php');

	$filename = "public/FL_insurance_sample.csv";
	$csvParser = new InsuranceParser($filename);
?>
