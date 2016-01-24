<?php
// This class includes all methods connected to Todolists

class Todolist{	

	//This method creates a new Todolist. 
	public static function createtodolist($params){

		if(isset($_POST['createtodolist'])){
			$mysqli = DB::getInstance();
			$todoname = $mysqli->real_escape_string($_POST['createtodolist']);
			$type = $mysqli->real_escape_string($_POST['todolisttype']);


			//If statement for choosing list type (day/week/monthly) list.
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

			return ['redirect' => '?/Todolist/all'];		
	}	

	//This method creates a new Listitem on the current Todolist.
	public static function createlistitem($params){

		if(isset($_POST['createItem'])){
			$mysqli = DB::getInstance();
			$task = $mysqli->real_escape_string($_POST['createItem']);
			$score= $mysqli->real_escape_string($_POST['createpoints']);
			$post_id = $mysqli->real_escape_string($_POST['todolist_id']);

			//Prevents "name" input field from being empty. 
			if ($task != NULL) {
			$query = "
				INSERT INTO listitem
				(task, score, todolist_id) 
				VALUES ('$task', '$score', $post_id)
			";

			$mysqli->query($query);

			return ['redirect' => $_SERVER['HTTP_REFERER']];
			}
			//If "name" input field is empty, do this: 
			else {
				return ['redirect' => $_SERVER['HTTP_REFERER']];
			}
		}
	}

	//This method prints a single todolist with its associated list items. 
	public static function single($params){
		
			$id = $params[0];
			$mysqli = DB::getInstance();
			$id = $mysqli->real_escape_string($id);
			//Prints Dashboard for current list. 
			$dashboard = Todolist::dashboardsingle($id);
			//Checked is for making sure that the users cant access another users list. 
			$checked = Todolist::isowner($_SESSION['user']['id'], $id);			

			if ($checked == TRUE) {
			$result = $mysqli->query("
							SELECT * FROM todolist
							WHERE id= ".$id." 
							");
			$todolist = $result->fetch_assoc();

			$result1 = $mysqli->query("
							SELECT * FROM listitem
							WHERE todolist_id= ".$id." 
							");
			$result2 = $mysqli->query("
							SELECT * FROM donelistitem
							WHERE todolist_id= ".$id." 
							");			

			while($listitem = $result1->fetch_assoc()){
				$listitems[] = $listitem;
			}
			
			while($doneitem = $result2->fetch_assoc()){
			 	$doneitems[] = $doneitem;
			 }	 	


	 		return ['dashboard' 	=> $dashboard, 
	 				'todolist' 		=> $todolist, 
	 				'listitems'		=> $listitems, 
	 				'donelistitems' => $doneitems, 
	 				'template' 		=> 'singleindex.html' ];  
			}
			else{
				return ['redirect' => '?/Todolist/all'];
			}
	}

	//This method shows all of the current users todolists.
	public static function all($params){
		
		 	$mysqli = DB::getInstance();
		 	//Checked is used here to see if the user is a premium user. 
		 	$checked = Todolist::checkifpremium($params);
		 	//Total Dashboard for user is shown, with overall scores and statistic.
		 	$dashboard = Todolist::dashboard();

		 	$result = $mysqli->query("
		 					SELECT * FROM todolist
		 					WHERE todolist.user_id = ".$_SESSION['user']['id']."
		 					AND todolist.expiration > NOW()
		 	  				");
 			
		 	while($todolist = $result->fetch_assoc()){
		 		$todolists[] = $todolist;
		 	}

		 	return ['todolists' => $todolists, 
		 			'premium' 	=> $checked, 
		 			'dashboard' => $dashboard];
	}	

	//This method deletes a specific listitem
	public static function deletelistitem($params){
		
			$id = $params[0];
			$mysqli = DB::getInstance();
			$id = $mysqli->real_escape_string($id);
			$result = $mysqli->query("
							DELETE FROM listitem WHERE id=$id
							");

	 		return ['redirect' => $_SERVER['HTTP_REFERER']];		
	}

	//This method deletes selected todolist and its associated items, and "done" items.
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

	//This method deletes selected item from DB table "listitem" and inserts it into a DB table "donelist".
	public static function doneitem($params){
		
			$id = $params[0];
			$mysqli = DB::getInstance();		
			$result = $mysqli->query("
							INSERT INTO donelistitem
							SELECT * FROM listitem
							WHERE listitem.id = ".$id."
							");
			$query2 = "
					DELETE
					FROM listitem
					WHERE listitem.id = ".$id." "; 

			$mysqli->query($query2);
		
			return ['redirect' => $_SERVER['HTTP_REFERER']];		
	}

	//This method  checks if the current user is premium.
	public static function checkifpremium($params){
		
			$mysqli = DB::getInstance();			
			$checkvalue = 1;
			//$password = crypt($password,'$2a$'.sha1($username));

			$query = "
				SELECT premium
				FROM user
				WHERE premium = '$checkvalue' 
				AND user.id = ".$_SESSION['user']['id']."				
				";

			$result = $mysqli->query($query);
			$checkresult = $result->fetch_assoc();		 
		 
		if($checkresult == TRUE){			
				return $checkresult;			 		 				
			}
			
			return [];
	}

	//This method checks if the current user is the owner of a todolist.
	public static function isowner($userId,$listId){

	$list = $listId;
	$mysqli = DB::getInstance();
	$userId = $mysqli->real_escape_string($userId);
	$result = $mysqli->query("
					SELECT * FROM todolist
					WHERE id = ".$list." 
					AND user_id = ". $userId . " 
					");

		if($result->num_rows > 0) {
		return true;
		} else {
		return false;
		}	
	}

	//This method for printing out the Dashboard, with statistics regarding the current users Todolists.
	public static function dashboard(){
			$mysqli = DB::getInstance();
		 	
		 	// Total score from current user.
		 	$result1 = $mysqli->query("
		 					SELECT SUM(score) as totalscore
							FROM donelistitem, todolist, user
							WHERE donelistitem.todolist_id = todolist.id
							AND todolist.user_id = user.id
							AND user.id = ".$_SESSION['user']['id']."
		 					");
		 	// Total score from list type "day".
		 	$result2 = $mysqli->query("
							SELECT SUM(score) as DailyScore
							FROM donelistitem, todolist, user
							WHERE donelistitem.todolist_id = todolist.id
							AND todolist.type = 'day'
							AND todolist.user_id = user.id
							AND user.id = ".$_SESSION['user']['id']."
		 					");
		 	// Total score from list type "week".
		 	$result3 = $mysqli->query("
							SELECT SUM(score) as WeeklyScore
							FROM donelistitem, todolist, user
							WHERE donelistitem.todolist_id = todolist.id
							AND todolist.type = 'week'
							AND todolist.user_id = user.id
							AND user.id = ".$_SESSION['user']['id']."
		 					");
		 	// Total score from list type "month".
		 	$result4 = $mysqli->query("
							SELECT SUM(score) as MonthlyScore
							FROM donelistitem, todolist, user
							WHERE donelistitem.todolist_id = todolist.id
							AND todolist.type = 'month'
							AND todolist.user_id = user.id
							AND user.id = ".$_SESSION['user']['id']."
		 					");
		 	
		 	$dash1 = $result1->fetch_assoc();
		 	$dash2 = $result2->fetch_assoc();
		 	$dash3 = $result3->fetch_assoc();
		 	$dash4 = $result4->fetch_assoc();

		 	return ['result1' => $dash1, 
		 			'result2' => $dash2, 
		 			'result3' => $dash3, 
		 			'result4' => $dash4]; 
	}

	//This method for printing the statistics for a single Todolist. 
	public static function dashboardsingle($id){
		
			$mysqli = DB::getInstance();
		 	$id = $mysqli->real_escape_string($id);
		 	// Total score from current list.
		 	$result1 = $mysqli->query("
		 					SELECT SUM(score) as doneScore
							FROM donelistitem, todolist
							WHERE donelistitem.todolist_id = todolist.id
							AND todolist.id = ".$id."
		 					");		 	
		 	// Total number of listitems from current list.
		 	$result2 = $mysqli->query("
							SELECT COUNT(listitem.id) as UnfinishedItems
							FROM listitem, todolist
							WHERE listitem.todolist_id = todolist.id
							AND todolist.id = ".$id."
		 					");		 	
		 	
		 	$singledash1 = $result1->fetch_assoc();
		 	$singledash2 = $result2->fetch_assoc();
		 	
		 	return ['result1' => $singledash1, 
		 			'result2' => $singledash2]; 
	}
}