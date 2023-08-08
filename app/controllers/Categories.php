<?php
  class Categories extends Controller {
    private $settingsModel;
    private $categoriesModel;

    public function __construct(){
      $this->settingsModel = $this->model("Setting");
      $this->categoriesModel = $this->model("Category");
    }
    
    public function index(){
      
    }

    public function insert() {
        if(isset($_POST['name']) && !empty($_POST['name'])) {
            $name = $_POST['name'];
            $type = $_POST['type'];
            $other_cat_id = $_POST['other_cat_id'] ?? 0;

            $res = $this->categoriesModel->insert($name, $type, $other_cat_id);
            
            if(!empty($res)) {
                echo "Category added successfully!";
            } else {
                echo "Error in adding category!";
            }
        }
    }

    public function getMainCategoryById($id) {
        $category = $this->categoriesModel->getMainCategoryById($id);

        echo json_encode($category);
    }

    public function getCategoryById($id) {
        $category = $this->categoriesModel->getCategoryById($id);

        echo json_encode($category);
    }

    public function getSubCategoryById($id) {
        $category = $this->categoriesModel->getSubCategoryById($id);

        echo json_encode($category);
    }

    public function getMainCategories() {
        $categories = $this->categoriesModel->getMainCategories();

        echo json_encode($categories);
    }

    public function getCategories() {
        $categories = $this->categoriesModel->getCategories();

        echo json_encode($categories);
    }

    public function getSubCategories() {
        $categories = $this->categoriesModel->getSubCategories();

        echo json_encode($categories);
    }
  }