<?php

session_start();

if(empty($_SESSION['id_admin'])) {
	header("Location: index.php");
	exit();
}


require_once("../db.php");

if(isset($_GET)) {

	//Delete Company using id and redirect
	$sql = "DELETE FROM company WHERE id_company='$_GET[id]'";
	if($conn->query($sql)) {
		$sql1 = "DELETE FROM job_post WHERE id_company='$_GET[id]'";
		$sql2 = "DELETE FROM apply_job_post WHERE id_company='$_GET[id]'";
		if($conn->query($sql1)&&$conn->query($sql2))
		{
			header("Location: companies.php");
			exit();
		}
		else
		{
			echo "Error";
		}
	} else {
		echo "Error";
	}
}