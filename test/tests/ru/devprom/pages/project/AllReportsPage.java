package ru.devprom.pages.project;

import java.util.ArrayList;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.TimeoutException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;

public class AllReportsPage extends SDLCPojectPageBase {

	public AllReportsPage(WebDriver driver) {
		super(driver);
	}

	public AllReportsPage(WebDriver driver, Project project) {
		super(driver, project);
	}
	
	public ProjectPageBase openReport(String name){
		driver.findElement(By.xpath("//table[contains(@id,'reportlist')]//a[contains(@href,'report="+name+"')]")).click();
		return new ProjectPageBase(driver);
	}
	
	
	public List<Report> getAllReportsList(){
	    List<Report> result = new ArrayList<Report>();
		List <WebElement> list = driver.findElements(By.xpath("//table[@id='reportlist1']/tbody/tr[contains(@id,'reportlist1_row_')]/td[@id='caption']//img/following-sibling::a"));
	    for (WebElement reportlink:list){
	    	result.add(new Report(reportlink));
	    }
        return result;	
	}

	public boolean checkReport(Report report){
		
		driver.findElement(By.xpath("//table[@id='reportlist1']/tbody/tr[contains(@id,'reportlist1_row_')]/td[@id='caption']//a[text()='"+ report.name +"']")).click();
		try {
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.id("page-content")));
		}
		catch (TimeoutException e) {
			report.status = "link error";
			return false;
		}
		if (!driver.getPageSource().contains(report.name)) {
			report.status = "caption error";
			return false;
		}
		else {
			report.status = "verified";
			return true;
		}
	}
	
	
	public class Report {
		private WebElement link;
		public String name; 
		public String url;
		public String status;
		
		public Report(WebElement element){
			link=element;
			name = element.getText().trim();
			url = element.getAttribute("href");
			status = "not verified";
		}

		@Override
		public String toString() {
			return "Report [link=" + link + ", name=" + name + ", url=" + url
					+ "]";
		}
		
		
	}

}
