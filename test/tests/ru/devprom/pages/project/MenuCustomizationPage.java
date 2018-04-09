package ru.devprom.pages.project;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;

public class MenuCustomizationPage extends SDLCPojectPageBase {

	@FindBy(id = "appendedInput")
	protected WebElement searchInput;
	
	
	public MenuCustomizationPage(WebDriver driver) {
		super(driver);
		try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}

	public MenuCustomizationPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public void removeMenuItem(String item){
		WebElement element = driver.findElement(By.xpath("//section[@id='menu']//span[text()='"+item+"']")); 
		WebElement target = driver.findElement(By.xpath("//div[contains(@class,'hdr') and text()='Мои отчеты']"));
		mouseMove(element);
		(new Actions(driver)).dragAndDrop(element, target).perform();
		mouseMove(target);
	}
	
	/**
	 * Всегда вставляется в конец
	 * @param item
	 */
	public void addMenuItem(String item){
		WebElement element = driver.findElement(By.xpath("//div[contains(@class,'ui-draggable') and text()='"+item+"']")); 
		WebElement target = driver.findElement(By.xpath("//section[@id='menu']"));
		mouseMove(element);
		(new Actions(driver)).dragAndDrop(element, target).perform();
		mouseMove(target);
	}
	
	
	/**
	 * Всегда вставляется в конец
	 * @param item
	 */
	public void addFilteredMenuItem(String item){
		WebElement element = driver.findElement(By.xpath("//div[contains(@class,'ui-draggable')]//span[@class='filter-match' and text()='"+item+"']")); 
		WebElement target = driver.findElement(By.xpath("//section[@id='menu']"));
		mouseMove(element);
		(new Actions(driver)).dragAndDrop(element, target).perform();
		mouseMove(target);
        try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
	}
	
	public void addFilteredMenuItem(String item, String itemBefore){
		WebElement element = driver.findElement(By.xpath("//div[contains(@class,'ui-draggable')]//span[@class='filter-match' and text()='"+item+"']")); 
		WebElement target = driver.findElement(By.xpath("//section[@id='menu']//span[text()='"+itemBefore+"']/.."));
		mouseMove(element);
		(new Actions(driver)).dragAndDrop(element, target).perform();
		mouseMove(target);
        try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
	}
	
	public void searchMenuItem(String item){
		searchInput.clear();
		searchInput.sendKeys(item);
	}
	
	public boolean isItemExists(String item){
		searchInput.clear();
		searchInput.sendKeys(item);
		return !driver.findElements(By.xpath("//div[contains(@class,'ui-draggable')]//span[@class='filter-match' and text()='"+item+"']")).isEmpty(); 
	}
	
	public void saveChanges(){
		driver.findElement(By.xpath("//div[@id='save-control']//button")).click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//div[@id='save-control']//button[@disabled]//span[text()='Сохранить']")));

	}
	
	
	public SDLCPojectPageBase close(){
		driver.findElement(By.xpath("//div[contains(@class,'row-fluid')]//a[text()='Закрыть']")).click();
		return new SDLCPojectPageBase(driver);
	}
	
}
