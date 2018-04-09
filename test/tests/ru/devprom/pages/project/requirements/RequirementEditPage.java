package ru.devprom.pages.project.requirements;

import java.io.File;
import java.util.ArrayList;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Request;

public class RequirementEditPage extends RequirementNewPage
{
	@FindBy(xpath = "//button[@id='WikiPageCancelBtn']")
	protected WebElement cancelBtn;

	@FindBy(xpath = "//form//a[contains(@class,'embedded-add-button') and preceding-sibling::input[@value='wikipagefile']]")
	protected WebElement addAttachmentBtn;
	
	public RequirementEditPage(WebDriver driver) {
		super(driver);
	}

	public RequirementViewPage saveChanges()
	{
		submitDialog(submitBtn);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//table[contains(@id,'pmwikidocumentlist')]")));
		return new RequirementViewPage(driver);
	}
	
	public void close() {
		cancelDialog(cancelBtn);
	}
	
	public String getType() {
		clickMainTab();
	    (new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.id("WikiPagePageType")));
		return new Select(driver.findElement(By.id("WikiPagePageType"))).getFirstSelectedOption().getText();
	}
	
	public String getAuthor() {
		clickMoreTab();
		return driver.findElement(By.xpath("//input[@id='WikiPageAuthor']/following-sibling::input")).getAttribute("value");
	}
	
	public void addAttachment(File attachment)
	{
		clickMoreTab();
		//turn off popup dialog
		String codeIE = "$.browser.msie = true; document.documentMode = 8;";
		((JavascriptExecutor) driver).executeScript(codeIE);
		addAttachmentBtn.click();
		// make file input visible
		((JavascriptExecutor) driver).executeScript("document.evaluate(\"//form//input[contains(@id,'_Content') and @type='file']\", document, null, 9, null).singleNodeValue.removeAttribute('style')");
		// fill file input
		driver.findElement(By.xpath("//form//input[contains(@id,'_Content') and @type='file']")).sendKeys(attachment.getAbsolutePath());
		driver.findElement(By.xpath("//form//span[@id='WikiPageAttachments']//input[@action='save']")).click();
	}
	
	public List<String> getAttachments() {
		clickMoreTab();
		List<String> attachements = new ArrayList<String>();
		List<WebElement> elements = driver.findElements(By.xpath("//input[@value='wikipagefile']/following-sibling::div[contains(@id,'embeddedItems')]/div[contains(@id,'Caption')]"));
		for (WebElement element:elements){
			attachements.add(element.findElement(By.xpath(".//*[contains(@class,'title')]/a")).getText());
		}
		return attachements;
	}
	
	public String getUserAttribute(String title) {
		clickMainTab();
		return driver.findElement(By.xpath("//label[text()='"+title+"']/following-sibling::div/input")).getAttribute("value");
	}
	
	public List<String> readLinkedRequests(){	
		clickTraceTab();
		List<String> requests = new ArrayList<String>();
		List<WebElement> elements = driver.findElements(By.xpath("//div[@id='fieldRowIssues']//input[@value='requestinversedtracerequirement']/following-sibling::div[contains(@id,'embeddedItems')]/div[contains(@id,'Caption')]//*[contains(@class,'title')]/a"));
		for (WebElement element:elements){
			String uid = element.getText();
			requests.add(uid.substring(1, uid.length()-1));
		}
		return requests;
	}
	
	public List<String> readLinkedEnhancements(){
		clickTraceTab();
		List<String> requests = new ArrayList<String>();
		List<WebElement> elements = driver.findElements(By.xpath("//div[@id='fieldRowIncrements']//input[@value='requestinversedtracerequirement']/following-sibling::div[contains(@id,'embeddedItems')]/div[contains(@id,'Caption')]//*[contains(@class,'title')]/a"));
		for (WebElement element:elements){
			String uid = element.getText();
			requests.add(uid.substring(1, uid.length()-1));
		}
		return requests;
	}
	
	public List<String> readLinkedFunctions(){
		clickTraceTab();
		List<String> functions = new ArrayList<String>();
		List<WebElement> elements = driver.findElements(By.xpath("//input[@value='functioninversedtracerequirement']/following-sibling::div[contains(@id,'embeddedItems')]/div[contains(@id,'Caption')]//*[contains(@class,'title')]/a"));
		for (WebElement element:elements){
			String uid = element.getText();
			functions.add(uid.substring(1, uid.length()-1));
		}
		return functions;
	}
	
	public List<String> readTags(){
		clickMoreTab();
		List<String> tags = new ArrayList<String>();
		List<WebElement> elements = driver.findElements(By.xpath("//input[@value='wikitag']/following-sibling::div[contains(@id,'embeddedItems')]/div[contains(@id,'Caption')]//*[contains(@class,'title')]"));
		for (WebElement element:elements){
			tags.add(element.getText().trim());
		}
		return tags;
	}	

}
