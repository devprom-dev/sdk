package ru.devprom.pages.project.repositories;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.settings.ProjectMembersPage;

public class RepositoryNewPage extends SDLCPojectPageBase {

	@FindBy(id="pm_SubversionConnectorClass")
	protected WebElement versionSelect;
	
	@FindBy(id="pm_SubversionSVNPath")
	protected WebElement repositoryPathEdit;
	
	@FindBy(id="pm_SubversionRootPath")
	protected WebElement rootPathEdit;
	
	@FindBy(id="pm_SubversionSVNPath")
	protected WebElement pathEdit;
	
	@FindBy(id="pm_SubversionCaption")
	protected WebElement shortNameEdit;
	
	@FindBy(id="pm_SubversionLoginName")
	protected WebElement userEdit;
	
	@FindBy(id="pm_SubversionSVNPassword")
	protected WebElement passEdit;
	
	@FindBy(id="pm_SubversionSubmitBtn")
	protected WebElement saveBtn;
	
	@FindBy(xpath="//select[contains(@id,'SystemUser')]")
	protected WebElement systemUserSelect;
	
	@FindBy(xpath="//input[contains(@id,'UserName')]")
	protected WebElement userNameInput;
	
	@FindBy(xpath="//input[contains(@id,'UserPassword')]")
	protected WebElement userPassInput;
	
	public RepositoryNewPage(WebDriver driver) {
		super(driver);
	}

	public RepositoryNewPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	
	public void createConnection(String version, String url, String path, String shortName, String user, String pass){
		//Select version
		switch (version) {
		case "svn":
			(new Select(versionSelect)).selectByValue("subversionconnector");
			break;
        case "git":
        	(new Select(versionSelect)).selectByValue("gitconnector");
			break;
        case "tfs":
        	(new Select(versionSelect)).selectByValue("tfsconnector");
			break;

		default:
			(new Select(versionSelect)).selectByValue("subversionconnector");
			break;
		}
		
		//set path
		repositoryPathEdit.sendKeys(url);
		rootPathEdit.sendKeys(path);
		
		//set name
		shortNameEdit.sendKeys(shortName);
		
		//set user and password
		userEdit.sendKeys(user);
		passEdit.sendKeys(pass);
	}
	

	public void addUserMapping(String systemUserName, String svnUserName, String svnUserPass){
		String addBtn = "//span[@name='pm_SubversionUsers']//a[contains(@class,'embedded-add-button')]";
		(new WebDriverWait(driver,timeoutValue)).until(ExpectedConditions.presenceOfElementLocated(By.xpath(addBtn)));
		driver.findElement(By.xpath(addBtn)).click();
		new Select(systemUserSelect).selectByVisibleText(systemUserName);
		userNameInput.sendKeys(svnUserName);
		userPassInput.sendKeys(svnUserPass);
		driver.findElement(By.xpath("//span[@name='pm_SubversionUsers']//input[contains(@id,'saveEmbedded')]")).click();
		
	}	
	
	public RepositoryCreatedPage saveConnection(){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(saveBtn));
		submitDialog(saveBtn);
		return new RepositoryCreatedPage(driver);
	}
}
