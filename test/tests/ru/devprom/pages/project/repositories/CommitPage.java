package ru.devprom.pages.project.repositories;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class CommitPage extends SDLCPojectPageBase {

	public CommitPage(WebDriver driver) {
		super(driver);
	}

	public CommitPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public ChangesPage seeChanges (String file){
		driver.navigate().to(
				driver.findElement(By.xpath("//tr[contains(@id,'subversionrevisiondetailslist1_row')]//td[@id='change']/a")).getAttribute("href")
				);
		try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
		}
		return new ChangesPage(driver);
	}
	
	
}
