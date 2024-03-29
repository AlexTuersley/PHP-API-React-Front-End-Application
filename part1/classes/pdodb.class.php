<?php
/**
* PDO (PHP Data Objects) Database Connection
* Create a singleton SQLite database connection
* @author Alex Tuersley
*/
class PDOdb {
 private static $dbConnection = null;

 /**
 * Make this private to prevent normal class instantiation 
 * @access private
 */
 private function __construct() {
     
 }

 /**
 * Return DB connection or create initial connection
 * @return object (PDO)
 * @access public
 * @todo the catch block should not directly 'echo' output. This will 'cost' you in the coursework.
 */
public static function getConnection($dbname) {
  if ( !self::$dbConnection ) {
      self::$dbConnection = new PDO("sqlite:".$dbname);
      self::$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  }
  return self::$dbConnection;
}
}
?>