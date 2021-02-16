package ru.devprom.pages.project.requirements;

import java.io.File;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.util.List;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.xpath.XPathExpressionException;

import org.openqa.selenium.By;
import org.openqa.selenium.ElementNotVisibleException;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.xml.sax.SAXException;

import ru.devprom.helpers.FileOperations;
import ru.devprom.helpers.XLTableParser;
import ru.devprom.items.Project;
import ru.devprom.items.Requirement;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requests.RequestsPage;
import ru.devprom.pages.project.testscenarios.TestScenarioNewPage;
import ru.devprom.pages.project.testscenarios.TestScenariosPage;

public class RequirementsPage extends SDLCPojectPageBase {

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]")
	protected WebElement actionsBtn;
        
    @FindBy(xpath = ".//*[@id='bulk-actions']/a")
	protected WebElement moreBtn;
        
 	//пункт тестовая документация меню Еще
	@FindBy(xpath = ".//*[@id='bulk-actions']//*[contains(text(),'Тестовая документация')]")
	protected WebElement testDocsItem;
	
	@FindBy(xpath = "//div[contains(@class,'filter-actions')]//a[contains(@id,'create') and contains(@class,'append-btn')]")
	protected WebElement newRequirementBtn;
	
	@FindBy(xpath = "//ul/li/a[text()='Удалить'and @href]")
	protected WebElement deleteBtn;
	
	@FindBy(xpath = "//ul/li/a[@id='import-excel']")
	protected WebElement importBtn;
	
	@FindBy(xpath = "//ul//a[text()='Массовые операции']")
	protected WebElement massBtn;
	
	@FindBy(xpath = "//a[@id='bulk-delete']")
	protected WebElement removeRequirementBtn;
	
	@FindBy(xpath = "//a[@id='export-excel-text']")
	protected WebElement excelBtn;
	
	@FindBy(xpath = "//a[@uid='document']")
	protected WebElement selectDocumentList;
	
	@FindBy(id="filter-settings")
	protected WebElement filterBtn;
	
	public RequirementsPage(WebDriver driver) {
		super(driver);
	}

	public RequirementsPage(WebDriver driver, Project project) {
		super(driver, project);
	}
	
	

	public RequirementViewPage clickToRequirement(String id) {
		driver.findElement(
				By.xpath("//tr[contains(@id,'requirementlist1_row_')]/td[@id='uid']/a[contains(@href,'R-" + id.split("-")[1] + "')]")).click();
		return new RequirementViewPage(driver);
	}
	
	public RequirementNewPage createNewRequirement()
	{
		newRequirementBtn.click();
		return new RequirementNewPage(driver);
	}
	
	public RequirementNewPage createRequirementWithType(String type)
	{
		clickOnInvisibleElement(driver.findElement(By.xpath("//a[contains(@id,'create-') and contains(.,'"+type+"')]")));
		return new RequirementNewPage(driver);
	}
	
	public String getRequirementProperty(String id, String propertyName) {
		WebElement requirement;
		requirement = driver
				.findElement(By
						.xpath("//tr[contains(@id,'requirementlist1_row_')]/td[@id='uid']/a[contains(.,'"
								+ id + "')]/../.."));
		switch (propertyName) {
		case "name": {
			return requirement.findElement(By.id("caption")).getText();
		}
		case "project": {
			return requirement.findElement(By.id("project")).getText();
		}
		case "state":
			return requirement.findElement(By.id("state")).getText();
		case "date":
			return requirement.findElement(By.id("recordcreated")).getText().trim();
		default:
			return "error property";
		}
	}
	


	public Requirement[] readAllRequirements() {
		Requirement[] requirementsList = new Requirement[driver.findElements(By.id("uid"))
				.size()];
		for (int i = 0; i < requirementsList.length; i++) {
			WebElement row = driver.findElement(By.id("requirementlist1_row_"
					+ (i + 1)));
			String id = row.findElement(By.id("uid")).getText();
			id = id.substring(1, id.length() - 1);
			String name = row.findElement(By.id("caption")).getText();
			String state = row.findElement(By.id("state")).getText();
			String project = row.findElement(By.id("project")).getText();
			String date = row.findElement(By.id("recordcreated")).getText().trim();
			requirementsList[i] = new Requirement(name);
			requirementsList[i].setDate(date);
			requirementsList[i].setState(state);
			requirementsList[i].setProjectID(project);
		}
		return requirementsList;
	}
	

	public void checkRequirement(String id) {
		driver.findElement(
				By.xpath("//tr[contains(@id,'requirementlist1_row_')]/td[@id='uid']/a[contains(.,'"
						+ id + "')]/../preceding-sibling::td/input[contains(@class,'checkbox')]"))
				.click();		
	}
	
	public boolean isRequirementPresent(String id) {
		try {
			driver.findElement(By
					.xpath("//tr[contains(@id,'requirementlist1_row_')]/td[@id='uid']/a[contains(.,'"
							+ id + "')]"));
			return true;
		} catch (NoSuchElementException e) {
			return false;
		}
	}
	
		public Requirement[] exportToExcel(String[] moreFields) throws XPathExpressionException, ParserConfigurationException, SAXException, IOException, InterruptedException
		{
			Requirement[] r = null;
			int attemptscount = 5;

			FileOperations.removeExisted("Нереализованные требования.xls");
			clickOnInvisibleElement(excelBtn);

			File excelTable = FileOperations.downloadFile("Нереализованные требования.xls");
			while (true)
				if (attemptscount == 0)
					break;
				else {
					try {
						attemptscount--;
						r = XLTableParser.getRequirements(excelTable,moreFields);
						break;
					} catch (FileNotFoundException e) {
						Thread.sleep(2000);
					}
				}
			return r;
		}
	
		public RequirementsPage deleteSelected()
		{
			clickOnInvisibleElement(removeRequirementBtn);
			waitForDialog();
			submitDialog(driver.findElement(By.id("SubmitBtn")));
			return new RequirementsPage(driver);
		}
	
		
		public RequirementsImportPage clickImport(){
			
			actionsBtn.click();
			try {
				importBtn.click();
			} catch (ElementNotVisibleException e) {
				clickOnInvisibleElement(importBtn);
			}
			return new RequirementsImportPage(driver);
		}
		
		public Requirement findRequirementById(String id) {
			Requirement requirement;
			WebElement row = driver
					.findElement(By
							.xpath("//tr[contains(@id,'requirementlist1_row_')]/td[@id='uid']/a[contains(.,'"
									+ id + "')]/../.."));
			String caption = row.findElement(By.id("caption")).getText();
			String state = row.findElement(By.id("state")).getText();
			requirement = new Requirement(caption);
			requirement.setId(id);
			requirement.setState(state);
			return requirement;
		}
		
		
		public Requirement findRequirementByName(String name) {
			Requirement requirement;
			WebElement row = driver
					.findElement(By
							.xpath("//tr[contains(@id,'requirementlist1_row_')]/td[@id='caption' and contains(.,'"
									+ name + "')]/.."));
			String id = row.findElement(By.xpath("td[@id='uid']/a")).getAttribute("href");
			id = id.substring(id.lastIndexOf("/")+1);
			String state = row.findElement(By.id("state")).getText();
			requirement = new Requirement(name);
			requirement.setId(id);
			requirement.setState(state);
			return requirement;
		}
		
		public 	RequirementsPage selectDocument(String requirement){
			selectDocumentList.click();
			WebElement documentItem = selectDocumentList.findElement(By.xpath("./ul[@role='menu']/li/a[contains(.,'"+requirement+"')]"));
			(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(documentItem));
			documentItem.click();
			return new RequirementsPage(driver);
		}
		
		
		public 	RequirementsPage massCreateRequests(){
			WebElement createRequests = driver.findElement(By.xpath("//div[@id='bulk-actions']//a[@id='requirementcreateissuewebmethod']"));
			clickOnInvisibleElement(createRequests);
			waitForDialog();
			submitDialog(driver.findElement(By.id("SubmitBtn")));
			return new RequirementsPage(driver);
		}

    public String getIdByName(String name) {
        String ids = driver.findElement(By.xpath("//tr[contains(@id,'requirementlist1_row_')]/td[@id='caption' and contains(.,'"+
                name+"')]/preceding-sibling::td[@id='uid']/a")).getText();
        ids = ids.substring(1, ids.length() - 1);
        FILELOG.debug("Click to UID of requirement");
        return ids; 
    }

    public void checkAll() {
		driver.findElement(By.xpath("//input[contains(@id,'to_delete_all')]")).click();
    }

    public TestScenariosPage clickMoreTestDocs() {
        clickOnInvisibleElement(moreBtn);
        clickOnInvisibleElement(testDocsItem);
        return new TestScenariosPage(driver);
    }

	public TestScenarioNewPage gotoCreateScenario(String idRequirement) {
		WebElement invisElement = driver.findElement(By.xpath(".//*[@object-id='"+idRequirement
				+"']//*[@id='operations']//a[contains(@class,'dropdown-toggle')]"));
		clickOnInvisibleElement(invisElement);
		clickOnInvisibleElement(
			driver.findElement(By.xpath(".//*[@object-id='"+idRequirement+"']//*[@id='operations']//a[contains(.,'Создать')]"))
		);
		clickOnInvisibleElement(
			driver.findElement(By.xpath(".//*[@object-id='"+idRequirement+"']//*[@id='operations']//a[contains(.,'Тестовый сценарий')]"))
		);
		waitForDialog();
		return new TestScenarioNewPage(driver);
	}
}
