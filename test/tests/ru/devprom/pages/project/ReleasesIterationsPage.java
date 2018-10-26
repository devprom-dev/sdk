package ru.devprom.pages.project;

import java.util.ArrayList;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
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
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(addIterationBtn));
		addIterationBtn.click();
		waitForDialog();
		return new IterationNewPage(driver);
	}
	
	public ReleaseNewPage addRelease(){
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(addReleaseBtn));
		addReleaseBtn.click();
		waitForDialog();
		return new ReleaseNewPage(driver);
	}
	
	public void showAll() {
		String code = "filterLocation.setup('state=all', 1);";
		//filterBtn.click();
		((JavascriptExecutor) driver).executeScript(code);
		//filterBtn.click();
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//div[contains(@class,'filter')]/div/a[not(contains(@class,'btn-info')) and contains(.,'Состояние')]")));
		
		/*try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}*/
		}

	public void showCurrent() {
		String code = "filterLocation.setup('state=current', 1);";
		//filterBtn.click();
		((JavascriptExecutor) driver).executeScript(code);
		//filterBtn.click();
		try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		}

	public void showPast() {
		String code = "filterLocation.setup('state=past', 1);";
		//filterBtn.click();
		((JavascriptExecutor) driver).executeScript(code);
		//filterBtn.click();
		try {
			Thread.sleep(3000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		}
	public Release readRelease(String releaseNumber)
	{
		clickOnInvisibleElement(driver.findElement(By.xpath("//td[@id='stage' and contains(.,'"+releaseNumber+"')]/following-sibling::td[@id='operations']//a[@id='modify']")));
		waitForDialog();
		ReleaseNewPage page = new ReleaseNewPage(driver);
		String startDate = page.getStartDate();
		String endDate = page.getFinishDate();
		page.cancel();
	    return new Release(releaseNumber,"",startDate,endDate);
	}
	
	public Iteration readIteration(String iterationFullName)
	{
		clickOnInvisibleElement(driver.findElement(By.xpath("//td[@id='stage' and contains(.,'"+iterationFullName+"')]/following-sibling::td[@id='operations']//a[@id='modify']")));
		waitForDialog();
		IterationNewPage page = new IterationNewPage(driver);
		String startDate = page.getStartDate();
		String endDate = page.getFinishDate();
		page.cancel();
	    return new Iteration(iterationFullName.split("\\.")[1].trim(),"",startDate,endDate,iterationFullName.split("\\.")[0].trim());
	}
	
	public EditReleaseIterationPage editRelease(String releaseNumber){
		WebElement releaseEditBtn = driver.findElement(By.xpath("//td[@id='stage' and contains(.,'"+releaseNumber+"')]/following-sibling::td//a[text()='Изменить']"));
		clickOnInvisibleElement(releaseEditBtn);
		return new EditReleaseIterationPage(driver);
	}
	
	/**Method deletes all releases and iterations excepts for '0'. All the rest of operations shouldn't be in use.*/
	public ReleasesIterationsPage deleteAllIterations(){
		  showAll();
		 if (driver.findElements(By.xpath("//td[@id='stage']/div[contains(.,'Итерация')]")).isEmpty()){
			  return new ReleasesIterationsPage(driver);
		 }
		WebElement releaseEditBtn = driver.findElement(By.xpath("//td[@id='stage']/div[contains(.,'Итерация')]/../following-sibling::td//a[text()='Изменить']"));
		clickOnInvisibleElement(releaseEditBtn);
		submitDelete(driver.findElement(By.id("pm_ReleaseDeleteBtn")));
		deleteAllIterations();
		return new ReleasesIterationsPage(driver);
	}
		
	public ReleasesIterationsPage deleteAllReleases(){
	    showAll();
		List<WebElement> btns = driver.findElements(By.xpath("//td[@id='stage']/following-sibling::td//a[text()='Изменить']"));
		if ( btns.size() < 1 ) return new ReleasesIterationsPage(driver);
		clickOnInvisibleElement(btns.get(0));
		submitDelete(driver.findElement(By.id("pm_VersionDeleteBtn")));
		deleteAllReleases();
		return new ReleasesIterationsPage(driver);
	}

	public List<String> getList() {
		List<String> iterationsReleases = new ArrayList<String>();
		List <WebElement> rows = driver.findElements(By.id("stage"));
		for (WebElement row:rows){
			iterationsReleases.add(last(row.getText().split("}")).trim());
		}
		return iterationsReleases;
	}
	
	public static <T> T last(T[] array) {
	    return array[array.length - 1];
	}
	
	public boolean isTaskPresent(String taskId){
		return (!driver.findElements(By.xpath("//a[text()='["+taskId+"]']")).isEmpty() ||  !driver.findElements(By.xpath("//a/strike[contains(.,'"	+ taskId + "')]")).isEmpty());
	}
}
