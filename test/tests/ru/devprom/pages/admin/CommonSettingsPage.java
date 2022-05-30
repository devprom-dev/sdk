package ru.devprom.pages.admin;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import ru.devprom.helpers.Configuration;

public class CommonSettingsPage extends AdminPageBase {

	@FindBy(id = "AdminProjectText")
	private WebElement adminProjectList;
	
	@FindBy(id = "cms_SystemSettingsSubmitBtn")
	private WebElement saveBtn;
	
	public CommonSettingsPage(WebDriver driver) {
		super(driver);
	}

	public void setAdministrationProject(String projectName){
		adminProjectList.clear();
		adminProjectList.sendKeys(projectName);
		autocompleteSelect(projectName);
	}
	
	public AdminPageBase saveChanges(){
		saveBtn.click();
		return new AdminPageBase(driver);
	}

	public String createAdministrativeProject() {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.xpath("//a[text()='перейдите по ссылке']")));
		driver.findElement(By.xpath("//a[text()='перейдите по ссылке']")).click();
		try {
			Thread.sleep(30000);
		} catch (InterruptedException e) {
		}
		driver.navigate().to(Configuration.getBaseUrl() + "/profile");
		driver.navigate().to(Configuration.getBaseUrl() + "/pm/administration");
		return driver.findElement(By.id("navbar-project")).getText().trim();
	}
}
