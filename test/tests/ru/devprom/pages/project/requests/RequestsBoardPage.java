package ru.devprom.pages.project.requests;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.openqa.selenium.By;
import org.openqa.selenium.ElementNotVisibleException;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.Keys;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.NotFoundException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.helpers.DateHelper;
import ru.devprom.helpers.WebDriverPointerRobot;
import ru.devprom.items.Project;
import ru.devprom.items.RTask;
import ru.devprom.items.Request;
import ru.devprom.items.Spent;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.IterationNewPage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.tasks.TaskNewPage;

public class RequestsBoardPage extends SDLCPojectPageBase {

	@FindBy(id = "filter-settings")
	protected WebElement asterixBtn;
	
	@FindBy(id = "append-issue")
	protected WebElement addRequestBtn;

	@FindBy(id = "append-issue-bug")
	protected WebElement addBugBtn;

	@FindBy(id = "append-issue-enhancement")
	protected WebElement addEnhancementBtn;
	
	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(text(),'Добавить')]")
	protected WebElement addBtn;

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(text(),'Действия')]")
	protected WebElement actionsBtn;
	
	@FindBy(xpath = "//a[text()='Выбрать все']")
	protected WebElement selectAllBtn;
	
	@FindBy(xpath = "//a[text()='Вставить в блог']")
	protected WebElement pasteToBlogBtn;
	
	@FindBy(xpath = "//li[@uid='append-issue']/a")
	protected WebElement newCRBtn;

	@FindBy(xpath = "//li[@uid='append-issue-bug']/a")
	protected WebElement newBugBtn;

	@FindBy(xpath = "//div[@id='bulk-modify-actions']/a")
	protected WebElement massChangeBtn;
	
	@FindBy(xpath = "//div[contains(@class,'btn-group')]//a[contains(text(),'Включить в релиз')]")
	protected WebElement massIncludeInReleaseBtn;
	
	@FindBy(xpath = "//div[contains(@class,'btn-group')]//a[contains(text(),'Удалить')]")
	protected WebElement massDeleteBtn;
	
	public RequestsBoardPage(WebDriver driver) {
		super(driver);
	}

	public RequestsBoardPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public RequestsBoardPage addGrouppingByUserAttribute (String attrCodeName){
		asterixBtn.click();
		((JavascriptExecutor) driver).executeScript("filterLocation.setup( 'group="+attrCodeName+"', 0 );");
		asterixBtn.click();
		return new RequestsBoardPage(driver);
	}
	
	public void addFilter(String filtername) {
		String code = "filterLocation.setup( '" + filtername + "=all', 1 );";
		((JavascriptExecutor) driver).executeScript(code);
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//a[@data-toggle='dropdown' and @uid='"+filtername+"']")));
	}
	
	public RequestsPage showAll() {
		driver.navigate().to(driver.getCurrentUrl()+"&state=all");
		return new RequestsPage(driver);
	}

	public RequestsBoardPage turnOnFilter(String value, String russianName) throws InterruptedException
	{
		WebElement filterButton = driver.findElement(
				By.xpath("//a[@data-toggle='dropdown' and contains(text(),'" + russianName + "')]"));
		filterButton.click();
		List<WebElement> inputElements = filterButton.findElements(By.xpath("./following-sibling::ul/li[@uid='search']/input"));
		if ( inputElements.size() > 0 ) {
			inputElements.get(0).sendKeys(value);
		}
		Thread.sleep(1000);
		filterButton.findElement(By.xpath("./following-sibling::ul/li/a[text()='" + value + "']")).click();
		Thread.sleep(600);
		filterButton.click();
		
		(new WebDriverWait(driver, waiting))
			.until(ExpectedConditions.presenceOfElementLocated(
					By.xpath("//a[@data-toggle='dropdown' and contains(@class,'btn-info') and contains(text(),'" + russianName + "')]")));
		return new RequestsBoardPage(driver);
	}

	public RequestsBoardPage turnOffFilter(String engName){
		String code = "filterLocation.turnOn('"+engName+"', 'all', 0)";
		asterixBtn.click();
		((JavascriptExecutor) driver).executeScript(code);
		asterixBtn.click();
		try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		return new RequestsBoardPage(driver);
	}

	
	public List<String> getListOfRequestsInGroup(String groupName){
		List<String> list = new ArrayList<String>();
		List<WebElement> elements = driver.findElements(By.xpath("//tr[@class='info']/td[contains(.,'"+groupName+"')]/../following-sibling::tr[1]//a[contains(@class,'with-tooltip')]"));
		for (WebElement el:elements){
			String id = el.getText();
			list.add(id.substring(1,id.length()-1));
		}
		return list;
	}
	
	public boolean isRequestPresent(String id) {
		return !driver.findElements(By.xpath("//a[contains(@class,'with-tooltip') and text()='["+id+"]']")).isEmpty();
	}
	
	
	public RequestNewPage clickNewCR() {
        addRequestBtn.click();		
        waitForDialog();
		return new RequestNewPage(driver);
	}

	public RequestNewPage clickNewBug() {
        addBugBtn.click();		
        waitForDialog();
		return new RequestNewPage(driver);
	}
	

	public RequestViewPage clickToRequest(String id) {
		driver.findElement(
				By.xpath("//table[contains(@id,'requestboard')]//a[contains(text(),'["
						+ id + "]')]")).click();
		return new RequestViewPage(driver);
	}

    public void addComment(String requestNumericId, String comment) throws InterruptedException{
    	
    	WebElement element = driver.findElement(By.xpath("//div[contains(@id,'context-menu-"+requestNumericId+"')]//a[text()='Добавить комментарий']"));
      //  clickOnInvisibleElement(element);
       
        WebElement onElement = driver.findElement(By.xpath("//table[contains(@id,'requestboard')]//a[contains(text(),'[I-"
						+ requestNumericId + "]')]/../.."));
        Actions contextClick = new Actions(driver);
        mouseMove(onElement);
        contextClick.contextClick(onElement).build().perform();
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(element));
        clickOnInvisibleElement(element);
        Thread.sleep(3000);
    	CKEditor we = new CKEditor(driver);
        we.typeText(comment);
        driver.findElement(By.id("CommentSubmitBtn")).click();
    	(new WebDriverWait(driver, waiting)).until(ExpectedConditions.invisibilityOfElementLocated(By.tagName("iframe")));
    }
	
	
    public RequestsBoardPage addTask(String requestNumericId, RTask task){
          WebElement onElement = driver.findElement(By.xpath("//table[contains(@id,'requestboard')]//a[contains(text(),'[I-"
  						+ requestNumericId + "]')]/../.."));
          mouseMove(onElement);
          new Actions(driver).contextClick(onElement).build().perform();
          clickOnInvisibleElement(driver.findElement(By.xpath("//div[contains(@id,'context-menu-"+requestNumericId+"')]//a[@id='new-task']")));
          (new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.id("pm_TaskCaption")));
          new TaskNewPage(driver).createEmbeddedTask(task);
          return new RequestsBoardPage(driver);
    }
    
    public TaskNewPage addTask(String requestNumericId){
        WebElement onElement = driver.findElement(By.xpath("//table[contains(@id,'requestboard')]//a[contains(text(),'[I-"
						+ requestNumericId + "]')]/../.."));
        mouseMove(onElement);
        new Actions(driver).contextClick(onElement).build().perform();
    	clickOnInvisibleElement(driver.findElement(By.xpath("//div[contains(@id,'context-menu-"+requestNumericId+"')]//a[@id='new-task']")));
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.id("pm_TaskCaption")));
        return  new TaskNewPage(driver);
  }
    
    
    public RequestEditPage editRequest(String requestNumericId){
    	
  	  WebElement element = driver.findElement(By.xpath("//div[contains(@id,'context-menu-"+requestNumericId+"')]//a[text()='Изменить']"));
       
        WebElement onElement = driver.findElement(By.xpath("//table[contains(@id,'requestboard')]//a[contains(text(),'[I-"
						+ requestNumericId + "]')]/../.."));
        mouseMove(onElement);
        new Actions(driver).contextClick(onElement).build().perform();
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(element));
        element.click();
        waitForDialog();
        return new RequestEditPage(driver);
  }
  
    public List<String> readTasksLinks(String numericId){
    	List<String> result = new ArrayList<String>();
    	List<WebElement> list = driver.findElements(By.xpath("//div[@object='"+numericId+"']//a[contains(@info,'Task')]/span"));
    	for (WebElement el:list){
    		result.add(el.getText().trim());
    	}
    	list = driver.findElements(By.xpath("//div[@object='"+numericId+"']//a[contains(@info,'Task')]/div"));
    	for (WebElement el:list){
    		result.add("P");
    	}
    	return result;
    }
    
    public List<String> readSpentLinks(String numericId){
    	List<String> result = new ArrayList<String>();
    	List<WebElement> list = driver.findElements(
				By.xpath("//div[@object='"+numericId+"']//div[contains(@class,'board-item-fact')]/a"));
    	for (WebElement el:list){
    		result.add(el.getText().trim());
    	}
    	return result;
    }
    

    public RequestsBoardPage writeOffSpentTime(String requestNumericId, Spent spent) throws InterruptedException{
    	  WebElement element = driver.findElement(By.xpath("//div[contains(@id,'context-menu-"+requestNumericId+"')]//a[text()='Списать время']"));
         
          WebElement onElement = driver.findElement(By.xpath("//table[contains(@id,'requestboard')]//a[contains(text(),'[I-"
  						+ requestNumericId + "]')]/../.."));
          mouseMove(onElement);
          new Actions(driver).contextClick(onElement).build().perform();
          (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(element));
          clickOnInvisibleElement(element);
          (new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.id("pm_ActivityCapacity")));
          driver.findElement(By.id("pm_ActivityCapacity")).sendKeys(String.valueOf(spent.hours));
          WebElement reportDate = driver.findElement(By.name("ReportDate"));
          reportDate.clear();
          reportDate.sendKeys(spent.date);
          reportDate.sendKeys(Keys.TAB);
          driver.findElement(By.id("pm_ActivityDescription")).sendKeys(spent.description);
          submitDialog(driver.findElement(By.id("pm_ActivitySubmitBtn")));
          return new RequestsBoardPage(driver);
    }
    
    
    public void selectRequest(String id)
    {
    	clickOnInvisibleElementWithCtrl(
    			driver.findElement(
    					By.xpath("//a[text()='["+id+"]']/ancestor::div[@class='board_item_body']")
    			)
    	);
    }
    
    public RequestsBoardPage selectAll(){
    	actionsBtn.click();
    	  (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(selectAllBtn));
    	  selectAllBtn.click();
    	  actionsBtn.click();
    	return new RequestsBoardPage(driver);
    }
    
    public RequestsBoardPage pasteSelectedToBlog(String postName){
    	  clickOnInvisibleElement(pasteToBlogBtn);
    	  (new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.id("BlogPostCaption")));
    	  driver.findElement(By.id("BlogPostCaption")).sendKeys(postName);
    	  driver.findElement(By.id("BlogPostSubmitBtn")).click();
    	  
    	  (new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//table[contains(@id,'requestboard')]")));
    	  return new RequestsBoardPage(driver);
    }
    
    public RequestsBoardPage moveToCompleted(String requestNumericId, String version, Spent spent, String verticalSectionName){
   	 WebElement element = driver.findElement(By.xpath("//div[@object='"+requestNumericId+"']"));
   	WebElement onElement = null;    
   	 if (verticalSectionName.isEmpty()) 
   		onElement = driver.findElement(By.xpath("//table[contains(@id,'requestboard')]//td[contains(@class,'board-column')][4]"));
        else  {
        	List<WebElement> rows = driver.findElements(By.xpath("//table[contains(@id,'requestboard')]//tr[@class='info']"));
        	int row = 0;
        	for (int i=0; i<rows.size();i++){
        		if ( rows.get(i).getText().contains(verticalSectionName) ) {
        			row = i+1;
        			break;
        		}
        	}
            String srow = String.valueOf(row);
            onElement = driver.findElement(By.xpath("//table[contains(@id,'requestboard')]//tr[@class='row-cards']["+srow+"]//td[contains(@class,'board-column')][4]"));
        }
   	 
   	 	mouseMove(element);
        new Actions(driver).dragAndDrop(element, onElement).build().perform();
        mouseMove(onElement);
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.id("ClosedInVersionText")));
        
        if (!version.isEmpty()) {
        	driver.findElement(By.id("ClosedInVersionText")).clear();
       	 driver.findElement(By.id("ClosedInVersionText")).sendKeys(version);
       	 autocompleteSelect(version);
        }
        if (spent!=null) {
          	 driver.findElement(By.xpath("//a[contains(@class,'embedded-add-button') and preceding-sibling::input[@value='activityrequest']]")).click();
          	 WebElement reportDate = driver.findElement(
 					By.xpath("//input[@value='activityrequest']/following-sibling::div[contains(@id,'fieldRowReportDate')]//input[contains(@id,'ReportDate')]"));
          	if (!spent.date.equals(DateHelper.getCurrentDate())) {
          		reportDate.clear();
          		reportDate.sendKeys(spent.date);
            }
          	reportDate.sendKeys(Keys.TAB);
    		driver.findElement(
    				By.xpath("//input[@value='activityrequest']/following-sibling::div//input[contains(@id,'Capacity')]"))
    				.sendKeys(String.valueOf(spent.hours));
    		driver.findElement(
    				By.xpath("//input[@value='activityrequest']/following-sibling::div[contains(@id,'fieldRowDescription')]//textarea[contains(@id,'Description')]"))
    				.sendKeys(spent.description);
    		driver.findElement(
    				By.xpath("//input[@value='activityrequest']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
    				.click();
           }
        
        submitDialog(driver.findElement(By.xpath("//span[text()='Сохранить']")));
   	 return new RequestsBoardPage(driver);
   }
    
    
    
    
    public RequestsBoardPage moveToCompletedUsingMenu(String requestNumericId, String version, String comment, Spent spent){
           WebElement element = driver.findElement(By.xpath("//div[contains(@id,'context-menu-"+requestNumericId+"')]//a[text()='Выполнить']"));
           WebElement onElement = driver.findElement(By.xpath("//table[contains(@id,'requestboard')]//a[contains(text(),'[I-"
   						+ requestNumericId + "]')]/../.."));
           mouseMove(onElement);
           new Actions(driver).contextClick(onElement).build().perform();
           (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(element));
           clickOnInvisibleElement(element);
           (new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.id("ClosedInVersionText")));
           
           if (!version.isEmpty()) {
           	driver.findElement(By.id("ClosedInVersionText")).clear();
          	 driver.findElement(By.id("ClosedInVersionText")).sendKeys(version);
          	 autocompleteSelect(version);
           }
           
           if (comment!=null) {
        	   (new CKEditor(driver)).typeText(comment);
           }
           if (spent!=null) {
             	 driver.findElement(By.xpath("//a[contains(@class,'embedded-add-button') and preceding-sibling::input[@value='activityrequest']]")).click();
       			WebElement reportDate = driver.findElement(
       					By.xpath("//input[@value='activityrequest']/following-sibling::div[contains(@id,'fieldRowReportDate')]//input[contains(@id,'ReportDate')]"));
       			reportDate.clear();
       			reportDate.sendKeys(spent.date);
       			reportDate.sendKeys(Keys.TAB);
       		driver.findElement(
       				By.xpath("//input[@value='activityrequest']/following-sibling::div//input[contains(@id,'Capacity')]"))
       				.sendKeys(String.valueOf(spent.hours));
       		driver.findElement(
       				By.xpath("//input[@value='activityrequest']/following-sibling::div[contains(@id,'fieldRowDescription')]//textarea[contains(@id,'Description')]"))
       				.sendKeys(spent.description);
       		driver.findElement(
       				By.xpath("//input[@value='activityrequest']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
       				.click();
              }
           
           submitDialog(driver.findElement(By.xpath("//span[text()='Сохранить']")));
           try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
		}
      	 return new RequestsBoardPage(driver);
      }
       
    
    
    public RequestPlanningPage moveToPlanned(String requestNumericId){
    	WebElement element = driver.findElement(By.xpath("//div[@object='"+requestNumericId+"']"));
         
         WebElement onElement = driver.findElement(By.xpath("//table[contains(@id,'requestboard')]//td[contains(@class,'board-column')][3]"));
         mouseMove(element);
         new Actions(driver).dragAndDrop(element, onElement).build().perform();
         mouseMove(onElement);
         waitForDialog();
    	 return new RequestPlanningPage(driver);
    }
    
    public RequestPlanningPage moveToPlannedUsingMenu(String requestNumericId){
         WebElement element = driver.findElement(By.xpath("//div[contains(@id,'context-menu-"+requestNumericId+"')]//a[text()='Запланировать']"));
         
         WebElement onElement = driver.findElement(By.xpath("//table[contains(@id,'requestboard')]//a[contains(text(),'[I-"
 						+ requestNumericId + "]')]/../.."));
         mouseMove(onElement);
         new Actions(driver).contextClick(onElement).build().perform();
         (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(element));
         clickOnInvisibleElement(element);
         waitForDialog();
         
    	 return new RequestPlanningPage(driver);
    }
    
    public boolean isMenuItemAccessible(String requestNumericId, String menuItem) {
    	return !driver.findElements(By.xpath("//div[contains(@id,'context-menu-"+requestNumericId+"')]//a[text()='"+menuItem+"']")).isEmpty();
    }
    
    
	/**
     * @param attrs - russian attribute name 
     * @return
     */
    public RequestsBoardPage showCommonAttributes(String... attrs){
    	asterixBtn.click();
    	WebElement attributesBtn = driver.findElement(By.xpath("//a[@href='#' and text()='Атрибуты']"));
    	  (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(attributesBtn));
    	  attributesBtn.click();
    	for (String a:attrs){
    	WebElement item = driver.findElement(By.xpath("//a[@href='#' and text()='Атрибуты']/following-sibling::ul//a[text()='"+a+"']"));
    	if (!item.getAttribute("class").contains("checked")) {
    	  if (!item.isDisplayed())   attributesBtn.click();
    		item.click();
    	  }
    	}
    	driver.findElement(By.id("tablePlaceholder")).click();
    	try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
   	 return new RequestsBoardPage(driver);
    	
    }
    
    /**
     * 
     * @param attrsEng - javascript attribute name
     * @return
     */
    public RequestsBoardPage showSpecificAttributes(String... attrsEng){
    	asterixBtn.click();
    
    	for (String a:attrsEng){
            String code = "filterLocation.showColumn('"+a+"', 0);";
            ((JavascriptExecutor) driver).executeScript(code);
    	}
    	
    	asterixBtn.click();
      	 return new RequestsBoardPage(driver);
    }
    
    
    public Request readCompletedRequest(String id){
    	WebElement requestEl = driver.findElement(By.xpath("//img/following-sibling::a/strike[contains(text(),'"+id+"')]/../.."));
    	String name = requestEl.findElement(By.xpath("./following-sibling::div[1]")).getText();
    	String description =  requestEl.findElement(By.xpath("./following-sibling::div[contains(text(),'Описание:')]")).getText().replace("Описание:", "").trim();
    	String type =  requestEl.findElement(By.xpath("./following-sibling::div[contains(text(),'Тип:')]")).getText().replace("Тип:", "").trim();
    	String priority =  requestEl.findElement(By.xpath("./following-sibling::div[contains(text(),'Приоритет:')]")).getText().replace("Приоритет:", "").trim();
    	String version =  requestEl.findElement(By.xpath("./following-sibling::div[contains(text(),'Обнаружено в версии:')]")).getText().replace("Обнаружено в версии:", "").trim();
    	String versionCompleted =  requestEl.findElement(By.xpath("./following-sibling::div[contains(text(),'Выполнено:')]")).getText().replace("Выполнено:", "").trim();
    	String release =  requestEl.findElement(By.xpath("./following-sibling::div[contains(text(),'Релиз:')]")).getText().replace("Релиз:", "").trim();
    	String tags = requestEl.findElement(By.xpath("./following-sibling::div[contains(text(),'Тэги:')]")).getText().replace("Тэги:", "").trim();
    	String watchers = requestEl.findElement(By.xpath("./following-sibling::div[contains(text(),'Наблюдатели:')]")).getText().replace("Наблюдатели:", "").trim();
    	String estimation = requestEl.findElement(By.xpath("./following-sibling::div//a[contains(@data-target,'#estimation')]")).getText().trim();
    	String pfunction = requestEl.findElement(By.xpath("./following-sibling::div[contains(text(),'Функция:')]/a")).getText();
    	String linkedRequest = requestEl.findElement(By.xpath("./following-sibling::div[contains(text(),'Связи:')]/a")).getText();
    	System.out.println(linkedRequest);
    	pfunction = pfunction.substring(1, pfunction.length()-1);
    	Request r = new Request(id, name, type, "Выполнено", priority);
    	r.setDescription(description);
    	r.setType(type);
    	r.setPriority(priority);
    	r.setPfunction(pfunction);
    	r.setVersion(version);
    	r.setClosedVersion(versionCompleted);
    	r.setRelease(release);
    	r.setEstimation(Double.parseDouble(estimation));
    	 if (!"".equals(tags)) {
    		 String[] tt = tags.split(","); 
    		 for (String t:tt){
    			 r.addTag(t);
    		 }
    	 }
    	 if (!"".equals(watchers)) {
    		 String[] ww = watchers.split(","); 
    		 for (String w:ww){
    			 r.addWatcher(w);
    		 }
    	 }
    	return r;
    }
    
    public String readAttributeByName(String numericId, String attributeName){
    	
    	WebElement element = driver.findElement(By.xpath("//div[@object='"+numericId+"']"));
    	int attempts = 5;
    while (attempts>0) {
    	try {
    	String parts[] = element.findElement(By.xpath(".//div[contains(text(),'"+attributeName+"')]")).getText().split(":");
    	return parts[1].trim();
    	}
    	catch (NoSuchElementException e) {
    		attempts--;
    		try {
				Thread.sleep(1000);
			} catch (InterruptedException e1) {
			}
    	}
    }
    	return "Ошибка при попытке считать атрибут";
    	
    }
    
public String readAllCardAttributesAsString(String numericId){
    	return driver.findElement(By.xpath("//div[@object='"+numericId+"']//div[@class='board_item_attributes']")).getText();
    }
    
    
    public boolean findTextInRequestCard(String numericId, String text){
    	return !driver.findElements(By.xpath("//div[@object='"+numericId+"']//div[contains(text(),'"+text+"')]")).isEmpty();
    }
    
    /**
     * Возвращает значение в зеленом квадратике справа в нижнем углу карточки
     * @param numericId
     * @return
     */
    public double readEstimation(String numericId){
    	WebElement element = driver.findElement(By.xpath("//div[@object='"+numericId+"']"));
    	String estimation = element.findElement(By.xpath(".//a[contains(@data-target,'#estimation')]")).getText().trim();
    	return Double.parseDouble(estimation);
    	
    }
    
    
    public SDLCPojectPageBase clickToEmbeddedLink(String id, boolean isRequestClosed, String parameter){
    	WebElement requestEl = null;
    	if (isRequestClosed) requestEl = driver.findElement(By.xpath("//img/following-sibling::a/strike[contains(text(),'"+id+"')]/../.."));
    	else requestEl = driver.findElement(By.xpath("//img/following-sibling::a[text()='["+id+"]']/.."));
    	requestEl.findElement(By.xpath("./following-sibling::div[contains(text(),'"+parameter+"')]/a")).click();
    	return new SDLCPojectPageBase(driver);
    }
    
    public RequestsBoardPage turnOfGrouping(){
    	asterixBtn.click();
    	String code = "filterLocation.setup( 'group=none', 0 );";
		((JavascriptExecutor) driver).executeScript(code);
		asterixBtn.click();
		try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
    	return new RequestsBoardPage(driver);
    }
    
    /**
     * 
     * @param parameter - "внутреннее" имя параметра, латиница
     * @return
     */
    public RequestsBoardPage setupGrouping(String parameter){
    	asterixBtn.click();
    	String code = "javascript: filterLocation.setup( 'group="+parameter+"', 0 ); ";
		((JavascriptExecutor) driver).executeScript(code);
		asterixBtn.click();
		try {
			Thread.sleep(5000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
    	return new RequestsBoardPage(driver);
    }
    
    
    public List<String> readDatesTitles(String requestNumericId) throws InterruptedException{
    	List<String> result = new ArrayList<String>();
    	Thread.sleep(1000);
    	 WebElement element = driver.findElement(By.xpath("//div[@object='"+requestNumericId+"']"));
    	List<WebElement> dates = element.findElements(By.xpath(".//img[contains(@src,'date.png')]/..")); 
    	for (WebElement d:dates){
    		result.add(d.getAttribute("title"));
    	}
    	return result;
    	
    }
    
    public RequestsBoardPage moveToAnotherSection(String requestNumericId, int row, int column){
   	 WebElement element = driver.findElement(By.xpath("//div[@object='"+requestNumericId+"']"));
        String srow = String.valueOf(row);
        String scolumn = String.valueOf(column);
        WebElement onElement = driver.findElement(By.xpath("//table[contains(@id,'requestboard')]//tr[@class='row-cards']["+srow+"]//td[contains(@class,'board-column')]["+scolumn+"]"));
        mouseMove(element);
        new Actions(driver).dragAndDrop(element, onElement).build().perform();
        mouseMove(onElement);
      
 	   (new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//table[contains(@id,'requestboard')]//tr[@class='row-cards']["+srow+"]//td[contains(@class,'board-column')]["+scolumn+"]//div[@object='"+requestNumericId+"']")));
 		
        /*try {
			Thread.sleep(5000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}*/
   	 return new RequestsBoardPage(driver);
   }
   
    public RequestsBoardPage moveToAnotherSection(String requestNumericId, String rowName, String columnName){
    	List<WebElement> rows = driver.findElements(By.xpath("//table[contains(@id,'requestboard')]//tr[@class='info']"));
    	int row = 0;
    	for (int i=0; i<rows.size();i++){
    		if ( rows.get(i).getText().contains(rowName) ) {
    			row = i;
    			break;
    		}
    	}
    	List<WebElement> cols = driver.findElements(By.xpath("//table[contains(@id,'requestboard')]/tbody/tr[contains(@class,'board-columns')]/th"));
    	String columnValue = "";
    	int column = 0;
    	for (int i=0; i<cols.size();i++){
    		if( cols.get(i).getText().contains(columnName) ) {
    			columnValue = cols.get(i).getAttribute("value"); 
    			break;
    		}
    	}
    	
      	 WebElement element = driver.findElement(By.xpath("//div[@object='"+requestNumericId+"']//div[@class='board_item_body']"));
		WebElement onElement = rows.get(row).findElement(By.xpath("./following-sibling::tr[@class='row-cards']//*[contains(@class,'list_cell') and contains(@more,'"+columnValue+"')]")); 
       scrollToElement(onElement);
       mouseMove(element);
       new Actions(driver).dragAndDrop(element, onElement).build().perform();
       mouseMove(onElement);
       try {
		Thread.sleep(5000);
		} catch (InterruptedException e) {
		}
      	 return new RequestsBoardPage(driver);
      }
    
    
      public List<String> getAllGroupingSections(){
    	  List<WebElement> rows = driver.findElements(By.xpath("//table[contains(@id,'requestboard')]//tr[@class='info']"));
    	  List<String> result = new ArrayList<String>();
    	   for (WebElement el:rows){
    		   result.add(el.getText());
    	   }
    	  return result;
      }
      
       public boolean isRequestInSection(String requestNumericId,  String rowName, String columnName)
       {
       	List<WebElement> cols = driver.findElements(By.xpath("//table[contains(@id,'requestboard')]/tbody/tr[contains(@class,'board-columns')]/th"));
  	    List<WebElement> rows = driver.findElements(By.xpath("//table[contains(@id,'requestboard')]//tr[@class='info']"));
       	String columnValue = "";
       	for (int i=0; i<cols.size();i++){
       		if ( cols.get(i).getText().contains(columnName) ) {
       			columnValue = cols.get(i).getAttribute("value"); 
      			break;
      		}
       	}
       	int row = -1;
       	try {
          	for (int i=0; i<rows.size();i++){
          		if ( rows.get(i).getText().contains(rowName) ) {
          			row = i;
          			break;
          		}
          	}
          	if ( row < 0 ) throw new NullPointerException();
       	}
       	catch (NullPointerException e ) {
       		throw new NullPointerException ("Не найдена группа с именем " + rowName);
       	}
           
        return (!rows.get(row).findElements(
        		By.xpath("./following-sibling::tr[@class='row-cards']//*[contains(@class,'list_cell') and contains(@more,'"+columnValue+"')]//div[@object='"+requestNumericId+"']"))
        			.isEmpty());
       }
    
       /**
        * Раньше этот метод честно использовал клики в составном контекстном меню, 
        * но с некоторой версии, вторая часть меню перестала удерживаться видимой сколь-нибудь продолжительное время.
        * Теперь используется чтение соответствующего скрипта из ссылки и выполнение его.
        * @param requestNumericId
        * @param priority
        * @return
        */
       public RequestsBoardPage changePriorityInContextMenu(String requestNumericId, String priority){
    	    WebElement onElement = driver.findElement(By.xpath("//table[contains(@id,'requestboard')]//a[contains(text(),'[I-"
   						+ requestNumericId + "]')]/../.."));
    	    mouseMove(onElement);
           new Actions(driver).contextClick(onElement).build().perform();
           
           WebElement prioritySubmenuLink = driver.findElement(By.xpath("//div[contains(@id,'context-menu-"+requestNumericId+"')]//a[text()='Приоритет']"));
           clickOnInvisibleElement(prioritySubmenuLink);
           WebElement priorityLink = driver.findElement(By.xpath("//div[contains(@id,'context-menu-"+requestNumericId+"')]//a[text()='"+priority+"']"));
           String script = priorityLink.getAttribute("href").replace("%20", " ");
           script = script.replace("javascript: ", "");
           System.out.println(script);
           ((JavascriptExecutor) driver).executeScript(script);
           try {
      			Thread.sleep(3000);
      		} catch (InterruptedException e) {
      			e.printStackTrace();
      		}
           return new RequestsBoardPage(driver);
       }
       
       public String tryToMoveDenied(String requestNumericId, String rowName, String columnName)
       {
    	   moveToAnotherSection(requestNumericId, rowName, columnName);
    	   waitForDialog();
    	   String errorMessage = driver.findElement(By.id("modal-form")).getText();
    	   driver.findElement(By.xpath("//div[@id='modal-form']/following-sibling::div//span[text()='Ok']")).click();
    	   return errorMessage;
       }
       
       public RequestsBoardPage deleteSelected()
       {
    	   clickOnInvisibleElement(massDeleteBtn);
    	   waitForDialog();
    	   submitDialog(driver.findElement(By.id("SubmitBtn")));
    	   driver.navigate().refresh();
    	   return new RequestsBoardPage(driver);
       }
       
       public RequestsBoardPage massIncludeInRelease(String releaseName)
       {
    	   clickOnInvisibleElement(massIncludeInReleaseBtn);
    	   waitForDialog();
    	   driver.findElement(By.id("PlannedReleaseText")).sendKeys(releaseName);
    	   autocompleteSelect(releaseName);
    	   submitDialog(driver.findElement(By.id("SubmitBtn")));
    	   return new RequestsBoardPage(driver);
       }
  
       public IterationNewPage versionChange(String sprint) {
           WebElement sprintTitle = driver.findElement(By.xpath("//td[@class='board-group']//span[contains(text(),'"+sprint+"')]"));
           clickOnInvisibleElement(sprintTitle.findElement(By.xpath("./ancestor::td//a[@id='row-modify']")));
           return new IterationNewPage(driver);
       }
}
