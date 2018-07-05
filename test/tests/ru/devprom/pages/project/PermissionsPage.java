package ru.devprom.pages.project;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.Select;

import ru.devprom.items.Project;

public class PermissionsPage extends SDLCPojectPageBase {

	@FindBy(id="filter-settings")
	protected WebElement filterBtn;
	
	
	public PermissionsPage(WebDriver driver) {
		super(driver);
	}

	public PermissionsPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public PermissionsPage selectRole(String role) {
		driver.findElement(By.xpath("//a[@uid='role']")).click();
		driver.findElement(By.xpath("//a[@uid='role']/following-sibling::ul/li/a[text()='"+role+"']")).click();
		return new PermissionsPage(driver);
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
	
	public PermissionsPage showAll() {
		filterBtn.click();
		String code = "filterLocation.setup( 'rows=all', 0 );";
		((JavascriptExecutor) driver).executeScript(code);
		filterBtn.click();
			return new PermissionsPage(driver);
	}
	
}
