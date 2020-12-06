<?php
/**
* This router will return a main, documentation or about page or pass the url to the JSON Page function for api handling
* @author Alex Tuersley
*/
class Router {

  private $page;
  private $type = "HTML";

  /**
    * This function runs when the class is iniated. It gets the URL and decides whether the page is a call to the api or to the web page
    */
  public function __construct($recordset) {
    $url = $_SERVER["REQUEST_URI"];
    echo $url;
    $path = parse_url($url)['path']; 
    echo $path;
    $path = str_replace(BASEPATH,"",$path);
    $pathArr = explode('/',$path);
    $path = (empty($pathArr[0])) ? "home" : $pathArr[0];
    echo $path;

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
    switch ($path) {
      case 'home':
        $title = "Home";
        $heading1 = "Home";  
        $footer = "Northumbria, 2020"; 
        $text = "<p class='item'>For information regarding the API, click on documentation </p>";
        break;
      case 'documentation':
        $title = "Documentation";
        $heading1 = "Documentation Page";  
        $footer = "Northumbria, 2020";
        $text = "<h2>CHI API</h2>
                  <p>Click on the links to show the type of data that will be displayed</p>
                  <div class='item'>
                  <h3><a href='".BASEPATH."api'>/api</a></h3>
                  <p>Displays information about the api</p>
                  </div>
                  <div class='item'>
                  <h3><a href ='".BASEPATH."api/schedule'>/api/schedule</a></h3>
                  <p>Overview of the conference schedule</p>
                  </div>
                  <div class='item'>
                  <h3><a href ='".BASEPATH."api/schedule/times?day=1'>/api/schedule/times/:dayinteger</a></h3>
                  <p>Time slots of a Day in the conference</p>
                  </div>
                  <div class='item'>
                  <h3><a href ='".BASEPATH."api/schedule/10259'>/api/schedule/:session</a></h3>
                  <p>Displays information about a session within the schedule</p>
                  </div>
                  <div class='item'>
                  <h3><a href ='".BASEPATH."api/authors'>/api/authors</a></h3>
                  <p>Lists all authors and the institution they belong to.</p>
                  </div>
                  <div class='item'>
                  <h3><a href ='".BASEPATH."api/authors?search=Juho%20Kim'>/authors/:authorname</a></h3>
                  <p>Returns a list of authors associated with the name, along with the content they are in.</p>
                  </div>
                  <div class='item'>
                  <h3><a href ='".BASEPATH."api/authors/8192'>/authors/id</a></h3>
                  <p>Returns the author associated with the id, along with the content they are in.</p>
                  </div>
                  <div class='item'>
                  <h3><a href ='".BASEPATH."api/authors/content/7433'>/authors/content/contentid</a></h3>
                  <p>Returns all authors associated with the content</p>
                  </div>
                  <div class='item'>
                  <h3><a href ='".BASEPATH."api/content'>/api/content</a></h3>
                  <p>Returns all content which is in the conference</p>
                  </div>
                  <div class='item'>
                  <h3><a href ='".BASEPATH."api/content?search=Examining%20Wikipedia'>/api/content/:content</a></h3>
                  <p>Returns any content that has a title like the user inputted search</p>
                  </div>
                  <div class='item'>
                  <h3><a href ='".BASEPATH."api/content/6145'>/api/content/contentid</a></h3>
                  <p>Returns authors, start and end times of the content based on the id passed through</p>
                  </div>
                  <div class='item'>
                  <h3><a href ='".BASEPATH."api/content/session/1008'>/api/content/:sessionId</a></h3>
                  <p>Returns content associated with a session</p>
                  </div>
                  <div class='item'>
                  <h3><a href ='".BASEPATH."api/login'>/api/login requires authentication</a></h3>
                  <p>User sends a post request  with their email and password to login. If the details are correct the user is authenticated and a JSON Web Token is returned</p>
                  </div>
                  <div class='item'>
                  <h3><a href ='".BASEPATH."api/update'>/api/update requires authentication</a></h3>
                  <p>User passes a JSON Web token to the API along with data to update such as a session or content title. If the Web Token is correct the data is used to update the database</p>
                  </div>";
        break;
      case 'about':
        $heading1 = "About Page";  
        $footer = "Northumbria, 2020"; 
        $text = "<p class='item'>Author: Alexander Tuersley</p>
                <p class='item'>Student ID: w17018264</p>
                <p class='item'>Email: alexander.tuersley@northumbria.ac.uk</p>
                <p class='item'>This Website is University Coursework and in no way associated with the CHI Conference or any of its sponsers</p>";
        break;
      default:
        $title = "Error";
        $heading1 = "Error Page";  
        $footer = "Northumbria, 2020"; 
        $text = "<p>Page not found</p>";
      break;
    }

  $this->page = new WebPageWithNav($title, $heading1, $footer);
  $this->page->addToBody($text);
  }

  public function get_type() {
    return $this->type ; 
  }

  public function get_page() {
    return $this->page->get_page(); 
  }
}
?>
