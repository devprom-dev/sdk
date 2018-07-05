package ru.devprom.pages.project.functions;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.ProductFunction;
import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class FunctionNewPage extends SDLCPojectPageBase {

	@FindBy(id = "pm_FunctionCaption")
	protected WebElement nameEdit;

	@FindBy(id = "pm_FunctionDescription")
	protected WebElement descriptionEdit;

	@FindBy(id = "pm_FunctionImportance")
	protected WebElement importanceSelect;

	@FindBy(xpath = "//a[contains(@class,'embedded-add-button') and preceding-sibling::input[@value='featuretag']]")
	protected WebElement addTag;

	@FindBy(xpath = "//a[contains(@class,'embedded-add-button') and preceding-sibling::input[@value='functiontracerequirement']]")
	protected WebElement addRequirementsBtn;

	@FindBy(id = "pm_FunctionSubmitBtn")
	protected WebElement submitBtn;

	public FunctionNewPage(WebDriver driver, Project project) {
		super(driver, project);

	}

	public FunctionNewPage(WebDriver driver) {
		super(driver);
	}

	
        public FunctionsPage createNewFunction(ProductFunction function) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(nameEdit));
		nameEdit.sendKeys(function.getName());
		
		if (function.getRequirements()!=null){
		for (String requirement : function.getRequirements()) {
			(new WebDriverWait(driver, waiting)).until(ExpectedConditions
					.visibilityOf(addRequirementsBtn));
			addRequirementsBtn.click();
			driver.findElement(
					By.xpath("//input[@value='functiontracerequirement']/following-sibling::div[contains(@id,'fieldRowObjectId')]//input[contains(@id,'ObjectIdText')]"))
					.sendKeys(requirement);
			autocompleteSelect(requirement);
			(new WebDriverWait(driver, waiting))
					.until(ExpectedConditions.presenceOfElementLocated(By
							.xpath(".//input[@class='fieldautocompleteobject'and contains(@id,'ObjectId') and @value!='']")));
			driver.findElement(
					By.xpath("//input[@value='functiontracerequirement']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
					.click();
		}
		}
		if (!"".equals(function.getDescription()))
			descriptionEdit.sendKeys(function.getDescription());
		if (!"".equals(function.getImportance()))
			(new Select(importanceSelect)).selectByVisibleText(function
					.getImportance());
		if (function.getTags() != null)
			for (String tag : function.getTags()) {
				(new WebDriverWait(driver, waiting)).until(ExpectedConditions
						.visibilityOf(addTag));
				addTag.click();
				driver.findElement(
						By.xpath("//input[@value='featuretag']/following-sibling::div[contains(@id,'fieldRowTag')]//input[contains(@id,'TagText')]"))
						.sendKeys(tag);
				autocompleteSelect(tag);
				driver.findElement(
						By.xpath("//input[@value='featuretag']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
						.click();
			}
		submitDialog(submitBtn);
    	//read ID
		  (new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//td[@id='caption' and contains(.,'"+function.getName()+"')]")));
    	String uid =driver.findElement(By.xpath("//td[@id='caption' and contains(.,'"+function.getName()+"')]/preceding-sibling::td[@id='uid']")).getText();
    	function.setId(uid.substring(1, uid.length()-1));
		return new FunctionsPage(driver);
	}
        
        public void createFunction(ProductFunction function) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(nameEdit));
		nameEdit.clear();
		nameEdit.sendKeys(function.getName());
		if (function.getRequirements()!=null){
		for (String requirement : function.getRequirements()) {
			(new WebDriverWait(driver, waiting)).until(ExpectedConditions
					.visibilityOf(addRequirementsBtn));
			addRequirementsBtn.click();
			driver.findElement(
					By.xpath("//input[@value='functiontracerequirement']/following-sibling::div[contains(@id,'fieldRowObjectId')]//input[contains(@id,'ObjectIdText')]"))
					.sendKeys(requirement);
			autocompleteSelect(requirement);
			(new WebDriverWait(driver, waiting))
					.until(ExpectedConditions.presenceOfElementLocated(By
							.xpath(".//input[@class='fieldautocompleteobject'and contains(@id,'ObjectId') and @value!='']")));
			driver.findElement(
					By.xpath("//input[@value='functiontracerequirement']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
					.click();
		}
		}
		if (!"".equals(function.getDescription()))
			descriptionEdit.sendKeys(function.getDescription());
		if (!"".equals(function.getImportance()))
			(new Select(importanceSelect)).selectByVisibleText(function
					.getImportance());
		if (function.getTags() != null)
			for (String tag : function.getTags()) {
				(new WebDriverWait(driver, waiting)).until(ExpectedConditions
						.visibilityOf(addTag));
				addTag.click();
				driver.findElement(
						By.xpath("//input[@value='featuretag']/following-sibling::div[contains(@id,'fieldRowTag')]//input[contains(@id,'TagText')]"))
						.sendKeys(tag);
				autocompleteSelect(tag);
				driver.findElement(
						By.xpath("//input[@value='featuretag']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
						.click();
			}
		submitDialog(submitBtn);
	}

}
