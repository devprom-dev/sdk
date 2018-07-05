package ru.devprom.pages.project.repositories;

import java.util.ArrayList;
import java.util.List;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Commit;
import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class RepositoryCommitsPage extends SDLCPojectPageBase {

	@FindBy(xpath = "//a[@id='refresh-commits']")
	protected WebElement updateBtn;
	
	public RepositoryCommitsPage(WebDriver driver) {
		super(driver);
	}

	public RepositoryCommitsPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public String readComment(String revisionNumber){
		return driver.findElement(By.xpath("//tr[contains(@id,'subversionrevisionlist')]/td[@id='version' and contains(.,'"
	                                        +revisionNumber+"')]/following-sibling::td[@id='description']")).getText();
	}
	
	public CommitPage clickToTheLastCommit(){
		driver.findElement(By.xpath("//tr[contains(@id,'subversionrevisionlist')]/td[@id='uid']/a")).click();
		return new CommitPage(driver);
	}
	
	public CommitPage clickToTheLastCommitCommentedAs(String comment){
		driver.findElement(By.xpath("//tr[contains(@id,'subversionrevisionlist')]/td[@id='description' and contains(.,'"+comment+"')]/preceding-sibling::td[@id='uid']/a")).click();
		return new CommitPage(driver);
	}
	
	public List<WebElement> readRequestLinksFromComment(String revisionNumber){
		List<WebElement> requestsLinks = new ArrayList<WebElement>();
		String comment = readComment(revisionNumber);
		  String regex = "\\[I-[0-9]+\\]";
	        Pattern p = Pattern.compile(regex);
	        Matcher m = p.matcher(comment);
	            while(m.find()) {
	            	try {
	            	requestsLinks.add(driver.findElement(By.xpath("//tr[contains(@id,'subversionrevisionlist')]/td[@id='version' and contains(.,'"
	                                        +revisionNumber+"')]/following-sibling::td[@id='description']/a[text()='"+m.group()+"']")));
	            	}
	            	catch (NoSuchElementException e) {
	            		String uid = m.group();
	            		uid = uid.substring(1,uid.length()-1);
	            		requestsLinks.add(driver.findElement(By.xpath("//tr[contains(@id,'subversionrevisionlist')]/td[@id='version' and contains(.,'"
                                +revisionNumber+"')]/following-sibling::td[@id='description']/a/strike[contains(.,'"+uid+"')]")));
	            	}
	            }
		return requestsLinks;
	}
	
	public List<WebElement> readTasksLinksFromComment(String revisionNumber){
		List<WebElement> tasksLinks = new ArrayList<WebElement>();
		String comment = readComment(revisionNumber);
		  String regex = "\\[T-[0-9]+\\]";
	        Pattern p = Pattern.compile(regex);
	        Matcher m = p.matcher(comment);
	            while(m.find()) {
	            	try {
	            		tasksLinks.add(driver.findElement(By.xpath("//tr[contains(@id,'subversionrevisionlist')]/td[@id='version' and contains(.,'"
	                                        +revisionNumber+"')]/following-sibling::td[@id='description']/a[text()='"+m.group()+"']")));
	            	}
	            	catch (NoSuchElementException e) {
	            		String uid = m.group();
	            		uid = uid.substring(1,uid.length()-1);
	            		tasksLinks.add(driver.findElement(By.xpath("//tr[contains(@id,'subversionrevisionlist')]/td[@id='version' and contains(.,'"
                                +revisionNumber+"')]/following-sibling::td[@id='description']/a/strike[contains(.,'"+uid+"')]")));
	            	}
	            }
		return tasksLinks;
	}
	
	
	public RepositoryCommitsPage setConnectionFilter(String connectionName){
		driver.findElement(By.xpath("//div[contains(@class,'filter')]/div[2]/a")).click();
		String code = driver.findElement(By.xpath("//div[contains(@class,'filter')]/div[2]/ul/li/a[text()='"+connectionName+"']")).getAttribute("onkeydown");
		
		String substr =  "filterLocation.turnOn\\('subversion', '[0-9]+', 0\\)";
	        Pattern p = Pattern.compile(substr);
	        Matcher m = p.matcher(code);
	        m.find();
	        String script = m.group(); 
	        ((JavascriptExecutor) driver).executeScript(script);
	     
	    driver.findElement(By.xpath("//div[contains(@class,'filter')]/div[2]/a")).click();   
		return new RepositoryCommitsPage(driver);
	}
	
	public List<Commit> readCommitsByVersion(String revisionNumber){
		List<Commit> commits = new ArrayList<Commit>();
		List<WebElement> rows = driver.findElements(By.xpath("//tr[contains(@id,'subversionrevisionlist')]/td[@id='version' and contains(.,'"
                +revisionNumber+"')]/.."));
		for (WebElement row:rows){
		String uid = row.findElement(By.id("uid")).getText().trim();
		String dateTime = row.findElement(By.id("commitdate")).getText().trim();
		uid = uid.substring(1, uid.length()-1);
		commits.add(new Commit(uid, revisionNumber, dateTime));
		}
		return commits;
	}
	
	public RepositoryCommitsPage update(){
		(new WebDriverWait(driver, 3)).until(ExpectedConditions.visibilityOf(updateBtn));
		updateBtn.click();
		try {
			Thread.sleep(5000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		return new RepositoryCommitsPage(driver);
	}
	
}
