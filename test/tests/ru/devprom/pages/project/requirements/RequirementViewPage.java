package ru.devprom.pages.project.requirements;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLConnection;
import java.nio.channels.Channels;
import java.nio.channels.ReadableByteChannel;
import java.util.ArrayList;
import java.util.List;
import javax.xml.bind.DatatypeConverter;

import org.apache.commons.codec.binary.Base64;
import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.TimeoutException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Requirement;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.kanban.KanbanTaskNewPage;
import ru.devprom.pages.kanban.KanbanTaskViewPage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requests.RequestRejectPage;
import ru.devprom.pages.project.tasks.TaskNewPage;
import ru.devprom.pages.project.testscenarios.TestScenarioNewPage;
import ru.devprom.pages.project.testscenarios.TestScenariosPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationsPage;


public class RequirementViewPage extends SDLCPojectPageBase
{
	@FindBy(xpath = ".//*[@class='trace-state']/..")
	protected WebElement attentionBtn;
	
        @FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]")
	protected WebElement actionsBtn;
        
        @FindBy(xpath = "//ul//*[text()='Экспорт']")
	protected WebElement exportItem;
        
	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(@class, 'actions-button')]")
	protected WebElement asterixBtn;
        
    //пункт Тестовый сценарий подменю Создать меню конопки со зведочкой
    @FindBy(xpath = "//a[contains(.,'Тестовый сценарий')]")
	protected WebElement testScenarioItem;
        
    //пункт Тестовый сценарий подменю Создать меню конопки со зведочкой
    @FindBy(xpath = "//a[@id='cover-requirement']")
	protected WebElement coverRequirementItem;
	
    @FindBy(xpath = "//a[contains(@id,'implement-issue')]")
	protected WebElement reworkItem;
        
	@FindBy(xpath = "//a[@id='modify']")
	protected WebElement editBtn;
	
	@FindBy(xpath = "//a[@id='workflow-completed']")
	protected WebElement completeBtn;
        
    @FindBy (xpath="//*[contains(@id,'workflow-State')]")
	protected WebElement agreeWithOsaItem;
        
    @FindBy(xpath = "//a[@id='workflow-submitted']")
	protected WebElement returnToWorkBtn;
	
	@FindBy(xpath = "//a[text()='Реализовано' and contains(@href,'workflowMoveObject')]")
	protected WebElement signBtn;
	
	@FindBy(xpath = "//a[@id='new-baseline']")
	protected WebElement addToBaselineBtn;

	@FindBy(xpath = "//a[@id='new-branch']")
	protected WebElement makeBranchBtn;

	@FindBy(xpath = "//a[contains(@id,'workflow') and text()='Вернуть в работу']")
	protected WebElement getBackBtn;
	
	@FindBy(xpath = "//a[@uid='baseline' and contains(@class,'dropdown-toggle')]")
	protected WebElement versionBtn;
	
	@FindBy(xpath = "//a[contains(@id,'append-child-page')]")
	protected WebElement addPartitionBtn;
	
	@FindBy(xpath = "//a[@uid='compareto']")
	protected WebElement compareWithBtn;
	
    @FindBy (xpath = "//*[contains(@class,'document-page-comments-link')]")
	protected WebElement commentsLink;
        
    @FindBy (xpath = "//*[@class='comment']/a")
	protected WebElement addCommentBtn;
        
    @FindBy (xpath = "//input[@id='btn']")
	protected WebElement sendBtn;
        
    //корневой каталог в дереве
    @FindBy (xpath="//ul[contains(@class,'ui-fancytree')]/li//*[@class='fancytree-title']")
	protected WebElement rootRequirement;
        
	public RequirementViewPage(WebDriver driver) {
		super(driver);
	}
	
	public RequirementEditPage editRequirement()
	{
        clickOnInvisibleElement(editBtn);
        waitForDialog();
        try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {
		}
		return new RequirementEditPage(driver);
	}
	
	public RequirementViewPage editContent(String clearId, String newContent)
	{
		WebElement editorBody = driver.findElement(By.xpath("//div[starts-with(@id,'WikiPageContent"+clearId+"') and contains(@class,'wysiwyg')]"));
		editorBody.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.attributeContains(editorBody, "class", "cke_editable_inline"));
		editorBody.clear();
		editorBody.sendKeys(newContent);
		sleep(Configuration.getPersistTimeout());
		return new RequirementViewPage(driver);
	}
        
	public RequirementViewPage addContent(String clearId, String content)
	{
		WebElement editableArea = driver.findElement(By.xpath("//div[starts-with(@id,'WikiPageContent"+clearId+"') and contains(@class,'wysiwyg')]"));
		mouseMove(editableArea);
		editableArea.click();
    	try {
			Thread.sleep(500);
		} catch (InterruptedException e) {
		}
		editableArea.sendKeys(content);
		sleep(Configuration.getPersistTimeout());
		return new RequirementViewPage(driver);
	}
	
	public RequirementViewPage changeHtmlInContent(String clearId, String html)
	{
		WebElement editableArea = driver.findElement(By.xpath("//div[starts-with(@id,'WikiPageContent"+clearId+"') and contains(@class,'wysiwyg')]"));
		((JavascriptExecutor) driver).executeScript("arguments[0].innerHTML = '"+html+"';", editableArea);
		mouseMove(editableArea);
		editableArea.click();
		try {
			Thread.sleep(500);
		} catch (InterruptedException e) {
		}
		editableArea.sendKeys(".");
		sleep(Configuration.getPersistTimeout());
		return new RequirementViewPage(driver);
	}
        
	public RequirementNewPage addChildRequirement(){
		clickOnInvisibleElement(addPartitionBtn);
		sleep(Configuration.getPersistTimeout());
		return new RequirementNewPage(driver);
	}
	
	public String readParentPage(Requirement r){
		return driver.findElement(By.xpath("//input[@name='ParentPage']")).getAttribute("value");
	}

	
	public String readContent ( String requirementId ) {
		return driver.findElement(By.xpath("//div[starts-with(@id,'WikiPageContent" + requirementId+"') and contains(@class,'wysiwyg')]")).getText();
	}
	
	public void waitForContent ( String requirementId, String content ) {
		driver.navigate().refresh();
		(new WebDriverWait(driver, waiting)).until(
				ExpectedConditions.presenceOfElementLocated(
						By.xpath("//div[starts-with(@id,'WikiPageContent" + requirementId+"') and contains(.,'"+content+"') and contains(@class,'wysiwyg')]")));
	}

	public String readType()
	{
		RequirementEditPage ep = editRequirement();
		String type = ep.getType();
		ep.close();
		return type;
	}
	
	public String readRequirementStatus() {
		return driver.findElement(By.className("label-state")).getText();
	}
	
	protected void waitForState( String stateName ) {
		(new WebDriverWait(driver, waiting)).until(
				ExpectedConditions.presenceOfElementLocated(
						By.xpath("//span[contains(@class,'label') and contains(.,'"+stateName+"')]")));
	}
	
	public RequirementViewPage completeRequirement(){
		clickOnInvisibleElement(asterixBtn);
		clickOnInvisibleElement(completeBtn);
		driver.navigate().refresh();
		waitForState("Готово");
		return new RequirementViewPage(driver);
		}
	
        public RequirementReturnToWorkPage returnToWork(){
		clickOnInvisibleElement(asterixBtn);
		clickOnInvisibleElement(returnToWorkBtn);
		return new RequirementReturnToWorkPage(driver);
		}
	
	public RequirementViewPage signRequirement(){
		clickOnInvisibleElement(asterixBtn);
		clickOnInvisibleElement(signBtn);
		driver.navigate().refresh();
		waitForState("Реализовано");
		return new RequirementViewPage(driver);
		}
	
	public RequirementViewPage getBackRequirement(String comment){
		clickOnInvisibleElement(getBackBtn);
		waitForDialog();
		(new CKEditor(driver)).typeText(comment);
		submitDialog(driver.findElement(By.id("WikiPageSubmitBtn")));
		driver.navigate().refresh();
		waitForState("В работе");
		return new RequirementViewPage(driver);
		}
	
	/**Use this method to get tag text decoration information (only controlled by tags, no css).
	 * The method searches the text in KB content and reads all the style tags for this text: bold, em, etc.*/
	public List<String> getStyleTagsForText(String requirementId, String text){
		List<String> tags = new ArrayList<String>();
		
		WebElement p = driver.findElement(By.xpath("//div[starts-with(@id,'WikiPageContent" + requirementId + "') and contains(@class,'wysiwyg')]//*[contains(text(),'"+text+"')]"));
		String tag = p.getTagName();
		while (!tag.equals("p")) {
			 tags.add(tag);
	          p=p.findElement(By.xpath("./.."));
	          tag = p.getTagName();
		}
		return tags;
	}
	
	public Boolean isImageContained(String requirementId){
		return !driver.findElements(By.xpath("//div[starts-with(@id,'WikiPageContent" + requirementId + "') and contains(@class,'wysiwyg')]//img")).isEmpty();
	}
	
	public Boolean isImageSrcCorrect(String requirementId){
		List<WebElement> img = driver.findElements(By.xpath("//div[starts-with(@id,'WikiPageContent" + requirementId + "') and contains(@class,'wysiwyg')]//img"));
	    if (img.size()==0) return false;
	    else
	    for (WebElement el:img){
	    	if (el.getAttribute("src").contains("cms_TempFile")) return false;
	    }
	    	return true;
}
	
	public long getAttachmentSize(String requirementId, String attachmentName) throws IOException{
		WebElement attach = driver.findElement(By.xpath("//div[starts-with(@id,'WikiPageContent" + requirementId + "') and contains(@class,'wysiwyg')]//img"));
			
		try {
			URL path = new URL(attach.getAttribute("src"));
			String authString = Configuration.getUsername() + ":" + Configuration.getPassword();
			byte[] authEncBytes = Base64.encodeBase64(authString.getBytes());
			String authStringEnc = new String(authEncBytes);
			
			URLConnection urlConnection = path.openConnection();
			urlConnection.setRequestProperty("Authorization", "Basic " + authStringEnc);
			urlConnection.connect();
			ReadableByteChannel rbc = Channels.newChannel(urlConnection.getInputStream());
			File downloaded = new File (Configuration.getDownloadPath() + "/temp."+DataProviders.getUniqueString());
			if (downloaded.exists()) downloaded.delete();
			FileOutputStream fos = new FileOutputStream(downloaded);
			try {
			fos.getChannel().transferFrom(rbc, 0, Long.MAX_VALUE);
			}
			finally {
			fos.close(); 
			}
			return downloaded.length();
		}
		catch (MalformedURLException e) {
			String base64parts[] = attach.getAttribute("src").split(",");
			byte[] imageData = DatatypeConverter.parseBase64Binary(base64parts[base64parts.length-1]);
			return imageData.length;
		}
	}
	
	public String readUserAttribute(String attributeName)
	{
		RequirementEditPage ep = editRequirement();
		String author = ep.getUserAttribute(attributeName);
		ep.close();
		return author;
	}

	public String readNestedContent() {
		return driver.findElement(By.xpath("//td[@id='content']/div[@class='reset wysiwyg']")).getText();
	}
	
	public RequirementViewPage showBaseline(String version){
		clickOnInvisibleElement(versionBtn);
		clickOnInvisibleElement(versionBtn.findElement(By.xpath("./following-sibling::ul//a[contains(.,'"+version+"')]/parent::*//a[contains(.,'Текущий')]")));
		return new RequirementViewPage(driver);
	}

	public RequirementViewPage compareWithVersion(String version){
		clickOnInvisibleElement(compareWithBtn);
		WebElement compareRow = compareWithBtn.findElement(By.xpath("./following-sibling::ul//a[contains(.,'"+version+"')]"));
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(compareRow));
		compareRow.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//a[@uid='compareto' and contains(.,'"+version+"')]")));
		return new RequirementViewPage(driver);
	}
	
	public RequirementAddToBaselinePage addToBaseline(){
		clickOnInvisibleElement(actionsBtn);
		clickOnInvisibleElement(addToBaselineBtn);
		waitForDialog();
		return new RequirementAddToBaselinePage(driver);
	}

	public RequirementAddToBaselinePage makeBranch(){
		clickOnInvisibleElement(actionsBtn);
		clickOnInvisibleElement(makeBranchBtn);
		waitForDialog();
		return new RequirementAddToBaselinePage(driver);
	}

	public String readCurrentBaseline(){
		if ( !versionBtn.isDisplayed() ) return "";
		return versionBtn.getText().replace("Бейзлайн:", "").trim();
	}
			
	public RequirementNewPage addPartition() {
     clickOnInvisibleElement(addPartitionBtn);
     (new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.id("WikiPageCaption")));
		return new RequirementNewPage(driver);
	}

    public RequirementEditPage editRequirement(String id) {
        try
        {
        String ids = id.split("-")[1];
        driver.findElement(By.xpath(".//tr[@object-id="+ids+"]//div[contains(@class,'operation')]//a[contains(@class,'actions-button')]")).click();
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(editBtn));
        editBtn.click();
        Thread.sleep(2000);
        return new RequirementEditPage(driver);
        }
        catch(InterruptedException e)
        {
            return null;
        }
    }
    
    public RequirementChangesHistoryPage seeChanges(String id) 
    {
    	try
        {
	        String ids = id.split("-")[1];
	        Thread.sleep(3000);
	        driver.findElement(By.xpath(".//tr[@object-id="+ids+"]//div[contains(@class,'operation')]//a[contains(@class,'actions-button')]")).click();
	        Thread.sleep(2000);
	        clickOnInvisibleElement(driver.findElement(By.xpath("//tr[@object-id='"+ids+"']//a[@id='history']")));
	        Thread.sleep(2000);
	        return new RequirementChangesHistoryPage(driver);
        }
        catch(InterruptedException e)
        {
            return null;
        }
    }

    public TestScenariosPage menuTestSuit(String id)
	{
        try {
			String ids = id.split("-")[1];
			Thread.sleep(3000);
			driver.findElement(By.xpath(".//tr[@object-id="+ids+"]//div[contains(@class,'operation')]//a[contains(@class,'actions-button')]")).click();
			clickOnInvisibleElement(driver.findElement(By.xpath("//tr[@object-id='"+ids+"']//a[@id='trace-testing']")));
			return new TestScenariosPage(driver);
        }
        catch(InterruptedException e) {
            return null;
        }
    }

    public void openRootRequirement() {
        rootRequirement.click();
    }

    public void editRequirementName(String newName) {
        try {
			Thread.sleep(800);
		} catch (InterruptedException e) {
		}
		driver.findElement(By.xpath(".//*[starts-with(@id,'WikiPageCaption')]")).clear();
		driver.findElement(By.xpath(".//*[starts-with(@id,'WikiPageCaption')]")).sendKeys(newName);
		sleep(Configuration.getPersistTimeout());
    }

    public TestScenarioNewPage createNewTestSuit() {
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(asterixBtn));
        clickOnInvisibleElement(testScenarioItem);
        return new TestScenarioNewPage(driver);
    }

    public TestSpecificationsPage clickAttention() {
       (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(attentionBtn));
       (new Actions(driver)).click(attentionBtn).build().perform();
       (new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//th[@uid='tracesourcerequirement']")));
       return new TestSpecificationsPage(driver);
    }

    public TestSpecificationsPage clickAttentionTesting() {
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(attentionBtn));
        (new Actions(driver)).click(attentionBtn).build().perform();
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//th[@uid='requirement']")));
        return new TestSpecificationsPage(driver);
     }

    public RequirementNewPage createCoverRequirement() {
        clickOnInvisibleElement(coverRequirementItem);
        return new RequirementNewPage(driver);
    }

    public void addComment(String comment) {
    	try {
        	(new WebDriverWait(driver,5)).until(ExpectedConditions.visibilityOf(addCommentBtn));
		} catch (TimeoutException e) {
	        clickOnInvisibleElement(commentsLink);
		}
        try {
        	(new WebDriverWait(driver,5)).until(ExpectedConditions.visibilityOf(addCommentBtn));
        	clickOnInvisibleElement(addCommentBtn);
        }
        catch(TimeoutException e) {
        }
        (new CKEditor(driver)).typeText(comment);
        submitDialog(sendBtn);
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//div[contains(@class,'comment-text') and contains(.,'"+comment+"')]")));
    }

    public void setAtributeStatusHistory() {
		showColumn("Workflow");
        (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOfElementLocated(By.xpath("//th[@uid='attributes']")));
    }

    public void agreeWithOSA() {
        clickOnInvisibleElement(asterixBtn);
        clickOnInvisibleElement(agreeWithOsaItem);
    }

    public void exportToPDF() {
        clickOnInvisibleElement(actionsBtn);
        clickOnInvisibleElement(exportItem);
        try {
			Thread.sleep(4000);
		} catch (InterruptedException e) {
		}
    }

    public KanbanTaskNewPage createRework() {
        clickOnInvisibleElement(reworkItem);
        return new KanbanTaskNewPage(driver);
    }

    public RequestRejectPage clickToRework() {
        WebElement rework = driver.findElement(By.xpath("//li[contains(.,'Доработки: ')]//a"));
        clickOnInvisibleElement(rework);
        return new RequestRejectPage(driver);
    }
    
    public void waitForTraceEntity( String className )
    {
    	(new WebDriverWait(driver,waiting)).until(
    			ExpectedConditions.presenceOfElementLocated(By.xpath("//span[contains(@class,'tracing-ref') and @entity='"+className+"']"))
    			);
    	try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {
		}
    }

    public void selectBaseline(String name) {
		WebElement baseline = driver.findElement(By.xpath("//a[@uid='baseline']/following-sibling::ul//a[contains(.,'"+name+"')]"));
		clickOnInvisibleElement(baseline);
	}

	public void selectInitialBaseline() {
		selectBaseline("Начальный");
	}
}
