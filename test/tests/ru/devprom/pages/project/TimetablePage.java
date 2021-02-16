package ru.devprom.pages.project;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.util.List;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.xpath.XPathExpressionException;

import org.openqa.selenium.By;
import org.openqa.selenium.ElementNotVisibleException;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.xml.sax.SAXException;

import ru.devprom.helpers.FileOperations;
import ru.devprom.helpers.XLTableParser;
import ru.devprom.items.Project;
import ru.devprom.items.TimetableItem;

public class TimetablePage extends SDLCPojectPageBase {


	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]")
	protected WebElement actionsBtn;
	
	@FindBy(xpath = "//a[text()='Экспорт в Excel']")
	protected WebElement excelBtn;
	
	public TimetablePage(WebDriver driver) {
		super(driver);
	}

	public void goToIssues() {
		clickOnInvisibleElement(driver.findElement(By.xpath("//a[@id='activitiesreport']")));
		(new WebDriverWait(driver, waiting)).until(
				ExpectedConditions.presenceOfElementLocated(
						By.xpath("//table[@uid='activitiesreport']")));
	};

	public void goToTasks() {
		clickOnInvisibleElement(driver.findElement(By.xpath("//a[@id='activitiesreporttasks']")));
		(new WebDriverWait(driver, waiting)).until(
				ExpectedConditions.presenceOfElementLocated(
						By.xpath("//table[@uid='activitiesreporttasks']")));
	};

	public void goToProjects() {
		clickOnInvisibleElement(driver.findElement(By.xpath("//a[@id='activitiesreportproject']")));
		(new WebDriverWait(driver, waiting)).until(
				ExpectedConditions.presenceOfElementLocated(
						By.xpath("//table[@uid='activitiesreportproject']")));
	};

	public void goToUsers() {
		clickOnInvisibleElement(driver.findElement(By.xpath("//a[@id='activitiesreportusers']")));
		(new WebDriverWait(driver, waiting)).until(
				ExpectedConditions.presenceOfElementLocated(
						By.xpath("//table[@uid='activitiesreportusers']")));
	};

	public TimetablePage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public TimetableItem[] readTimetable(){
		List<WebElement> rows = driver.findElements(By.xpath("//tr[contains(@id,'reportspenttimelist')]"));
		TimetableItem[] items = new TimetableItem[rows.size()];
		for (int i=1; i<=rows.size();i++){
			WebElement row = rows.get(i-1);
			String name = row.findElement(By.id("caption")).getText().trim().replace("__", ""); 
			String sum =  row.findElement(By.id("total")).getText().trim().replace("ч", "").replace(",00", "");
			List<WebElement> elements = row.findElements(By.xpath(".//td[contains(@id,'day')]"));
			String[] days = new String[elements.size()];
			int k = 0;
			for( WebElement element : elements ) {
				days[k++] = element.getText().trim().replace("ч", "");
			}
			items[i-1] = new TimetableItem(name, days, sum);
		}
		return items;
		
	}
	
	public String readTimetableType(){
		return driver.findElement(By.xpath("//table[contains(@id,'reportspenttimelist')]//th[@uid='caption']")).getText().trim();
	}
	
	public TimetableItem[] exportToExcel(String type) throws XPathExpressionException,
		ParserConfigurationException, SAXException, IOException,
		InterruptedException {
			TimetableItem[] t = null;
		   int attemptscount = 5;
			FileOperations.removeExisted("Затраченное время.xls");
			 actionsBtn.click();
			 try {
			excelBtn.click();
			} catch (ElementNotVisibleException e) {
			 clickOnInvisibleElement(excelBtn);
			 }

		   File excelTable = FileOperations.downloadFile("Затраченное время.xls");
		   while (true)
		if (attemptscount == 0)
			break;
		else {
			try {
				attemptscount--;
				t = XLTableParser.getTimetableItems(excelTable, type);
				break;
			} catch (FileNotFoundException e) {
				Thread.sleep(2000);
			}
		}
		return t;
	}
	
	public void setFilterRole(int value) {
		setFilter("role", Integer.toString(value));
	}
	
	public void setFilterParticipant(int value) throws InterruptedException {
		setFilter("participant", Integer.toString(value));
	}
}
