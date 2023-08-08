<?php
  // Load Config
  require_once 'config/config.php';

  require_once 'helpers/url_helper.php';
  require_once 'helpers/session_helper.php';
  require_once 'helpers/posts_helper.php';

// Autoload Core Libraries
spl_autoload_register(function ($className) {
  if (file_exists(APPROOT . '/models/' . $className . '.php')) {
    // Load classes from the "models" directory
    require_once 'models/' . $className . '.php';
  } else {
    // Load classes from the "libraries" directory
    require_once 'libraries/' . $className . '.php';
  }
});
  
