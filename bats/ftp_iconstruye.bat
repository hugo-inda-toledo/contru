@echo off

REM Generate the script. Will overwrite any existing temp.txt
echo open 200.27.7.252> temp.txt
echo LDZ>> temp.txt
echo kaOjjGjt>> temp.txt
echo get /backup/test.txt>> temp.txt
echo quit>> temp.txt

REM Launch FTP and pass it the script
ftp -s:temp.txt

REM Clean up.
del temp.txt