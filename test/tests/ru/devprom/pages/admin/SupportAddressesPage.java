package ru.devprom.pages.admin;

import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.TimeoutException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

public class SupportAddressesPage extends AdminPageBase
{
	@FindBy(xpath = "//a[contains(.,'Добавить') and contains(@class,'append-btn')]")
	protected WebElement addAddressBtn;
	
	@FindBy(xpath = "//a[@id='bulk-delete']")
	protected WebElement removeAddressBtn;

	@FindBy(id = "SubmitBtn")
	protected WebElement submitBtn;
	
	public SupportAddressesPage(WebDriver driver) {
		super(driver);
	}
	
	public NewAddressPage addAddress() {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(addAddressBtn));
		addAddressBtn.click();
		return new NewAddressPage(driver);
	}
	
	public void CheckAddress(String boxname){
		driver.findElement(By.xpath("//td[@id='caption' and text()='"+boxname+"']/preceding-sibling::td/input[contains(@class,'checkbox')]")).click();
	}
	
	public SupportAddressesPage deleteAddress(String boxname)
	{
		while( true ) {
			List<WebElement> items = driver.findElements(By.xpath("//td[@id='caption' and contains(.,'"+boxname+"')]"));
			if ( items.size() < 1 ) break;
			
			driver.findElement(By.id("to_delete_allmailboxlist1")).click();
			clickOnInvisibleElement(removeAddressBtn);
			waitForDialog();
	        submitDialog(submitBtn);
	        driver.navigate().refresh();
		}
		return new SupportAddressesPage(driver);
	}
	
	public boolean isAddressPresent(String boxname)
	{
		try {
			(new WebDriverWait(driver, 5)).until(
					ExpectedConditions.presenceOfElementLocated(
						By.xpath("//td[@id='caption' and text()='"+boxname+"']")
					)
				);
			return true;
		}
		catch( TimeoutException e ) {
			return false;
		}
	}
}
