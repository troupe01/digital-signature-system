<?php
require_once "vendor/autoload.php";
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

try {
    $qr = QrCode::create("test");
    echo "QrCode class loaded successfully\n";
    echo "Available methods: " . implode(", ", get_class_methods($qr)) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

