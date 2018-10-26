package ru.devprom.pages.project.milestones;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

public class MilestoneEditPage extends MilestoneNewPage {

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(.,'Действия')]")
	protected WebElement actionsBtn;
	
	@FindBy(xpath = "//ul//a[text()='Пройдена']")
	protected WebElement passMilestoneBtn;
	
	public MilestoneEditPage(WebDriver driver) {
		super(driver); 
	}

	public MilestoneEditPage passMilestone() {
		actionsBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(passMilestoneBtn));
		passMilestoneBtn.click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//input[@id='pm_MilestonePassed' and @checked]")));
		return new MilestoneEditPage(driver);
	}
}
