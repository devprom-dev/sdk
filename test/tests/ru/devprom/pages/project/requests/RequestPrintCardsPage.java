package ru.devprom.pages.project.requests;

import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import ru.devprom.items.Request;

public class RequestPrintCardsPage {
	private final WebDriver driver;

	RequestPrintCardsPage(WebDriver driver) {
		this.driver = driver;
	}

	public Request[] getPrintedRequests() {
		List<WebElement> cards = driver.findElements(By.className("taskcard"));
		Request[] requests = new Request[cards.size()];
		for (int i = 0; i < requests.length; i++) {
			requests[i] = new Request(
					cards.get(i)
							.findElement(
									By.xpath(".//td[@class='right']/table/tbody/tr[1]/td"))
							.getText(),
					cards.get(i)
							.findElement(
									By.xpath(".//td[@class='left']/div[@class='caption']"))
							.getText(),
					"",
					"",
					cards.get(i)
							.findElement(
									By.xpath(".//td[@class='right']/table/tbody/tr[2]/td"))
							.getText());
			if (!cards
					.get(i)
					.findElement(
							By.xpath(".//td[@class='right']/table/tbody/tr[4]/td"))
					.getText().equals(""))
				requests[i]
						.setEstimation(Double.parseDouble(cards
								.get(i)
								.findElement(
										By.xpath(".//td[@class='right']/table/tbody/tr[4]/td"))
								.getText()));
		}

		return requests;

	}

}
