package ru.devprom.pages.allprojects;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;

public class AllProjectsReleaseGraphPage extends AllProjectsPageBase {

	public AllProjectsReleaseGraphPage(WebDriver driver) {
		super(driver);
	}

	public int getTablesRowCount(){
		return driver.findElements(By.xpath("//tr[contains(@id,'eeprojectslist1_row_')]")).size();
	}
	
}
