param(
	[string]$Slot = "",
	[string]$Key = "rs-vs-ts-11am-1pm-elogic",
	[string]$BaseUrl = "http://localhost/timesheet_demo"
)

$ErrorActionPreference = "Stop"
if ([string]::IsNullOrWhiteSpace($Slot)) {
	$hour = [int](Get-Date).ToString("HH")
	if ($hour -ge 13) { $Slot = "1pm" } elseif ($hour -gt 11 -or ($hour -eq 11 -and [int](Get-Date).ToString("mm") -ge 30)) { $Slot = "11am" } else { $Slot = "11am" }
}

$uri = "{0}/resource_schedule/send_today_resource_schedule_email_cron?key={1}&slot={2}" -f $BaseUrl.TrimEnd("/"), [uri]::EscapeDataString($Key), [uri]::EscapeDataString($Slot)
Write-Output ("Triggering Resource Schedule email: " + $uri)
$response = Invoke-WebRequest -Uri $uri -UseBasicParsing -TimeoutSec 180
Write-Output $response.Content
