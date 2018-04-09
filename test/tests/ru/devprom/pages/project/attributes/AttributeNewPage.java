package ru.devprom.pages.project.attributes;

import java.util.Iterator;
import java.util.Map;
import java.util.Map.Entry;
import java.util.Set;

import org.openqa.selenium.By;
import org.openqa.selenium.Keys;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.ProjectCommonSettingsPage;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class AttributeNewPage extends SDLCPojectPageBase {

	@FindBy(id = "pm_CustomAttributeCaption")
	protected WebElement nameEdit;

	@FindBy(id = "pm_CustomAttributeReferenceName")
	protected WebElement refereneNameEdit;

	@FindBy(id = "pm_CustomAttributeDefaultValue")
	protected WebElement defaultValueEdit;

	@FindBy(id = "pm_CustomAttributeDescription")
	protected WebElement descriptionEdit;
	
	@FindBy(id = "pm_CustomAttributeValueRange")
	protected WebElement valuesRangeEdit;
	
	@FindBy(id = "pm_CustomAttributeIsUnique")
	protected WebElement isUniqueCheckbox;

	@FindBy(id = "pm_CustomAttributeSubmitBtn")
	protected WebElement createBtn;

	public AttributeNewPage(WebDriver driver) {
		super(driver);
	}

	public AttributeNewPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public void enterNewAttribute(String name, String referenceName,
			String description, Boolean isUnique) {
		nameEdit.clear();
		nameEdit.sendKeys(name);
		refereneNameEdit.clear();
		refereneNameEdit.sendKeys(referenceName);
		descriptionEdit.clear();
		descriptionEdit.sendKeys(description);
		if (isUnique && isUniqueCheckbox.getAttribute("checked")==null)
			isUniqueCheckbox.click();
		if (!isUnique && isUniqueCheckbox.getAttribute("checked")!=null)
			isUniqueCheckbox.click();
	}

	public AttributeSettingsPage createNewAttribute() {
		createBtn.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//table[@uid='dicts-pmcustomattribute']")));
		String nowUrl = driver.getCurrentUrl();
		ProjectCommonSettingsPage psp = gotoCommonSettings();
		psp.saveChanges();
		driver.navigate().to(nowUrl);
		return new AttributeSettingsPage(driver);
	}

	public void setDefaultStringValue(String defaultvalue) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(defaultValueEdit));
		try {
			Thread.sleep(500);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		defaultValueEdit.sendKeys(defaultvalue);
	}

	public String createWithError() {
		createBtn.click();
		if (driver.findElements(By.xpath("//div[contains(@class,'alert-error')]")).isEmpty()) return "";
		return driver.findElement(By.xpath("//div[contains(@class,'alert-error')]")).getText();
	}
	
	public void addValuesRange(Map<Integer, String> values){
		Set<Entry<Integer, String>> set = values.entrySet();
		Iterator<Entry<Integer, String>> iter = set.iterator();

			while (iter.hasNext()) {
			Map.Entry<Integer, String> item = iter.next();
				valuesRangeEdit.sendKeys(item.getKey().toString());
			valuesRangeEdit.sendKeys(":");
			valuesRangeEdit.sendKeys(item.getValue());
			if (iter.hasNext())valuesRangeEdit.sendKeys(Keys.ENTER);
			
		}
		
	}
	
}
