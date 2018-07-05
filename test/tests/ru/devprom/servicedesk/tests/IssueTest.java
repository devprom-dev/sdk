package ru.devprom.servicedesk.tests;

import org.testng.annotations.Test;
import ru.devprom.helpers.DataProviders;
import ru.devprom.servicedesk.pages.*;

import java.io.File;

import static org.testng.Assert.assertEquals;
import static org.testng.Assert.assertTrue;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
public class IssueTest extends BaseLoggedInServicedeskTest {

    @Test
    public void createIssueWithoutAttachment() {
        IssuesListPage issuesListPage = new ServicedeskPage(driver).goToIssuesList();
        NewIssuePage newIssuePage = issuesListPage.clickNewIssueButton();

        // создаем и отправляем новую заявку
        String issuePriority = newIssuePage.getDifferentPriority();
        String issueTitle = "Issue title " + DataProviders.getUniqueString() + " <b>some html</b>";
        String issueDescription = "Issue description " + DataProviders.getUniqueString() + " <script>alert('ops');</script>";

        newIssuePage.enterIssueTitle(issueTitle);
        newIssuePage.enterIssueDescription(issueDescription);
        newIssuePage.selectPriority(issuePriority);
        ViewIssuePage viewIssuePage = newIssuePage.clickSubmitButton();

        // проверяем значения полей на экране заявки
        assertEquals(viewIssuePage.getIssueTitle(), issueTitle, "Issue title");
        assertEquals(viewIssuePage.getIssueDescription(), issueDescription, "Issue description");
        assertEquals(viewIssuePage.getIssuePriority(), issuePriority, "Issue priority");

        // проверяем, что заявка появилась в списке
        issuesListPage = viewIssuePage.goToIssuesList();
        assertTrue(issuesListPage.containsIssueWithTitle(issueTitle), "New issue should appear in issue list");
    }

    @Test
    public void createIssueWithAttachment() {
        IssuesListPage issuesListPage = new ServicedeskPage(driver).goToIssuesList();
        NewIssuePage newIssuePage = issuesListPage.clickNewIssueButton();

        // создаем и отправляем новую заявку
        String issueTitle = "Issue title " + DataProviders.getUniqueString();
        String issueDescription = "Issue description " + DataProviders.getUniqueString();
        File randomFile = DataProviders.createRandomTextFile();

        newIssuePage.enterIssueTitle(issueTitle);
        newIssuePage.enterIssueDescription(issueDescription);
        newIssuePage.selectFileForUpload(randomFile.getAbsolutePath());
        ViewIssuePage viewIssuePage = newIssuePage.clickSubmitButton();

        // проверяем значения полей на экране заявки
        assertTrue(viewIssuePage.containsAttachmentWithName(randomFile.getName()), "Attachment is present");
    }

    @Test
    public void updateIssue() {
        // открываем на редактирование первую заявку из списка
        IssuesListPage issuesListPage = new ServicedeskPage(driver).goToIssuesList();
        ViewIssuePage issuePage = issuesListPage.openFirstIssueInList();
        EditIssuePage editIssuePage = issuePage.clickEdit();

        // подготовим новые значения атрибутов заявки
        String newTitle = "Issue title " + DataProviders.getUniqueString();
        String newDescription = "Issue description " + DataProviders.getUniqueString();
        String newPriority = editIssuePage.getDifferentPriority();

        // заполняем форму новыми значениями и отправляем
        editIssuePage.enterIssueTitle(newTitle);
        editIssuePage.enterIssueDescription(newDescription);
        editIssuePage.selectPriority(newPriority);
        issuePage = editIssuePage.clickSubmitButton();

        // проверяем значения полей на экране заявки
        assertEquals(issuePage.getIssueTitle(), newTitle, "Issue title");
        assertEquals(issuePage.getIssueDescription(), newDescription, "Issue description");
        assertEquals(issuePage.getIssuePriority(), newPriority, "Issue priority");
    }
}
