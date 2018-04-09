package ru.devprom.pages.admin;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;

import ru.devprom.helpers.Configuration;

public class BackUpsPage extends AdminPageBase {

	@FindBy(xpath = "//a[contains(.,'Создать резервную копию')]")
	private WebElement makeBackUpBtn;

	protected BackUpsPage(WebDriver driver) {
		super(driver);
		Assert.assertTrue(isElementPresent(By.id("backuplist1")));
		FILELOG.debug("Open Backups page");
		FILELOG.debug("Current URL is: " + driver.getCurrentUrl());
	}

	public void makeBackUp() {
		makeBackUpBtn.click();
		(new WebDriverWait(driver, Configuration.getUpgradeTimeout()))
				.until(ExpectedConditions.presenceOfElementLocated(By
						.id("tablePlaceholder")));
		FILELOG.info("Backup has been made");
	}

	public void restoreLastBackUp() {
		String backupURL = driver
				.findElement(
						By.xpath(".//tr[@id='backuplist1_row_1']//a[text()='Восстановить']"))
				.getAttribute("href");
		driver.get(backupURL);
		(new WebDriverWait(driver, Configuration.getUpgradeTimeout()))
				.until(ExpectedConditions.presenceOfElementLocated(By
						.id("tablePlaceholder")));
		FILELOG.info("System restored from backup");
	}

}
