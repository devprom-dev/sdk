package ru.devprom.pages.project.settings;

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

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class TransitionEditPage extends SDLCPojectPageBase {

	@FindBy(id = "pm_TransitionSubmitBtn")
	protected WebElement saveBtn;
	
	@FindBy(id = "pm_TransitionIsReasonRequired")
	protected WebElement reasonSelect;

	@FindBy(xpath = "//span[@name='pm_TransitionAttributes']//a[contains(@class,'embedded-add-button')]")
	protected WebElement obligatoryFieldAddBtn;
	
	@FindBy(xpath = "//span[@name='pm_TransitionPredicates']//a[contains(@class,'embedded-add-button')]")
	protected WebElement preconditionAddBtn;

	@FindBy(xpath = "//span[@name='pm_TransitionProjectRoles']//a[contains(@class,'embedded-add-button')]")
	protected WebElement projectRoleAddBtn;
	
	@FindBy(xpath = "//span[@name='pm_TransitionResetFields']//a[contains(@class,'embedded-add-button')]")
	protected WebElement resetFieldAddBtn;
	
	public TransitionEditPage(WebDriver driver) {
		super(driver);
	}

	public TransitionEditPage(WebDriver driver, Project project) {
		super(driver, project);
	}
	
	public void addObligatoryField(String field) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(obligatoryFieldAddBtn));
		obligatoryFieldAddBtn.click();
		WebElement selectElem = driver
				.findElement(By
						.xpath("//input[@value='transitionattribute']/following-sibling::div[contains(@id,'fieldRowReferenceName')]//select[contains(@id,'ReferenceName')]"));
		(new Select(selectElem)).selectByVisibleText(field);
		driver.findElement(
				By.xpath("//input[@value='transitionattribute']/following-sibling::div[contains(@class,'embedded_footer')]/input[contains(@id,'saveEmbedded')]"))
				.click();
	}

	
	public void addProjectRole(String role) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(projectRoleAddBtn));
		projectRoleAddBtn.click();
		WebElement selectElem = driver
				.findElement(By
						.xpath("//input[@value='transitionrole']/following-sibling::div[contains(@id,'fieldRowProjectRole')]//select[contains(@id,'ProjectRole')]"));
		(new Select(selectElem)).selectByVisibleText(role);
		driver.findElement(
				By.xpath("//input[@value='transitionrole']/following-sibling::div[contains(@class,'embedded_footer')]/input[contains(@id,'saveEmbedded')]"))
				.click();
	}


	public TransitionEditPage addPrecondition(String precondition) {
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(preconditionAddBtn));
		preconditionAddBtn.click();
		WebElement selectElem = driver
				.findElement(By
						.xpath("//input[@value='transitionpredicate']/following-sibling::div[contains(@id,'fieldRowPredicate')]//select[contains(@id,'Predicate')]"));
		(new Select(selectElem)).selectByVisibleText(precondition);
		driver.findElement(
				By.xpath("//input[@value='transitionpredicate']/following-sibling::div[contains(@class,'embedded_footer')]/input[contains(@id,'saveEmbedded')]"))
				.click();
		return new TransitionEditPage(driver);
	}

	public void addResetField(String field)
	{
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(resetFieldAddBtn));
		resetFieldAddBtn.click();
		WebElement selectElem = driver
				.findElement(By
						.xpath("//input[@value='transitionresetfield']/following-sibling::div[contains(@id,'fieldRowReferenceName')]//select[contains(@id,'ReferenceName')]"));
		(new Select(selectElem)).selectByVisibleText(field);
		driver.findElement(
				By.xpath("//input[@value='transitionresetfield']/following-sibling::div[contains(@class,'embedded_footer')]/input[contains(@id,'saveEmbedded')]"))
				.click();
	}

	
	
	public TransitionEditPage removePrecondition(String precondition) {
	WebElement deleteBtn = driver.findElement(By.xpath("//div[@id='fieldRowPredicates']//span[@name='pm_TransitionPredicates']//*[contains(@class,'title') and contains(.,'"
	                   +precondition+"')]/following-sibling::ul/li[@uid='delete']/a"));
		driver.findElement(By.xpath("//div[@id='fieldRowPredicates']//span[@name='pm_TransitionPredicates']//*[contains(@class,'title') and contains(.,'" +precondition+"')]")).click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(deleteBtn));
		deleteBtn.click();
		return new TransitionEditPage(driver);
	}

	
	public void checkIsNeedReasonForTransitionBox(){
		(new Select(reasonSelect)).selectByValue("Y");	
	}
	
	public void uncheckIsNeedReasonForTransitionBox(){
		(new Select(reasonSelect)).selectByValue("N");	
	}
	
	public Boolean isNeedReasonForTransition(){
		return (new Select(reasonSelect)).getFirstSelectedOption().getAttribute("value").equals("Y");
	}
	
	public List<String> getObligatoryFields(){
		List<String> list = new ArrayList<String>();
		
		List<WebElement> elements = driver.findElements(By.xpath("//span[@name='pm_TransitionAttributes']//div[contains(@id,'Caption')]//*[contains(@class,'title')]"));
		 for (WebElement el:elements){
			 String[] items = el.getText().split("\\(");
			 list.add(items[0].trim());
		 }
		
		return list;
	}
	
	
	public List<String> getProjectRoles(){
		List<String> list = new ArrayList<String>();
		
		List<WebElement> elements = driver.findElements(By.xpath("//span[@name='pm_TransitionProjectRoles']//div[contains(@id,'Caption')]//*[contains(@class,'title')]"));
		 for (WebElement el:elements){
			 list.add(el.getText().trim());
		 }
		
		return list;
	}
	
	
	public List<String> getResetFields()
	{
		List<String> list = new ArrayList<String>();
		
		List<WebElement> elements = driver.findElements(By.xpath("//span[@name='pm_TransitionResetFields']//div[contains(@id,'Caption')]//*[contains(@class,'title')]"));
		 for (WebElement el:elements){
			 list.add(el.getText().trim());
		 }
		
		return list;
	}
	
	public List<String> getRemovePreconditions(){
		List<String> list = new ArrayList<String>();
		
		List<WebElement> elements = driver.findElements(By.xpath("//span[@name='pm_TransitionPredicates']//div[contains(@id,'Caption')]//*[contains(@class,'title')]"));
		 for (WebElement el:elements){
			 list.add(el.getText().trim());
		 }
		
		return list;
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
