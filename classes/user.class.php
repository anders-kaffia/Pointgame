<?php

// This class includes all methods connected to the user.
class User{

	//This method creates a new user.
	public static function createUser($params){

		if(isset($_POST['createUser'])){
			$mysqli = DB::getInstance();
			$firstname = $mysqli->real_escape_string($_POST['firstname']);
			$lastname = $mysqli->real_escape_string($_POST['lastname']);
			$email = $mysqli->real_escape_string($_POST['email']);
			$username = $mysqli->real_escape_string($_POST['username']);
			$password = $mysqli->real_escape_string($_POST['password']);
			// Sets all new users to "freemium", i.e not "premium".		
			$premiumstartvalue = NULL;

			// All fields HAS to have value, != null.
			if ($firstname && $lastname && $email && $username && $password != null) {
			$query = "
				INSERT INTO user
				(firstname, lastname, email, username, password, premium) 
				VALUES ('$firstname', '$lastname', '$email', '$username', '$password', '$premiumstartvalue')
				";

			$mysqli->query($query) or die($mysqli->error);
			
			return ['redirect' => '/Pointgame/#login'];
			}else {
			return [ 'notallowed' => '/Pointgame/#createaccount'];
			}			
		}
	}	

	//This method converts a Freemium user into a Premium user
	public static function premiumSuccess($params){

			$mysqli = DB::getInstance();
			$_SESSION['user']['id'] = $mysqli->real_escape_string($_SESSION['user']['id']);
			$query = "
				UPDATE user
				SET premium = 1
				WHERE user.id = ".$_SESSION['user']['id']."
				";

			$mysqli->query($query);

			return['template' => 'premiumsuccess.html'];
	}

	//This method is used to log in users.
	public static function login($params){
		
		if(isset($_POST['login'])){

			$mysqli = DB::getInstance();			
			$username = $mysqli->real_escape_string($_POST['username']);
			$password = $mysqli->real_escape_string($_POST['password']);
			//$password = crypt($password,'$2a$'.sha1($username));

			$query = "
				SELECT id, username
				FROM user
				WHERE username = '$username'
				AND password = '$password'
				LIMIT 1
				";

			$result = $mysqli->query($query);
			$user = $result->fetch_assoc();								 
		 }
		 
		if(isset($user['id'])){

			$_SESSION['user']['id'] = $user['id'];
			$_SESSION['user']['name'] = $user['username'];
			
					
			return ['user'		=> $_SESSION['user'],
					'redirect' 	=> '?/Todolist/all'];			 		 				
			}else {
		 	return ['redirect' => '/Pointgame#login'];
		 }
	}	

	//This method is used to log out users
	public static function logout($params){ 

        if(isset($_POST['logout'])){            
            session_destroy();            
            return ['redirect' => '/Pointgame'];				 				
			}		
	}
}