<?php
/**
* Return a JSON recordset
* @author Alex Tuersley
*/
class JSONRecordSet extends RecordSet {
/**
 * function to return a record set as an associative array
 * @param $query   string with sql to execute to retrieve the record set
 * @param $params  associative array of params for preparted statement 
 * @return string  a json documnent
 */
function getJSONRecordSet($query, $params = null) {
  $stmt = $this->getRecordSet($query, $params);
  $recordSet = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $nRecords = count($recordSet);
  if($nRecords > 0){
     $status = 200;
  }
  else{
    $status = 204;
  }
  return json_encode(array("status" => $status,"count"=>$nRecords, "data"=>$recordSet));                 
}
}
?>