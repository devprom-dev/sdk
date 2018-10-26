package ru.devprom.pages.project.requests;

import org.openqa.selenium.By;
import org.openqa.selenium.Keys;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.UnhandledAlertException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Spent;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class RequestDonePage extends SDLCPojectPageBase {

	@FindBy(id = "ClosedInVersionText")
	protected WebElement versionInput;

	@FindBy(id = "pm_ChangeRequestSubmitBtn")
	protected WebElement submitBtn;

	@FindBy(xpath = "//div[@id='modal-form']//span[@name='pm_ChangeRequestFact']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addedDoToDoneLink;
        
        //кнопка сохранить время после ввода на форме  перехода для требования
        @FindBy(xpath=".//*[@id='pm_ChangeRequestSubmitBtn']")
	protected WebElement submitAddedTimeReq;

	public RequestDonePage(WebDriver driver) {
		super(driver);
	}

	public String getMessage() {
		try {
			return driver
					.findElement(
							By.xpath("//div[contains(@class,'alert-info')]"))
					.getText().trim();

		} catch (NoSuchElementException e) {
			return "";
		}
	}

	public RequestViewPage complete(String comment, String version)
	{
		if ( !version.equals("") ) {
			versionInput.clear();
			versionInput.sendKeys(version);
			autocompleteSelect(version);
		}
		(new CKEditor(driver)).typeText(comment);
		submitDialog(submitBtn);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.xpath("//span[@id='state-label' and contains(.,'Выполнено')]")));
		return new RequestViewPage(driver);
	}
	
	
	public boolean checkRequired(String idOfThefieldToCheck, String comment, String version) {
		versionInput.clear();
		versionInput.sendKeys(version);
		autocompleteSelect(version);
		(new CKEditor(driver)).typeText(comment);
		submitBtn.click();
		return !driver.findElements(By.xpath("//*[contains(@id,'"+idOfThefieldToCheck+"') and contains(@style,'rgb(255, 175, 175)')]")).isEmpty();
	}
	
	/**
	 * Пытаемся выполнить Пожелание, заполнив поля "Комментария" и "Выполнено в версии"
	 * @param comment
	 * @param version
	 * @return
	 */
	public void tryToComplete(String comment, String version) {
		versionInput.clear();
		versionInput.sendKeys(version);
		autocompleteSelect(version);
		(new CKEditor(driver)).typeText(comment);
		submitBtn.click();
	}
	
	public RequestViewPage cancel(){
		driver.findElement(By.id("pm_ChangeRequestCancelBtn")).click();
		safeAlertAccept();
		(new WebDriverWait(driver, waiting)).until(elementDissapeared(By.xpath("//div[@id='modal-form']")));
		return new RequestViewPage(driver);
	}

	public RequestViewPage complete(String comment, String version, Spent spent) {
		addedDoToDoneLink.click();
		driver.findElement(
				By.xpath("//div[@id='modal-form']//span[@name='pm_ChangeRequestFact']//input[contains(@id,'Capacity')]"))
				.sendKeys(String.valueOf(spent.hours));
		WebElement reportDate = driver.findElement(
				By.xpath("//div[@id='modal-form']//span[@name='pm_ChangeRequestFact']//input[contains(@id,'ReportDate')]"));
		reportDate.clear();
		reportDate.sendKeys(spent.date);
		reportDate.sendKeys(Keys.TAB);
		driver.findElement(
				By.xpath("//div[@id='modal-form']//span[@name='pm_ChangeRequestFact']//textarea[contains(@id,'Description')]"))
				.sendKeys(spent.description);
		driver.findElement(
				By.xpath("//div[@id='modal-form']//span[@name='pm_ChangeRequestFact']//input[contains(@id,'saveEmbedded')]"))
				.click();
		if (version!=null && !"".equals(version)) {
			versionInput.clear();
			versionInput.sendKeys(version); 
			autocompleteSelect(version);
		}
		(new CKEditor(driver)).typeText(comment);
		submitDialog(submitBtn);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By.xpath("//span[@id='state-label' and contains(.,'Выполнено')]")));
		return new RequestViewPage(driver);
	}

    public void submit() {
        submitDialog(submitAddedTimeReq);
    }
}
