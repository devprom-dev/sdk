package ru.devprom.pages.admin;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.testng.Assert;

public class ActivitiesPage extends AdminPageBase {

	public ActivitiesPage(WebDriver driver) {
		super(driver);
		Assert.assertTrue(isElementPresent(By.id("activitylist1")));
		FILELOG.debug("Open Activities page");
		FILELOG.debug("Current URL is: " + driver.getCurrentUrl());
	}

}
