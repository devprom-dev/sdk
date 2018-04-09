echo off
call java -Dwebdriver.firefox.bin="C:\Program Files (x86)\Mozilla Firefox\FIREFOX.EXE" -cp ".\bin\;.\resources\;.\lib\log4j-1.2-api-2.0-beta4.jar;.\lib\log4j-api-2.0-beta4.jar;.\lib\log4j-core-2.0-beta4.jar;.\lib\log4j-core-2.0-beta4.jar;.\lib\selenium-server-standalone-2.53.1.jar;.\lib\testng.jar;.\lib\mail.jar;.\lib\mysql-connector-java-5.1.23-bin.jar;" org.testng.TestNG sdkTests.xml


