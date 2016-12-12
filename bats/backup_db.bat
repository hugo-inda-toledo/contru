echo off
C:\wamp\bin\mysql\mysql5.6.17\bin\mysqldump -u root ldz2 > respaldo_ldz_%Date:~6,4%%Date:~3,2%%Date:~0,2%_.sql
exit