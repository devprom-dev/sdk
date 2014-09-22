echo off
echo started %1
date /t
time /t
cd ""
rem call crontab_rc.vbs
call crontab_trunk.vbs
rem call crontab_stable.vbs
rem call crontab_feature.vbs
echo completed %1
date /t
time /t
