<?php
/**
 * Update designation in employee_details table (MySQL) from source CSV.
 *
 * Reads: data/csv/employee_details_source.csv (or employee_details 1.csv)
 * Updates: employee_details.designation only (matched by empId)
 *
 * Run: http://localhost/timesheet_demo/update_designations_db.php
 *
 * IMPORTANT: Backup employee_details table before running.
 */

header('Content-Type: text/html; charset=utf-8');

$baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'csv' . DIRECTORY_SEPARATOR;

function resolveCsvFile($baseDir, $candidates) {
    foreach ($candidates as $name) {
        $path = $baseDir . $name;
        if (file_exists($path)) {
            return $path;
        }
    }
    throw new RuntimeException('Source CSV not found in data/csv/. Expected: employee_details_source.csv');
}

function readDesignations($path) {
    $map = array();
    $handle = fopen($path, 'r');
    if ($handle === false) {
        throw new RuntimeException("Cannot open: $path");
    }
    $headers = fgetcsv($handle);
    $empIdIndex = array_search('empId', $headers);
    $designationIndex = array_search('designation', $headers);
    if ($empIdIndex === false || $designationIndex === false) {
        fclose($handle);
        throw new RuntimeException('CSV must contain empId and designation columns');
    }
    while (($data = fgetcsv($handle)) !== false) {
        if (isset($data[$empIdIndex], $data[$designationIndex])) {
            $map[trim($data[$empIdIndex])] = $data[$designationIndex];
        }
    }
    fclose($handle);
    return $map;
}

function loadDbConfig() {
    $candidates = array(
        __DIR__ . '/application/config/database.php',
        dirname(__DIR__) . '/timesheet_demo/application/config/database.php',
        '//elogicserv/xampp/htdocs/timesheet_demo/application/config/database.php',
    );

    $configFile = null;
    foreach ($candidates as $path) {
        if (file_exists($path)) {
            $configFile = $path;
            break;
        }
    }

    if ($configFile === null) {
        throw new RuntimeException(
            'database.php not found. Ensure application/config/database.php exists in the project.'
        );
    }

    if (!defined('BASEPATH')) {
        define('BASEPATH', __DIR__ . '/system/');
    }
    if (!defined('ENVIRONMENT')) {
        define('ENVIRONMENT', 'development');
    }

    $active_group = 'default';
    $db = array();
    include $configFile;
    if (!isset($db[$active_group])) {
        throw new RuntimeException('Database config missing');
    }
    return $db[$active_group];
}

try {
    $sourcePath = resolveCsvFile($baseDir, array(
        'employee_details_source.csv',
        'employee_details 1.csv',
        'employee_details_1.csv',
    ));

    $designations = readDesignations($sourcePath);
    $dbConfig = loadDbConfig();

    $mysqli = new mysqli(
        $dbConfig['hostname'],
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['database']
    );

    if ($mysqli->connect_error) {
        throw new RuntimeException('Database connection failed: ' . $mysqli->connect_error);
    }

    $mysqli->set_charset(isset($dbConfig['char_set']) ? $dbConfig['char_set'] : 'utf8');

    $selectStmt = $mysqli->prepare('SELECT empId, name, designation FROM employee_details WHERE empId = ? LIMIT 1');
    $updateStmt = $mysqli->prepare('UPDATE employee_details SET designation = ?, updated_at = NOW() WHERE empId = ?');

    if (!$selectStmt || !$updateStmt) {
        throw new RuntimeException('Failed to prepare SQL: ' . $mysqli->error);
    }

    $updated = array();
    $unchanged = array();
    $notFound = array();

    $mysqli->begin_transaction();

    foreach ($designations as $empId => $newDesignation) {
        $selectStmt->bind_param('i', $empId);
        $selectStmt->execute();
        $result = $selectStmt->get_result();
        $row = $result->fetch_assoc();

        if (!$row) {
            $notFound[] = array('empId' => $empId, 'new' => $newDesignation);
            continue;
        }

        if ($row['designation'] === $newDesignation) {
            $unchanged[] = array(
                'empId' => $empId,
                'name' => $row['name'],
                'designation' => $newDesignation,
            );
            continue;
        }

        $updateStmt->bind_param('si', $newDesignation, $empId);
        if (!$updateStmt->execute()) {
            throw new RuntimeException('Update failed for empId ' . $empId . ': ' . $updateStmt->error);
        }

        $updated[] = array(
            'empId' => $empId,
            'name' => $row['name'],
            'old' => $row['designation'],
            'new' => $newDesignation,
        );
    }

    $mysqli->commit();

    echo '<pre>';
    echo "Database: {$dbConfig['database']}\n";
    echo "Table: employee_details\n";
    echo "Source: $sourcePath\n\n";
    echo 'Source designations: ' . count($designations) . "\n";
    echo 'Updated in DB: ' . count($updated) . "\n";
    echo 'Already same: ' . count($unchanged) . "\n";
    echo 'Not found in DB: ' . count($notFound) . "\n\n";

    if (count($updated)) {
        echo "=== UPDATED IN employee_details ===\n";
        foreach ($updated as $c) {
            echo "empId {$c['empId']} ({$c['name']})\n";
            echo "  OLD: {$c['old']}\n";
            echo "  NEW: {$c['new']}\n\n";
        }
    }

    if (count($notFound)) {
        echo "=== NOT FOUND IN DB ===\n";
        foreach ($notFound as $c) {
            echo "empId {$c['empId']} -> {$c['new']}\n";
        }
    }

    echo "\nDone. employee_details table updated successfully.\n";
    echo '</pre>';

    $selectStmt->close();
    $updateStmt->close();
    $mysqli->close();
} catch (Exception $e) {
    if (isset($mysqli) && $mysqli instanceof mysqli) {
        $mysqli->rollback();
        $mysqli->close();
    }
    http_response_code(500);
    echo '<pre>Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</pre>';
}
