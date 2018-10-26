package ru.devprom.pages.project.blogs;

import java.util.ArrayList;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Blogpost;
import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class BlogPage extends SDLCPojectPageBase {
	
	@FindBy(xpath = "//a[contains(.,'Добавить новость')]")
	protected WebElement addBlogpost;
	
	
	public BlogPage(WebDriver driver) {
		super(driver);
	}

	public BlogPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	
	public BlogpostNewPage addBlogpost(){
		 (new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(addBlogpost));
		 addBlogpost.click();
		 return new BlogpostNewPage(driver);
	}
	
	
	public Blogpost readPost(String id){
		Blogpost post;
		String name = driver.findElement(By.xpath("//a[text()='["+id+"]']/../preceding-sibling::h4/div")).getText();
		String content = driver.findElement(By.xpath("//a[text()='["+id+"]']/../../following-sibling::div[contains(@id,'BlogPostContent')]")).getText();
	    post = new Blogpost(name, content);
	    post.setAuthor(driver.findElement(By.xpath("//a[text()='["+id+"]']/../../following-sibling::blockquote/p")).getText());
		return post;
	}
	
	public Blogpost readPostByName(String name){
		Blogpost post;
		WebElement postRoot = driver.findElement(By.xpath("//div[contains(@id,'BlogPostCaption') and contains(.,'"+name+"')]/../.."));
		String id = postRoot.findElement(By.xpath("./div/a")).getText();
		id = id.substring(1, id.length()-1);
		String content = postRoot.findElement(By.xpath("./following-sibling::div[contains(@id,'BlogPostContent')]")).getText();
	    post = new Blogpost(name, content);
	    post.setId(id);
	    post.setAuthor(driver.findElement(By.xpath("//a[text()='["+id+"]']/../../following-sibling::blockquote/p")).getText());
		return post;
	}
	
	
	public Blogpost[] readAllPosts(){
		Blogpost[] posts;
		
		List<WebElement> elements = driver.findElements(By.className("bs"));
		posts = new Blogpost[elements.size()];
		for (int i=0; i<elements.size();i++){
			String name = elements.get(i).getText();
			String content = elements.get(i).findElement(By.xpath("../following-sibling::div[contains(@id,'BlogPostContent')]")).getText();
			posts[i] = new Blogpost(name, content);
			String uid = elements.get(i).findElement(By.xpath("./following-sibling::div/a[contains(@class,'with-tooltip')]")).getText();
			posts[i].setId(uid.substring(1, uid.length()-1));
			posts[i].setAuthor(elements.get(i).findElement(By.xpath("../following-sibling::blockquote/p")).getText());
		}
		return posts;
	}
	
	public BlogpostEditPage editPost(Blogpost b){
		driver.findElement(By.xpath("//a[text()='["+b.getId()+"]']/../../preceding-sibling::div[@class='actions']//a[text()='Редактировать']")).click();
		return new BlogpostEditPage(driver);
	}
	
	
	public BlogPage deletePost(Blogpost b) {
		driver.findElement(By.xpath("//a[text()='["+b.getId()+"]']/../../preceding-sibling::div[@class='actions']//a[text()='Удалить']")).click();
		safeAlertAccept();
		try {
			Thread.sleep(1000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		return new BlogPage(driver);
	}
	
	public List<WebElement> getLinksFromBlogpost(String blogpostNumericId) {
		return driver.findElements(By.xpath("//div[contains(@id,'BlogPostContent"+blogpostNumericId+"')]//a[contains(@href,'/I-')]"));
	}
	
	
}
