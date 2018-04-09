package ru.devprom.pages.project.settings;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;

import ru.devprom.pages.project.AddMemberPage;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class ProjectMembersPage extends SDLCPojectPageBase {

	@FindBy(xpath = "//a[@id='add-user']")
	private WebElement addMemberBtn;

	public ProjectMembersPage(WebDriver driver) {
		super(driver);
		Assert.assertTrue(isElementPresent(By.id("participantlist1")));
		FILELOG.info("Open Project Members list page");
		FILELOG.debug("Current URL is: " + driver.getCurrentUrl());
	}

	public AddMemberPage gotoAddMember() {
		addMemberBtn.click();
		return new AddMemberPage(driver);
	}

	public MemberProfilePage gotoMemberProfile(String name){
		WebElement menu = driver.findElement(By.xpath("//tr[contains(@id,'participantlist1_row_')]/td[@id='caption' and text()='"+name+"']/following-sibling::td[@id='operations']"));
		WebElement edit = menu.findElement(By.xpath(".//ul[@role='menu']/li/a[text()='Изменить']"));
		clickOnInvisibleElement(edit);
		return new MemberProfilePage(driver);
	}
	
	public String readUserRole(String userNameLong){
		String text = driver.findElement(By.xpath("//tr[contains(@id,'participantlist1_row_')]/td[@id='caption' and text()='"+userNameLong+"']/following-sibling::td[@id='participantrole']//*[contains(@class,'title')]")).getText();
	    return text.split(" \\(")[0];
	
	
	}

	public ProjectMembersPage assignRole(String username, String role) {
		
		WebElement btn = driver.findElement(By.xpath("//td[@id = 'caption' and text()='"+username+"']/following-sibling::td[@id='operations']//a[text()='Назначить роль']"));
		clickOnInvisibleElement(btn);
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOfElementLocated(By.id("pm_ParticipantRoleProjectRole")));
		new Select(driver.findElement(By.id("pm_ParticipantRoleProjectRole"))).selectByVisibleText(role);
		submitDialog(driver.findElement(By.id("pm_ParticipantRoleSubmitBtn")));
		return new ProjectMembersPage(driver);
	}
	
	public boolean isMember(String username) {
		return !driver.findElements(By.xpath("//td[@id='caption' and text()='"+username+"']")).isEmpty();
	}
	
	public boolean isGreyedOut(String username) {
		return !driver.findElements(By.xpath("//td[@id='caption' and contains(@style,'color:silver') and text()='"+username+"']")).isEmpty();
	}
	
}
