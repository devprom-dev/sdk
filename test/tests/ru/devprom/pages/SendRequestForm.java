package ru.devprom.pages;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;

import ru.devprom.pages.project.requests.RequestViewPage;

public class SendRequestForm extends PageBase {


	@FindBy(id = "Description")
	private WebElement textInput;
	
	@FindBy(id = "btn")
	private WebElement sendBtn;
	
	public SendRequestForm(WebDriver driver) {
		super(driver);
	}

	public String send(String text) {
		textInput.sendKeys(text);
		sendBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.className("alert-success")));
		return driver.findElement(By.className("alert-success")).getText();
	}

	public RequestViewPage gotoRequestLink(){
		if (driver.findElements(By.className("alert-success")).size()==0){
			Assert.fail("Sending request was failed");
		}
		
		driver.findElement(By.xpath("//div[@class='alert alert-success']/a[@href]")).click();
		return new RequestViewPage(driver);
	}
	
	
	
}
