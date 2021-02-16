package ru.devprom.pages.project.autoactions;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.pages.kanban.KanbanPageBase;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class AutoActionSettingsPage extends KanbanPageBase {

	@FindBy(id = "new-issueautoaction")
	protected WebElement addBtn;

	@FindBy(id = "bulk-delete")
	protected WebElement deleteBtn;

	public AutoActionSettingsPage(WebDriver driver) {
		super(driver);
	}

	public AutoActionNewPage addNewAutoAction() {
		clickOnInvisibleElement(addBtn);
		waitForDialog();
		return new AutoActionNewPage(driver);
	}

	public AutoActionSettingsPage deleteAutoAction(String referenceName) {
		driver.findElement(
				By.xpath("//td[@id='referencename' and text()='"
						+ referenceName
						+ "']/preceding-sibling::td/input[@type='checkbox']"))
				.click();
		clickOnInvisibleElement(deleteBtn);
		waitForDialog();
        submitDialog(driver.findElement(By.id("SubmitBtn")));
		return new AutoActionSettingsPage(driver);
	}
	
	public AutoActionSettingsPage deleteAll(){
		if (driver.findElements(By.xpath("//tr[contains(@id,'dictionaryitemslist1_row_')]")).isEmpty()) return new AutoActionSettingsPage(driver);
		driver.findElement(By.id("to_delete_alldictionaryitemslist1")).click();
		clickOnInvisibleElement(deleteBtn);
		waitForDialog();
		submitDialog(driver.findElement(By.id("SubmitBtn")));
		return new AutoActionSettingsPage(driver);
	}

	public boolean isAutoAction(String referenceName){
		return !driver.findElements(By.xpath("//td[@id='referencename' and text()='"+referenceName+"']")).isEmpty();
	}
	


public KanbanPageBase add()
{
	(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(addBtn));
	addBtn.click();
	try {
		Thread.sleep(6000);
	} catch (InterruptedException e) {
	}
	return new KanbanPageBase(driver);
}
}
	
