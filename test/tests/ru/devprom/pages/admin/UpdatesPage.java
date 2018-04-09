package ru.devprom.pages.admin;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.testng.Assert;

public class UpdatesPage extends AdminPageBase {

	@FindBy(xpath = "//a[contains(.,'Действия')]")
	private WebElement actionsBtn;

	@FindBy(xpath = "//a[@href='?action=upload']")
	private WebElement installBtn;

	public UpdatesPage(WebDriver driver) {
		super(driver);
		Assert.assertTrue(isElementPresent(By.id("updatelist1")));
		FILELOG.debug("Open Updates page");
		FILELOG.debug("Current URL is: " + driver.getCurrentUrl());
	}

	public String getCurrentVersion() {
		String[] gcv = currentVersion.getText().split(" ");
		FILELOG.info("Current version is " + gcv[1]);
		if (gcv[1].equals("Trial")) {
			FILELOG.info("Current version is " + gcv[2]);
			return gcv[2];
		} else {
			FILELOG.info("Current version is " + gcv[1]);
			return gcv[1];
		}
	}

	public UploadPage gotoUploadPage() {
		actionsBtn.click();
		installBtn.click();
		return new UploadPage(driver);
	}

	public int updatesCount() {
		return driver.findElements(By.xpath(".//*[@id='caption']")).size();
	}

}
