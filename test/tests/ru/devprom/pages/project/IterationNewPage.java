package ru.devprom.pages.project;

import org.openqa.selenium.Keys;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Iteration;
import ru.devprom.items.Project;
import ru.devprom.pages.CKEditor;

public class IterationNewPage extends SDLCPojectPageBase {

	@FindBy(id="pm_ReleaseReleaseNumber")
	protected WebElement nameEdit;
	
	@FindBy(name="StartDate")
	protected WebElement beginDateEdit;
	
	@FindBy(name="FinishDate")
	protected WebElement endDateEdit;
	
	@FindBy(id="VersionText")
	protected WebElement releaseInput;
	
	@FindBy(id="pm_ReleaseProjectStage")
	protected WebElement ProjectStageSelect;
	
	@FindBy(id="pm_ReleaseSubmitBtn")
	protected WebElement submitBtn;
	
	@FindBy(id="pm_ReleaseCancelBtn")
	protected WebElement cancelBtn;

	@FindBy(id="pm_ReleaseInitialVelocity")
	protected WebElement velocityEdit;
	
	public IterationNewPage(WebDriver driver) {
		super(driver);
	}

	public IterationNewPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public ReleasesIterationsPage createIteration(Iteration iteration){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(nameEdit));
	       if (iteration.getName().equals("")) {
	       iteration.setName(nameEdit.getAttribute("value"));
	       }
	       else {
	    	   nameEdit.clear();
	    	   nameEdit.sendKeys(iteration.getName());
	       }
		   
	       if ( !iteration.getDescription().equals("") ) {
		       (new CKEditor(driver)).changeText(iteration.getDescription());
	       }
	       
	       if (iteration.getBeginDate().equals("")) {
		       iteration.setBeginDate(beginDateEdit.getText());
	       }
	       else {
	    	   beginDateEdit.clear();
	    	   beginDateEdit.sendKeys(iteration.getBeginDate());
	    	   beginDateEdit.sendKeys(Keys.TAB);
	       }
	       
	       if (iteration.getEndDate().equals("")) {
	    	   iteration.setEndDate(iteration.getBeginDate());
	       }
           endDateEdit.clear();
           endDateEdit.sendKeys(iteration.getEndDate());
           endDateEdit.sendKeys(Keys.TAB);
		   
	         releaseInput.clear();
	         releaseInput.sendKeys(iteration.getReleaseName());
	         autocompleteSelect(iteration.getReleaseName(), true);
	         
	         submitDialog(submitBtn);
		
		return new ReleasesIterationsPage(driver);
	}
	
    public void cancel() {
    	cancelDialog(cancelBtn);
    }
	
    public void save() {
    	cancelDialog(submitBtn);
    }

    public String getStartDate() {
    	return beginDateEdit.getAttribute("value");
    }
	    
    public String getFinishDate() {
    	return endDateEdit.getAttribute("value");
    }

	public void addNumber(String name)
	{
        clickTab("main");
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(nameEdit));
        nameEdit.clear();
        nameEdit.sendKeys(name);
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

    public void openBurnDown(){
        clickTab("iterationburndownsection");
    }
}
