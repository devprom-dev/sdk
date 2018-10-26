package ru.devprom.pages.project.milestones;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Milestone;
import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class MilestoneNewPage extends SDLCPojectPageBase {
	
	@FindBy(name = "MilestoneDate")
	protected WebElement dateEdit;

	@FindBy(id = "pm_MilestoneCaption")
	protected WebElement nameEdit;
	
	@FindBy(id = "pm_MilestoneDescription")
	protected WebElement descriptionEdit;
	
	@FindBy(id = "pm_MilestoneReasonToChangeDate")
	protected WebElement dataChangeCauseEdit;
	
	@FindBy(id = "pm_MilestoneCompleteResult")
	protected WebElement resultEdit;
	
	@FindBy(id = "pm_MilestonePassed")
	protected WebElement isDoneBox;
	
	@FindBy(xpath = "//span[@name='pm_MilestoneTraceRequests']//a[contains(@class,'embedded-add-button')]")
	protected WebElement addRequestBtn;

	@FindBy(id = "pm_MilestoneSubmitBtn")
	protected WebElement submitBtn;
	
	@FindBy(xpath = "//input[@type='button' and @value='Отменить']")
	protected WebElement cancelBtn;
	
	
	
	public MilestoneNewPage(WebDriver driver) {
		super(driver);
	}

	public MilestoneNewPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	
	public MilestonesPage createMilestone(Milestone milestone){
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(dateEdit));
		fillDate(milestone.getDate());
		fillName(milestone.getName());
		if (!milestone.getDescription().isEmpty()) fillDescription(milestone.getDescription());
		if (!milestone.getDataChangeReason().isEmpty()) fillDataChangeCause(milestone.getDataChangeReason());
		if (!milestone.getCompleteResult().isEmpty()) fillResult(milestone.getCompleteResult());
		if (milestone.getIsDone()) checkIsDone();
		submitDialog(submitBtn);
		
    	//read ID
		  (new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//td[@id='caption' and contains(.,'"+milestone.getName()+"')]")));
    	String uid =driver.findElement(By.xpath("//td[@id='caption' and contains(.,'"+milestone.getName()+"')]/preceding-sibling::td[@id='uid']")).getText();
    	milestone.setId(uid.substring(1, uid.length()-1));

    	return new MilestonesPage(driver);
	}
	
	
	public void fillDate(String date){
		dateEdit.clear();
		dateEdit.sendKeys(date);
	}
	
	public void fillName(String name){
		nameEdit.clear();
		nameEdit.sendKeys(name);
	}
	
	public void fillDescription(String description){
		descriptionEdit.clear();
		descriptionEdit.sendKeys(description);
	}
	
	public void fillDataChangeCause(String dataChangeCause){
		dataChangeCauseEdit.clear();
		dataChangeCauseEdit.sendKeys(dataChangeCause);
	}

	public void fillResult(String result){
		resultEdit.clear();
		resultEdit.sendKeys(result);
	}
	
	//TODO добавить проверку
	public void checkIsDone(){
		isDoneBox.click();
	}

	public void uncheckIsDone(){
		isDoneBox.click();
	}

	public void addRequest(String requestName){
		addRequestBtn.click();

		driver.findElement(
				By.xpath("//input[@value='requestinversedtracemilestone']/following-sibling::div[contains(@id,'fieldRowObjectId')]//input[contains(@id,'ChangeRequestText')]"))
				.sendKeys(requestName);
		autocompleteSelect(requestName);
		driver.findElement(
				By.xpath("//input[@value='requestinversedtracemilestone']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
				.click();

	}
	

	public void addAnyRequest() {
		addRequestBtn.click();

		driver.findElement(
				By.xpath("//input[@value='requestinversedtracemilestone']/following-sibling::div[contains(@id,'fieldRowChangeRequest')]//input[contains(@id,'ChangeRequestText')]"))
				.sendKeys("I");
		autocompleteSelect("I");
		driver.findElement(
				By.xpath("//input[@value='requestinversedtracemilestone']/following-sibling::div//input[contains(@id,'saveEmbedded')]"))
				.click();
	}
	
	
}
