# PowerShell script to spin up the API Gateway and all microservices on separate ports

Write-Host "==========================================================" -ForegroundColor Cyan
Write-Host "  Starting Laravel Microservices on Separate Ports        " -ForegroundColor Green
Write-Host "==========================================================" -ForegroundColor Cyan

$services = @(
    @{ Name = "API Gateway";            Port = 8000 },
    @{ Name = "Identity Service";       Port = 8001 },
    @{ Name = "Attendance Service";     Port = 8002 },
    @{ Name = "Payroll Service";        Port = 8003 },
    @{ Name = "Accounting Service";     Port = 8004 },
    @{ Name = "Inventory Service";      Port = 8005 },
    @{ Name = "Notification Service";   Port = 8006 }
)

foreach ($svc in $services) {
    Write-Host "-> Launching $($svc.Name) on http://127.0.0.1:$($svc.Port)..." -ForegroundColor Yellow
    Start-Process powershell -ArgumentList "-NoExit", "-Command", "`$host.ui.RawUI.WindowTitle='$($svc.Name)'; php artisan serve --port=$($svc.Port)"
}

Write-Host "`nAll 7 services launched successfully!" -ForegroundColor Green
Write-Host "Access Gateway at: http://127.0.0.1:8000/api/gateway/overview" -ForegroundColor Cyan

