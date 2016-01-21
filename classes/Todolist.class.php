
<?php

class Todolist{

	private $week, $day, $month;


	public static function createtodolist($params){

		if(isset($_POST['createtodolist'])){
			$mysqli = DB::getInstance();
			$todoname = $mysqli->real_escape_string($_POST['createtodolist']);
			$type = $mysqli->real_escape_string($_POST['todolisttype']);
			$_SESSION['user']['id'] = $mysqli->real_escape_string($_SESSION['user']['id']);
			$interval = $mysqli->real_escape_string($interval);

			if($_POST['todolisttype'] == 'day') {
				$interval = 'day';

			}
			if($_POST['todolisttype'] == 'week') {
				$interval = 'week';

			}
			if($_POST['todolisttype'] == 'month') {
				$interval = 'month';

			}
			
		$query = "
				INSERT INTO todolist
				(name, user_id, type, expiration)
				VALUES ('$todoname', ".$_SESSION['user']['id'].", '$type', now() + INTERVAL 1 ".$interval." ) ";

				$mysqli->query($query);
			}		

			return ['redirect' =>  '?/Todolist/all'];		
}	

	public static function createlistitem($params){

		if(isset($_POST['createItem'])){
			$mysqli = DB::getInstance();
			$task = $mysqli->real_escape_string($_POST['createItem']);
			$score= $mysqli->real_escape_string($_POST['createpoints']);
			$post_id = $mysqli->real_escape_string($_POST['todolist_id']);



			$query = "
				INSERT INTO listitem
				(task, score, todolist_id) 
				VALUES ('$task', '$score', $post_id)
			";

			$mysqli->query($query);

			return ['redirect' => $_SERVER['HTTP_REFERER']];
		}

	}

	public static function single($params){
		
			$id = $params[0];
			$mysqli = DB::getInstance();
			$id = $mysqli->real_escape_string($id);
			$result = $mysqli->query("
							SELECT * FROM todolist
							WHERE id= ".$id."
							");
			$todolist = $result->fetch_assoc();

			$result = $mysqli->query("
							SELECT * FROM listitem
							WHERE todolist_id= ".$id."
							");
			$result2 = $mysqli->query("
							SELECT * FROM donelistitem
							WHERE todolist_id= ".$id."
							");
		
			while($listitem = $result->fetch_assoc()){
				$listitems[] = $listitem;
			}
			while($doneitem = $result2->fetch_assoc()){
			 	$doneitems[] = $doneitem;
			 }	 	


	 		return ['todolist' => $todolist, 'listitems' => $listitems, 'donelistitems' => $doneitems, 'template' => 'singleindex.html', ];  
		
	}

	public static function all($params){
			#17. Värdet som kommer ut här som $params är $url_parts som vi skickade in från index.php. ($params kan heta vad somhelst.)
		 	$mysqli = DB::getInstance();
		 	$checked = Todolist::checkifpremium($params);

		 	//KOLL FÖR DASHBOARDEN! typ...
		 	/*if ($checked == TRUE) {
		 		$a = Todolist::dashboard($stj);
		 		echo "hej fan";
		 		var_dump($a);
		 		die();
		 	}*/

		 	/* $deleteexpireddates = $mysqli->query("DELETE FROM todolist WHERE  
		 										todolist.user_id = ".$_SESSION['user']['id']." and expiration < NOW()"); */
		 	$result = $mysqli->query("
		 					SELECT * FROM todolist
		 					WHERE todolist.user_id = ".$_SESSION['user']['id']."
		 					AND todolist.expiration > NOW()
		 	  				");
		 		
		 	while($todolist = $result->fetch_assoc()){
		 		$todolists[] = $todolist;
		 	}

		 	return ['todolists' => $todolists, 'premium' => $checked];
	}
	
	public static function deletelistitem($params){
		
			$id = $params[0];
			$mysqli = DB::getInstance();
			$id = $mysqli->real_escape_string($id);
			$result = $mysqli->query("
							DELETE FROM listitem WHERE id=$id
							");

	 		return ['redirect' => $_SERVER['HTTP_REFERER']];		
	}
	
	public static function deletetodolist($params){
			$mysqli = DB::getInstance();
			$id = $params[0];
			$id = $mysqli->real_escape_string($id);
			$result = $mysqli->multi_query("
				DELETE FROM todolist WHERE todolist.id = $id;
				DELETE FROM listitem WHERE listitem.todolist_id = $id;
				DELETE FROM donelistitem WHERE donelistitem.todolist_id = $id	
				");

			return ['redirect' => $_SERVER['HTTP_REFERER']];
	}
	
	public static function doneitem($params){
		
			$id = $params[0];
			$mysqli = DB::getInstance();		
			$result = $mysqli->query("
				INSERT INTO donelistitem
				SELECT * FROM listitem
				WHERE listitem.id = ".$id." ");
			$query2 = "
				DELETE FROM listitem where listitem.id = ".$id." "; 

			$mysqli->query($query2);
		
			return ['redirect' => $_SERVER['HTTP_REFERER']];		
	}


	public static function checkifpremium($params){
		
			$mysqli = DB::getInstance();
			$_SESSION['user']['id'] = $mysqli->real_escape_string($_SESSION['user']['id']);		
			$checktabel = 1; 

			//$password = crypt($password,'$2a$'.sha1($username));

			$query = "
				SELECT premium FROM user WHERE  premium = '$checktabel' and user.id = ".$_SESSION['user']['id']."
				
			";

			$result = $mysqli->query($query);
			$checkresult = $result->fetch_assoc();

						 
		 
		 
		if($checkresult == TRUE){

			echo "Du är asasas premium";
			
				return $checkresult;		
			 		 				
			}
			
			return [];

		}

	public static function dashboard($params){
		 /*	$mysqli = DB::getInstance();
		 	
		 	# TOTAL SCORE FROM CURRENT USER:
		 	$result = $mysqli->query("
		 					SELECT SUM(score)
							FROM donelistitem, todolist, user
							WHERE donelistitem.todolist_id = todolist.id
							AND todolist.user_id = user.id
							AND user.id = ".$_SESSION['user']['id']."
		 					");

		 	# TOTAL SCORE FROM CURRENT LIST: OBS, byt ut $id !!!!!!!!!
		 	$result2 = $mysqli->query("
		 					SELECT SUM(score)
							FROM donelistitem, todolist
							WHERE donelistitem.todolist_id = todolist.id
							AND todolist.id = ".$id."
		 					");
		 	# TOTAL NUMBER OF LISTITEMS FROM CURRENT LIST: OBS, byt ut $id !!!!!!!!!
		 	$result3 = $mysqli->query("
							SELECT COUNT(listitem.id)
							FROM listitem, todolist
							WHERE listitem.todolist_id = todolist.id
							AND todolist.id = ".$id."
		 		");

		 	
		 	$dash1 = $result1->fetch_assoc();
		 	$dash2 = $result2->fetch_assoc();
		 	$dash3 = $result3->fetch_assoc();
		 	return ['resultalt1' => $dash1, 'resultat2' => $dash2, 'resultat3' => $dash3]; */
	}

}