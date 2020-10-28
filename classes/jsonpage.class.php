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
            elseif($pathArr[1] == "session"){

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
          echo "api";
        }
    }
    else{
      $this->page = $this->json_error();
    }

  }

  //an arbitrary max length of 20 is set
  private function sanitiseString($x) {
    return substr(trim(filter_var($x, FILTER_SANITIZE_STRING)), 0, 20);
  }
  //an arbitrary max range of 1000 is set
  private function sanitiseNum($x) {
    return filter_var($x, FILTER_VALIDATE_INT, array("options"=>array("min_range"=>0, "max_range"=>1000)));
  }

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
          if(isset($_REQUEST['search'])) {
              $query = "SELECT DISTINCT authors.name, authorInst,title, abstract, award, sessions.name FROM authors
              INNER JOIN content_authors On authors.authorId = content_authors.authorId
              INNER JOIN content ON content_authors.contentId = content.contentId
              INNER JOIN sessions_content ON content.contentId = sessions_content.contentId
              INNER JOIN sessions ON  sessions_content.sessionId = sessions.sessionId 
              WHERE authors.name LIKE :authorname";
              $name = str_replace("%20"," ", $_REQUEST['search']);
              $name = $this->sanitiseString("%".$name."%");
              $params = ["authorname" => $name];
          }
          else{
            $query = "SELECT DISTINCT authors.name,authorInst FROM authors
            INNER JOIN content_authors ON authors.authorId = content_authors.authorId
            ORDER BY authors.name";
            $params = [];
          }
        
      }
      return ($this->recordset->getJSONRecordSet($query, $params));
  }

  private function json_update(){

  }
  private function json_login(){

  }

  public function get_page() {
    return $this->page;
  }
}
?>