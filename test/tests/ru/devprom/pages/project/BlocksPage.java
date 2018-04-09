package ru.devprom.pages.project;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import ru.devprom.items.Project;

public class BlocksPage extends SDLCPojectPageBase {

	public BlocksPage(WebDriver driver) {
		super(driver);
		// TODO Auto-generated constructor stub
	}

	public BlocksPage(WebDriver driver, Project project) {
		super(driver, project);
		// TODO Auto-generated constructor stub
	}

	
	public boolean isUserInList(String username){
		return !driver.findElements(By.xpath("//td[@id='systemuser' and contains(text(),'"+username + "')]")).isEmpty();
	}

	
	
	public BlocksPage unblockUser(String userName) {
		WebElement unBlockBtn = driver.findElement(By.xpath("//td[@id='systemuser' and contains(text(),'"+userName + "')]/following-sibling::td[@id='operations']//a[contains(.,'Разблокировать')]"));
        clickOnInvisibleElement(unBlockBtn);
	    try {
			Thread.sleep(2000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
        return new BlocksPage(driver);
	}

	
}
