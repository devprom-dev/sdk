package ru.devprom.pages.project.repositories;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class ChangesPage extends SDLCPojectPageBase {

	public ChangesPage(WebDriver driver) {
		super(driver);
	}

	public ChangesPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public boolean isFileChanged(){
		return (driver.findElements(By.xpath("//code//span[contains(@style,'background:#F59191')]")).size()>0 
				&&  driver.findElements(By.xpath("//code//span[contains(@style,'background:#90EC90')]")).size()>0);
	}
	
}
