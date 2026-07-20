$inputPath = 'c:\Users\laxmikanth\Downloads\project_details (1).csv'
$outputPath = 'c:\Users\laxmikanth\Downloads\project_details_updated.csv'

if (-not (Test-Path $inputPath)) {
    Write-Error "Input file not found: $inputPath"
    exit 1
}

$rows = Import-Csv -Path $inputPath
$updated = 0

foreach ($row in $rows) {
    if ($row.project_start_date -eq '0000-00-00' -and $row.created_at) {
        $createdDate = ($row.created_at -split '\s+')[0]
        if ($createdDate) {
            $row.project_start_date = $createdDate
            $updated++
        }
    }
}

$rows | Export-Csv -Path $outputPath -NoTypeInformation -Encoding UTF8

Write-Host "Total rows: $($rows.Count)"
Write-Host "Updated start dates: $updated"
Write-Host "Saved to: $outputPath"
