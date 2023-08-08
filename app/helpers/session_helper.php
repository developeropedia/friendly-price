<?php

session_start();

function flash($name = "", $message = "", $class = "successMsg") {
    if(!empty($name)) {
        if(!empty($message) && empty($_SESSION[$name])) {
            $_SESSION[$name] = $message;
        }

        $icons = ["successMsg" => "<i style='color: #00D26A' class='bi bi-check-circle-fill'></i>", "errorMsg" => "<i style='color: #ff4d40' class='bi bi-x-circle-fill'></i>"];

        if(empty($message) && !empty($_SESSION[$name])) {
            echo "<div class='$class' id='msg-flash'>$icons[$class] &nbsp $_SESSION[$name]</div>";
            unset($_SESSION[$name]);
        }
    }
}