<?php
/**
* This router will return a main, documentation or about page if the URL doesn't point to the api.
* If the URl points to the api it will find the relevant query and run it
* @author Alex Tuersley
*
*/
Class Router{
    //Class Variables, one for the page that will be displayed and the other for the type of page
    private $page;
    private $type = "HTML";

    /**
     * This function runs when the class is iniated. It gets the URL and decides whether the page is a call to the api or to the web page
     */
    function __construct($recordset){
        $url = $_SERVER["REQUEST_URI"];
        $path = parse_url($url)['path'];

        $path = str_replace(BASEPATH,"",$path);
        $pathArr = explode('/',$path);
        $path = (empty($pathArr[0])) ? "main" : $pathArr[0];
        
        ($path == "api") 
        ? $this->api_route($pathArr, $recordset) 
        : $this->html_route($path);
    }

    public function api_route($pathArr, $recordset) {
        $this->type = "JSON";
        $this->page = new JSONpage($pathArr, $recordset);
      }
     
      public function html_route($path) {
        $ini['routes'] = parse_ini_file("config/routes.ini",true);
        $pageInfo = isset($path, $ini['routes'][$path]) 
          ? $ini['routes'][$path] 
          : $ini['routes']['error'];
     
        $this->page = new WebPageWithNav($pageInfo['title'], $pageInfo['heading1'], $pageInfo['footer']);
        $this->page->addToBody($pageInfo['text']);
      }
     
      public function get_type() {
        return $this->type ; 
      }
     
      public function get_page() {
        return $this->page->get_page(); 
      }

}
?>