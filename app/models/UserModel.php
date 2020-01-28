<?php
  class UserModel
  {
    private $db;

    public function __construct()
    {
      $this->db = new Database;
    }

    // Register user
    public function register($data)
    {
      $this->db->query('INSERT INTO users (name, email, password) VALUES(:name, :email, :password)');
      // Bind values
      $this->db->bind(':name', $data['name']);
      $this->db->bind(':email', $data['email']);
      $this->db->bind(':password', $data['password']);

      // Execute
      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

    // Login UserModel
    public function login($email, $password)
    {
      $this->db->query('SELECT * FROM users WHERE email = :email');
      $this->db->bind(':email', $email);

      $row = $this->db->getSingle();

      $hashed_password = $row->password;
      if (password_verify($password, $hashed_password)) {
        return $row;
      } else {
        return false;
      }
    }

    // Change password
    public function resetPass($data)
    {
      $this->db->query('UPDATE users SET password = :password WHERE email = :email');
      // Bind values
      $this->db->bind(':email', $data['email']);
      $this->db->bind(':password', $data['password']);

      // Execute
      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

    public function editProfile($data)
    {
      $this->db->query('UPDATE users SET name = :name, email = :email, password = :password WHERE id = :id');
      // Bind values
      $this->db->bind(':name', $data['name']);
      $this->db->bind(':email', $data['email']);
      $this->db->bind(':password', $data['password']);
      $this->db->bind(':id', $_SESSION['user_id']);

      // Execute
      if ($this->db->execute()) {
        return true;
      } else {
        return false;
      }
    }

    // Find user by email
    public function findUserByEmail($email)
    {
      $this->db->query('SELECT * FROM users WHERE email = :email');
      // Bind value
      $this->db->bind(':email', $email);

      $row = $this->db->getSingle();

      // Check row
      if ($this->db->rowCount() > 0) {
        return true;
      } else {
        return false;
      }
    }

    // Get user by ID
    public function getUserById($id)
    {
      $this->db->query('SELECT * FROM users WHERE id = :id');
      // Bind value
      $this->db->bind(':id', $id);

      return $this->db->getSingle();
    }

    public function getUserIdByEmail($email)
    {
      $this->db->query("SELECT id FROM users WHERE email = :email");
      $this->db->bind(':email', $email);
      try {
        $result = $this->db->getSingle();
        return $result->id;
      } catch (\Throwable $th) {
        return null;
      }
    }

    public function createPasswordReset($user_id, $token)
    {
      // Delete previous tokens before saving a new one
      try {
        $this->deletePasswordResetToken($user_id);
      } catch (Throwable $th) {
        throw $th;
      }
      $this->db->query("INSERT INTO pass_reset_token (user_id, expire_date, token) VALUES (:user_id, :expire_date, :token)");
      $expire_date = new DateTime();
      $expire_date->add(new DateInterval('PT12H'));
      $this->db->bind(':user_id', $user_id);
      $this->db->bind(':expire_date', $expire_date->format('Y-m-d H:i:s'));
      $this->db->bind(':token', $token);
      try {
        $this->db->execute();
      } catch (Throwable $th) {
        throw new Exception('Creating the password token failed: ' . $th->getMessage());
      }
    }

    public function deletePasswordResetToken($user_id)
    {
      $this->db->query("DELETE FROM pass_reset_token WHERE user_id = :user_id");
      $this->db->bind(':user_id', $user_id);
      try {
        $this->db->execute();
      } catch (Throwable $th) {
        throw new Exception('Deleting the password token failed: ' . $th->getMessage());
      }
    }

    public function getUserIdByToken($token)
    {
      $this->db->query("SELECT user_id FROM pass_reset_token WHERE token = :token");
      $this->db->bind(':token', $token);
      try {
        $row = $this->db->getSingle();
        return $row->user_id;
      } catch (\Throwable $th) {
        return null;
      }
    }

    public function setNewPasswordFromReset($password, $token)
  {
    $this->db->query("SELECT user_id AS id, token FROM pass_reset_token WHERE token = :token");
    $this->db->bind(':token', $token);
    $row = $this->db->getSingle();
    if (isset($row)) {
      try {
        $this->db->query('UPDATE users SET password = :password WHERE id = :id');
        // Bind values
        $this->db->bind(':password', $password);
        $this->db->bind(':id', $row->id);

        // Execute
        if ($this->db->execute()) {
          $this->deletePasswordResetToken($row->user_id);
          return true;
        } else {
          return false;
        }
      } catch (Throwable $th) {
        throw new Exception('Updating the password from reset token failed: ' . $th->getMessage());
      }
    } else {
      throw new Exception('User ID for this token was not found');
    }
  }
 }