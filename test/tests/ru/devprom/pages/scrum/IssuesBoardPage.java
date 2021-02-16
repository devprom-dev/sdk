package ru.devprom.pages.scrum;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.items.ScrumIssue;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.requests.RequestNewPage;

public class IssuesBoardPage extends ScrumPageBase {

	@FindBy(id = "filter-settings")
	protected WebElement asterixBtn;
	
	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Добавить')]")
	protected WebElement addBtn;

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]")
	protected WebElement actionsBtn;
	
	@FindBy(xpath = "//a[@id='new-issue']")
	protected WebElement newIssueBtn;

	@FindBy(xpath = "//a[@id='new-issue-technical-issue']")
	protected WebElement newBugBtn;
	
	
	public IssuesBoardPage(WebDriver driver) {
		super(driver);
	}

	public IssuesBoardPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	
	public IssuesBoardPage addNewIssue(ScrumIssue issue) {
		(new WebDriverWait(driver, waiting))
		.until(ExpectedConditions.visibilityOf(newIssueBtn));
		newIssueBtn.click();
		(new WebDriverWait(driver, waiting))
		.until(ExpectedConditions.visibilityOfElementLocated(
				By.id("pm_ChangeRequestCaption")));
		driver.findElement(By.id("pm_ChangeRequestCaption")).sendKeys(issue.getName());
		if (!"".equals(issue.getDescription())) {
			(new CKEditor(driver)).changeText(issue.getDescription());
		}
		if (!"".equals(issue.getPriority())) new Select(driver.findElement(By.id("pm_ChangeRequestPriority"))).selectByVisibleText(issue.getPriority());
		if (!"".equals(issue.getEpic())) {
			driver.findElement(By.id("FunctionText")).sendKeys(issue.getEpic());
          autocompleteSelect(issue.getEpic());
		}
		submitDialog(driver.findElement(By.id("pm_ChangeRequestSubmitBtn")));
		By locator = By.xpath("//div[contains(@class,'bi-cap') and contains(.,'"+issue.getName()+"')]/preceding-sibling::div//a");
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(locator));
		String uid = driver.findElement(locator).getText();
		issue.setId(uid.substring(1, uid.length()-1));
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//div[@object='"+issue.getNumericId()+"']")));
    	FILELOG.debug("Created Issue: " + issue.getId());
		return new IssuesBoardPage(driver);
	}
	public boolean isIssuePresent(String numericId) {
		return !driver.findElements(By.xpath("//div[@object='"+numericId+"']")).isEmpty();
	}

}
