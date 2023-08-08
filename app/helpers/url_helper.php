<?php
  // Simple page redirect
  function redirect($page){
    echo "<script>window.location.href = '" . URLROOT . "/" . $page . "'</script>";
    // header('location: '.URLROOT.'/'.$page);
  }