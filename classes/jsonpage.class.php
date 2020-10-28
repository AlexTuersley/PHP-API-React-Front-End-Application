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
  $path = (empty($pathArr[1])) ? "api" : $pathArr[1];

  switch ($path) {
    case 'api':
      $this->page = $this->json_welcome();
      break;
    case 'actors':
      $this->page = $this->json_actors();
      break;
    case 'films':
      $this->page = $this->json_films();
      break;
    default:
      $this->page = $this->json_error();
      break;
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

private function json_welcome() {
  $msg = array("message"=>"welcome", "author"=>"Alex Tuersley");
  return json_encode($msg);
}

private function json_error() {
  $msg = array("message"=>"error");
  return json_encode($msg);
}

private function json_actors() {
    $query  = "SELECT first_name, last_name FROM actor";
    $params = [];
 
    if (isset($_REQUEST['search'])) {
        $query .= " WHERE last_name LIKE :term";
        $term = $this->sanitiseString("%".$_REQUEST['search']."%");
        $params = ["term" => $term];
    }else {
        if (isset($_REQUEST['id'])) {
          $query .= " WHERE actor_id = :term";
          $term = $this->sanitiseNum($_REQUEST['id']);
          $params = ["term" => $term];
        }
    }
    if (isset($_REQUEST['page'])) {
        $query .= " ORDER BY last_name";
        $query .= " LIMIT 10 ";
        $query .= " OFFSET ";
        $query .= 10 * ($this->sanitiseNum($_REQUEST['page'])-1);
    }
    return ($this->recordset->getJSONRecordSet($query, $params));
}

private function json_films() {
  $query  = "SELECT title, description FROM film";
  $params = [];
 
  if (isset($_REQUEST['search'])) {
    $query .= " WHERE title LIKE :title";
    $params = ["title" => "%".$_REQUEST['search']."%"];
  }else {
    if (isset($_REQUEST['id'])) {
      $query .= " WHERE film_id = :term";
      $term = $this->sanitiseNum($_REQUEST['id']);
      $params = ["term" => $term];
    }
}
if (isset($_REQUEST['page'])) {
    $query .= " ORDER BY title";
    $query .= " LIMIT 10 ";
    $query .= " OFFSET ";
    $query .= 10 * ($this->sanitiseNum($_REQUEST['page'])-1);
}
  return ($this->recordset->getJSONRecordSet($query, $params));
}

public function get_page() {
  return $this->page;
}
}
?>