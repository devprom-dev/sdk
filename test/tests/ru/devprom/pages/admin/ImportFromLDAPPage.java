package ru.devprom.pages.admin;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

public class ImportFromLDAPPage extends AdminPageBase {

	@FindBy(id = "LDAPServer")
	protected WebElement serverInput;
	
	@FindBy(id = "DirectoryType")
	protected WebElement directoryTypeSelect;
	
	@FindBy(id = "UserName") 
	protected WebElement usernameInput;
	
	@FindBy(id = "Password")
	protected WebElement passwordInput;
	
	@FindBy(id = "SearchDomain")
	protected WebElement domainInput;
	
	@FindBy(id = "LoginAttribute")
	protected WebElement loginAttrInput;
		
	@FindBy(id = "EmailAttribute")
	protected WebElement mailAttrInput;
	
	public ImportFromLDAPPage(WebDriver driver) {
		super(driver);
	}

	public ImportFromLDAPPage setupLDAPConfiguration(String serverAndPort, String directoryType, String account, String password, String firstLevelDomain){
		serverInput.clear();
		serverInput.sendKeys(serverAndPort);
		(new Select(directoryTypeSelect)).selectByValue(directoryType);
		usernameInput.clear();
		usernameInput.sendKeys(account);
		passwordInput.clear();
		passwordInput.sendKeys(password);
		domainInput.clear();
		domainInput.sendKeys(firstLevelDomain);
		driver.findElement(By.id("btn")).click();
		return new ImportFromLDAPPage(driver);
	}

	
	public ImportFromLDAPPage setupAttributes(String name, String mail){
		loginAttrInput.clear();
		loginAttrInput.sendKeys(name);
		mailAttrInput.clear();
		mailAttrInput.sendKeys(mail);
		driver.findElement(By.id("btn")).click();
		return new ImportFromLDAPPage(driver);
	}
	
	
	public ImportFromLDAPPage selectUserToImport(String item){
	  	driver.findElement(By.xpath("//span[contains(@class,'fancytree-title') and .='"+item+"']/preceding-sibling::span[@role='checkbox']")).click();
		return new ImportFromLDAPPage(driver);
	}
	
	public ImportFromLDAPPage importUsers(){
		driver.findElement(By.id("btn")).click();
		try {
			Thread.sleep(10000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		return new ImportFromLDAPPage(driver);
	}
	
	public UsersListPage completeImport(Boolean isCreateTask){
		if (!isCreateTask) driver.findElement(By.id("SubmitJob")).click();
		driver.findElement(By.id("btn")).click();
		return new UsersListPage(driver);
	}
	
}
