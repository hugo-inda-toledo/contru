@echo off
for /f "tokens=2 delims==" %%a in ('wmic OS Get localdatetime /value') do set "dt=%%a"
set "YY=%dt:~2,2%" & set "YYYY=%dt:~0,4%" & set "MM=%dt:~4,2%" & set "DD=%dt:~6,2%"

set "namefile=dbBACKUP_LDZ_INTEGRACIONES%YYYY%%MM%%DD%.zip"
set "source=C:\wamp\www\cl_ldz_cpo.git\bats\ftp_iconstruye\"
set "fullsource=%source%%namefile%"

set "destination=C:\wamp\www\cl_ldz_cpo.git\bats\ftp_iconstruye\backups\"
set "destinationname=dbBackupLDZ.BAK"
set "fulldestination=%destination%%destinationname%"

winscp.com /command ^
    "open ftp://LDZ:kaOjjGjt@200.27.7.252/" ^
    "get /backup/%namefile%" ^
    "exit"

call zipjs.bat unzip -source "%fullsource%" -destination "%destination%" -keep no -force yes
SqlCmd -E -S DESARROLLO -Q "RESTORE DATABASE [BackUpLDZ] FROM DISK='%fulldestination%'"
del "%fulldestination%"