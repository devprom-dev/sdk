package ru.devprom.pages.project.milestones;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.pages.project.SDLCPojectPageBase;

public class MilestonesPage extends SDLCPojectPageBase {
	
	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]")
	protected WebElement actionsBtn;

	@FindBy(id="filter-settings")
	protected WebElement filterBtn;
	
	@FindBy(xpath = "//a[contains(.,'Добавить') and contains(@class,'append-btn')]")
	protected WebElement addBtn;

	public MilestonesPage(WebDriver driver) {
		super(driver);
	}

	public MilestoneNewPage addNewMilestone(){
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(addBtn));
		addBtn.click();
		return new MilestoneNewPage(driver);
	}
	
	public MilestoneEditPage editMilestone(String id){
		driver.findElement(
				By.xpath("//tr[contains(@id,'milestonelist1_row_')]/td[@id='uid']/a[contains(.,'["
						+ id + "]')]")).click();
		return new MilestoneEditPage(driver);
	}
	
	public String getLinkedRequestId(String milestoneId){
		String requestId = "";
		String path = "//tr[contains(@id,'milestonelist1_row_')]/td[@id='uid']/a[contains(.,'["
				+ milestoneId + "]')]/../following-sibling::td[@id='tracerequests']/a";
		if (driver.findElements(By.xpath(path)).size()>0) {
			requestId = driver.findElement(By.xpath(path)).getText();
					requestId = requestId.substring(1, requestId.length()-1);
		}
		return requestId;
		
	}
	
	public boolean isMilestonePresent(String milestoneId){
		return driver.findElements(
				By.xpath("//tr[contains(@id,'milestonelist1_row_')]/td[@id='uid']/a[contains(.,'["
						+ milestoneId + "]')]")).size()>0;
	}
	
}
