<?php 
/*
  Bware:RobertoFlores
  TSv:120615
  Name:MysqlConnector
*/

class MysqlConnector {
  
  private $conn = NULL,
          $conn_status = FALSE;
          
  function __construct($servername, $db, $username, $password) {
    // Create connection
    $this->conn = new mysqli($servername, $username, $password);
    if ($this->conn->connect_error) {
      die("Connection failed: " . $this->conn->connect_error);
      return FALSE;
    }
    else {
      if ($this->conn->set_charset("utf8")) {
        if($this->conn->select_db($db)) {
          $this->conn_status = TRUE;
          return TRUE;
        }
        else {
          die("Can't connect to DB:".$db);
          return FALSE;
        }
      }
      else {
        die("Can't change character set to UTF-8");
        return FALSE;
      }
    }
  }
  
  function query($query) {
    if($this->conn_status)
      return $this->conn->query($query);
    else
      return NULL;
  }
  /* remember $result->free(); */
  
  function fetchAll($result) {
    $rows = array();
    while($row = $result->fetch_array(MYSQLI_ASSOC)) { // MYSQLI_BOTH
      $rows[] = $row;
    }
    return $rows;
  }
  
  function queryFetchAll($query) {
    $result = $this->query($query);
    $rows = $this->fetchAll($result);
    $result->free();
    return $rows;
  }
  
  function numRows($result) {
    return $result->num_rows;
  }
  
  function affectedRows() {
    return $this->conn->affected_rows;
  }
  
  function insertId() {
    return $this->conn->insert_id;
  }
  
  function real_escape_string($string){
    return $this->conn->real_escape_string($string);
  }

  function __destruct() {
    if($this->conn_status)
      mysqli_close($this->conn);
  }

}
?>