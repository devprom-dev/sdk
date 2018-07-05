package ru.devprom.pages.project.settings;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.openqa.selenium.By;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requests.RequestsStatePage;
import ru.devprom.pages.project.tasks.TasksStatePage;



public class StateEditPage extends SDLCPojectPageBase {

	@FindBy(xpath = "//input[@value='stateaction']//following-sibling::a[contains(@class,'embedded-add-button')]")
	protected WebElement addSystemActionBtn;

	@FindBy(xpath = "//span[@id='pm_StateActions']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addActionBtn;
	
	@FindBy(xpath = "//span[@id='pm_StateActions']//select[contains(@id,'ReferenceName')]")
	protected WebElement addSystemActionSelect;
	
	@FindBy(id = "pm_StateSubmitBtn")
	protected WebElement saveBtn;
	
	@FindBy(xpath = "//a[contains(@class,'embedded-add-button') and preceding-sibling::input[@value='stateattribute']]")
	protected WebElement addAttributeBtn;

	@FindBy(xpath = "//span[@id='pm_StateAttributes']//select[contains(@id,'ReferenceName')]")
	protected WebElement addAttributeSelect;
	
	@FindBy(xpath = "//span[@id='pm_StateAttributes']//input[contains(@id,'IsVisible')]")
	protected WebElement addAttributeIsVisible;
	
	@FindBy(xpath = "//span[@id='pm_StateAttributes']//input[contains(@id,'IsRequired')]")
	protected WebElement addAttributeIsRequired;
	
	@FindBy(xpath = "//span[@id='pm_StateAttributes']//input[contains(@id,'saveEmbedded')]")
	protected WebElement saveAttributeBtn;
	
	@FindBy(xpath = "//span[@id='pm_StateActions']//input[contains(@id,'saveEmbedded')]")
	protected WebElement saveActionBtn;
	
	@FindBy(xpath = "//span[@id='pm_StateActions']//select[contains(@name,'ReferenceName')]")
	protected WebElement actionSelect;
	
	public StateEditPage(WebDriver driver) {
		super(driver);
	}

	public StateEditPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	
	public void addSystemAction(String systemAction){
		addSystemActionBtn.click();
		(new Select(addSystemActionSelect)).selectByVisibleText(systemAction);
		saveActionBtn.click();
		
	}
	
	/**
	 * Метод не возвращает новую страницу, так как является универсальным для различных страниц.
	 * В тесте необходимо создавать объект нужной страницы дополнительно.
	 */
	public void saveSystemAction(){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(saveBtn));
		submitDialog(saveBtn);
	}

	public void addAttribute(String attributeName, boolean isVisible, boolean isRequired){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOf(addAttributeBtn));
		addAttributeBtn.click();
		new Select(addAttributeSelect).selectByVisibleText(attributeName);
		
		if (isVisible) {if (!addAttributeIsVisible.isSelected())
		addAttributeIsVisible.click();
		}
		else {if (addAttributeIsVisible.isSelected())
			addAttributeIsVisible.click();
		}
		if (isRequired) {if (!addAttributeIsRequired.isSelected())
			addAttributeIsRequired.click();
		}
			else {if (addAttributeIsRequired.isSelected())
				addAttributeIsRequired.click();
			}
		saveAttributeBtn.click();
	}
	
	
	/**
	 * Ищет атрибут в списке, если находит, удаляет.
	 * Возвращает true если атрибут был, false - если его не было изначально
	 * @param attributeName
	 * @return
	 */
	public boolean removeAttribute(String attributeName) {
		boolean isFound=false; 
		if (!driver.findElements(By.xpath("//span[@id='pm_StateAttributes']//div[contains(@id,'Caption')]//*[contains(@class,'title') and contains(text(),'"+attributeName+"')]")).isEmpty()) {
			isFound = true;
			clickOnInvisibleElement(driver.findElement(By.xpath("//div[@class='embeddedRowTitle']//*[contains(@class,'title') and contains(text(),'" + attributeName
						+ "')]/following-sibling::ul//a[text()='Удалить']")));
		}
		return isFound;
	}

	public void removeAction(String actionName) {
		clickOnInvisibleElement(driver.findElement(By
				.xpath("//div[@class='embeddedRowTitle']//*[contains(@class,'title') and contains(text(),'" + actionName
						+ "')]/following-sibling::ul//a[text()='Удалить']")));
	}

	public void addAction(String actionName) {
		addActionBtn.click();
		(new Select(actionSelect)).selectByVisibleText(actionName);
		saveActionBtn.click();
	}

	/**
	 * Метод не возвращает новую страницу, так как является универсальным для различных страниц.
	 * В тесте необходимо создавать объект нужной страницы дополнительно.
	 */
	public void saveChanges() {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(saveBtn));
		submitDialog(saveBtn);
	}
	
}
