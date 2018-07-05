package ru.devprom.pages.project.tasks;

import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import ru.devprom.items.RTask;

public class TasksPrintListPage {

	private final WebDriver driver;

	TasksPrintListPage(WebDriver driver) {
		this.driver = driver;
	}

	public RTask[] getPrintedTasks() {
		List<WebElement> rows = driver.findElements(By
				.xpath("html/body/table/tbody/tr"));
		List<WebElement> header = rows.get(0).findElements(By.xpath("./node()"));
		int idN=1;
		int nameN=2;
		int typeN=3;
		int priorityN=4;
		int statusN=5;
		for (int i=0;i<header.size();i++){
			switch (header.get(i).getText()) {
			case "UID":
				idN=i+1;
				break;
			case "Название":
				nameN=i+1;
				break;
			case "Тип":
				typeN=i+1;
				break;
			case "Приоритет":
				priorityN=i+1;
				break;
			case "Состояние":
				statusN=i+1;
				break;
			default:
				break;
			}
		}
		rows.remove(0);
		RTask[] tasks = new RTask[rows.size()];
		for (int i = 0; i < tasks.length; i++) {
			tasks[i] = new RTask(rows.get(i)
					.findElement(By.xpath("./td["+idN+"]")).getText(), rows.get(i)
					.findElement(By.xpath("./td["+nameN+"]")).getText(), rows
					.get(i).findElement(By.xpath("./td["+typeN+"]")).getText(), rows
					.get(i).findElement(By.xpath("./td["+priorityN+"]")).getText(), rows
					.get(i).findElement(By.xpath("./td["+statusN+"]")).getText());
		  //  tasks[i].setExecutor(rows.get(i).findElement(By.xpath("./td[4]")).getText());
		}
		

		return tasks;

	}

}