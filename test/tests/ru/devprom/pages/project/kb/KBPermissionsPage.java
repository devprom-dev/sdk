package ru.devprom.pages.project.kb;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.Select;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class KBPermissionsPage extends SDLCPojectPageBase {

	public KBPermissionsPage(WebDriver driver) {
		super(driver);
	}

	public KBPermissionsPage(WebDriver driver, Project project) {
		super(driver, project);
	}
  
	// use 'y' - to give access, 'n' - to deny access and 'd' or any other char for default settings 
	public void changePermissions(String role, char access) {
		String xpath = String.format("//table[@id='accessobjectlist1']//tr[child::td[@id='projectrole' and contains(.,'%s')]]", role);
		WebElement row = driver.findElement(By.xpath(xpath));
		Select select =  new Select (row.findElement(By.xpath(".//td[@id='accesstype']/select[contains(@id,'select_ObjectAccessWebMethod')]")));
		switch (access) {
		case 'y':
		case 'Y':
		    	select.selectByValue("view");
			break;
		case 'n':
		case 'N':
			select.selectByValue("none");
			 break;
		default:
			select.selectByValue("");
			break;
		}

		try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
		}
	}
}
