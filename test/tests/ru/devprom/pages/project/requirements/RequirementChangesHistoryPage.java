package ru.devprom.pages.project.requirements;

import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.items.RequirementChange;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class RequirementChangesHistoryPage extends SDLCPojectPageBase {

	public RequirementChangesHistoryPage(WebDriver driver) {
		super(driver);
	}

	public RequirementChangesHistoryPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public RequirementChange readChange(int number){
		RequirementChange change = new RequirementChange(); 
		try {
		WebElement record = driver.findElement(By.xpath("//tr[@id='wikihistorylist1_row_"+String.valueOf(number)+"']"));
		change.authorName=record.findElement(By.id("author")).getText().trim();
		change.creationDate=record.findElement(By.id("recordcreated")).getText().trim();
		change.textBefore= record.findElement(By.xpath("//td[@id='content']//div[@class='original']/span")).getText().trim();
		change.textAfter= record.findElement(By.xpath("//td[@id='content']//div[@class='final']/span")).getText().trim();
		change.isExist=true;
		}
		catch (NoSuchElementException e) {
			driver.findElement(By.xpath("//table[@id='wikihistorylist1']/tbody/tr/td[text()='Нет элементов']"));
		}
		
		return change;
	}
	
	
	public RequirementChangesHistoryPage cancelChange(int number){
		WebElement cancelBtn = driver.findElement(By.xpath("//tr[@id='wikihistorylist1_row_"+String.valueOf(number)+"']/td//a[@data-toggle='dropdown']/following-sibling::ul/li/a[text()='Отменить']"));
		clickOnInvisibleElement(cancelBtn);
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.stalenessOf(cancelBtn));
		
		return new RequirementChangesHistoryPage(driver);
	}

	public RequirementViewPage openRequirement() {
		driver.findElement(By.xpath("//ul[contains(@class,'breadcrumb')]/li/a[contains(.,'[')]")).click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//div[contains(@id,'WikiPageContent') and contains(@class,'wysiwyg')]")));
		return new RequirementViewPage(driver);
	}
	
}
