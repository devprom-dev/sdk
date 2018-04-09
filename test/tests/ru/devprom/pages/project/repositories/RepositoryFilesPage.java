package ru.devprom.pages.project.repositories;

import java.util.ArrayList;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class RepositoryFilesPage extends SDLCPojectPageBase {


	@FindBy(xpath = "//a[@uid='subversion']")
	protected WebElement connectionSelectBtn;
	
	public RepositoryFilesPage(WebDriver driver) {
		super(driver);
	}

	public RepositoryFilesPage(WebDriver driver, Project project) {
		super(driver, project);
	}

        public List<String> getTestFilesList(){
		List<String> result = new ArrayList<String>();
		List<WebElement> rows = driver.findElements(By.xpath("//table[@id='subversionfileslist1']//td[@id='file']/a"));
		for (WebElement element:rows){
			result.add(element.getText());
		}
		return result;
	}
	
	public  RepositoryFilesPage selectConnection (String connection){
		connectionSelectBtn.click();
		driver.findElement(By.xpath("//a[@uid='subversion']/following-sibling::ul/li/a[text()='"+connection+"']")).click();
		return new RepositoryFilesPage(driver);
	}
}
