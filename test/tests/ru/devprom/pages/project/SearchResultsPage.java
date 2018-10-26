package ru.devprom.pages.project;

import java.util.ArrayList;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import ru.devprom.items.Project;
import ru.devprom.items.SearchResultItem;
import ru.devprom.pages.project.requests.RequestViewPage;

public class SearchResultsPage extends SDLCPojectPageBase {

	public SearchResultsPage(WebDriver driver) {
		super(driver);
	}

	public SearchResultsPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public List<SearchResultItem> readAllResults(){
		List<SearchResultItem> results = new ArrayList<SearchResultItem>();
		List<WebElement> itemsList = driver.findElements(By.xpath("//tr[contains(@id,'searchlist1_row_') and not(contains(@class,'info'))]"));
		 for (WebElement item:itemsList){
			 WebElement link = item.findElement(By.xpath("./td[@id='uid']/a"));
			 String id = link.getText().trim();
			 id = id.substring(1, id.length() - 1);
			 String findString = item.findElement(By.xpath("./td[@id='caption']")).getText().trim();
			 findString = findString.replace("&lt;", "<").replace("&gt;", ">");
			 String bold = item.findElement(By.xpath("./td[@id='caption']//span")).getText().trim();
			 bold = bold.replace("&lt;", "<").replace("&gt;", ">");
			 results.add(new SearchResultItem(link, "", id, "", findString, bold));
		 }
		return results;
	}

	
	
}
