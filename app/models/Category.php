<?php
  class Category {
    private $db;

    public function __construct(){
      $this->db = Database::getInstance();
    }

    public function insert($name, $type, $other_cat_id = 0) {
        $query = "";
        if($type === "main") {
            $query = "INSERT INTO main_categories (name) VALUES (:name)";
            $this->db->query($query);
            $this->db->bind(":name", $name);
        } else if ($type === "normal") {
            $query = "INSERT INTO categories (main_cat_id, name) VALUES (:main_cat_id, :name)";
            $this->db->query($query);
            $this->db->bind(":name", $name);
            $this->db->bind(":main_cat_id", $other_cat_id);
        } else if ($type === "sub") {
            $query = "INSERT INTO sub_categories (cat_id, name) VALUES (:cat_id, :name)";
            $this->db->query($query);
            $this->db->bind(":name", $name);
            $this->db->bind(":cat_id", $other_cat_id);
        }

        if($this->db->execute()) {
          return $this->db->lastInsertID();
        } else {
          return false;
        }
    }


    public function getMainCategoryById($id) {
      $query = "SELECT * FROM main_categories WHERE id = :id";
      $this->db->query($query);
      $this->db->bind(":id", $id);
      $this->db->execute();
      return $this->db->single();
    }

    public function getCategoryById($id) {
      $query = "SELECT * FROM categories WHERE id = :id";
      $this->db->query($query);
      $this->db->bind(":id", $id);
      $this->db->execute();
      return $this->db->single();
    }

    public function getSubCategoryById($id) {
      $query = "SELECT * FROM sub_categories WHERE id = :id";
      $this->db->query($query);
      $this->db->bind(":id", $id);
      $this->db->execute();
      return $this->db->single();
    }

    public function getMainCategories() {
      $query = "SELECT * FROM main_categories";
      $this->db->query($query);
      $this->db->execute();
      return $this->db->resultSet();
    }

    public function getCategories() {
      $query = "SELECT c.name AS cat_name, c.id AS cat_id, mc.id AS main_cat_id, mc.name AS main_cat_name FROM categories c 
      INNER JOIN main_categories mc ON c.main_cat_id = mc.id";
      $this->db->query($query);
      $this->db->execute();
      return $this->db->resultSet();
    }

    public function getSubCategories() {
      $query = "SELECT sc.name AS sub_cat_name, sc.id AS sub_cat_id, c.id AS cat_id, c.name AS cat_name FROM sub_categories sc
      INNER JOIN categories c ON c.id = sc.cat_id";
      $this->db->query($query);
      $this->db->execute();
      return $this->db->resultSet();
    }
}