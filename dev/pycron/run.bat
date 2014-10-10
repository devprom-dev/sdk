echo off
echo started %1
date /t
time /t
cd ""
call run.vbs
echo completed %1
date /t
time /t
exit