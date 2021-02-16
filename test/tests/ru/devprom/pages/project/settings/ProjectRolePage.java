package ru.devprom.pages.project.settings;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class ProjectRolePage extends SDLCPojectPageBase {

	public ProjectRolePage(WebDriver driver) {
		super(driver);
		// TODO Auto-generated constructor stub
	}

	public ProjectRolePage(WebDriver driver, Project project) {
		super(driver, project);
		// TODO Auto-generated constructor stub
	}

	public String getRoleId( String roleName ) {
		return driver.findElement(
				By.xpath("//tr[contains(@id,'dictionaryitemslist')]/td[@id='caption' and contains(.,'"+roleName+"')]/parent::tr"))
					.getAttribute("object-id");
	}
}
