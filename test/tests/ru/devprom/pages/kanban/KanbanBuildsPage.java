/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.pages.kanban;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import ru.devprom.items.Project;

/**
 *
 * @author лена
 */
public class KanbanBuildsPage extends KanbanPageBase {

	@FindBy(xpath = ".//*[@id='new-build']")
	protected WebElement addBuild;
        
        //кнопка Еще
        @FindBy(xpath = ".//*[@id='bulk-actions']/a")
	protected WebElement moreBtn;
        
        //пункт Реализовано меню Еще
        @FindBy(xpath = ".//*[@id='bulk-actions']//a[contains(.,'Реализовано')]")
	protected WebElement realizedItem;

    public KanbanBuildsPage(WebDriver driver, Project project) {
        super(driver, project);
    }
	
	public KanbanBuildsPage(WebDriver driver) {
		super(driver);
	}

    public KanbanNewBuildPage clickNewBuild() {
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(addBuild));
        addBuild.click();
        return new KanbanNewBuildPage(driver);
    }

    public void checkAll() {
		driver.findElement(By.xpath("//input[contains(@id,'to_delete_all')]")).click();
    }

    public void clickRealized() {
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(moreBtn));
        moreBtn.click();
        (new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(realizedItem));
        realizedItem.click();
    }

    
        
}
