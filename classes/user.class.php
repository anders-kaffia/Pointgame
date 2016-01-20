<?php

class User{


	public static function createuser($params){
		if(isset($_POST['createuser'])){
			$mysqli = DB::getInstance();
			$firstname = $mysqli->real_escape_string($_POST['firstname']);
			$lastname = $mysqli->real_escape_string($_POST['lastname']);
			$email = $mysqli->real_escape_string($_POST['email']);
			$username = $mysqli->real_escape_string($_POST['username']);
			$password = $mysqli->real_escape_string($_POST['password']);			

			$query = "
				INSERT INTO user
				(firstname, lastname, email, username, password) 
				VALUES ('$firstname', '$lastname', '$email', '$username', '$password')
			";

			$mysqli->query($query) or die($mysqli->error);
			
			return ['redirect' => '/Pointgame#login'];
		}
	}	

	public static function premiumsuccess($params){

			$mysqli = DB::getInstance();
			$query = "
				UPDATE user
				SET premium = 1
				WHERE user.id = ".$_SESSION['user']['id']."

			";

			$mysqli->query($query);


	}


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
			
					
			return ['user' => $_SESSION['user'],
			'redirect' => '?/Todolist/all'
			];
			 		 				
			}
			
			return [];

		}



	public static function logout($params){ 

        if(isset($_POST['logout'])){
            
            session_unset();
            session_destroy();
            
            return ['redirect' => '/Pointgame'];
				 				
			} 
		
		}

	}
