package ru.devprom.pages.project.requirements;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class TraceMatrixPage extends SDLCPojectPageBase {

	public TraceMatrixPage(WebDriver driver) {
		super(driver);
	}

	public TraceMatrixPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public Boolean isRequirementHasAlert(String requirementId){
		return driver.findElements(By.xpath("//td[@id='uid']/a[contains(@href,'"+requirementId+"')]/../following-sibling::td[@id='caption']/a/img")).size()>0;
	}
	
	public RequirementChangesPage clickToAlertOnRequirement(String requirementId){
		driver.findElement(By.xpath("//td[@id='uid']/a[contains(@href,'"+requirementId+"')]/../following-sibling::td[@id='caption']/a")).click();
		return new RequirementChangesPage(driver);
	}
}
