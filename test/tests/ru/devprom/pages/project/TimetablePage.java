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

	public TimetablePage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public TimetablePage setMode(String mode) {
		driver.findElement(By.xpath("//a[@data-toggle='dropdown' and contains(.,'Вид:')]")).click();
		driver.findElement(By.xpath("//ul[@uid='view']/li/a[contains(@onkeydown,'view="+mode+"') or contains(@href,'view="+mode+"')]")).click();
	    return new TimetablePage(driver);
	}

	public TimetableItem[] readTimetable(){
		List<WebElement> rows = driver.findElements(By.xpath("//tr[contains(@id,'reportspenttimelist')]"));
		TimetableItem[] items = new TimetableItem[rows.size()];
		for (int i=1; i<=rows.size();i++){
			WebElement row = rows.get(i-1);
			String name = row.findElement(By.id("caption")).getText().trim().replace("__", ""); 
			String sum =  row.findElement(By.id("total")).getText().trim().replace("ч", "");
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
		return driver.findElement(By.xpath("//table[contains(@id,'reportspenttimelist')]/tbody/tr/th/a")).getText().trim();
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
	
	public void addFilterParticipant() {
		String code = "filterLocation.setup( 'participant=all', 1 );";
		((JavascriptExecutor) driver).executeScript(code);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOfElementLocated(By.xpath("//a[@data-toggle='dropdown' and @uid='participant']")));
	}
	
	public void addFilterRole() {
		String code = "filterLocation.setup( 'role=all', 1 );";
		((JavascriptExecutor) driver).executeScript(code);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOfElementLocated(By.xpath("//a[@data-toggle='dropdown' and @uid='role']")));
	}
	

	public void removeFilterParticipant() {
		WebElement element = driver.findElement(By.xpath("//a[@data-toggle='dropdown' and @uid='participant']"));
		String code = "filterLocation.setup( 'participant=hide', 1 );";
		((JavascriptExecutor) driver).executeScript(code);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.stalenessOf(element));
	}
	
	
	public void removeFilterRole() {
		WebElement element = driver.findElement(By.xpath("//a[@data-toggle='dropdown' and @uid='role']"));
		String code = "filterLocation.setup( 'role=hide', 1 );";
		((JavascriptExecutor) driver).executeScript(code);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.stalenessOf(element));
	}
	
	
	public TimetablePage setFilterRole(String value) {

		driver.findElement(
				By.xpath("//a[@data-toggle='dropdown' and @uid='role']")).click();
		try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {
		}
		driver.findElement(
				By.xpath("//a[@data-toggle='dropdown' and @uid='role']/following-sibling::ul/li/a[text()='" + value + "']")).click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//a[@data-toggle='dropdown' and contains(@class,'btn-info') and contains(.,'"	+ value + "')]")));
		
		return new TimetablePage(driver);
	}
	

	public TimetablePage setFilterParticipant(String value) throws InterruptedException {
		driver.findElement(
				By.xpath("//a[@data-toggle='dropdown' and @uid='participant']")).click();
		try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {}
		try {
			driver.findElement( By.xpath("//li[@uid='show-all']/a")).click();
		}
		catch(NoSuchElementException e) {}
		try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {}
		driver.findElement(
				By.xpath("//a[@data-toggle='dropdown' and @uid='participant']/following-sibling::ul/li/a[text()='" + value + "']")).click();
		driver.findElement(
				By.xpath("//a[@data-toggle='dropdown' and @uid='participant']")).click();
		try {
			Thread.sleep(6000);
		} catch (InterruptedException e) {
		}
		driver.navigate().refresh();
		(new WebDriverWait(driver, waiting)).until(
				ExpectedConditions.presenceOfElementLocated(
						By.xpath("//a[@data-toggle='dropdown' and @uid='participant' and contains(.,'" + value.substring(0, 9) + "')]")));
		return new TimetablePage(driver);
	}

}
