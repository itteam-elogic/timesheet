<?php
/**
 * Update designation in target CSV from source CSV (match by empId).
 *
 * Put CSV files in: data/csv/
 *   employee_details_22_07_2026.csv  OR  employee_details_target.csv
 *   employee_details_source.csv      OR  employee_details 1.csv
 *
 * Run: http://localhost/timesheet_demo/update_designations.php
 */

$baseDir = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'csv' . DIRECTORY_SEPARATOR;

function resolveCsvFile($baseDir, $candidates) {
    foreach ($candidates as $name) {
        $path = $baseDir . $name;
        if (file_exists($path)) {
            return $path;
        }
    }

    $available = array();
    if (is_dir($baseDir)) {
        foreach (scandir($baseDir) as $file) {
            if (substr($file, -4) === '.csv') {
                $available[] = $file;
            }
        }
    }

    $expected = implode(', ', $candidates);
    $found = count($available) ? implode(', ', $available) : '(none)';

    throw new RuntimeException(
        "Required CSV not found in: $baseDir\n" .
        "Expected one of: $expected\n" .
        "CSV files currently in folder: $found\n\n" .
        "Copy your files into data/csv/ with these names:\n" .
        "  - employee_details_22_07_2026.csv  (full database export)\n" .
        "  - employee_details_source.csv      (designation source)"
    );
}

function readCsv($path) {
    $rows = array();
    $handle = fopen($path, 'r');
    if ($handle === false) {
        throw new RuntimeException("Cannot open: $path");
    }
    $headers = fgetcsv($handle);
    while (($data = fgetcsv($handle)) !== false) {
        if (count($data) === count($headers)) {
            $rows[] = array_combine($headers, $data);
        }
    }
    fclose($handle);
    return array($headers, $rows);
}

function writeCsv($path, $headers, $rows) {
    $handle = fopen($path, 'w');
    if ($handle === false) {
        throw new RuntimeException("Cannot write: $path");
    }
    fputcsv($handle, $headers);
    foreach ($rows as $row) {
        $line = array();
        foreach ($headers as $header) {
            $line[] = isset($row[$header]) ? $row[$header] : '';
        }
        fputcsv($handle, $line);
    }
    fclose($handle);
}

$targetPath = resolveCsvFile($baseDir, array(
    'employee_details_22_07_2026.csv',
    'employee_details_target.csv',
));

$sourcePath = resolveCsvFile($baseDir, array(
    'employee_details_source.csv',
    'employee_details 1.csv',
    'employee_details_1.csv',
));

$outputPath = $baseDir . 'employee_details_22_07_2026_updated.csv';
$changesPath = $baseDir . 'employee_details_designation_changes.csv';

list($sourceHeaders, $sourceRows) = readCsv($sourcePath);
list($targetHeaders, $targetRows) = readCsv($targetPath);

$designationMap = array();
foreach ($sourceRows as $row) {
    $designationMap[trim($row['empId'])] = $row['designation'];
}

$changes = array();
foreach ($targetRows as &$row) {
    $empId = trim($row['empId']);
    if (isset($designationMap[$empId])) {
        $old = $row['designation'];
        $new = $designationMap[$empId];
        if ($old !== $new) {
            $changes[] = array(
                'empId' => $empId,
                'name' => $row['name'],
                'old' => $old,
                'new' => $new,
            );
        }
        $row['designation'] = $new;
    }
}
unset($row);

writeCsv($outputPath, $targetHeaders, $targetRows);

$changeHeaders = array('empId', 'name', 'old_designation', 'new_designation');
$changeRows = array();
foreach ($changes as $c) {
    $changeRows[] = array(
        'empId' => $c['empId'],
        'name' => $c['name'],
        'old_designation' => $c['old'],
        'new_designation' => $c['new'],
    );
}
writeCsv($changesPath, $changeHeaders, $changeRows);

echo '<pre>';
echo "Source file: $sourcePath" . PHP_EOL;
echo "Target file: $targetPath" . PHP_EOL;
echo "Source employees: " . count($sourceRows) . PHP_EOL;
echo "Target employees: " . count($targetRows) . PHP_EOL;
echo "Designations updated: " . count($changes) . PHP_EOL . PHP_EOL;
echo "=== CHANGES ===" . PHP_EOL;
foreach ($changes as $c) {
    echo "empId {$c['empId']} ({$c['name']}):" . PHP_EOL;
    echo "  OLD: {$c['old']}" . PHP_EOL;
    echo "  NEW: {$c['new']}" . PHP_EOL . PHP_EOL;
}
echo "Output saved to: $outputPath" . PHP_EOL;
echo "Changes log saved to: $changesPath" . PHP_EOL;
echo '</pre>';
