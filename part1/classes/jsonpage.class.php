<?php
/**
* Creates a JSON page based on the URL path 
* @author Alex Tuersley
*/
class JSONpage {

  private $page; 
  private $recordset;

  /**
  * Based on the $pathArr variable passed through different functions are called to grab and return JSON data 
  * @param $pathArr - an array containing the route information
  * @param $recordset - The database used for the queries
  */
  public function __construct($pathArr, $recordset) {
    $this->recordset = $recordset;
    if($pathArr[0] === "api"){
        if(isset($pathArr[1])){
            if($pathArr[1] === "schedule"){
                if(isset($pathArr[2])){
                  if($pathArr[2] === "times"){
                    $this->page = $this->json_schedule(0,$pathArr[2]);
                  }
                  else{
                    $this->page = $this->json_schedule($pathArr[2]);
                  }      
                }
                else{
                  $this->page = $this->json_schedule();
                }
            }
            elseif($pathArr[1] === "content"){
              if(isset($pathArr[2])){
                  if($pathArr[2] === "session"){
                    if(isset($pathArr[3]) && is_numeric($pathArr[3])){
                      $this->page = $this->json_content(0,$pathArr[3]);
                    }
                    else{
                      $this->page = $this->json_error();
                    }
                  }
                  elseif(is_numeric($pathArr[2])){
                    $this->page = $this->json_content($pathArr[2]);
                  }
                  else{
                    $this->page = $this->json_error();
                  }          
              }
              else{
                $this->page = $this->json_content();
              }
            }
            elseif($pathArr[1] === "sessions"){
              $this->page = $this->json_sessions();
            }
            elseif($pathArr[1] === "authors"){
                if(isset($pathArr[2])){
                  if($pathArr[2] === "content"){
                    if(isset($pathArr[3]) && is_numeric($pathArr[3])){
                      $this->page = $this->json_authors(0,$pathArr[3]);
                    }
                    else{
                      $this->page = $this->json_error();
                    }               
                  }
                  elseif(is_numeric($pathArr[2])){
                    $this->page = $this->json_authors($pathArr[2]);
                  }
                  else{
                    $this->page = $this->json_error();
                  }   
                    
                }
                else{
                  $this->page = $this->json_authors();
                }
                
            }
            elseif($pathArr[1] === "update"){
              $this->page = $this->json_update();
            }
            elseif($pathArr[1] === "login"){
              $this->page = $this->json_login();
            }
            else{
              $this->page = $this->json_error();
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

  /**
   * Function gets a string and filters any uncessary character out
   * @param string $x
   * @return string filter string x of an potentially harmful characters and trim any whitespace
   */
  private function sanitiseString($x) {
    return substr(trim(filter_var($x, FILTER_SANITIZE_STRING)), 0, 50);
  }

  /**
   * Function sanitises an Interger passed to it
   * @param int $x
   * @return int validate the $x variable and return it out of the function
   */
  private function sanitiseNum($x) {
    return filter_var($x, FILTER_VALIDATE_INT);
  }

  /**
   * @return JSON data about the api in JSON format
   */
  private function json_api(){
      $api = array("status" => 200,
                   "message"=>"Welcome",
                   "author"=>"Alex Tuersley",
                    "api"=> array("/api" => "returns api endpoints and basic info",
                                  "/api/schedule" => "returns the days of the schedule",
                                  "/api/schedule/times" => "returns all time slots within the database",  
                                  "/api/schedule/times?day=:int" => array("return" => "returns the time slots of the schedule", ":int" => "an integer with the value of dayString used to get all times for a specific day"),
                                  "/api/schedule/slotid" => array("return" => "All sessions within a time slot along with some information about them", "slotid"=>"id of a time slot"),
                                  "/api/sessions" => "return all sessions in the database along with their name and some other information",
                                  "/api/authors" => "return all authors",
                                  "/api/authors?search=name" => "searches for users with a name and return results",
                                  "/api/authors/id" => array("return"=>"an author with all the presentations they are in and other info","id"=>"id of an author in the database"),
                                  "/api/content" => "returns all the content",
                                  "/api/content/contentid" => array("return"=>"Detailed information about the content such as title, authors, time and room","contentd"=>"id of the content"),
                                  "/api/content?search=name" => "searches for content abstract or title with the name",
                                  "/api/content/session/sessionId" => array("return" => "All content associated with a session along with some information", "sessionId" => "id of a session"),
                                  "/api/login" => array("return" => "a JSON Web Token if the login is successful", "authentication" => "email and password from a form"),
                                  "/api/update" => array("return" => "updates the title of a session id the JSON Web Token used is correct", "authentication" => "JSON Web Token and the updated title of the session")
                                  ));
      return json_encode($api);
  } 
  /**
   * @return json encoded error message
   */
  private function json_error() {
    $msg = array("status"=>404,"message"=>"error page not found");
    return json_encode($msg);
  }
 
  /**
   * function for the schedule
   * @param $day is an integer of the day selected, if no day is selected runs a query for all days
   * @return JSON data based on query results
   */
  private function json_schedule($slot = 0, $times = null){
    if($slot > 0){
      $query = "SELECT sessions.sessionId, sessions.name as sessionname, rooms.name as room, session_types.name as type,(SELECT authors.name FROM authors WHERE authors.authorId = sessions.chairId)AS chair FROM sessions
      JOIN rooms ON rooms.roomId = sessions.roomId
      JOIN session_types ON sessions.typeId = session_types.typeId
      WHERE sessions.slotId = :slot";
      $slot = $this->sanitiseNum($slot);
      $params = ["slot" => $slot];
      if (isset($_REQUEST['page']) && is_numeric($_REQUEST['page'])) {
        $query .= " ORDER BY sessionname";
        $query .= " LIMIT 10 ";
        $query .= " OFFSET ";
        $query .= 10 * ($this->sanitiseNum($_REQUEST['page'])-1);
      }
    }
    elseif($times){
      if(isset($_REQUEST['day']) && is_numeric($_REQUEST['day'])) {
        $query = "SELECT slotId,startHour,startMinute,endHour,endMinute,type FROM slots WHERE dayInt = :dayint
        ORDER BY dayInt";
        $day = $this->sanitiseNum($_REQUEST["day"]);
        $params = ["dayint" => $day];
      }  
    }
    else{
      $query = "SELECT DISTINCT dayInt,dayString FROM slots
      ORDER BY dayInt";
      $params = [];
    }
    return ($this->recordset->getJSONRecordSet($query, $params));
  }

   /**
   * function for sessions
   * @return JSON data based on the query results
   */
  private function json_sessions(){
    $query = "SELECT sessionId, sessions.name as sessionname, 
              (SELECT rooms.name FROM rooms WHERE rooms.roomId = sessions.roomId) as room,
              (SELECT session_types.name FROM session_types WHERE session_types.typeId = sessions.typeId) as type,
              (SELECT authors.name FROM authors WHERE authors.authorId = sessions.chairId) as chair,
              slots.dayString,
              slots.startHour, slots.startMinute, slots.endHour, slots.endMinute
              from sessions
              JOIN slots ON sessions.slotId = slots.slotId
              ORDER BY sessions.sessionId";
    $params = [];
    return ($this->recordset->getJSONRecordSet($query, $params));
  }

  /**
   * function for author queries
   * @param $id is the id of an author that has been selected 
   * if a search has been run the searched name is grabbed from the url and runs a different query
   * @return JSON data based on query results 
   */ 
  private function json_authors($authorId = 0,$contentId = 0){
      if($authorId > 0){ 
          $query = "SELECT DISTINCT content_authors.contentId, content.title, content.abstract, sessions.name as sessionname, 
          (SELECT name FROM rooms WHERE rooms.roomId = sessions.roomId) as room,
          (SELECT name FROM session_types WHERE session_types.typeId = sessions.typeId) as sessiontype,
          (SELECT award FROM content WHERE content.contentId = content_authors.contentId) as award,
          slots.dayString,
          slots.startHour,
          slots.startMinute,
          slots.endHour,
          slots.endMinute
          FROM content_authors
          JOIN content ON content.contentId = content_authors.contentId
          JOIN sessions_content ON content.contentId = sessions_content.contentId
          JOIN sessions ON sessions_content.sessionId = sessions.sessionId
          JOIN slots ON sessions.slotId = slots.slotId
          WHERE content_authors.authorId = :authorid";
          $authorId = $this->sanitiseNum($authorId);
          $params = ["authorid" => $authorId];

          if (isset($_REQUEST['page']) && is_numeric($_REQUEST['page'])) {
            $query .= " ORDER BY content.title";
            $query .= " LIMIT 10 ";
            $query .= " OFFSET ";
            $query .= 10 * ($this->sanitiseNum($_REQUEST['page'])-1);
          }
      }
      elseif($contentId){
          $query = "SELECT authors.name as authorName,authorInst FROM authors 
          JOIN content_authors ON authors.authorId = content_authors.authorId
          WHERE content_authors.contentId = :contentId";
          $contentId = $this->sanitiseNum($contentId);
          $params = ["contentId" => $contentId];

      }
      else{
          $query = "SELECT DISTINCT authors.authorId, authors.name as authorName, authorInst FROM authors
          INNER JOIN content_authors ON authors.authorId = content_authors.authorId
          ";
          $params = [];
          if(isset($_REQUEST['search'])) {
            $query .= "WHERE authors.name LIKE :authorname";
            $name = str_replace("%20"," ", $_REQUEST['search']);
            $name = $this->sanitiseString("%".$name."%");
            $params = ["authorname" => $name];
          }    
          elseif (isset($_REQUEST['page']) && is_numeric($_REQUEST['page'])) {
            $query .= " ORDER BY authors.name";
            $query .= " LIMIT 10 ";
            $query .= " OFFSET ";
            $query .= 10 * ($this->sanitiseNum($_REQUEST['page'])-1);
          }    
      }
      return ($this->recordset->getJSONRecordSet($query, $params));
  }

  /**
   * function for content
   * @param $id - the id of some content which is used to gather further information about it
   * @return JSON data based on query results
   */
  private function json_content($contentId = 0,$sessionId = 0){
      if($contentId > 0){
        $query = "SELECT content.title, content.abstract, content.award, sessions.slotId, session_types.name, sessions.name, slots.startHour, slots.startMinute, slots.endHour, slots.endMinute, slots.dayString, authors.name as author, content_authors.authorInst FROM content
        JOIN content_authors ON content_authors.contentId = content.contentId
        JOIN authors ON authors.authorId = content_authors.authorId
        JOIN sessions_content ON sessions_content.contentId = content.contentId
        JOIN sessions ON sessions_content.sessionId = sessions.sessionId
        JOIN slots ON sessions.slotId = slots.slotId
        JOIN session_types ON sessions.typeId = session_types.typeId
        WHERE content.contentId = :id
        ORDER BY sessions.slotId";
        $contentId = $this->sanitiseNum($contentId);
        $params = ["id" => $contentId];
      }
      elseif($sessionId){
          $query = "SELECT content.contentId, title,abstract,award FROM content 
          JOIN sessions_content ON content.contentId = sessions_content.contentId
          WHERE sessions_content.sessionId = :sessionId";
          $sessionId = $this->sanitiseNum($sessionId);
          $params = ["sessionId" => $sessionId];
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
        elseif (isset($_REQUEST['page']) && is_numeric($_REQUEST['page'])) {
          $query .= " ORDER BY content.title";
          $query .= " LIMIT 10 ";
          $query .= " OFFSET ";
          $query .= 10 * ($this->sanitiseNum($_REQUEST['page'])-1);
        }
      }
      return ($this->recordset->getJSONRecordSet($query, $params));
  }

  /**
   * function gets php input and checks the database to see if the user exists
   * @return JSON Web token if the user credentials are correct, status of query and welcome message if successful
   */
  private function json_login() {
    $msg = "Invalid request. Username and password required";
    $status = 400;
    $jwt= null;
    $input = json_decode(file_get_contents("php://input"));
    


    if (!is_null($input->email) && $input->password != "") {  
      $query  = "SELECT email, username, admin, password FROM users WHERE email LIKE :email";
      $params = ["email" => $input->email];
      $res = json_decode($this->recordset->getJSONRecordSet($query, $params),true);

      if (password_verify($input->password, $res['data'][0]['password'])) {
        $msg = "User authorised. Welcome ". $res['data'][0]['username'];
        $status = 200;
        $token = array();
        $token['email'] = $input->email;
        $token['name'] = $res['data'][0]['username'];
        $token['admin'] = $res['data'][0]['admin'];
        $token['iat'] = time();
        $token['exp']= time() + 60*60;
        $jwt = \Firebase\JWT\JWT::encode($token,JWTKEY);
      } else { 
        $msg = "username or password are invalid";
        $status = 401;
      }
    }

    return json_encode(array("status" => $status, "message" => $msg, "token" => $jwt));
  }

  /**
   * function gets php input decodes the token passed through and updates a title if the token is authorised
   * if authorisation fails the appropriate http status code is returned along with some information
   * @return JSON message with query status
   */
  private function json_update() {
    $input = json_decode(file_get_contents("php://input"));
  
    if (is_null($input->token)) {
      return json_encode(array("status" => 401, "message" => "Not authorised"));
    }
    if (is_null($input->sessionname) || is_null($input->sessionId)) {  
      return json_encode(array("status" => 400, "message" => "Invalid request"));
    }   
    try {
      $tokenDecoded = \Firebase\JWT\JWT::decode($input->token, JWTKEY, array('HS256'));
    }
    catch (UnexpectedValueException $e) {        
      return json_encode(array("status" => 401, "message" => $e->getMessage()));
    }
    if($tokenDecoded->exp > time()){
      if($tokenDecoded->admin > 0){
          $query  = "UPDATE sessions SET name = :sessionname WHERE sessionId = :sessionId";
          $params = ["sessionname" => $input->sessionname, "sessionId" => $input->sessionId];
          $res = $this->recordset->getJSONRecordSet($query, $params);    
          return json_encode(array("status" => 200, "message" => "Update Successful"));
      }
      else{
        return json_encode(array("status" => 401, "message" => "Not Authorised")); 
      }
    
    }
    else{
      return json_encode(array("status" => 401, "message" => "Session Expired"));
    } 
  }

  public function get_page() {
    return $this->page;
  }
}
?>