package ru.devprom.pages.project;

import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.NotFoundException;
import org.openqa.selenium.TimeoutException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.PageBase;

public class ProjectPageBase extends PageBase implements IProjectBase {

	@FindBy(xpath = ".//a[@id='navbar-project']")
	protected WebElement projectLink;
	
	protected Project project;

	public ProjectPageBase(WebDriver driver) {
		super(driver);
		// TODO Auto-generated constructor stub
	}

	public ProjectPageBase(WebDriver driver, Project project) {
		this(driver);
		this.project = project;
	}

	public String getProjectTitle() {
		return projectLink.getText().trim();
	}

	public Project getProject() {
		return this.project;
	}


/**
 * Открывает отчет с произвольным именем, находящийся в произвольном месте меню
 * @param headMenu - вторая часть id пункта верхнего меню, варианты: favs, mgmt, reqs, dev, qa, docs, stg
 * @param leftMenuId - id пункта меню (id элемента li), если отчет в корне, передаем пустое значение
 * @param reportName - видимое имя отчета
 * @return
 */

public ProjectPageBase gotoCustomReport(String headMenu, String leftMenuId, String reportName){
	driver.findElement(By.xpath("//li[@id='tab_"+headMenu+"']/a")).click();
	WebElement menuItem = driver.findElement(By.xpath("//ul[@id='menu_"+headMenu+"']//a[text()='"+reportName+"']"));
	if (!leftMenuId.isEmpty() && !menuItem.isDisplayed()) {
		WebElement menuGroup = driver.findElement(By.xpath("//ul[@id='menu_"+headMenu+"']//li[@id='"+leftMenuId+"']/a"));
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(menuGroup));
		menuGroup.click();
	}
	menuItem.click();
	return new ProjectPageBase(driver);
}
	


/**
 * Проверяет 
 *  - наличие отчета (все параметры имеют непустые значения);
 *  - либо группы отчетов (передать reportName пустым);
 *  - либо пункта верхнего меню (пустые reportName и  leftMenuId)
 * 
 * @param headMenu - вторая часть id пункта верхнего меню, варианты: favs, mgmt, reqs, dev, qa, docs, stg
 * @param leftMenuId - id пункта меню (id элемента li), если отчет в корне, передаем пустое значение
 * @param reportName - видимое имя отчета
 * @return
 */
public boolean isReportAccessible (String headMenu, String leftMenuId, String reportName){
    
	if ((leftMenuId==null || leftMenuId.isEmpty()) && (reportName==null || reportName.isEmpty())) 
		return !driver.findElements(By.xpath("//li[@id='tab_"+headMenu+"']/a")).isEmpty();
	else 
		driver.findElement(By.xpath("//li[@id='tab_"+headMenu+"']/a")).click();
	if (reportName==null || reportName.isEmpty()) 
		return !driver.findElements(By.xpath("//li[@id='"+leftMenuId+"']/a")).isEmpty();
	else
		return !driver.findElements(By.xpath("//ul[@id='menu_"+headMenu+"']//a[text()='"+reportName+"']")).isEmpty();
	
  }

	public String requestReadLinkedIdOnPage()
	{
		By linkedRequest = By.xpath("//div[@class='embeddedRowTitle']//a[contains(@class,'with-tooltip') and contains(.,'[I-')]");
		int times = 5;
		while (times-- > 0) {
			try {
				(new WebDriverWait(driver,2)).until(ExpectedConditions.visibilityOfElementLocated(linkedRequest));
				break;
			} catch (TimeoutException e) {
			} catch (NoSuchElementException e) {
			}
		}
		String parts[] = driver.findElement(linkedRequest).getAttribute("href").split("/");
		return parts[parts.length-1];
	}

    protected void clickOnFavoriteLink() {
		try {
			driver.findElement(By.xpath("//li[@id='tab_favs']/a")).click();
		}
		catch( NotFoundException ex ) {
		}
    }
}
