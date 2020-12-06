<?php
/**
* This file handles errors and sets the paths to different files and routes based on .ini files
* @author Alex Tuersley
*/

/**
 * This function handles exceptions, logging the detailed exception to a file and displaying a basic message to the user
 */
function exceptionHandler($e) {
  $msg = "Exception Error Detected: ".$e->getMessage()." line: ".$e->getLine()." file: ".$e->getFile()."";
  $usr_msg = array("status" => "500", "message" => "Internal Server Error");
  header("Access-Control-Allow-Origin: *"); 
  header("Content-Type: application/json; charset=UTF-8"); 
  header("Access-Control-Allow-Methods: GET, POST");
  echo json_encode($usr_msg);
  logError($msg);
}

/**
* This function is an error handler that shwos the user a basic message and logs the detailed error to a file.
*/
function errorHandler($errno, $errstr, $errfile, $errline) {
  if ($errno != 2 && $errno != 8) {
    throw new Exception("Fatal Error Detected: 500 Internal Server Error", 1);
    logError("Fatal Error Detected: [$errno] $errstr line: $errline file: $errfile");
  }
  else{
    logError("Fatal Error Detected: [$errno] $errstr line: $errline file: $errfile");
  }
}
set_error_handler('errorHandler');
set_exception_handler('exceptionHandler');

$ini['routes'] = parse_ini_file("routes.ini",true);
$ini['main'] = parse_ini_file("config.ini",true);

define('BASEPATH', $ini['main']['paths']['basepath']);
define('CSSPATH', $ini['main']['paths']['css']);
define('JWTKEY', $ini['main']['keys']['jwt']);

/**
 * Loops through the classes folder and subfolders and includes all php files in the page with the name .class.php
 */
function autoloadClasses($className) {
   $filename = "classes\\" . strtolower($className) . ".class.php";
   $filename = str_replace('\\', DIRECTORY_SEPARATOR, $filename);
   if (is_readable($filename)) {
     include_once $filename;
   } else {
    throw new exception("File not found: " . $className . " (" . $filename . ")");
   }

}

/**
 * @param $Error - an error passed from one of the handlers with information on what error has been triggered
 * This function writes the error passed to it to an error log file which is stored on the server
 */
function logError($Error){
  $fileHandle = fopen("error_log_file.log", "ab");
  $errorMsg = date('D M j G:i:s T Y');
  $errorMsg .= $Error;
  fwrite($fileHandle, "$errorMsg\r\n");
  fclose($fileHandle);
}

spl_autoload_register("autoloadClasses");

?>