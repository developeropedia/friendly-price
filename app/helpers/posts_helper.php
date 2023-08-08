<?php

// Fomat views in short form
function formatStats($views) {
    if ($views >= 1000 && $views < 1000000) {
        return number_format($views / 1000, 1) . 'K';
    } elseif ($views >= 1000000 && $views < 1000000000) {
        return number_format($views / 1000000, 1) . 'M';
    } elseif ($views >= 1000000000) {
        return number_format($views / 1000000000, 1) . 'B';
    } else {
        return $views;
    }
}

// Show time ago
function timeAgo($timestamp) {
    // Get the current local time as an array
    $localTimeArray = localtime(time(), true);
    var_dump($localTimeArray);

    // Extract the individual components from the local time array
    $hours = $localTimeArray["tm_hour"];
    $minutes = $localTimeArray["tm_min"];
    $seconds = $localTimeArray["tm_sec"];
    $month = $localTimeArray["tm_mon"] + 1; // Adjust month value as it is zero-based
    $day = $localTimeArray["tm_mday"];
    $year = $localTimeArray["tm_year"] + 1900; // Adjust year value as it is years since 1900

    // Convert local time components to a timestamp
    $current_time = mktime($hours, $minutes, $seconds, $month, $day, $year);
    // var_dump(date("d-m-y h:i:s", $current_time));

    $time_diff = $current_time - $timestamp;
    $seconds = $time_diff;
    $minutes = round($time_diff / 60);
    $hours = round($time_diff / 3600);
    $days = round($time_diff / 86400);
    $weeks = round($time_diff / 604800);
    $months = round($time_diff / 2419200);
    $years = round($time_diff / 29030400);

    if ($seconds <= 60) {
        return $seconds . ' seconds ago';
    } elseif ($minutes <= 60) {
        return $minutes . ' minutes ago';
    } elseif ($hours <= 24) {
        return $hours . ' hours ago';
    } elseif ($days <= 7) {
        return $days . ' days ago';
    } elseif ($weeks <= 4) {
        return $weeks . ' weeks ago';
    } elseif ($months <= 12) {
        return $months . ' months ago';
    } else {
        return $years . ' years ago';
    }
}


?>