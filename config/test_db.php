<?php

require_once 'config.php';

try {
    $db = getDBConnection();
    echo "Database connected successfully!";
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}