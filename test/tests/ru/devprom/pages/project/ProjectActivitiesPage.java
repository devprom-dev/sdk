package ru.devprom.pages.project;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;

import ru.devprom.items.Project;

public class ProjectActivitiesPage extends SDLCPojectPageBase {

	public ProjectActivitiesPage(WebDriver driver) {
		super(driver);
	}

	public ProjectActivitiesPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public String readLastActivity(){
		return driver.findElement(By.xpath("//table[contains(@id,'projectloglist')]//td[@id='content']")).getText().trim();
	}
}
