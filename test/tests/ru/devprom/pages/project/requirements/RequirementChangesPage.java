package ru.devprom.pages.project.requirements;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

public class RequirementChangesPage extends RequirementViewPage {

	@FindBy(xpath = "//a[@uid='compare-actions']")
	protected WebElement processChangesBtn;
	
	@FindBy(xpath = "//li[@uid='reintegrate']/a")
	protected WebElement useTextBtn;
	
	@FindBy(xpath = "//a[contains(.,'Оставить текст')]")
	protected WebElement leaveTextBtn;
	
	public RequirementChangesPage(WebDriver driver) {
		super(driver);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//li[@uid='reintegrate']/a")));
	}

	public RequirementViewPage useText()
	{
		try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		clickOnInvisibleElement(useTextBtn);
		waitForDialog();
		submitDialog(driver.findElement(By.id("SubmitBtn")));
		return new RequirementViewPage(driver);
	}
}
