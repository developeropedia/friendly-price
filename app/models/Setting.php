<?php
  class Setting {
    private $db;

    public function __construct(){
      $this->db = Database::getInstance();
    }

    public function getSettings() {
      $query = "SELECT * FROM settings";
      $this->db->query($query);
      $this->db->execute();
      return $this->db->resultSet();
    }

    public function getSetting($setting_name) {
      $query = "SELECT * FROM settings WHERE name = :name";
      $this->db->query($query);
      $this->db->bind(":name", $setting_name);
      $this->db->execute();
      $setting = $this->db->single();
      
      return $setting->value;
    }
}