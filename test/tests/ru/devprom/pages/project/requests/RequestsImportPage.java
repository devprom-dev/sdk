package ru.devprom.pages.project.requests;

import java.io.File;
import java.util.ArrayList;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.items.Request;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class RequestsImportPage extends SDLCPojectPageBase {

	public RequestsImportPage(WebDriver driver) {
		super(driver);
		// TODO Auto-generated constructor stub
	}

	public RequestsImportPage(WebDriver driver, Project project) {
		super(driver, project);
		// TODO Auto-generated constructor stub
	}

	public void loadFile(String filePath){
		((JavascriptExecutor) driver).executeScript("document.evaluate(\"//input[@id='Excel']\", document, null, 9, null).singleNodeValue.removeAttribute('style')");
		driver.findElement(By.xpath("//input[@id='Excel']")).sendKeys(new File(filePath).getAbsolutePath());
	}
	
	public RequestsImportPage clickImport(){
		driver.findElement(By.xpath("//input[@value='Импортировать']")).click();
		new WebDriverWait(driver, waiting).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//div[contains(@class,'alert-success')]")));
		return new RequestsImportPage(driver);
	}
	
	public List<Request> readRequestsFromPreview(){
		List<Request> requests = new ArrayList<Request>();
		driver.findElement(By.xpath("//input[@value='Просмотр']")).click();
		new WebDriverWait(driver, waiting).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//div[@id='preview']/table")));
		List<WebElement> elements = driver.findElements(By.xpath("//div[@id='preview']/table//tr"));
		elements.remove(0);
		
		for (WebElement el:elements){
			String name = el.findElement(By.xpath("./td[@id='Caption']")).getText();
			String description = el.findElement(By.xpath("./td[@id='Description']")).getText();
			String type = el.findElement(By.xpath("./td[@id='Type']")).getText();
			String priority = el.findElement(By.xpath("./td[@id='Priority']")).getText();
			String author = el.findElement(By.xpath("./td[@id='Author']")).getText();
			Request request = new Request(name, description, priority, 0.0, type);
			request.setOriginator(author);
			requests.add(request);
		}
		
		return requests;
	}
	
}
