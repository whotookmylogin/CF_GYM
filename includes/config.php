<?php
/**
 * Centralized Configuration File
 * This file handles environment configuration, database connection,
 * and language setup for the GYM One application.
 */

if (!isset($_SESSION)) {
    session_start();
}

/**
 * Read and parse .env file
 * @param string $file_path Path to .env file
 * @return array Associative array of environment variables
 */
function read_env_file($file_path)
{
    if (!file_exists($file_path)) {
        die("Environment file not found: $file_path");
    }

    $env_file = file_get_contents($file_path);
    $env_lines = explode("\n", $env_file);
    $env_data = [];

    foreach ($env_lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue; // Skip empty lines and comments
        }

        $line_parts = explode('=', $line, 2);
        if (count($line_parts) == 2) {
            $key = trim($line_parts[0]);
            $value = trim($line_parts[1]);
            $env_data[$key] = $value;
        }
    }

    return $env_data;
}

// Determine the correct path to .env based on the calling file's location
$env_path = __DIR__ . '/../.env';
$env_data = read_env_file($env_path);

// Database configuration
$db_host = $env_data['DB_SERVER'] ?? '';
$db_username = $env_data['DB_USERNAME'] ?? '';
$db_password = $env_data['DB_PASSWORD'] ?? '';
$db_name = $env_data['DB_NAME'] ?? '';

// Application configuration
$business_name = $env_data['BUSINESS_NAME'] ?? 'GYM One';
$lang_code = $env_data['LANG_CODE'] ?? 'GB';
$version = $env_data['APP_VERSION'] ?? '0.6.1';
$capacity = $env_data['CAPACITY'] ?? 0;
$currency = $env_data['CURRENCY'] ?? 'USD';

$lang = $lang_code;

// Load language translations
$langDir = __DIR__ . "/../assets/lang/";
$langFile = $langDir . "$lang.json";

if (!file_exists($langFile)) {
    die("Language file not found: $langFile");
}

$translations = json_decode(file_get_contents($langFile), true);

if ($translations === null) {
    die("Failed to parse language file: $langFile");
}

// Database connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection error. Please contact support.");
}

// Set charset to utf8mb4 for full Unicode support
$conn->set_charset("utf8mb4");

/**
 * Check for application updates
 * @param string $current_version Current application version
 * @return bool True if update is available
 */
function check_for_updates($current_version)
{
    $file_path = 'https://api.gymoneglobal.com/latest/version.txt';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $file_path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

    $latest_version = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200 && !empty($latest_version)) {
        return version_compare(trim($latest_version), $current_version) > 0;
    }

    return false;
}
