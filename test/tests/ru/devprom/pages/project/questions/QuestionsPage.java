package ru.devprom.pages.project.questions;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;

import ru.devprom.items.Project;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class QuestionsPage extends SDLCPojectPageBase {

	@FindBy(xpath = "//a[@data-toggle='dropdown' and contains(text(),'Действия')]")
	protected WebElement actionsBtn;

	@FindBy(xpath = "//a[@id='new-question']")
	protected WebElement addQuestionBtn;
	
	public QuestionsPage(WebDriver driver) {
		super(driver);
		// TODO Auto-generated constructor stub
	}

	public QuestionsPage(WebDriver driver, Project project) {
		super(driver, project);
		// TODO Auto-generated constructor stub
	}

	public QuestionNewPage addNewQuestion()
	{
		(new WebDriverWait(driver,waiting)).until(ExpectedConditions.visibilityOf(addQuestionBtn));
		addQuestionBtn.click();
		waitForDialog();
		return new QuestionNewPage(driver);
	}
}
