param([ValidateSet('Start','Stop','Status')][string]$Action = 'Status')
$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$mysqlDirectory = 'C:\laragon\bin\mysql\mysql-8.4.3-winx64'
$dataDirectory = Join-Path $projectRoot '.runtime\mysql-data'
$credentialPath = Join-Path $projectRoot '.runtime\database-credentials.json'
$mysqlAdmin = Join-Path $mysqlDirectory 'bin\mysqladmin.exe'
$clientConfig = Join-Path $projectRoot '.runtime\mysql-client.cnf'
if (!(Test-Path $credentialPath)) { throw 'Database lokal belum disiapkan. Lihat docs/operations/runtime.md.' }
$credentials = Get-Content -LiteralPath $credentialPath -Raw | ConvertFrom-Json
$clientContents = @"
[client]
user=root
password=$($credentials.root)
host=127.0.0.1
port=3307
"@
[System.IO.File]::WriteAllText($clientConfig, $clientContents, [System.Text.UTF8Encoding]::new($false))
if ($Action -eq 'Stop') {
    & $mysqlAdmin "--defaults-extra-file=$clientConfig" shutdown
    exit $LASTEXITCODE
}
if ($Action -eq 'Status') {
    & $mysqlAdmin "--defaults-extra-file=$clientConfig" ping
    exit $LASTEXITCODE
}
& $mysqlAdmin "--defaults-extra-file=$clientConfig" ping 2>$null
if ($LASTEXITCODE -eq 0) { exit 0 }
if (!(Test-Path $dataDirectory)) { throw 'Data directory tidak ditemukan; jangan menginisialisasi ulang database yang sudah digunakan.' }
$arguments = @('--no-defaults', "--basedir=$($mysqlDirectory.Replace('\','/'))", "--datadir=$($dataDirectory.Replace('\','/'))", '--port=3307', '--bind-address=127.0.0.1', '--mysqlx=OFF', "--log-error=$($projectRoot.Replace('\','/'))/.runtime/mysql-error.log")
$process = Start-Process -FilePath (Join-Path $mysqlDirectory 'bin\mysqld.exe') -ArgumentList $arguments -WindowStyle Hidden -PassThru
$process.Id | Set-Content (Join-Path $projectRoot '.runtime\mysql.pid')
Write-Output 'MySQL lokal sedang mulai pada 127.0.0.1:3307; periksa dengan database.ps1 Status.'
