package ru.devprom.servicedesk.tests;

import static org.testng.Assert.*;
import org.testng.annotations.Test;
import ru.devprom.helpers.DataProviders;
import ru.devprom.servicedesk.pages.IssuesListPage;
import ru.devprom.servicedesk.pages.NewAttachmentPage;
import ru.devprom.servicedesk.pages.ServicedeskPage;
import ru.devprom.servicedesk.pages.ViewIssuePage;

import java.io.File;

import static org.testng.Assert.assertTrue;

/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
public class AttachmentTest extends BaseLoggedInServicedeskTest {

    @Test
    public void attachFile() {
        File randomFile = DataProviders.createRandomTextFile();

        // открываем первую заявку из списка и крепим файл
        IssuesListPage issuesListPage = new ServicedeskPage(driver).goToIssuesList();
        ViewIssuePage issuePage = issuesListPage.openFirstIssueInList();
        NewAttachmentPage attachmentPage = issuePage.clickAttachFileButton();

        attachmentPage.selectFileForUpload(randomFile.getAbsolutePath());
        issuePage = attachmentPage.submit();

        // проверяем значения полей на экране заявки
        assertTrue(issuePage.containsAttachmentWithName(randomFile.getName()), "Attachment is present");
    }

    @Test (dependsOnMethods = "attachFile")
    public void deleteAttachment() {
        ViewIssuePage issuePage = new ViewIssuePage(driver);

        String attachmentName = issuePage.getFirstAttachmentName();
        issuePage.deleteAttachmentWithName(attachmentName);

        // проверяем значения полей на экране заявки
        assertFalse(issuePage.containsAttachmentWithName(attachmentName), "Attachment is not present");
    }
}
