package ru.devprom.pages.admin;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

public class SystemTasksPage extends AdminPageBase {

	public SystemTasksPage(WebDriver driver) {
		super(driver);
	}

	public SystemTasksPage runSystemTask(String systemTaskName){
		WebElement runTaskBtn = driver.findElement(By.xpath("//table[@id='joblist1']//td[@id='caption' and text()='"
	                   +systemTaskName+"']/following-sibling::td//ul[@role='menu']/li/a[text()='Запустить']"));
		clickOnInvisibleElement(runTaskBtn);
	 	return new SystemTasksPage(driver);
	}
	
}
