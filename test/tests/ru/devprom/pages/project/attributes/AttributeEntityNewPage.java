package ru.devprom.pages.project.attributes;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class AttributeEntityNewPage extends SDLCPojectPageBase {

	@FindBy(name = "EntityReferenceName")
	protected WebElement entityNameSelector;

	@FindBy(id = "AttributeTypeText")
	protected WebElement typeselector;

	@FindBy(id = "btn")
	protected WebElement nextBtn;

	public AttributeEntityNewPage(WebDriver driver) {
		super(driver);
	}

	public AttributeEntityNewPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public AttributeNewPage selectEntity(String entityName, String type) {
		(new Select(entityNameSelector)).selectByValue("projectpage");
		(new Select(entityNameSelector)).selectByValue(entityName);
		typeselector.sendKeys(type);
		autocompleteSelect(type);
		nextBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.id("pm_CustomAttributeCaption")));
		return new AttributeNewPage(driver);
	}

	
	public AttributeNewPage selectEntity(String entityName, String type, String dataType) {
		(new Select(entityNameSelector)).selectByValue("projectpage");
		(new Select(entityNameSelector)).selectByValue(entityName);
		typeselector.sendKeys(type);
		autocompleteSelect(type);
		(new Select(driver.findElement(By.id("AttributeTypeClassName")))).selectByValue(dataType);
		nextBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.id("pm_CustomAttributeCaption")));
		return new AttributeNewPage(driver);
	}
	
}
