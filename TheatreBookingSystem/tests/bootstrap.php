<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/pricing.php';
require_once __DIR__ . '/../includes/functions.php';

/**
 * Open a mysqli connection to the test database described by the
 * DB_HOST / DB_PORT / DB_USER / DB_PASS / TEST_DB_NAME environment
 * variables, creating the database and schema if needed.
 *
 * Returns null when no database server is reachable, so integration
 * tests can self-skip on machines without MySQL.
 */
function test_db_connect(): ?mysqli {
    $host   = getenv('DB_HOST') ?: '127.0.0.1';
    $port   = (int)(getenv('DB_PORT') ?: 3306);
    $user   = getenv('DB_USER') ?: 'root';
    $pass   = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
    $dbname = getenv('TEST_DB_NAME') ?: 'theatre_booking_test';

    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = @mysqli_connect($host, $user, $pass, '', $port);
    if (!$conn) {
        return null;
    }

    mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4");
    mysqli_select_db($conn, $dbname);
    mysqli_set_charset($conn, 'utf8mb4');

    // Load the schema (idempotent: CREATE TABLE IF NOT EXISTS / INSERT IGNORE)
    $schema = file_get_contents(__DIR__ . '/../database/db_init.sql');
    if (mysqli_multi_query($conn, $schema)) {
        while (mysqli_more_results($conn) && mysqli_next_result($conn)) {
            if ($res = mysqli_store_result($conn)) {
                mysqli_free_result($res);
            }
        }
    }
    if (mysqli_errno($conn)) {
        fwrite(STDERR, 'Schema load error: ' . mysqli_error($conn) . PHP_EOL);
        return null;
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    return $conn;
}
