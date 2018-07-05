package ru.devprom.pages.project;

import java.util.ArrayList;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.Keys;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.helpers.DateHelper;
import ru.devprom.items.Commit;
import ru.devprom.items.Project;
import ru.devprom.items.RTask;
import ru.devprom.items.Request;
import ru.devprom.items.Requirement;
import ru.devprom.items.Spent;
import ru.devprom.items.Milestone;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.testscenarios.TestScenarioTestingPage;

public class SpentTimePage extends SDLCPojectPageBase {

	public SpentTimePage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public SpentTimePage(WebDriver driver) {
		super(driver);
	}

	public List<Spent> readSpentRecords() 
	{
		(new WebDriverWait(driver, waiting))
			.until(ExpectedConditions.presenceOfElementLocated(By.xpath("//table[contains(@id,'spenttimelist')]")));

		List<WebElement> rows = driver.findElements(By.xpath("//table[contains(@id,'spenttimelist')]//tr[contains(@id,'spenttimelist1_row') and @sort-type='asc']"));
		List<Spent> spentList = new ArrayList<Spent>();
		for(WebElement row : rows) {
			spentList.add(
				new Spent(
					"",
					Double.parseDouble(row.findElement(By.xpath("./td[@id='capacity']")).getText().replace("ч", "")),
					"",
					row.findElement(By.xpath("./td[@id='description']")).getText()
				)
			);
		}
		
		return spentList;
	}
}
