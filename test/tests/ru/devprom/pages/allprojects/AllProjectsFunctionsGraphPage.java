package ru.devprom.pages.allprojects;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;

public class AllProjectsFunctionsGraphPage extends AllProjectsPageBase {

	public AllProjectsFunctionsGraphPage(WebDriver driver) {
		super(driver);
	}

	public int getTablesRowCount(){
		return driver.findElements(By.xpath("//tr[contains(@id,'functionchartlist1_row_')]")).size();
	}
	
}
