package ru.devprom.pages;

import java.io.File;
import java.util.logging.Level;

import org.apache.log4j.LogManager;
import org.apache.log4j.Logger;
import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.Keys;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.helpers.Configuration;
import static ru.devprom.helpers.WebDriverPointerRobot.mouseMove;


public class CKEditor {

	@FindBy(className = "cke_button__bgcolor")
	protected WebElement colorBtn;

	@FindBy(className = "cke_button__bold")
	protected WebElement boldBtn;
	
	@FindBy(className = "cke_button__italic")
	protected WebElement italikBtn;
	
	@FindBy(className = "cke_button__underline")
	protected WebElement underlineBtn;
	
	@FindBy(className = "cke_button__strike")
	protected WebElement crosslineBtn;
	
	@FindBy(className = "cke_button__image")
	protected WebElement addImageToRequirementBtn;
        
    @FindBy(className = "cke_button__plantuml")
	protected WebElement addUMLBtn;
        
    @FindBy(xpath = "//*[@class='cke_dialog_ui_vbox_child']//*[@class='cke_dialog_ui_input_textarea']/textarea")
	protected WebElement addUMLField;
        
    @FindBy(xpath = ".//*[contains(@class,'cke_dialog_ui_button_ok')]")
	protected WebElement okUMLBtn;
        
    @FindBy(className = "cke_button__source")
	protected WebElement addSourseBtn;
	
	@FindBy(xpath = ".//div[@role='presentation']/iframe")
	protected WebElement wikiFrame;
	
	protected final WebDriver driver;
	protected final Logger FILELOG = LogManager.getLogger("MAIN");
	
	public CKEditor(WebDriver driver) {
		PageFactory.initElements(driver, this);
		this.driver = driver;
		FILELOG.debug("Entering Wiki Editor section");
		
	}
	public void boldOnOff() {
		(new WebDriverWait(driver, Configuration.getWaiting())).until(ExpectedConditions.visibilityOf(boldBtn));
		boldBtn.click();
	}
	
	public void typeText(String text)
	{
		WebElement newFrame = driver.findElement(By
				.xpath(".//div[@role='presentation']/iframe"));
		driver.switchTo().frame(newFrame);
		WebElement editable = driver.findElement(By.xpath("//body"));
		editable.sendKeys(text);
		driver.switchTo().defaultContent();
	}
	
	public void typeTemplate(String templateName)
	{
		WebElement newFrame = driver.findElement(By
				.xpath(".//div[@role='presentation']/iframe"));
		driver.switchTo().frame(newFrame);
		WebElement editable = driver.findElement(By.xpath("//body"));
		editable.clear();
		editable.sendKeys("#");
		try {
			Thread.sleep(6000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		driver.switchTo().defaultContent();
		(new WebDriverWait(driver, Configuration.getWaiting())).until(
				ExpectedConditions.presenceOfElementLocated(By.xpath("//ul[not(contains(@style,'none'))]/li[contains(@class,'textcomplete-item')]/a"))
				);
		driver.findElement(By.xpath("//ul[not(contains(@style,'none'))]/li[contains(@class,'textcomplete-item')]/a[contains(.,'"+templateName+"')]")).click();
	}

	public void changeText(String text){
		WebElement newFrame = driver.findElement(By
				.xpath(".//div[@role='presentation']/iframe"));
		driver.switchTo().frame(newFrame);
		driver.findElement(By.xpath("//body")).clear();
		driver.findElement(By.xpath("//body")).sendKeys(text);
		driver.switchTo().defaultContent();
		//((JavascriptExecutor) driver).executeScript("document.body.innerHTML = '"+text+"';");
	}
	
	public String getText(){
		WebElement newFrame = driver.findElement(By.xpath(".//div[@role='presentation']/iframe"));
		driver.switchTo().frame(newFrame);
		String text = driver.findElement(By.xpath("//body")).getText();
		driver.switchTo().defaultContent();
		return text;
	}

	public void loadAttachementToRequirement(File image)
	{
		(new WebDriverWait(driver, Configuration.getWaiting())).until(
				ExpectedConditions.presenceOfElementLocated(By.xpath(".//form//*[contains(@class,'editor-area')]//input"))
				);

		String codeIE = "$('input[type]').css('visibility','visible')";
		((JavascriptExecutor) driver).executeScript(codeIE);
		
		driver.findElement(By.xpath(".//form//*[contains(@class,'editor-area')]//input")).sendKeys(image.getAbsolutePath());
		
		try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {
		}
	}

    public void addUMLdiagramm(String uml) 
    {
    	(new WebDriverWait(driver,Configuration.getWaiting())).until(ExpectedConditions.visibilityOf(addUMLBtn));
    	addUMLBtn.click();
    	(new WebDriverWait(driver,Configuration.getWaiting())).until(ExpectedConditions.visibilityOf(okUMLBtn));
    	addUMLField.sendKeys(uml);
		clickOnInvisibleElement(okUMLBtn);
    }

	public void clickOnInvisibleElement(WebElement element) {
		mouseMove(element);
		String script = "var object = arguments[0];"
				+ "var theEvent = document.createEvent(\'MouseEvent\');"
				+ "theEvent.initMouseEvent(\'click\', true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);"
				+ "object.dispatchEvent(theEvent);";
		((JavascriptExecutor) driver).executeScript(script, element);
	}

     public void addFormula(String formula) {
        try{
        	By locator = By.xpath("//div[contains(@class,'editor-area')]//span[contains(@class,'cke_toolgroup')]//a[contains(@class,'cke_button__mathjax')]");
        	(new WebDriverWait(driver,Configuration.getWaiting())).until(ExpectedConditions.presenceOfElementLocated(locator));
        	for( WebElement btn : driver.findElements(locator) ) {
        		if ( btn.isDisplayed() ) {
        			btn.click();
        			break;
        		}
        	}
	        Thread.sleep(200);
	        if ( !formula.isEmpty() ) {
	            addUMLField.clear();
	            addUMLField.sendKeys(formula);
	        }
	        (new WebDriverWait(driver,Configuration.getWaiting())).until(ExpectedConditions.visibilityOf(okUMLBtn));
	        okUMLBtn.click(); 
        }
        catch(InterruptedException e)
        {}
    }

    public void fillCell(String row, String column, String text) {
        WebElement newFrame = driver.findElement(By
				.xpath(".//div[@role='presentation']/iframe"));
		driver.switchTo().frame(newFrame);
        WebElement editableArea = driver.findElement(By.xpath("//body"));
        WebElement cell = driver.findElement(By.xpath("//body/table//tr["+row+"]/td["+column+"]"));
        cell.click();
        editableArea.sendKeys(text);
        driver.switchTo().defaultContent();
    }

    public void addSourseCode(String html) {
            try {
                addSourseBtn.click();
                WebElement editableArea = driver.findElement(By.xpath("//div[@role='presentation']/textarea"));
                editableArea.click();
                editableArea.sendKeys(html);
                Thread.sleep(3000);
                addSourseBtn.click();
            } catch (InterruptedException ex) {
                java.util.logging.Logger.getLogger(CKEditor.class.getName()).log(Level.SEVERE, null, ex);
            }
    }

    public void addInnerText(String html) {
        WebElement newFrame = driver.findElement(By
				.xpath(".//div[@role='presentation']/iframe"));
		driver.switchTo().frame(newFrame);
                WebElement textField = driver.findElement(By.xpath("//body")); 
                ((JavascriptExecutor) driver).executeScript("arguments[0].innerHTML = '"+html+"';", textField);
		//driver.findElement(By.xpath("//body")).clear();
		//driver.findElement(By.xpath("//body")).sendKeys(html);
		driver.switchTo().defaultContent();
    }
}
