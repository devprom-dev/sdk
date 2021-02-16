package ru.devprom.pages.project;

import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;

import ru.devprom.items.Project;
import ru.devprom.pages.project.testscenarios.TestScenarioViewPage;

public class TestSpecificationsPage extends SDLCPojectPageBase {

	public TestSpecificationsPage(WebDriver driver) {
		super(driver);
	}

	public TestSpecificationsPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	
	public TestScenarioViewPage clickToTestScenario(String id){
		driver.findElement(
				By.xpath("//tr[@role='row']/td[@id='uid']/a[contains(@href,'"
						+ id + "')]")).click();
		return new TestScenarioViewPage(driver);
	}
}
