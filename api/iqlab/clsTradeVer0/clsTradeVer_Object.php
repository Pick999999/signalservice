<?php
//clsTrade.php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class clsTrade {

// เป็น Class ระดับ เพจ 
// Properties
 public $shopid;
 public $saleid;
 public $lang;

 public $shopnameurl;
 public $salename;
 public $pdo;
 

 public $pagename ;
 public $clsDataservice ;

function __construct() { 
/*
     $this->lang     = $lang;
	 $this->shopid   = $shopid ;
	 $this->shopnameurl = $shopnameurl ; 
	 $this->saleid = $saleid ; 
*/
	 
     $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
	 $newUtilPath = '/home/ddhousin/domains/lovetoshopmall.com/private_html/';
	 require_once( "newutil2.php"); 

	 //require_once($newUtilPath ."iqlab/AjaxLabSideway.php"); 

	 
	 
	 
	 
	 $dbname = 'thepapers_lab' ;
	 $pdo = getPDONew()  ;
	 //$pdo->exec("set names utf8mb4") ;
	 $this->pdo = $pdo;

	 
	 
	 
	 


	


} // end __construct


  // Methods

function init_Data() { 


}




} // end class

  

?>