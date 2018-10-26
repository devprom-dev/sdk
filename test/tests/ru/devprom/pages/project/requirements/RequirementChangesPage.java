package ru.devprom.pages.project.requirements;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

public class RequirementChangesPage extends RequirementViewPage {

	@FindBy(xpath = "//a[contains(.,'Использовать текст')]/../../preceding-sibling::a")
	protected WebElement processChangesBtn;
	
	@FindBy(xpath = "//a[contains(.,'Использовать текст')]")
	protected WebElement useTextBtn;
	
	@FindBy(xpath = "//a[contains(.,'Оставить текст')]")
	protected WebElement leaveTextBtn;
	
	public RequirementChangesPage(WebDriver driver) {
		super(driver);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//a[contains(.,'Использовать текст')]")));
	}

	public RequirementViewPage useText(){
	
		try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		processChangesBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.elementToBeClickable(useTextBtn));
		useTextBtn.click();
		waitForFilterActivity();
		/*try {
			Thread.sleep(5000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}*/
		return new RequirementViewPage(driver);
	}
}
