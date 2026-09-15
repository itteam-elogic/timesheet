param(
	[string]$Key = "rs-vs-ts-11am-1pm-elogic",
	[string]$BaseUrl = "http://localhost/timesheet_demo",
	[string]$ScriptPath = ""
)

$ErrorActionPreference = "Stop"
if ([string]::IsNullOrWhiteSpace($ScriptPath)) {
	$ScriptPath = Join-Path $PSScriptRoot "send_resource_schedule_email.ps1"
}

$tr11 = "powershell.exe -NoProfile -ExecutionPolicy Bypass -File `"$ScriptPath`" -Slot 11am -Key `"$Key`" -BaseUrl `"$BaseUrl`""
$tr13 = "powershell.exe -NoProfile -ExecutionPolicy Bypass -File `"$ScriptPath`" -Slot 1pm -Key `"$Key`" -BaseUrl `"$BaseUrl`""

schtasks /Create /TN "Timesheet Resource Schedule 11AM" /TR $tr11 /SC DAILY /ST 11:30 /RL LIMITED /F
if ($LASTEXITCODE -ne 0) { throw "Failed to create 11:30 AM scheduled task." }

schtasks /Create /TN "Timesheet Resource Schedule 1PM" /TR $tr13 /SC DAILY /ST 13:00 /RL LIMITED /F
if ($LASTEXITCODE -ne 0) { throw "Failed to create 1:00 PM scheduled task." }

Write-Output "Created daily Windows tasks:"
Write-Output "  Timesheet Resource Schedule 11AM -> 11:30"
Write-Output "  Timesheet Resource Schedule 1PM  -> 13:00"
Write-Output ("Endpoint: " + $BaseUrl + "/resource_schedule/send_today_resource_schedule_email_cron")
