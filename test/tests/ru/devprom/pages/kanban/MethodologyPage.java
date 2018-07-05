package ru.devprom.pages.kanban;


import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.pages.kanban.KanbanPageBase;


public class MethodologyPage extends KanbanPageBase {

	
	@FindBy(id = "pm_MethodologyIsTasks")
	protected WebElement isTasks;
	
	@FindBy(id = "pm_MethodologySubmitBtn")
	protected WebElement saveBtn;
        
        @FindBy(id = "pm_MethodologyIsReleasesUsed")
	protected WebElement planingType;

	
	public MethodologyPage(WebDriver driver)
	{
		super(driver);
	}

	public MethodologyPage uncheckIsTasks()
	{
		if ( isTasks.isSelected() ) isTasks.click();
		return this;
	}
	
	public MethodologyPage checkIsTasks()
	{
		if ( !isTasks.isSelected() ) isTasks.click();
		return this;
	}

	public KanbanPageBase save()
	{
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(saveBtn));
		saveBtn.click();
		try {
			Thread.sleep(6000);
		} catch (InterruptedException e) {
		}
		return new KanbanPageBase(driver);
	}

    public MethodologyPage selectPlanType(String planType) {
        (new Select(planingType)).selectByVisibleText(planType);
        return this;
    }
}
