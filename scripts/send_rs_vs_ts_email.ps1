param(
	[string]$Slot = "",
	[string]$Key = "rs-vs-ts-11am-1pm-elogic",
	[string]$BaseUrl = "http://172.168.0.12:82/elogic_timesheet"
)

$ErrorActionPreference = "Stop"
if ([string]::IsNullOrWhiteSpace($Slot)) {
	$hour = [int](Get-Date).ToString("HH")
	if ($hour -ge 13) { $Slot = "1pm" } elseif ($hour -ge 11) { $Slot = "11am" } else { $Slot = "11am" }
}

$uri = "{0}/clients/send_rs_vs_ts_report_cron?key={1}&slot={2}" -f $BaseUrl.TrimEnd("/"), [uri]::EscapeDataString($Key), [uri]::EscapeDataString($Slot)
Write-Output ("Triggering RS vs TS email: " + $uri)
$response = Invoke-WebRequest -Uri $uri -UseBasicParsing -TimeoutSec 180
Write-Output $response.Content
