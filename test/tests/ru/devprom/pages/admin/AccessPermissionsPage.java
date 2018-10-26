package ru.devprom.pages.admin;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.Select;

public class AccessPermissionsPage extends AdminPageBase {

	public AccessPermissionsPage(WebDriver driver) {
		super(driver);
	}

	public AccessPermissionsPage givePermissions(String group, String module){
		WebElement select = driver.findElement(By.xpath("//td[@id='usergroup' and contains(.,'"+group+"')]/following-sibling::td[@id='referencename' and contains(.,'"+module+"')]/following-sibling::td[@id='accesstype']/select"));
		 (new Select(select)).selectByVisibleText("Есть");
		 try {	Thread.sleep(3000);	} catch (InterruptedException e) {		}
		return new AccessPermissionsPage(driver);
	}
}
