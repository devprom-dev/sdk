package ru.devprom.pages.project.attributes;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.pages.project.SDLCPojectPageBase;

public class AttributeSettingsPage extends SDLCPojectPageBase {

	@FindBy(xpath = "//a[contains(.,'Добавить') and contains(@class,'append-btn')]")
	protected WebElement addBtn;

	@FindBy(id = "bulk-delete")
	protected WebElement deleteBtn;

	public AttributeSettingsPage(WebDriver driver) {
		super(driver);
	}

	public AttributeEntityNewPage addNewAttribute() {
		clickOnInvisibleElement(addBtn);
		return new AttributeEntityNewPage(driver);
	}

	public AttributeSettingsPage deleteAttribute(String referenceName) {
		driver.findElement(
				By.xpath("//td[@id='referencename' and text()='"
						+ referenceName
						+ "']/preceding-sibling::td/input[@type='checkbox']"))
				.click();
		clickOnInvisibleElement(deleteBtn);
		waitForDialog();
        submitDialog(driver.findElement(By.id("SubmitBtn")));
		return new AttributeSettingsPage(driver);
	}
	
	public AttributeSettingsPage deleteAll(){
		if (driver.findElements(By.xpath("//tr[contains(@id,'dictionaryitemslist1_row_')]")).isEmpty()) return new AttributeSettingsPage(driver);
		driver.findElement(By.id("to_delete_alldictionaryitemslist1")).click();
		clickOnInvisibleElement(deleteBtn);
		waitForDialog();
		submitDialog(driver.findElement(By.id("SubmitBtn")));
		return new AttributeSettingsPage(driver);
	}

	public boolean isAttribute(String referenceName){
		return !driver.findElements(By.xpath("//td[@id='referencename' and text()='"+referenceName+"']")).isEmpty();
	}
	
}
