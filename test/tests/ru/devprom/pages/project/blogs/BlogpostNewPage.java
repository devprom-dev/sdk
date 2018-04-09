package ru.devprom.pages.project.blogs;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Blogpost;
import ru.devprom.items.Project;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class BlogpostNewPage extends SDLCPojectPageBase {

	@FindBy(id = "BlogPostCaption")
	protected WebElement captionEdit;
	
	@FindBy(id = "BlogPostOrderNum")
	protected WebElement numberEdit;
	
	@FindBy(id = "BlogPostSubmitBtn")
	protected WebElement saveBtn;
	
	public BlogpostNewPage(WebDriver driver) {
		super(driver);
	}

	public BlogpostNewPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public BlogPage createNewPost(Blogpost b){
		
		captionEdit.clear();
		captionEdit.sendKeys(b.getName());
		
		if (!b.getContent().isEmpty()) {
			CKEditor we = new CKEditor(driver);
			we.typeText(b.getContent());
		}
		saveBtn.click();
		
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.presenceOfElementLocated(By.xpath("//a[contains(@class,'with-tooltip')]")));
		String uid = driver.findElement(By.xpath("//a[contains(@class,'with-tooltip')]")).getText();
		b.setId(uid.substring(1, uid.length()-1));
		return new BlogPage(driver);
	}
}
