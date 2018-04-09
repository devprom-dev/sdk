package ru.devprom.tests;

import org.openqa.selenium.By;
import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.items.Project;
import ru.devprom.items.Template;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requests.RequestsPage;
import ru.devprom.pages.project.settings.TerminologyPage;

public class TerminologyTest extends ProjectTestBase {

	/** Test changes user terminology in the project. "Функции" are changed to "Продукты". 
	 * @throws InterruptedException */
	@Test 
	public void changeFunctionToProduct() throws InterruptedException{
		String oldTerm = "Функции";
		String newTerm = "Продукты";
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		page.gotoProject(webTest);
		
		//Go to Terminology page and change the caption of "Функции" term 	
		TerminologyPage tp = (new SDLCPojectPageBase(driver)).gotoTerminologyPage();
		tp = tp.filterBy("Функции");
		tp.changeTerm(oldTerm, newTerm);
		
		//Go to Request page just to refresh a view and then check new term display
		RequestsPage mip = tp.gotoRequests();
		driver.navigate().refresh();
		Assert.assertEquals(driver.findElement(By.xpath("//a[@uid='features-list']")).getText(), newTerm, "Название раздела не соответствует ожиданию");
		
		//Go back to Terminology and reset all the terms to Defaults
		tp = mip.gotoTerminologyPage();
		tp = tp.resetToDefaults();
		
		//Check the term has default caption
		mip = tp.gotoRequests();
		Assert.assertEquals(driver.findElement(By.xpath("//a[@uid='features-list']")).getText(), oldTerm, "Название раздела не является названием по умолчанию");
		
	}
}
