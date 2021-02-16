package ru.devprom.pages.project.documents;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Document;
import ru.devprom.items.Project;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requirements.RequirementViewPage;

public class DocumentNewPage extends SDLCPojectPageBase
{
	@FindBy(xpath = "//div[@id='modal-form']//*[@id='WikiPageCaption']")
	protected WebElement captionEdit;

	@FindBy(id = "ParentPageText")
	protected WebElement parentPageEdit;

	@FindBy(xpath = "//div[@id='modal-form']//*[@id='WikiPagePageType']")
	protected WebElement typeEdit;

	@FindBy(id = "WikiPageSubmitBtn")
	protected WebElement submitBtn;

	@FindBy(id = "WikiPageSubmitOpenBtn")
	protected WebElement submitThenOpenBtn;

	public DocumentNewPage(WebDriver driver) {
		super(driver);
	}

	public DocumentNewPage(WebDriver driver, Project project) {
		super(driver, project);
	}

	public DocumentViewPage createNewDoc(Document doc) 
	{
		clickMainTab();
		captionEdit.clear();
		captionEdit.sendKeys(doc.getName());

		if (doc.getContent() != null && !doc.getContent().equals("")) {
			addContent(doc.getContent());
		}

		submitDialog(submitBtn);
		driver.navigate().refresh();

		By locator = By.xpath("//td[@id='caption' and contains(.,'" + doc.getName() + "')]/preceding-sibling::td[@id='uid']");
		String uid = driver.findElement(locator).getText();
		doc.setId(uid.substring(1, uid.length() - 1));
		return clickToDoc(doc.getId());
	}

	public DocumentViewPage clickToDoc(String id) {
		driver.findElement(
				By.xpath("//tr/td[@id='uid']/a[contains(.,'" + id + "')]")).click();
		(new WebDriverWait(driver, waiting)).until(ExpectedConditions
				.presenceOfElementLocated(By
						.xpath("//div[@class='wiki-page-document']")));
		return new DocumentViewPage(driver);
	}

	public void clickMainTab() {
		clickTab("main");
	}

	public void addContent(String content) {
		clickMainTab();
		CKEditor we = new CKEditor(driver);
		we.changeText(content);
	}
}
