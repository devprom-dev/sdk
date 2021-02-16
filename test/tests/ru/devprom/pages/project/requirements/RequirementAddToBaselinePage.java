package ru.devprom.pages.project.requirements;

import java.util.ArrayList;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.Keys;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.items.Requirement;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class RequirementAddToBaselinePage extends SDLCPojectPageBase {

	@FindBy (id="VersionText")
	protected WebElement nameInput;
	
	@FindBy (id="Description")
	protected WebElement descriptionInput;
	
	@FindBy (id="Snapshot")
	protected WebElement versionSelect;
	
	@FindBy (id="ProjectText")
	protected WebElement projectInput;
	
	@FindBy (id="SubmitBtn")
	protected WebElement submitBtn;
	public RequirementAddToBaselinePage(WebDriver driver) {
		super(driver);
	}

	public RequirementAddToBaselinePage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public RequirementViewPage Submit(Requirement requirement, String baselineName){
		return Submit(requirement, baselineName, baselineName);
	}
	
	public RequirementViewPage Submit(Requirement requirement, String baselineName, String description)
	{
		waitForDialog();
		nameInput.sendKeys(baselineName);
		try {
			Thread.sleep(1500);
		} catch (InterruptedException e) {
		}
		nameInput.sendKeys(Keys.TAB);
		if (!"".equals(description)) addDescription(description);
		submitDialog(submitBtn);
		String uid = "R-" + driver.findElement(
				By.xpath("//tr[contains(@id,'pmwikidocumentlist1_row')]//div[contains(@class,'wysiwyg-text') and contains(.,'" + requirement.getName() + "')]")).getAttribute("objectid");
		requirement.setId(uid);
		return new RequirementViewPage(driver);
	}

	public void addDescription(String description){
		descriptionInput.sendKeys(description);
	}
}
