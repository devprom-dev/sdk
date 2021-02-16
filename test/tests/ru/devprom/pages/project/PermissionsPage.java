package ru.devprom.pages.project;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.Select;

import ru.devprom.items.Project;

public class PermissionsPage extends SDLCPojectPageBase {

	public PermissionsPage(WebDriver driver) {
		super(driver);
	}

	public PermissionsPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	/** Set permission level for selected role.
	 * @param rightsLevel should be one of: "modify", "view", "none", ""(default)
	 * */
	public PermissionsPage setRight(String entityName, String rightsLevel){
		WebElement selector = null;
		selector = driver.findElement(By.xpath("//td[@id='referencename' and text()='"+entityName+"']/following-sibling::td[@id='accesstype']/select"));
		(new Select(selector)).selectByValue(rightsLevel);
		return new PermissionsPage(driver);
	}
}
