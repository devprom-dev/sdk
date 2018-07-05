package ru.devprom.pages.project;

import org.openqa.selenium.Keys;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.items.Release;
import ru.devprom.pages.CKEditor;

public class ReleaseNewPage extends SDLCPojectPageBase {

	@FindBy(id="pm_VersionCaption")
	protected WebElement numberEdit;
	
	@FindBy(name="StartDate")
	protected WebElement beginDateEdit;
	
	@FindBy(name="FinishDate")
	protected WebElement endDateEdit;
	
	@FindBy(id="pm_VersionInitialVelocity")
	protected WebElement velocityEdit;
	
	@FindBy(id="pm_VersionSubmitBtn")
	protected WebElement submitBtn;
	
	@FindBy(id="pm_VersionCancelBtn")
	protected WebElement cancelBtn;
	
	public ReleaseNewPage(WebDriver driver) {
		super(driver);
	}

	public ReleaseNewPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public ReleasesIterationsPage createRelease(Release release){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(numberEdit));
	       if (release.getNumber().equals("")) {
	    	   release.setNumber(numberEdit.getText());
	       }
	       else {
	    	   numberEdit.clear();
	    	   numberEdit.sendKeys(release.getNumber());
	       }
		   
	       if( !release.getDescription().equals("") ) {
		       (new CKEditor(driver)).changeText(release.getDescription());
	       }
	       
	       if (release.getBeginDate().equals("")) {
	    	   release.setBeginDate(beginDateEdit.getAttribute("value"));
		       }
		       else {
		    	   beginDateEdit.clear();
		    	   beginDateEdit.sendKeys(release.getBeginDate());
		    	   beginDateEdit.sendKeys(Keys.TAB);
		       }
	       
	       if (!release.getEndDate().equals("")) {
	    	       endDateEdit.clear();
	               endDateEdit.sendKeys(release.getEndDate());
	               endDateEdit.sendKeys(Keys.TAB);
	       }
	       try {
		       velocityEdit.clear();
	           velocityEdit.sendKeys("80");
	       } catch (NoSuchElementException e) {
	       }
     
           submitDialog(submitBtn);
		
		return new ReleasesIterationsPage(driver);
	}
        
	public void addNumber(String name)
	{
        clickTab("main");
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(numberEdit));
    	   numberEdit.clear();
    	   numberEdit.sendKeys(name);
    }
    
    public void addDescription(String description) {
        clickTab("main");
       (new CKEditor(driver)).changeText(description);
    }
    
    public void addVelocity(String velocity){
        clickTab("main");
    	velocityEdit.clear();
        velocityEdit.sendKeys(velocity);
	}
    
    public void save(){
        submitDialog(submitBtn);
    }
    
    public void cancel() {
    	cancelDialog(cancelBtn);
    }
    
    public void openBurnDown(){
        clickTab("releaseburndownsection");
    }
        
    public String getStartDate() {
    	return beginDateEdit.getAttribute("value");
    }
	    
    public String getFinishDate() {
    	return endDateEdit.getAttribute("value");
    }
}
