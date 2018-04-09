package ru.devprom.pages.project.testscenarios;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.items.TestScenario;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class TestSpecificationNewPage extends SDLCPojectPageBase  {

	
	@FindBy(id = "WikiPageCaption")
	protected WebElement nameEdit;
	
	@FindBy(id = "WikiPageSubmitBtn")
	protected WebElement saveBtn;
	
	public TestSpecificationNewPage(WebDriver driver) {
		super(driver);
	}

	public TestSpecificationNewPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public TestSpecificationViewPage create(TestScenario specification)
	{
		editName (specification.getName());
		if ( !specification.getContent().equals("") ) {
			editDescription(specification.getContent());
		}
		submitDialog(saveBtn);
		
		WebElement title = driver.findElement(
				By.xpath("//tr[contains(@id,'pmwikidocumentlist')]//div[contains(@class,'wysiwyg-text') and contains(.,'" + specification.getName() + "')]"));
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions.visibilityOf(title));
		specification.setId("S-" + title.getAttribute("objectid"));

		return new TestSpecificationViewPage(driver);
	}
	
	public void editName(String name){
		nameEdit.clear();
		nameEdit.sendKeys(name);
	}

	public void editDescription(String description){
		CKEditor we = new CKEditor(driver);
		we.changeText(description);
	}
	
	public TestSpecificationViewPage saveSpecification(){
		submitDialog(saveBtn);
		return new TestSpecificationViewPage(driver);
	}
}
