package ru.devprom.pages.project.repositories;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class RepositoryConnectPage extends SDLCPojectPageBase {

	@FindBy(xpath = "//a[contains(.,'Добавить') and contains(@class,'append-btn')]")
	protected WebElement addBtn;
	
	@FindBy(xpath = "//a[@id='bulk-delete']")
	protected WebElement removeBtn;
	
	@FindBy(id = "SubmitBtn")
	protected WebElement submitBtn;

	public RepositoryConnectPage(WebDriver driver) {
		super(driver);
	}

	public RepositoryConnectPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	
	public RepositoryNewPage addNewConnection(){
		 (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(addBtn));
		 addBtn.click();
		return new RepositoryNewPage(driver);
	}
	
	
	public void checkRepository(String name){
		driver.findElement(By.xpath("//tr[contains(@id,'connectorlist1_row_')]/td[@id='caption' and contains(.,'"+name+"')]/preceding-sibling::td/input[contains(@class,'checkbox')]")).click();
	}
	
	public RepositoryConnectPage deleteRepository(String name){
       checkRepository(name);
       clickOnInvisibleElement(removeBtn);
       waitForDialog();
       submitDialog(submitBtn);
       (new WebDriverWait(driver,waiting)).until(ExpectedConditions.invisibilityOfElementLocated(By.xpath("//tr[contains(@id,'connectorlist1_row_')]/td[@id='caption' and contains(.,'"+name+"')]")));
       return new RepositoryConnectPage(driver);
	}
	
	public RepositoryConnectPage deleteAllRepositories(){
		if ( driver.findElements(By.xpath("//tr[contains(@id,'connectorlist1_row_')]")).size() < 1 ) return new RepositoryConnectPage(driver);
		driver.findElement(By.xpath("//input[contains(@id,'to_delete_allconnectorlist')]")).click();
		removeBtn.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(submitBtn));
		submitBtn.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.invisibilityOfElementLocated(By.xpath("//tr[contains(@id,'connectorlist1_row_')]")));
		driver.navigate().refresh();
		return new RepositoryConnectPage(driver);
	}

	public RepositoryFilesPage gotoFiles(String repositoryName) {
		driver.findElement(By.xpath("//tr[contains(@id,'connectorlist1_row_')]/td[@id='caption' and contains(.,'"+repositoryName+"')]//a")).click();
		return new RepositoryFilesPage(driver);
	}
}
