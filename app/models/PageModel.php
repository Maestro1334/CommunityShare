<?php
class PageModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database;
  }

  /**
   * Insert a new donation into the database
   *
   * @param $data array with user donation information
   * @return bool
   */
  public function addDonation($data){
    $this->db->query('INSERT INTO donation (amount, status) VALUES(:amount, :status)');

    $this->db->bind(':amount', ($data['amount']));
    $this->db->bind(':status', ($data['status']));

    if ($this->db->execute()) {
      return $this->db->lastInsertId();
    } else {
      return false;
    }
  }

  /**
   * Update payment status
   *
   * @param $payment_id
   * @param $status
   * @return bool
   */
  public function updateStatus($payment_id, $status){
    $this->db->query('UPDATE donation SET status = :status WHERE id = :id');

    $this->db->bind(':id', $payment_id);
    $this->db->bind(':status', $status);

    if ($this->db->execute()) {
      return true;
    } else {
      return false;
    }
  }
}