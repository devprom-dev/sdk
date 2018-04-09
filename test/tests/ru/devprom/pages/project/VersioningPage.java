package ru.devprom.pages.project;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;

import ru.devprom.items.Project;

public class VersioningPage extends SDLCPojectPageBase {

	public VersioningPage(WebDriver driver) {
		super(driver);
	}

	public VersioningPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public boolean findVersionByName(String name){
		return (!driver.findElements(By.xpath("//td[@id='caption' and contains (text(),'"+name+"')]")).isEmpty());
	}
	
}
