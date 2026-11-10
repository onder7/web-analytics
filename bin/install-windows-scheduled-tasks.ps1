param(
    [string] $PhpPath,
    [switch] $Uninstall
)

$ErrorActionPreference = 'Stop'
$taskNames = @(
    'WebAnalytics-GeoIP-Worker',
    'WebAnalytics-Notification-Worker',
    'WebAnalytics-Maintenance-Worker'
)

if ($Uninstall) {
    foreach ($taskName in $taskNames) {
        & schtasks.exe /Delete /TN $taskName /F 2>$null
        if ($LASTEXITCODE -ne 0) {
            Write-Output "Görev bulunamadı veya silinemedi: $taskName"
        } else {
            Write-Output "Görev silindi: $taskName"
        }
    }
    exit 0
}

$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
if ([string]::IsNullOrWhiteSpace($PhpPath)) {
    $portablePhp = Join-Path $projectRoot '..\..\..\bin\php\php-8\php.exe'
    if (Test-Path $portablePhp) {
        $PhpPath = (Resolve-Path $portablePhp).Path
    } else {
        $phpCommand = Get-Command php.exe -ErrorAction SilentlyContinue
        if ($null -eq $phpCommand) {
            throw 'PHP bulunamadı. -PhpPath ile php.exe dosyasının tam yolunu belirtin.'
        }
        $PhpPath = $phpCommand.Source
    }
}

if (!(Test-Path $PhpPath -PathType Leaf)) {
    throw "PHP çalıştırıcısı bulunamadı: $PhpPath"
}
$PhpPath = (Resolve-Path $PhpPath).Path

$workers = @(
    @{ Name = $taskNames[0]; Script = Join-Path $PSScriptRoot 'process-geo-lookups.php' },
    @{ Name = $taskNames[1]; Script = Join-Path $PSScriptRoot 'process-notifications.php' }
)
$runAs = "$env:USERDOMAIN\$env:USERNAME"
$trigger = New-ScheduledTaskTrigger `
    -Once `
    -At (Get-Date).AddMinutes(1) `
    -RepetitionInterval (New-TimeSpan -Minutes 5) `
    -RepetitionDuration (New-TimeSpan -Days 3650)
$principal = New-ScheduledTaskPrincipal -UserId $runAs -LogonType Interactive -RunLevel Limited

foreach ($worker in $workers) {
    $action = New-ScheduledTaskAction `
        -Execute $PhpPath `
        -Argument ('"{0}" 50' -f $worker.Script) `
        -WorkingDirectory $projectRoot
    Register-ScheduledTask `
        -TaskName $worker.Name `
        -Action $action `
        -Trigger $trigger `
        -Principal $principal `
        -Description 'Web Analytics arka plan işleri' `
        -Force | Out-Null
    Write-Output "5 dakikalık görev oluşturuldu: $($worker.Name)"
}

$maintenanceAction = New-ScheduledTaskAction `
    -Execute $PhpPath `
    -Argument ('"{0}" --execute --batch-size=500' -f (Join-Path $PSScriptRoot 'run-maintenance.php')) `
    -WorkingDirectory $projectRoot
$maintenanceTrigger = New-ScheduledTaskTrigger -Daily -At '3:15AM'
Register-ScheduledTask `
    -TaskName $taskNames[2] `
    -Action $maintenanceAction `
    -Trigger $maintenanceTrigger `
    -Principal $principal `
    -Description '12 aylık analitik ve 30 günlük sistem günlüğü saklama temizliği' `
    -Force | Out-Null
Write-Output "Günlük bakım görevi oluşturuldu: $($taskNames[2])"
Write-Output 'Görevler geçerli Windows kullanıcısı oturum açmışken çalışır.'
