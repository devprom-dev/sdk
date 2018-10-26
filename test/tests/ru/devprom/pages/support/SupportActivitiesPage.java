package ru.devprom.pages.support;

import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;

import ru.devprom.items.Project;
import ru.devprom.pages.project.requests.RequestViewPage;

public class SupportActivitiesPage extends SupportPageBase {

	public SupportActivitiesPage(WebDriver driver) {
		super(driver);
		// TODO Auto-generated constructor stub
	}

	public SupportActivitiesPage(WebDriver driver, Project project) {
		super(driver, project);
		// TODO Auto-generated constructor stub
	}


	public RequestViewPage clickToLastTask(){
		driver.findElement(By.xpath("//td[@id='content']/a[contains(.,'I')]")).click();
		return new RequestViewPage(driver);
	}
	
	public RequestViewPage clickToTask(String id){
		try {
			driver.findElement(By.xpath("//td[@id='content']/a[contains(.,'["+ id + "]')]")).click();
		} catch (NoSuchElementException e) {
			driver.findElement(
					By.xpath("//td[@id='content']/a/strike[contains(.,'" + id + "')]/..")).click();
		}
		return new RequestViewPage(driver);
	}
	
}
