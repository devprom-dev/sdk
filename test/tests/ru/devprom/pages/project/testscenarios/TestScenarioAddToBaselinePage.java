package ru.devprom.pages.project.testscenarios;

import org.openqa.selenium.By;
import org.openqa.selenium.Keys;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.items.TestScenario;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class TestScenarioAddToBaselinePage extends SDLCPojectPageBase {

	public TestScenarioAddToBaselinePage(WebDriver driver) {
		super(driver);
	}

	public TestScenarioAddToBaselinePage(WebDriver driver, Project project) {
		super(driver, project);
	}

	@FindBy (id="VersionText")
	protected WebElement nameInput;
	
	@FindBy (id="Description")
	protected WebElement descriptionInput;
	
	@FindBy (id="ProjectText")
	protected WebElement projectInput;
	
	@FindBy (id="SubmitBtn")
	protected WebElement submitBtn;

	public TestScenarioViewPage Submit(TestScenario testScenario, String baselineName)
	{
		waitForDialog();
		projectInput.sendKeys(Keys.TAB);
		nameInput.sendKeys(baselineName);
		try {
			Thread.sleep(1500);
		} catch (InterruptedException e) {
		}
		nameInput.sendKeys(Keys.TAB);
		descriptionInput.sendKeys(baselineName);
		try {
			Thread.sleep(1500);
		} catch (InterruptedException e) {
		}
		descriptionInput.sendKeys(Keys.TAB);
		submitDialog(submitBtn);
		try {
			Thread.sleep(5000);
		} catch (InterruptedException e) {
		}
		String uid = "S-" + driver.findElement(
				By.xpath("//tr[contains(@id,'pmwikidocumentlist1_row')]//div[contains(@class,'wysiwyg-text') and  contains(.,'" + testScenario.getName() + "')]")).getAttribute("objectid");
		testScenario.setId(uid);
		return new TestScenarioViewPage(driver);
	}

	public TestScenarioViewPage Submit(TestScenario testScenario, String baselineName, String description, String projectName){
		if (!"".equals(description)) addDescription(description);
		if (!"".equals(projectName)) selectProject(projectName);
		return Submit(testScenario, baselineName);
	}
	
	public void addDescription(String description){
		descriptionInput.sendKeys(description);
	}
	public void selectProject(String project){
		projectInput.sendKeys(project);
		autocompleteSelect(project);
	}
}
