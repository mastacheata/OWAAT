<?php
/*
	OWASP ASVS Assessment Tool (OWAAT)

	Copyright (C) 2014 Mahmoud Ghorbanzadeh <mdgh (a) aut.ac.ir>
  
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

	include "settings.php";

	include 'function.php';
    if(!isset($_SESSION['user']))
      error($PN.'10', $con);
	
    header('Content-Type: text/html; charset=utf-8');
	
	include 'db.php';
	
	if(!isset($_GET["action"]))
	  error($PN.'11', $con);
	
	if($_GET["action"] == "update")
	{
		if(!isset($_POST['password']))
		{
			if(isset($_POST["fname"]) && isset($_POST["lname"]) && isset($_POST["email"]))
			{
				$fname_tmp = strip_tags($_POST['fname']);
				$fname = mysqli_real_escape_string($con, $fname_tmp);
				$fname = trim($fname);

				$lname_tmp = strip_tags($_POST['lname']);
				$lname = mysqli_real_escape_string($con, $lname_tmp);
				$lname = trim($lname);

				$email_tmp = strip_tags($_POST['email']);
				$email = mysqli_real_escape_string($con, $email_tmp);
				$email = trim($email);

				if(empty($fname) || empty($lname) || empty($email))
					error($PN.'12', $con);
				  
				if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email))
					error($PN.'13', $con);

			}
			else
				error($PN.'14', $con);
		}
		else
		{
			$password_tmp = strip_tags($_POST['password']);
			$password = mysqli_real_escape_string($con, $password_tmp);
			$password = trim($password);

			if(strlen($password) < 6)
				error($PN.'15', $con);
			
			if($password != @$_POST['password2'])
				error($PN.'16', $con);
		}
	}
	
	if($_GET["action"] == "list")
	{
		$recordCount = 1;

		$result = mysqli_query($con, "SELECT id, fname, lname, email, uname, administrator, enabled FROM users WHERE id='".$_SESSION['id']."';") or error($PN.'17', $con);

		$rows = array();
		$row = mysqli_fetch_array($result);
			
		if(!$row){
			error($PN.'18', $con);
		}
			
		$rows[] = $row;

		$response = array();
		$response['Result'] = "OK";
		$response['TotalRecordCount'] = $recordCount;
		$response['Records'] = $rows;
		print json_encode($response);
	}
	else if($_GET["action"] == "update")
	{
		$id = $_SESSION['id'];

		if(!isset($_GET['token']) || validate_token($_GET['token']) == false)
			error($PN.'19', $con);

		if(empty($password))
			mysqli_query($con, "UPDATE users SET fname='".$fname."', lname='".$lname."', email='".$email."' WHERE id = ".$id.";") or error($PN.'20', $con);
		else
			mysqli_query($con, "UPDATE users SET password='".sha1($password)."' WHERE id = ".$id.";") or error($PN.'21', $con);

		$result = mysqli_query($con, "SELECT id, fname, lname, email, uname, administrator, enabled FROM users WHERE id = ".$id.";") or error($PN.'22', $con);
		$row = mysqli_fetch_array($result);

		$response = array();
		$response['Result'] = "OK";
		$response['Record'] = $row;
		print json_encode($response);
	}
	else
		error($PN.'23', $con);

	@mysqli_close($con) ;
?>