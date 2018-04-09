package ru.devprom.pages.allprojects;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;

public class AllProjectsTimetableReportPage extends AllProjectsPageBase {

	public AllProjectsTimetableReportPage(WebDriver driver) {
		super(driver);
	}
	
	public int getTablesRowCount(){
		return driver.findElements(By.xpath("//tr[contains(@id,'reportspenttimelist')]")).size();
	}
	
}
