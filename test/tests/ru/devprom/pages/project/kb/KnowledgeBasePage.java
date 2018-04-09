package ru.devprom.pages.project.kb;

import java.util.ArrayList;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requirements.RequirementNewPage;

public class KnowledgeBasePage extends SDLCPojectPageBase {

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(text(),'Действия')]")
	protected WebElement actionsBtn;
	
	@FindBy(xpath = "//a[@id='append-child-page']")
	protected WebElement addKbBtn;
	
	@FindBy(xpath = "//ul/li/a[text()='Права доступа'and @href]")
	protected WebElement permissionsBtn;


	public KnowledgeBasePage(WebDriver driver) {
		super(driver);
	}

	public KnowledgeBasePage(WebDriver driver, Project project) {
		super(driver, project);
	}

	
	public KBNewPage addKb(){
		clickOnInvisibleElement(addKbBtn);
		return new KBNewPage(driver);
	}
	
	public KBNewPage addChildKb(String parentId) {
		clickOnInvisibleElement(addKbBtn);
		return new KBNewPage(driver);
	}

    public RequirementNewPage insertSection() {
        driver.findElement(By.xpath("//tr[contains(@id,'knowledgebasedocumentlist')]//div[contains(@class,'wysiwyg-text')]")).click();
        clickOnInvisibleElement(addKbBtn);
        return new RequirementNewPage(driver);
    }
	
	public KnowledgeBasePage openKb(String kbName){
		driver.findElement(By.xpath("//*[@id='wikitree']//span[contains(.,'"+kbName+"')]")).click();
		return new KnowledgeBasePage(driver);
	}
	
	public String readContent(String pageId){
		return driver.findElement(By.xpath("//div[contains(@id,'WikiPageContent"+pageId+"') and contains(@class,'wysiwyg')]")).getText();
	}
	
	/**Use this method to get tag text decoration information (only controlled by tags, no css).   
	 * The method searches the text in KB content and reads all the style tags for this text: bold, em, etc.*/
	public List<String> getStyleTagsForText(String requirementId, String text){
		List<String> tags = new ArrayList<String>();
		
		WebElement p = driver.findElement(By.xpath("//div[contains(@id,'WikiPageContent"+requirementId+"') and contains(@class,'wysiwyg')]//*[contains(text(),'"+text+"')]"));
		String tag = p.getTagName();
		while (!tag.equals("p")) {
			 tags.add(tag);
	          p=p.findElement(By.xpath("./.."));
	          tag = p.getTagName();
		}
		return tags;
	}
	
	
	public KBPermissionsPage gotoChangePermissionsPage()
	{
		clickOnInvisibleElement(permissionsBtn);
		return new KBPermissionsPage(driver);
	}
	
	
	public boolean isKBExists(String kbName) {
		return driver.findElements(By.xpath("//*[@id='wikitree']//span[contains(.,'"+kbName+"')]")).size() > 0;
	}
}
