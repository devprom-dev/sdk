package ru.devprom.pages.project;

import java.util.ArrayList;
import java.util.List;

import org.openqa.selenium.*;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Iteration;
import ru.devprom.items.Project;
import ru.devprom.items.Release;
import ru.devprom.pages.project.tasks.TasksPage;

public class ReleasesIterationsPage extends SDLCPojectPageBase {

	@FindBy(xpath = "//a[contains(.,'Релиз') and contains(@class,'append-btn')]")
	protected WebElement addReleaseBtn;
	
	@FindBy(xpath = "//a[contains(.,'Итерация') and contains(@class,'append-btn')]")
	protected WebElement addIterationBtn;
	
	@FindBy(id="filter-settings")
	protected WebElement filterBtn;
	
	public ReleasesIterationsPage(WebDriver driver) {
		super(driver);
	}

	public ReleasesIterationsPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public IterationNewPage addIteration()
	{
		clickOnInvisibleElement(addIterationBtn);
		waitForDialog();
		return new IterationNewPage(driver);
	}
	
	public ReleaseNewPage addRelease(){
		clickOnInvisibleElement(addReleaseBtn);
		waitForDialog();
		return new ReleaseNewPage(driver);
	}
	
	public Release readRelease(String releaseNumber)
	{
		clickOnInvisibleElement(driver.findElement(By.xpath("//td[@id='caption' and contains(.,'"+releaseNumber+"')]/following-sibling::td[@id='operations']//a[@id='modify']")));
		waitForDialog();
		ReleaseNewPage page = new ReleaseNewPage(driver);
		String startDate = page.getStartDate();
		String endDate = page.getFinishDate();
		page.cancel();
	    return new Release(releaseNumber,"",startDate,endDate);
	}

	public void loadIterations()
	{
		try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
		}
		driver.findElement(By.id("restoreTree")).click();
		try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
		}
		try {
			(new WebDriverWait(driver, 3)).until(ExpectedConditions
					.presenceOfElementLocated(By.xpath("//td[@id='caption' and contains(.,'Итерация')]")));
		} catch(TimeoutException e) {
		}
	}

	public Iteration readIteration(String iterationFullName)
	{
		loadIterations();
		clickOnInvisibleElement(driver.findElement(By.xpath("//td[@id='caption' and contains(.,'"+iterationFullName+"')]/following-sibling::td[@id='operations']//a[@id='modify']")));
		waitForDialog();
		IterationNewPage page = new IterationNewPage(driver);
		String startDate = page.getStartDate();
		String endDate = page.getFinishDate();
		page.cancel();
	    return new Iteration(iterationFullName.split("\\.")[1].trim(),"",startDate,endDate,iterationFullName.split("\\.")[0].trim());
	}
	
	public ReleasesIterationsPage deleteAllIterations()
	{
		loadIterations();
		List<WebElement> list = driver.findElements(By.xpath("//td[@id='caption' and contains(.,'Итерация')]"));
		for (int i = 0; i < list.size(); i++) {
			WebElement releaseEditBtn = list.get(i).findElement(By.xpath("./following-sibling::td//a[text()='Изменить']"));
			clickOnInvisibleElement(releaseEditBtn);
			submitDelete(driver.findElement(By.id("pm_ReleaseDeleteBtn")));
		}
		return new ReleasesIterationsPage(driver);
	}
		
	public ReleasesIterationsPage deleteAllReleases()
	{
	    showAll();
		List<WebElement> btns = driver.findElements(By.xpath("//td[@id='caption']/following-sibling::td//a[text()='Изменить']"));
		if ( btns.size() < 1 ) return new ReleasesIterationsPage(driver);
		clickOnInvisibleElement(btns.get(0));
		submitDelete(driver.findElement(By.id("pm_VersionDeleteBtn")));
		deleteAllReleases();
		return new ReleasesIterationsPage(driver);
	}

	public List<String> getList()
	{
		loadIterations();
		List<String> iterationsReleases = new ArrayList<String>();
		List <WebElement> rows = driver.findElements(By.id("caption"));
		for (WebElement row:rows){
			iterationsReleases.add(last(row.getText().split("}")).split("\\[")[0].trim());
		}
		return iterationsReleases;
	}
	
	public static <T> T last(T[] array) {
	    return array[array.length - 1];
	}
	
	public boolean isTaskPresent(String taskId)
	{
		int hierarchyMaxLevel = 5;
		while( hierarchyMaxLevel-- > 0 ) {
			try {
				Thread.sleep(3000);
			} catch (InterruptedException e) {
			}
			driver.findElement(By.id("restoreTree")).click();
			try {
				(new WebDriverWait(driver, 1)).until(ExpectedConditions
						.presenceOfElementLocated(By.xpath("//td[@id='caption' and contains(.,'"+taskId+"')]")));
			} catch(TimeoutException e) {
			}
		}
		return (!driver.findElements(By.xpath("//a[text()='["+taskId+"]']")).isEmpty() ||  !driver.findElements(By.xpath("//a/strike[contains(.,'"	+ taskId + "')]")).isEmpty());
	}

	public String getStageId( String stageName ) {
		return driver.findElement(
				By.xpath("//table[contains(@id,'versiontree')]//td[@id='caption' and contains(.,'"+stageName+"')]/parent::tr"))
					.getAttribute("raw-id");
	}
}
