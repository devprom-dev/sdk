package ru.devprom.helpers;

import java.io.File;
import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Paths;
import org.apache.commons.codec.binary.Base64;
import org.openqa.selenium.WebDriver;
import org.testng.ITestResult;
import org.testng.Reporter;
import org.testng.TestListenerAdapter;

import ru.devprom.tests.TestBase;

public class TestAndMethodListener extends TestListenerAdapter
{
	public void onTestFailure(ITestResult tr)
	{
		Object currentClass = tr.getInstance();
		WebDriver driver = ((TestBase) currentClass).getDriver();
		if (driver == null) return;
		  
	  	File file = ScreenshotsHelper.takeScreenshotOnFail(driver);
		try {
			String base64image = Base64.encodeBase64String(
					Files.readAllBytes(Paths.get(file.getAbsolutePath()))
				);
			Reporter.log("<img src=\"data:image/png;base64," + base64image + "\">");
		} catch (IOException e) {
		}
	}

	public void onTestStart(ITestResult result)
	{
		if (result.getTestClass().getName().contains(".")){
		String a = result.getTestClass().getName();
		String[] b =a.split("\\.");
		testClassName = b[b.length-1];}
		else testClassName = result.getTestClass().getName();
		testMethodName =  result.getMethod().getMethodName();
	}

	static String testClassName="";
	static String testMethodName="";	
}
