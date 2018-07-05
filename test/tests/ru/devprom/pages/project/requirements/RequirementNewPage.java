package ru.devprom.pages.project.requirements;

import java.io.File;
import java.util.List;
import java.util.logging.Level;
import java.util.logging.Logger;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.Keys;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Requirement;
import ru.devprom.items.TestScenario;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requests.RequestViewPage;

public class RequirementNewPage extends SDLCPojectPageBase {

	@FindBy(id = "WikiPageCaption")
	protected WebElement captionEdit;

	@FindBy(id = "ParentPageText")
	protected WebElement parentPageEdit;

	@FindBy(id = "WikiPagePageType")
	protected WebElement typeEdit;

	@FindBy(id = "WikiPageSubmitBtn")
	protected WebElement submitBtn;

	@FindBy(id = "WikiPageSubmitOpenBtn")
	protected WebElement submitThenOpenBtn;
	
	@FindBy(xpath = "//input[@type='button' and @value='Отменить']")
	protected WebElement cancelBtn;

	@FindBy(xpath = "//a[contains(@class,'embedded-add-button') and preceding-sibling::input[@value='functioninversedtracerequirement']]")
	protected WebElement addFunctionBtn;

	@FindBy(xpath = "//a[contains(@class,'embedded-add-button') and preceding-sibling::input[@value='requestinversedtracerequirement']]")
	protected WebElement addRequestBtn;

	@FindBy(xpath = "//a[contains(@class,'embedded-add-button') and preceding-sibling::input[@value='wikitag']]")
	protected WebElement addTagBtn;
        
        //добавить исходное пожелание на вкладке Трассировка
        @FindBy(xpath = ".//*[@id='WikiPageIssues']/div/div/a")
	protected WebElement addSourseWish;
        
        //поле для добавления исходного пожелания
        @FindBy(xpath = ".//input[@class='autocomplete-text input-block-level ui-autocomplete-input']")
	protected WebElement addSourseWishField;
        
        //нопка сохранения добавленного исходного пожелания
        @FindBy(xpath = ".//input[@class='btn btn-primary btn-small']")
	protected WebElement saveAddedSourseWish;

	// кнопка добавить изображение в контент
	@FindBy(css = "span.cke_button_icon.cke_button__image_icon")
	protected WebElement addImageBtn;

	// кнопка загрузить изображение
	@FindBy(xpath = "//div[@name='uploadImage']//input[@type='file']")
	protected WebElement loadImageBtn;

	// кнопка сохранить изображение
	@FindBy(id = "cke_129_label")
	protected WebElement saveImageBtn;

	public RequirementNewPage(WebDriver driver) {
		super(driver);
	}

	public RequirementViewPage createSimple(Requirement r) {
		clickMainTab();
		captionEdit.clear();
		captionEdit.sendKeys(r.getName());

		submitDialog(submitBtn);
		driver.navigate().refresh();

		By locator = By.xpath("//td[@id='caption' and contains(text(),'"
				+ r.getName() + "')]/preceding-sibling::td[@id='uid']");
		String uid = driver.findElement(locator).getText();
		r.setId(uid.substring(1, uid.length() - 1));
		return clickToRequirement(r.getId());
	}

	public RequirementViewPage createChild(Requirement r) {
		return createChild(r,false);
	}

	public RequirementViewPage createChild(Requirement r, boolean doOpen) {
		clickMainTab();
		captionEdit.clear();
		captionEdit.sendKeys(r.getName());

		if (r.getContent() != null && !r.getContent().equals("")) {
			addContent(r.getContent());
		}
		if (r.getTemplateName() != null && !r.getTemplateName().equals("")) {
			CKEditor we = new CKEditor(driver);
			we.typeTemplate(r.getTemplateName());
		}
		submitDialog(doOpen ? submitThenOpenBtn : submitBtn);

		try {
			Thread.sleep(doOpen ? 9000 : 1500);
		} catch (InterruptedException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}

		By elementPath = By.xpath("//tr[contains(@id,'pmwikidocumentlist1_row')]//div[contains(@class,'wysiwyg-text') and contains(.,'" + r.getName() + "')]");
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(elementPath));
		String uid = "R-" + driver.findElement(elementPath).getAttribute("objectid");
		r.setId(uid);

		return new RequirementViewPage(driver);
	}

	public RequirementViewPage create(Requirement r) {
		clickMainTab();
		captionEdit.clear();
		captionEdit.sendKeys(r.getName());

		if (r.getContent() != null && !r.getContent().equals("")) {
			addContent(r.getContent());
		}

		if (r.getTemplateName() != null && !r.getTemplateName().equals("")) {
			CKEditor we = new CKEditor(driver);
			we.typeTemplate(r.getTemplateName());
		}

		if (r.getParentPage() != null && !r.getParentPage().equals("")) {
			addParentPage(r.getParentPage().getId().equals("") ? r.getParentPage().getName() : r.getParentPage().getId());
		}

		if (r.getType() != null && !r.getType().equals("")) {
			selectType(r.getType());
		}

		if (r.getTags().size() > 0) {
			addTags(r.getTags());
		}

		if (r.getRequests().size() > 0) {
			addRequests(r.getRequests());
		}

		if (r.getFunctions().size() > 0) {
			addFunctions(r.getFunctions());
		}

		submitDialog(submitBtn);
		driver.navigate().refresh();

		By locator = By.xpath("//td[@id='caption' and contains(text(),'" + r.getName() + "')]/preceding-sibling::td[@id='uid']");
		String uid = driver.findElement(locator).getText();
		r.setId(uid.substring(1, uid.length() - 1));
		return clickToRequirement(r.getId());
	}

	public void createFromBoard(Requirement r) {
		createFromBoard(r, new File(""), true);
	}
	public void createFromBoard(Requirement r, File filePath) {
		createFromBoard(r, filePath, true);
	}
	public void createFromBoard(Requirement r, boolean searchForParent) {
		createFromBoard(r, new File(""), searchForParent);
	}
	
	public void createFromBoard(Requirement r, File filePath, boolean searchForParent)
	{
		clickMainTab();
		captionEdit.clear();
		captionEdit.sendKeys(r.getName());

		if (r.getContent() != null && !r.getContent().equals("")) {
			addContent(r.getContent());
		}

		if (r.getType() != null && !r.getType().equals("")) {
			selectType(r.getType());
		}

		if (r.getTemplateName() != null && !r.getTemplateName().equals("")) {
			CKEditor we = (new CKEditor(driver)); 
			we.typeTemplate(r.getTemplateName());
		}

		if (filePath.isFile()) {
			CKEditor we = (new CKEditor(driver)); 
			we.typeText("\n");
			we.loadAttachementToRequirement(filePath);
		}
		
		if (r.getParentPage() != null && !r.getParentPage().equals("")) {
			addParentPage(r.getParentPage().getId().equals("") ? r.getParentPage().getName() : r.getParentPage().getId(), searchForParent);
		}

		if (r.getTags().size() > 0) {
			addTags(r.getTags());
		}

		if (r.getRequests().size() > 0) {
			addRequests(r.getRequests());
		}

		if (r.getFunctions().size() > 0) {
			addFunctions(r.getFunctions());
		}

		submitDialog(submitBtn);
	}

	public RequirementViewPage clickToRequirement(String id) {
		driver.findElement(
				By.xpath("//tr[contains(@id,'requirementlist1_row_')]/td[@id='uid']/a[contains(@href,'"
						+ id + "')]")).click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By
						.xpath("//div[@class='wiki-page-document']")));
		return new RequirementViewPage(driver);
	}

	private void addFunctions(List<String> functions) {
		clickTraceTab();
		for (String function : functions) {
			(new WebDriverWait(driver, waiting)).until(ExpectedConditions
					.visibilityOf(addFunctionBtn));
			addFunctionBtn.click();
			(new WebDriverWait(driver, waiting))
					.until(ExpectedConditions.visibilityOfElementLocated(By
							.xpath("//input[@value='functioninversedtracerequirement']/following-sibling::div[contains(@id,'fieldRowFeature')]//input[contains(@id,'FeatureText')]")));
			driver.findElement(
					By.xpath("//input[@value='functioninversedtracerequirement']/following-sibling::div[contains(@id,'fieldRowFeature')]//input[contains(@id,'FeatureText')]"))
					.sendKeys(function);
			autocompleteSelect(function);
			driver.findElement(
					By.xpath("//input[@value='functioninversedtracerequirement']/following-sibling::div[contains(@class,'embedded_footer')]//input[contains(@id,'saveEmbedded')]"))
					.click();
		}

	}

	private void addRequests(List<String> requests) {
		clickTraceTab();
		for (String request : requests) {
			(new WebDriverWait(driver, waiting)).until(ExpectedConditions
					.visibilityOf(addRequestBtn));
			addRequestBtn.click();
			(new WebDriverWait(driver, waiting))
					.until(ExpectedConditions.visibilityOfElementLocated(By
							.xpath("//input[@value='requestinversedtracerequirement']/following-sibling::div[contains(@id,'fieldRowChangeRequest')]//input[contains(@id,'ChangeRequestText')]")));
			driver.findElement(
					By.xpath("//input[@value='requestinversedtracerequirement']/following-sibling::div[contains(@id,'fieldRowChangeRequest')]//input[contains(@id,'ChangeRequestText')]"))
					.sendKeys(request);
			autocompleteSelect(request);
			driver.findElement(
					By.xpath("//input[@value='requestinversedtracerequirement']/following-sibling::div[contains(@class,'embedded_footer')]//input[contains(@id,'saveEmbedded')]"))
					.click();
		}
	}

	private void addTags(List<String> tags) {
		clickMoreTab();
		for (String tag : tags) {
			(new WebDriverWait(driver, waiting)).until(ExpectedConditions
					.visibilityOf(addTagBtn));
			addTagBtn.click();
			(new WebDriverWait(driver, waiting))
					.until(ExpectedConditions.visibilityOfElementLocated(By
							.xpath("//input[@value='wikitag']/following-sibling::div[contains(@id,'fieldRowTag')]//input[contains(@id,'TagText')]")));
			WebElement tagInput = driver.findElement(
					By.xpath("//input[@value='wikitag']/following-sibling::div[contains(@id,'fieldRowTag')]//input[contains(@id,'TagText')]"
							+ "\n"));
			tagInput.sendKeys(tag);
			tagInput.sendKeys(Keys.TAB);

			driver.findElement(
					By.xpath("//input[@value='wikitag']/following-sibling::div[contains(@class,'embedded_footer')]//input[contains(@id,'saveEmbedded')]"))
					.click();
			try {
				Thread.sleep(3000);
			} catch (InterruptedException e) {
				e.printStackTrace();
			}
			(new WebDriverWait(driver, waiting)).until(ExpectedConditions
					.visibilityOf(addTagBtn));
		}
	}

	public void addContent(String content) {
		clickMainTab();
		CKEditor we = new CKEditor(driver);
		we.changeText(content);
	}

	public void appendContent(String content) {
		clickMainTab();
		CKEditor we = new CKEditor(driver);
		we.typeText(content);
	}

	public void addParentPage(String name) {
		addParentPage(name, true);
	}
        
        public void addNewParentPage(String name) {
		addParentPage(name, false);
	}
        
	public void addParentPage(String name, boolean searchForParent) {
		clickMainTab();
		parentPageEdit.sendKeys(name);
		if ( searchForParent ) autocompleteSelect(name);
	}

	public void selectType(String type) {
		clickMainTab();
		(new Select(typeEdit)).selectByVisibleText(type);
		try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
		}
	}

	public boolean isAttributePresent(String attrRefName) {
		clickMoreTab();
		if (driver.findElements(By.name(attrRefName)).size() > 0)
			return true;
		else
			return false;
	}

	public String checkStringAttributeDefaultValue(String attrRefName) {
		clickMoreTab();
		return driver.findElement(By.name(attrRefName)).getAttribute("value");
	}

	public void setUserStringAttribute(String attrRefName, String value) {
		clickMoreTab();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOfElementLocated(By.name(attrRefName)));
		driver.findElement(By.name(attrRefName)).clear();
		driver.findElement(By.name(attrRefName)).sendKeys(value);
	}

	public void setUserOptionAttribute(String attrRefName, String value) {
		clickMoreTab();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.visibilityOfElementLocated(By.name(attrRefName)));
		new Select(driver.findElement(By.name(attrRefName)))
				.selectByValue(value);
	}

	public void clickMoreTab() {
		clickTab("additional");
	}

	public void clickMainTab() {
		clickTab("main");
	}

	public void clickTraceTab() {
		clickTab("trace-source-attribute");
	}

    public void addSourseWish(Requirement recuirement) {
            addRequests(recuirement.getRequests());
            submitDialog(submitBtn);
    }

    public void createWithUML(Requirement requirement, String uml) {
        clickMainTab();
		captionEdit.clear();
		captionEdit.sendKeys(requirement.getName());
                (new CKEditor(driver)).addUMLdiagramm(uml);
		addParentPage(requirement.getParentPage().getName());
		submitDialog(submitBtn);
    }
    
    public void createWithUMLWithNewParent(Requirement requirement, String uml) {
        clickMainTab();
		captionEdit.clear();
		captionEdit.sendKeys(requirement.getName());
                (new CKEditor(driver)).addUMLdiagramm(uml);
		addNewParentPage(requirement.getParentPage().getName());
		submitDialog(submitBtn);
    }
    
    public void createWithFormula(Requirement requirement, String formula) {
        clickMainTab();
		captionEdit.clear();
		captionEdit.sendKeys(requirement.getName());
                addContent(requirement.getContent());
                (new CKEditor(driver)).addFormula(formula);
		addParentPage(requirement.getParentPage().getName());
                selectType(requirement.getType());
		submitDialog(submitBtn);
    }

    public void createWithTemplate(Requirement testScenario)
	{
		clickMainTab();
	   CKEditor we = new CKEditor(driver);
	   if(testScenario.getTemplateName() != null)
		   we.typeTemplate(testScenario.getTemplateName());   
	   if(testScenario.getType()!= null)
	       (new Select(typeEdit)).selectByValue(
	    		   typeEdit.findElement(By.xpath("./option[contains(text(),'"+testScenario.getType()+"')]")).getAttribute("value")
	    		   );
		captionEdit.sendKeys(testScenario.getName());
		submitDialog(submitBtn);
	}

   public void createWithHTML(Requirement testScenario, String html) {
       clickMainTab();
       captionEdit.sendKeys(testScenario.getName());
       
       CKEditor we = new CKEditor(driver);
       we.addInnerText(html);
       if(testScenario.getTemplateName() != null) {
    	   we.typeTemplate(testScenario.getTemplateName());   
       }
       if(testScenario.getType()!= null) {
		   (new Select(typeEdit)).selectByValue(
				   typeEdit.findElement(By.xpath("./option[contains(text(),'"+testScenario.getType()+"')]")).getAttribute("value")
				   );
       }
        try {
            Thread.sleep(1000);
        } catch (InterruptedException ex) {
            Logger.getLogger(RequirementNewPage.class.getName()).log(Level.SEVERE, null, ex);
        }
        
		submitDialog(submitBtn);
   }
}
