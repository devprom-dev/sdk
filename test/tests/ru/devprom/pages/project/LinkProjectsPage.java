package ru.devprom.pages.project;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;

public class LinkProjectsPage extends SDLCPojectPageBase {

	@FindBy(id = "TargetText")
	protected WebElement projectSelect;
	
	@FindBy(id = "pm_ProjectLinkRequests")
	protected WebElement requestsOption;
	
	@FindBy(id = "pm_ProjectLinkReleases")
	protected WebElement releasesOption;
	
	@FindBy(id = "pm_ProjectLinkTasks")
	protected WebElement tasksOption;
	
	@FindBy(id = "pm_ProjectLinkKnowledgeBase")
	protected WebElement kbOption;
	
	@FindBy(id = "pm_ProjectLinkBlog")
	protected WebElement blogOption;
	
	@FindBy(id = "pm_ProjectLinkRequirements")
	protected WebElement requirementsOption;
	
	@FindBy(id = "pm_ProjectLinkTesting")
	protected WebElement testDocumentationOption;
	
	@FindBy(id = "pm_ProjectLinkHelpFiles")
	protected WebElement helpDocumentationOption;
	
	@FindBy(id = "pm_ProjectLinkSourceCode")
	protected WebElement sourceOption;
		
	@FindBy(id = "pm_ProjectLinkSubmitBtn")
	protected WebElement saveBtn;
	
	public LinkProjectsPage(WebDriver driver) {
		super(driver);
	}

	public LinkProjectsPage(WebDriver driver, Project project) {
		super(driver, project);
	}
	
	public LinkedProjectsPage linkProject(String projectName){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(projectSelect));
		try {
			Thread.sleep(5000);
		} catch (InterruptedException e) {
		}
		projectSelect.sendKeys(projectName);
		autocompleteSelect(projectName);
		submitDialog(saveBtn);
		return new LinkedProjectsPage(driver);
	}
	
	public void setRequestOptions(String requestOptions){
		(new Select(requestsOption)).selectByVisibleText(requestOptions);
	}
	
	public void setRequestOptionsValue(String requestOptions){
		(new Select(requestsOption)).selectByValue(requestOptions);
	}

	public void setReleaseOptions(String releasesOptions){
		(new Select(releasesOption)).selectByVisibleText(releasesOptions);
	}
	
	public void setReleaseOptionsValue(String releasesOptions){
		(new Select(releasesOption)).selectByValue(releasesOptions);
	}

	public void setTasksOptions(String tasksOptions){
		(new Select(tasksOption)).selectByValue(tasksOptions);
	}
	
	public void setKBOptions(String kbOptions){
		(new Select(kbOption)).selectByVisibleText(kbOptions);
	}
	
	public void setRequirementsOptions(String requirementsOptions){
		(new Select(requirementsOption)).selectByVisibleText(requirementsOptions);
	}
	
	public void setTestDocumentationOption(String testDocumentationOptions){
		(new Select(testDocumentationOption)).selectByVisibleText(testDocumentationOptions);
	}
	
	public void setHelpDocumentationOptionOptions(String helpDocumentationOptions){
		(new Select(helpDocumentationOption)).selectByVisibleText(helpDocumentationOptions);
	}
	
	public void setSourceOption(String sourceOptions){
		(new Select(sourceOption)).selectByVisibleText(sourceOptions);
		}
	
	public void setBlogOptions(String blogOptions){
		(new Select(blogOption)).selectByVisibleText(blogOptions);
		}
	
	
}
