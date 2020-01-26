<?php
  class PostModel {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

    public function getPosts(){
      $this->db->query('SELECT
                        posts.id as postId,
                        users.id as userId,
                        posts.title as title,
                        users.name as name,
                        posts.body as body,
                        posts.created_at as postCreated,
                        users.created_at as userCreated,
                        (SELECT GROUP_CONCAT(i.file_name) FROM images i WHERE post_id = posts.id) AS filenames
                        FROM posts
                        INNER JOIN users
                        ON posts.user_id = users.id
                        ORDER BY posts.created_at DESC
                        ');

      return $this->db->getAll();
    }

    public function getPost($name){
      $this->db->query('SELECT
                        posts.id as postId,
                        users.id as userId,
                        posts.title as title,
                        users.name as name,
                        posts.body as body,
                        posts.created_at as postCreated,
                        users.created_at as userCreated,
                        (SELECT GROUP_CONCAT(i.file_name) FROM images i WHERE post_id = posts.id) AS filenames
                        FROM posts
                        INNER JOIN users
                        ON posts.user_id = users.id
                        WHERE users.name = :name
                        ORDER BY posts.created_at DESC
                        ');

      $this->db->bind(':name', $name);
      return $this->db->getAll();
    }

    public function addPost($data){
      $this->db->query('INSERT INTO posts (title, user_id, body) VALUES(:title, :user_id, :body)');
      // Bind values
      $this->db->bind(':title', $data['title']);
      $this->db->bind(':user_id', $data['user_id']);
      $this->db->bind(':body', $data['body']);

      // Execute
      if($this->db->execute()){
        $postId = $this->db->lastInsertId();
        if (isset($data['filenames'])) {
          foreach($data['filenames'] as $filename) {
            $this->addImage($data, $filename, $postId);
          }
        }
        return true;
      } else {
        return false;
      }
    }

    public function addImage($data, $filename, $postId) {
      $this->db->query('INSERT INTO images (file_name, user_id, post_id) VALUES(:filename, :user_id, :post_id)');
      // Bind values
      $this->db->bind(':filename', $filename);
      $this->db->bind(':user_id', $data['user_id']);
      $this->db->bind(':post_id', $postId);

      // Execute
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    public function updatePost($data){
      $this->db->query('UPDATE posts SET title = :title, body = :body WHERE id = :id');
      // Bind values
      $this->db->bind(':id', $data['id']);      
      $this->db->bind(':title', $data['title']);
      $this->db->bind(':body', $data['body']);

      // Execute
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    public function getPostById($id){
      $this->db->query('SELECT * FROM posts WHERE id = :id');
      $this->db->bind(':id', $id);

      $row = $this->db->getSingle();
      
      return $row;
    }

    public function deletePost($id){
      $this->db->query('DELETE FROM posts WHERE id = :id;
                              DELETE FROM images WHERE post_id = :id');
      // Bind values
      $this->db->bind(':id', $id);

      // Execute
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }
  }