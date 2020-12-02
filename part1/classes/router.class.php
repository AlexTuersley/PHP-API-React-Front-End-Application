<?php
/**
* This router will return a main, documentation or about page
* 
* @author Alex Tuersley
*
*/
class Router {
  //Class Variables, one for the page that will be displayed and the other for the type of page
  private $page;
  private $type = "HTML";

  /**
  * This function runs when the class is iniated. It gets the URL and decides whether the page is a call to the api or to the web page
 */
 public function __construct($recordset) {
   $url = $_SERVER["REQUEST_URI"];
   $path = parse_url($url)['path'];

   $path = str_replace(BASEPATH,"",$path);
   $pathArr = explode('/',$path);
   $path = (empty($pathArr[0])) ? "home" : $pathArr[0];

   ($path == "api") 
     ? $this->api_route($pathArr, $recordset) 
     : $this->html_route($path);

 }

 /**
  * @param $pathArr - an array containing all paths from the URL
  * @param $recordset passes a recordset through to JSONpage 
  * Funciton turns the page into JSON and passes through the path to JSONpage which is call a query based on the route
  */
 public function api_route($pathArr, $recordset) {
   $this->type = "JSON";
   $this->page = new JSONpage($pathArr, $recordset);
 }

 /**
  * @param $path - passes the path to the function so it will route to the correct page
  * function turns the page into a HTML page based on the path passed through, if there is no path or an incorrect path will show the error page
  */
 public function html_route($path) {
   $ini['routes'] = parse_ini_file("config/routes.ini",true);
   $pageInfo = isset($path, $ini['routes'][$path]) 
     ? $ini['routes'][$path] 
     : $ini['routes']['error'];

   $this->page = new WebPageWithNav($pageInfo['title'], $pageInfo['heading1'], $pageInfo['footer']);
   $this->page->addToBody($pageInfo['text']);
 }

 //Getter functions
 public function get_type() {
   return $this->type ; 
 }

 public function get_page() {
   return $this->page->get_page(); 
 }
}
?>
