package ru.devprom.pages.project.questions;

import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import ru.devprom.items.Project;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class QuestionNewPage extends SDLCPojectPageBase {

	@FindBy(id = "pm_QuestionSubmitBtn")
	protected WebElement submitBtn;
	
	
	public QuestionNewPage(WebDriver driver) {
		super(driver);
		// TODO Auto-generated constructor stub
	}

	public QuestionNewPage(WebDriver driver, Project project) {
		super(driver, project);
		// TODO Auto-generated constructor stub
	}

	public QuestionsPage createNewQuestion(String content)
	{
		CKEditor we = new CKEditor(driver);
		we.changeText(content);
		submitDialog(submitBtn);
		return new QuestionsPage(driver);
	}
	
	
}
