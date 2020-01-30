<?php
class DataModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  /**
   * Insert a new data row into the database
   *
   * @param $column1 with row name
   * @param $column2 with row data
   */
  public function importCVS($column1, $column2){
    $this->db->query('INSERT INTO information (name, data) VALUES(:name, :data)');

    $this->db->bind(':name', $column1);
    $this->db->bind(':data', $column2);

    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function clearCSV(){
    $this->db->query('DELETE FROM INFORMATION');

    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function getAllInformation() {
    $this->db->query('SELECT name, data FROM information');

    return $this->db->getAll();
  }
}
