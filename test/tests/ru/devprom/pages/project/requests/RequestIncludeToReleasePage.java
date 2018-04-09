package ru.devprom.pages.project.requests;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.pages.PageBase;

public class RequestIncludeToReleasePage extends PageBase {

	public RequestIncludeToReleasePage(WebDriver driver) {
		super(driver);
	}

   public boolean isFieldVisible(String fieldId){
	   if (driver.findElements(By.xpath("//*[@name='"+fieldId+"']")).isEmpty()) return false;
	   else return driver.findElement(By.xpath("//*[@name='"+fieldId+"']")).isDisplayed();
   }
	
   public boolean isFieldRequired(String fieldId){
	   return !driver.findElements(By.xpath("//*[@name='"+fieldId+"' and @required]")).isEmpty();
   }
	
   public void fillUserStringField(String fieldId, String text) throws Exception{
	   if (!isFieldVisible(fieldId)) throw new Exception ("The field is not found or not visible");
	   driver.findElement(By.xpath("//*[@name='"+fieldId+"']")).sendKeys(text);
   }
	
	public RequestViewPage includeToRelease(String releaseNumber)
	{
		WebElement input = driver.findElement(By.id("PlannedReleaseText"));
		if ( !input.getAttribute("value").equals(releaseNumber) ) {
			input.clear();
			input.sendKeys(releaseNumber); 
	   		autocompleteSelect(releaseNumber);
		}
		driver.findElement(By.xpath("//button[@type='button']/span[text()='Сохранить']/..")).click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.xpath("//span[@id='state-label' and contains(text(),'В релизе')]")));
		return new RequestViewPage(driver);
	}

	public boolean isFieldVisibleByLabel(String fieldName)
	{
		WebElement e = driver.findElement(By.xpath("//label[text()='"+fieldName+"']"));
		return e == null ? false : e.isDisplayed();
	}
}
