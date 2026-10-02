param([Parameter(ValueFromRemainingArguments=$true)][string[]]$Arguments)
$phpCommand = Get-Command php -ErrorAction SilentlyContinue
$phpExe = if (Test-Path 'C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe') {
    'C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe'
} elseif ($phpCommand) { $phpCommand.Source } else { throw 'PHP tidak ditemukan.' }
$env:Path = (Split-Path $phpExe) + ';' + $env:Path
$previousScanDirectory = [Environment]::GetEnvironmentVariable('PHP_INI_SCAN_DIR')
try {
    # artisan test spawns another PHP process; -d flags do not reach that child.
    if ((& $phpExe -r "echo extension_loaded('zip') ? 'yes' : 'no';") -eq 'no') {
        $projectRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
        $extraIniDirectory = Join-Path $projectRoot '.runtime\php-conf'
        New-Item -ItemType Directory -Path $extraIniDirectory -Force | Out-Null
        [IO.File]::WriteAllText((Join-Path $extraIniDirectory 'zip.ini'), "extension=zip`n", [Text.UTF8Encoding]::new($false))
        $env:PHP_INI_SCAN_DIR = if ($previousScanDirectory) { $previousScanDirectory + ';' + $extraIniDirectory } else { $extraIniDirectory }
    }
    & $phpExe @Arguments
    $phpExitCode = $LASTEXITCODE
} finally {
    [Environment]::SetEnvironmentVariable('PHP_INI_SCAN_DIR', $previousScanDirectory)
}
exit $phpExitCode
