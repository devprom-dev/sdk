package ru.devprom.pages.admin;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

public class MailerPage extends AdminPageBase {

	@FindBy(id = "AdminEmail")
	private WebElement adminEmail;
	
	@FindBy(id = "btn")
	private WebElement saveBtn;
	
	public MailerPage(WebDriver driver) {
		super(driver);
	}

	public void setAdminEmail(String email){
		adminEmail.clear();
		adminEmail.sendKeys(email);
	}
	
	public AdminPageBase saveChanges(){
		saveBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.className("alert-success")));
		return new AdminPageBase(driver);
	}
}
