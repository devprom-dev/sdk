package ru.devprom.pages.project.kb;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.KnowledgeBase;
import ru.devprom.items.Project;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class KBNewPage extends SDLCPojectPageBase {

	
	@FindBy(id = "WikiPageCaption")
	protected WebElement nameEdit;

	@FindBy(id = "ParentPageText")
	protected WebElement parentPageSelect;
	
	@FindBy(id = "WikiPageOrderNum")
	protected WebElement numberEdit;
	
	@FindBy(id = "WikiPageSubmitBtn")
	protected WebElement saveBtn;
	
	public KBNewPage(WebDriver driver) {
		super(driver);
	}

	public KBNewPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public KnowledgeBasePage createShort(KnowledgeBase kb)
	{
		nameEdit.clear();
		nameEdit.sendKeys(kb.getName());
		submitDialog(saveBtn);
		By titleLoc = By.xpath("//div[contains(@class,'table-master')]//tr[contains(@id,'knowledgebasedocumentlist')]//div[contains(@class,'wysiwyg-text') and contains(.,'" + kb.getName() + "')]");
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.presenceOfElementLocated(titleLoc));
		kb.setId("K-" + driver.findElement(titleLoc).getAttribute("objectid"));
		return new KnowledgeBasePage(driver);
	}
	
	public void addContent(String content){
		CKEditor we = new CKEditor(driver);
		we.changeText(content);
	}

	public String readContent(){
		WebElement newFrame = driver.findElement(By
				.xpath(".//div[@role='presentation']/iframe"));
		driver.switchTo().frame(newFrame);
		String content = driver.findElement(By.tagName("body")).getText().trim();
		driver.switchTo().defaultContent();
		return content;
	}

    public KnowledgeBasePage createKB(KnowledgeBase kb) {
        nameEdit.clear();
		nameEdit.sendKeys(kb.getName());
                addContent(kb.getContent());
		submitDialog(saveBtn);
		
		WebElement title = driver.findElement(
			By.xpath("//tr[contains(@id,'knowledgebasedocumentlist')]//div[contains(@class,'wysiwyg-text') and contains(.,'" + kb.getName() + "')]"));
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(title));
		kb.setId("K-" + title.getAttribute("objectid"));
		return new KnowledgeBasePage(driver);
    }
}
