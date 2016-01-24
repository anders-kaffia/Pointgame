<?php
// Creates a connection to the database
class DB{
	
	private static $instance;

	private function __construct(){}
	private function __clone(){}

	public static function getInstance(){
		if(!self::$instance){
			self::$instance = new mysqli("localhost","root","root","Pointgame");
			return self::$instance;
		}else{
			return self::$instance;
		}
	}
}