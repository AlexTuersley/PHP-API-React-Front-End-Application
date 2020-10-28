<?php
/**
* Creates a JSON page based on the parameters
* 
* @author Alex Tuersley
* 
*/
class JSONpage {
  private $page; 
  private $recordset;

  /**
  * @param $pathArr - an array containing the route information
  */
  //THIS IS WHERE I PUT THE ROUTING FROM LAST YEAR
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
            elseif($pathArr[1] == "presentations"){
              //content

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
    
            }
            elseif($pathArr[1] == "login"){
    
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
   * returns a json encoded error message
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
  private function json_schedule($day = 0){
    if($day > 0){
      $query = "SELECT slotId, startHour,startMinute, endHour,endMinute, dayString FROM slots
                WHERE dayInt = :dayint";
      $day = $this->sanitiseNum($day);
      $params = ["dayint" => $day];
    }
    else{
      $query = "SELECT DISTINCT dayInt, dayString
                FROM slots
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
   * @return string json query results 
   */ 
  private function json_authors($id = 0){
      if($id > 0){ 
          $query = "SELECT DISTINCT authors.name, authorInst,title, abstract, award, sessions.name FROM authors
          INNER JOIN content_authors On authors.authorId = content_authors.authorId
          INNER JOIN content ON content_authors.contentId = content.contentId
          INNER JOIN sessions_content ON content.contentId = sessions_content.contentId
          INNER JOIN sessions ON  sessions_content.sessionId = sessions.sessionId 
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
  private function json_presentations($id = 0){
      if($id > 0){

      }
      else{

      }
      return ($this->recordset->getJSONRecordSet($query, $params));
  }
  private function json_update(){

  }
  private function json_login(){
    include('pdodb.class.php');
    $data = json_decode(file_get_contents("php://input"));
    $email = isset($data->email) ? filter_var($data->email,FILTER_SANITIZE_STRING,FILTER_NULL_ON_FAILURE) : null;
    $password = isset($data->password) ? filter_var($data->password,FILTER_SANITIZE_STRING,FILTER_NULL_ON_FAILURE) : null;
    if(!is_null($email)) {
      $sqlQuery = "SELECT email, password FROM users WHERE email LIKE :email";
      $params = array("email" => $email); 
      $dbConn = pdoDB::getConnection();
      $queryResult = $dbConn->prepare($sqlQuery);
      $queryResult->execute($params);
      $rows = $queryResult->fetchAll(PDO::FETCH_ASSOC);
      $dbConn = null;

      if (count($rows) > 0) {
        if (password_verify($password, $rows[0]['password']))
        {
          $token = array();
          $token['email'] = $email;
          $token['exp'] = "hello";
          $encodedToken = JWT::encode($token, 'secret_server_key');

          http_response_code(201);
          echo json_encode(array("message" => "User Logged in.", "token" => $encodedToken));
        }
        else {
          http_response_code(201);
          echo json_encode(array("message" => "Invalid password."));
        }
      }
      else
      {
        http_response_code(201);
        echo json_encode(array("message" => "Account not found."));
      } 
    } 
    else{
      http_response_code(400);
      echo json_encode(array("message" => "Error: Data is incomplete."));
    }
  }

  public function get_page() {
    return $this->page;
  }
}
?>