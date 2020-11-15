<?php
/**
* Creates a JSON page based on the parameters
* 
* @author Alex Tuersley
* 
*/
class JSONpage {
  //Class Variables
  private $page; 
  private $recordset;

  /**
  * @param $pathArr - an array containing the route information
  * @param $recordset - The database used for the queries
  */
  public function __construct($pathArr, $recordset) {
    $this->recordset = $recordset;
    
    if($pathArr[0] == "api"){
        if(isset($pathArr[1])){
            if($pathArr[1] == "schedule"){
                if(isset($pathArr[2])){
                  if(isset($pathArr[3])){

                  }
                  else{
                    $this->page = $this->json_schedule($pathArr[2]);
                  }
                  
                }
                else{
                  $this->page = $this->json_schedule();
                }
            }
            elseif($pathArr[1] == "content"){
              //content
              if(isset($pathArr[2])){
                  $this->page = $this->json_content($pathArr[2]);
              }
              else{
                $this->page = $this->json_content();
              }
            }
            elseif($pathArr[1]== "authors"){
                if(isset($pathArr[2])){
                    $this->page = $this->json_authors($pathArr[2]);
                }
                else{
                  $this->page = $this->json_authors();
                }
                
            }
            elseif($pathArr[1] == "update"){
              $this->page = $this->json_update();
            }
            elseif($pathArr[1] == "login"){
              $this->page = $this->json_login();
            }
          
        }
        else{
          $this->page = $this->json_api();
        }
    }
    else{
      $this->page = $this->json_error();
    }

  }

  //a max length of 50 is set as names will not be longer than this
  private function sanitiseString($x) {
    return substr(trim(filter_var($x, FILTER_SANITIZE_STRING)), 0, 50);
  }
  //sanitise an integer
  private function sanitiseNum($x) {
    return filter_var($x, FILTER_VALIDATE_INT);
  }

  /**
   * @return JSON data about the api in JSON format
   */
  private function json_api(){
      $api = array("message"=>"Welcome",
                   "author"=>"Alex Tuersley",
                    "api"=> array("/api" => "returns api endpoints and basic info",
                                  "/api/schedule" => "returns the days of the schedule",
                                  "/api/schedule/id" => array("returns"=> "time slots within the day", "id" => "An integer slot id that links to the times in a day"),
                                  "/api/schedule/presentations" => "lists all presentations",
                                  "/api/schedule/presentations/id" => array("returns" => "All presentations that have a specific slot id", "id"),
                                  "/api/authors" => "lists all authors",
                                  "/api/authors?search=name" => "searches for users with a name",
                                  "/api/authors/id" => array("returns"=>"an author with all the presentations they are in and other info","id"=>"id of an author in the database"),
                                  "/api/login" => array("returns" => "a JSON Web Token if the login is successful", "requires" => "email and password from a form"),
                                  "/api/update" => array("returns" => "updates the title of a session id the JSON Web Token used is correct", "requires" => "JSON Web Token and the updated title of the session")
                                  ));
      return json_encode($api);
  } 
  /**
   * @return json encoded error message
   */
  private function json_error() {
    $msg = array("message"=>"error");
    return json_encode($msg);
  }
  /**
   * function for the schedule
   * @param $day is an integer of the day selected, if no day is selected runs a query for all days
   * @return string json query results
   */
  private function json_schedule($slot = 0){
    if($slot > 0){
      $query = "SELECT dayString, startHour, startMinute, endHour, endMinute, sessions.sessionId, sessions.name, session_types.name as sessionstype, rooms.name As room, (SELECT authors.name FROM authors JOIN sessions ON authors.authorId = sessions.chairId WHERE sessions.slotId = :slot) as sessionchair FROM slots
      JOIN sessions ON slots.slotId = sessions.slotId
      JOIN session_types ON sessions.typeId = session_types.typeId
      JOIN rooms ON sessions.roomId = rooms.roomId
      WHERE sessions.slotId = :slot";
      $slot = $this->sanitiseNum($slot);
      $params = ["slot" => $slot];
    }
    else{
      $query = "SELECT slotId,dayInt,dayString,startHour,startMinute,endHour,endMinute,type  FROM slots
                ORDER BY dayInt
                LIMIT 4";
      $params = [];
    }
    return ($this->recordset->getJSONRecordSet($query, $params));
  }
  /**
   * function for author queries
   * @param $id is the id of an author that has been selected 
   * if a search has been run the searched name is grabbed from the url and runs a different query
   * @return JSON data based on query results 
   */ 
  private function json_authors($id = 0){
      if($id > 0){ 
          $query = "SELECT DISTINCT authors.name, authorInst,title, abstract, award, sessions.name, session_types.name FROM authors
          INNER JOIN content_authors On authors.authorId = content_authors.authorId
          INNER JOIN content ON content_authors.contentId = content.contentId
          INNER JOIN sessions_content ON content.contentId = sessions_content.contentId
          INNER JOIN sessions ON  sessions_content.sessionId = sessions.sessionId 
          INNER JOIN session_types ON sessions.typeId = session_types.typeId
          WHERE authors.authorId = :authorid";
          $authorId = $this->sanitiseNum($id);
          $params = ["authorid" => $authorId];
      }
      else{
        
          $query = "SELECT DISTINCT authors.authorId, authors.name, authorInst FROM authors
          INNER JOIN content_authors ON authors.authorId = content_authors.authorId
          ";
          $params = [];
          if(isset($_REQUEST['search'])) {
            $query .= "WHERE authors.name LIKE :authorname";
            $name = str_replace("%20"," ", $_REQUEST['search']);
            $name = $this->sanitiseString("%".$name."%");
            $params = ["authorname" => $name];
          }        
      }
      return ($this->recordset->getJSONRecordSet($query, $params));
  }
  /**
   * function for content
   * @param $id - the id of some content which is used to gather further information about it
   * @return JSON data based on the query that is ran
   */
  private function json_content($id = 0){
      if($id > 0){
        $query = "SELECT content.title, content.abstract, content.award, sessions.slotId, session_types.name, sessions.name, slots.startHour, slots.startMinute, slots.endHour, slots.endMinute, slots.dayString, authors.name as author, content_authors.authorInst FROM content
        JOIN content_authors ON content_authors.contentId = content.contentId
        JOIN authors ON authors.authorId = content_authors.authorId
        JOIN sessions_content ON sessions_content.contentId = content.contentId
        JOIN sessions ON sessions_content.sessionId = sessions.sessionId
        JOIN slots ON sessions.slotId = slots.slotId
        JOIN session_types ON sessions.typeId = session_types.typeId
        WHERE content.contentId = :id
        ORDER BY sessions.slotId";
        $id = $this->sanitiseNum($id);
        $params = ["id" => $id];
      }
      else{      
        $query = "SELECT * FROM content";
        $params = [];
        if(isset($_REQUEST['search'])) {
          $query .= " WHERE title LIKE :content";
          $content = str_replace("%20"," ", $_REQUEST['search']);
          $content = $this->sanitiseString("%".$content."%");
          $params = ["content" => $content];
        }   
      }
      return ($this->recordset->getJSONRecordSet($query, $params));
  }
  /**
   * function gets JSON Web Token and checks whether it is valid
   * if the token is valid then function updates 
   */
  private function json_update(){
    $msg = "Invalid token. You do not have permission to update";
    $status = 400;
    $input = json_decode(file_get_contents("php://input"));
    $token = $input->token;
    $admin = $input->admin;


  }
  /**
   * function gets php input and checks the database to see if the user exists
   * @return JSON Web token if the user credentials are correct
   */
  private function json_login(){
    $msg = "Invalid request. Username and password required";
    $status = "400";
    $encodedToken = null;
    $input = json_decode(file_get_contents("php://input"));

    if (!is_null($input->email) && !is_null($input->password)) {  
      $query  = "SELECT firstname, lastname, password, admin FROM users WHERE email LIKE :email";
      $params = ["email" => $input->email];
      $res = json_decode($this->recordset->getJSONRecordSet($query, $params),true);

      if (password_verify($input->password, $res['data'][0]['password'])) {
        $msg = "User authorised. Welcome ". $res['data'][0]['firstname'] . " " . $res['data'][0]['lastname'];
        $status = "200";
        $token["email"] = $input->email;
        $token["exp"] = "hello";
        $encodedToken = JWT::encode($token,"secret_server_key");
        $admin = $res['data'][0]['admin'];
      } else { 
        $msg = "username or password are invalid";
        $status = "401";
      }
    }
    return json_encode(array("status" => $status, "message" => $msg, "token" => $encodedToken, "admin" => $admin));
}

  /**
   * getter function for the page
   * @return page
   */
  public function get_page() {
    return $this->page;
  }
}
?>