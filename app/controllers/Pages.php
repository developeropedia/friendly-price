<?php
  class Pages extends Controller {
    private $settingsModel;

    public function __construct(){
      $this->settingsModel = $this->model("Setting");
    }
    
    public function index(){
      $site_name = $this->settingsModel->getSetting("site_name");
      $data = [
        'title' => $site_name,
      ];
     
      $this->view('pages/index', $data);
    }

    public function about(){
      $data = [
        'title' => 'About Us'
      ];

      $this->view('pages/about', $data);
    }
  }