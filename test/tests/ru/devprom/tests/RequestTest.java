package ru.devprom.tests;

import java.io.File;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

import org.openqa.selenium.By;
import org.openqa.selenium.NoSuchElementException;
import org.openqa.selenium.NotFoundException;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.DataProviders;
import ru.devprom.helpers.FileOperations;
import ru.devprom.items.Document;
import ru.devprom.items.ProductFunction;
import ru.devprom.items.Project;
import ru.devprom.items.RTask;
import ru.devprom.items.Request;
import ru.devprom.items.Spent;
import ru.devprom.items.Template;
import ru.devprom.items.TestScenario;
import ru.devprom.items.User;
import ru.devprom.pages.FavoritesPage;
import ru.devprom.pages.LoginPage;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.admin.ActivitiesPage;
import ru.devprom.pages.admin.UsersListPage;
import ru.devprom.pages.project.ProjectPageBase;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.attributes.AttributeEntityNewPage;
import ru.devprom.pages.project.attributes.AttributeNewPage;
import ru.devprom.pages.project.attributes.AttributeSettingsPage;
import ru.devprom.pages.project.documents.DocumentNewPage;
import ru.devprom.pages.project.documents.DocumentViewPage;
import ru.devprom.pages.project.documents.DocumentsPage;
import ru.devprom.pages.project.functions.FunctionNewPage;
import ru.devprom.pages.project.functions.FunctionsPage;
import ru.devprom.pages.project.requests.RequestDonePage;
import ru.devprom.pages.project.requests.RequestEditPage;
import ru.devprom.pages.project.requests.RequestNewPage;
import ru.devprom.pages.project.requests.RequestPlanningPage;
import ru.devprom.pages.project.requests.RequestPrintCardsPage;
import ru.devprom.pages.project.requests.RequestPrintListPage;
import ru.devprom.pages.project.requests.RequestRejectPage;
import ru.devprom.pages.project.requests.RequestViewPage;
import ru.devprom.pages.project.requests.RequestsImportPage;
import ru.devprom.pages.project.requests.RequestsPage;
import ru.devprom.pages.project.requests.RequestsStatePage;
import ru.devprom.pages.project.settings.ProjectMembersPage;
import ru.devprom.pages.project.settings.StateEditPage;
import ru.devprom.pages.project.settings.TransitionEditPage;
import ru.devprom.pages.project.tasks.TaskCompletePage;
import ru.devprom.pages.project.tasks.TaskViewPage;
import ru.devprom.pages.project.tasks.TasksPage;
import ru.devprom.pages.project.tasks.TasksStatePage;
import ru.devprom.pages.project.testscenarios.TestScenarioTestingPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationNewPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationViewPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationsPage;
import ru.devprom.pages.support.SupportActivitiesPage;
import ru.devprom.pages.support.SupportPageBase;
import ru.devprom.pages.support.SupportRequestsPage;

public class RequestTest extends ProjectTestBase {
	private String coordinator = "WebTestUser";
	private Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
			new Template(
					this.waterfallTemplateName));
	
	/**
	 * This test creates another request and then check if it's displayed
	 * correctly in the list
	 * @throws InterruptedException 
	 */
	@Test
	public void createRequestAndCheck() throws InterruptedException {
		new PageBase(driver).gotoProject(webTest);
		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		Request testRequest = new Request("TestCR-"
				+ DataProviders.getUniqueString(), "dd", "Низкий", 10, "Доработка");
		RequestNewPage ncrp = mip.clickNewCR();
		mip = ncrp.createCRShort(testRequest);
		RequestViewPage rv = mip.clickToRequest(testRequest.getId());
		RequestEditPage rep = rv.gotoEditRequest();
		rep.updateRequest(testRequest);
		rep.addWatcher(user);
		rv = rep.saveEdited();
		mip = rv.gotoRequests();
		mip.showAll();
		Request createdRequest = mip.findRequestById(testRequest.getId());
		FILELOG.debug(createdRequest);
		Assert.assertEquals(createdRequest, testRequest);
	}

	/**
	 * The method checks the correspondence between real requests list and print
	 * list preview
	 */
	@Test
	public void testRequestsPrintList() {
		new PageBase(driver).gotoProject(webTest);
		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		mip.addColumn("Type");
		mip.showAll();
		Request[] requests = mip.readAllRequests();
		Arrays.sort(requests);
		FILELOG.debug("Requests: ");
		for (Request r : requests) {
			FILELOG.debug(r);
		}
		RequestPrintListPage plp = mip.clickPrintList();
		Request[] printedRequests = plp.getPrintedRequests();
		Arrays.sort(printedRequests);
		FILELOG.debug("Printed requests: ");
		for (Request r : printedRequests) {
			FILELOG.debug(r);
		}
		driver.navigate().back();
		Assert.assertEquals(printedRequests, requests);
		for (int i = 0; i < requests.length; i++) {
			Assert.assertEquals(printedRequests[i].getState(),requests[i].getState());
			Assert.assertTrue(requests[i].getPriority().contains(printedRequests[i].getPriority()));
		}
	}

	/**
	 * The method checks the correspondence between real requests list and print
	 * cards preview
	 */
	@Test
	public void testRequestsPrintCards() {
		new PageBase(driver).gotoProject(webTest);
		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		mip.addColumn("Type");
		mip.showAll();
		Request[] requests = mip.readAllRequests();
		Arrays.sort(requests);
		FILELOG.debug("Requests: ");
		for (Request r : requests) {
			FILELOG.debug(r);
		}
		RequestPrintCardsPage pcp = mip.clickPrintCards();
		Request[] printedRequests = pcp.getPrintedRequests();
		Arrays.sort(printedRequests);
		FILELOG.debug("Printed requests: ");
		for (Request r : printedRequests) {
			FILELOG.debug(r);
		}
		driver.navigate().back();
		Assert.assertEquals(printedRequests, requests);
		for (int i = 0; i < requests.length; i++) {
			Assert.assertTrue(requests[i].getPriority().contains(printedRequests[i].getPriority()));
		}
	}

	/**
	 * The method checks exported to Excel data and compare it with the Request
	 * board data
	 * 
	 * @throws Exception
	 */
	@Test
	public void exportToExcelTest() throws Exception {
		new PageBase(driver).gotoProject(webTest);
		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		mip.addColumn("Type");
		mip.showAll();
		driver.navigate().refresh();
		Request[] rExisted = mip.readAllRequests();
		Arrays.sort(rExisted);
		for (Request r : rExisted) {
			FILELOG.debug(r);
		}
		Request[] rExported = mip.exportToExcel();
		Arrays.sort(rExported);
		for (Request r : rExported) {
			FILELOG.debug(r);
		}
		Assert.assertEquals(rExported, rExisted);
		for (int i = 0; i < rExisted.length; i++) {
			Assert.assertEquals(rExported[i].getState(), rExisted[i].getState());
			Assert.assertTrue(rExisted[i].getPriority().contains(rExported[i].getPriority()));
		}
	}

	/** The test deletes all the requests found in range of IDs 
	 * @throws InterruptedException */
	@Test(priority = 10)
	public void deleteRequests() throws InterruptedException {
		new PageBase(driver).gotoProject(webTest);
		Request testRequest = createRequest();
		int deleteCount = 1;
		int startFrom = Integer.parseInt(testRequest.getId().substring(2,
				testRequest.getId().length()));
		List<Request> requestsDeleted = new ArrayList<Request>();
		List<Request> rBefore;
		List<Request> rAfter;
		RequestViewPage rvp;
		FILELOG.debug(startFrom);
		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		mip.addColumn("Type");
		mip.showAll();
		rBefore = new ArrayList<Request>(Arrays.asList(mip.readAllRequests()));
		for (int i = 0; i < deleteCount; i++) {
			try {
				requestsDeleted
						.add(mip.findRequestById("I-" + (i + startFrom)));
				rvp = mip.clickToRequest("I-" + (i + startFrom));
				mip = rvp.deleteRequest();
				mip.gotoRequests();
			} catch (NoSuchElementException e) {
				FILELOG.warn("Request with ID 'I-" + (i + startFrom)
						+ "' was not found");
				mip = (new SDLCPojectPageBase(driver)).gotoRequests();
			}
			mip.showAll();
		}
		mip.addColumn("Type");
		rAfter = new ArrayList<Request>(Arrays.asList(mip.readAllRequests()));
		rBefore.removeAll(requestsDeleted);
		Request[] requestsBefore = rBefore.toArray(new Request[0]);
		Request[] requestsAfter = rAfter.toArray(new Request[0]);
		Arrays.sort(requestsBefore);
		Arrays.sort(requestsAfter);
		FILELOG.debug("Requests Before - Requests Deleted:");
		for (Request r : requestsBefore) {
			FILELOG.debug(r);
		}
		FILELOG.debug("Requests After:");
		for (Request r : requestsAfter) {
			FILELOG.debug(r);
		}
		Assert.assertEquals(requestsAfter, requestsBefore);
	}

	/**
	 * The test edits all of the request's parameters and save the changes, then
	 * read them from the view page and compare
	 * @throws InterruptedException 
	 */
	@Test
	public void editRequest() throws InterruptedException {
		new PageBase(driver).gotoProject(webTest);
		DocumentsPage dp = (new SDLCPojectPageBase(driver)).gotoDocuments();
		DocumentNewPage ndp = dp.clickNewDoc();
		Document tDoc = new Document("TestDoc"
				+ DataProviders.getUniqueString(), "Some document body");
		DocumentViewPage dvp = ndp.createNewDoc(tDoc);
		RequestsPage mip = dvp.gotoRequests();
		Request linkedRequest = createRequest();
		Request testRequest = createRequest();
		
		mip.showAll();

		testRequest.setState(mip.findRequestById(testRequest.getId())
				.getState());
		RequestViewPage rvp = mip.clickToRequest(testRequest.getId());

		RequestEditPage rep = rvp.gotoEditRequest();
		rep.addSpentTimeRecord(new Spent("", 2, user,
				"some test description"));
		testRequest.addSpentTimeRecord("", 2, user,
				"some test description");
		rep.addSpentTimeRecord(new Spent("", 2, user,
				"some test description 2"));
		testRequest.addSpentTimeRecord("", 2, user,
				"some test description 2");
		testRequest.setVersion("0.1");
		rep.addLinkedReqs(linkedRequest.getName(),
				Request.getRandomLinkType());
		RTask task = new RTask("Task" + DataProviders.getUniqueString(),
				coordinator, RTask.getRandomType(),
				testRequest.getEstimation() - 9);
		FILELOG.debug("Будет добавлена задача: " + task);
		rep.addTask(task);
		testRequest.addTask(task);
		String testDoc = Request.getRandomTestDoc();
		//rep.addTestDoc(testDoc);
		testRequest.addTestDocs(testDoc);
		rep.addDocs(tDoc.getName());
		testRequest.addDocs(tDoc);
		rvp = rep.saveEdited();

		Request savedRequest = rvp.readRequest();

		Assert.assertEquals(savedRequest, testRequest);
		Assert.assertEquals(savedRequest.getType(), testRequest.getType());
		Assert.assertTrue(savedRequest.getState().contains(
				testRequest.getState()));
		
		Assert.assertTrue(savedRequest.getPriority().contains(testRequest.getPriority()));
		Assert.assertEquals(savedRequest.getPfunction(),
				testRequest.getPfunction());
		Assert.assertEquals(savedRequest.getEstimation(),
				testRequest.getEstimation());
		Assert.assertEquals(savedRequest.getOriginator(),
				testRequest.getOriginator());
	}

	/**
	 * The test creates new Request, then some comments and answers to comments,
	 * then read them and check the text
	 * @throws InterruptedException 
	 */
	@Test
	public void addCommentTest() throws InterruptedException {
		String testComments[] = { "First comment", "Second comment",
				"Answer to first comment" };
		new PageBase(driver).gotoProject(webTest);
		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		Request testRequest = createRequest();
		
		mip.showAll();
		RequestViewPage rvp = mip.clickToRequest(testRequest.getId());
		rvp = rvp.addComment(testComments[0]);
		int shift = rvp.readFirstInPageCommentNumber();
		rvp = rvp.addComment(testComments[1]);
		rvp = rvp.addAnswerToComment(shift, testComments[2]);
		for (int i = 0; i < testComments.length; i++) {
			(new WebDriverWait(driver,waiting)).until(
					ExpectedConditions.presenceOfElementLocated(By.xpath("//div[contains(@class,'comment-text') and contains(.,'"+testComments[i]+"')]"))
					);
		}
		File image = FileOperations.createPNG("Pic"+DataProviders.getUniqueStringAlphaNum()+".png");
		rvp = rvp.addCommentWithAttachment("Comment with a picture", image.getAbsolutePath());
		(new WebDriverWait(driver,waiting)).until(
				ExpectedConditions.presenceOfElementLocated(By.xpath("//div[contains(@class,'comment-text') and contains(.,'"+image.getName()+"')]"))
				);
        Assert.assertTrue(rvp.isPictureFromCommentOpens(image.getName()), "Изображение не открывается");
	}

	/**
	 * The method perform Edit Type mass operation on a few Requests, and then
	 * checks the types
	 * @throws InterruptedException 
	 */
	@Test(priority = 8)
	public void massOperationsTest() throws InterruptedException {
		new PageBase(driver).gotoProject(webTest);
		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
        Request testRequest1 = createRequest();
        Request testRequest2 = createRequest();
		mip.showAll();
		mip.checkRequest(testRequest1.getId());
		mip.checkRequest(testRequest2.getId());
		mip = mip.massChangeType("Ошибка");
		testRequest1.setType("Ошибка");
		testRequest2.setType("Ошибка");
		Assert.assertEquals(
				mip.getRequestProperty(testRequest1.getId(), "type"),
				"Ошибка");
		Assert.assertEquals(
				mip.getRequestProperty(testRequest2.getId(), "type"),
				"Ошибка");
	}

	/**
	 * Test creates 2 Requests - one blocks another, then tests if dependencies
	 * are checked when trying to complete
	 */
	 @Test
	public void useOfPreconditionsForTransitionsBetweenStates() {
		
		new PageBase(driver).gotoProject(webTest);
		RequestsStatePage rsp = (new SDLCPojectPageBase(driver))
				.gotoRequestsStatePage();
		TransitionEditPage adtdp = rsp.clickChangeTransition("Добавлено","Выполнить > Выполнено");
		adtdp = adtdp.addPrecondition("Завершены блокирующие пожелания");
		adtdp.saveChanges();
		rsp = new RequestsStatePage(driver);
		RequestsPage mip = rsp.gotoRequests();
		Request firstR = new Request("TestFirstPreReq"
				+ DataProviders.getUniqueString());
		Request secondR = new Request("TestSecondPreReq"
				+ DataProviders.getUniqueString());
		mip = rsp.gotoRequests();
		RequestNewPage ncrp = mip.clickNewCR();
		mip = ncrp.createCRShort(firstR);
		FILELOG.debug("First Request ID:" + firstR.getId());
		ncrp = mip.clickNewCR();
		mip = ncrp.createCRShort(secondR);
		FILELOG.debug("Second Request ID:" + secondR.getId());
		RequestViewPage	rv = mip.clickToRequest(secondR.getId());
		RequestEditPage	rep = rv.gotoEditRequest();
		rep.addLinkedReqs(firstR.getId(), "Блокирует");
		rv = rep.saveEdited();
		mip = rv.gotoRequests();
		mip.showAll();
		RequestViewPage rvp = mip.clickToRequest(firstR.getId());
		Assert.assertFalse(rvp.isOperationAvailable("Выполнить"), "Operation still is enabled");
		mip = rvp.gotoRequests();
		mip.showAll();
		rvp = mip.clickToRequest(secondR.getId());
		RequestDonePage rdp = rvp.completeRequest();
		rvp = rdp.complete("done", "0.1");
		mip = rvp.gotoRequests();
		mip.showAll();
		rvp = mip.clickToRequest(firstR.getId());
		rdp = rvp.completeRequest();
		Assert.assertEquals(rdp.getMessage(), "");
		rvp = rdp.complete("done", "0.1");

	}

	/**
	 * Creates-completes-rejects-and completes again. Every complete adds 2
	 * hours spent on the request. Then we check how much spent records we have
	 * after all and read and check their parameters.
	 */
	@Test
	public void testRequestFactTimeOnCompletion() {

		Request testR = new Request("TestR" + DataProviders.getUniqueString(),
				"description", Request.getHighPriority(), 10, "Доработка");
		Spent firstSpent = new Spent("", 2, coordinator, "First spent");
		Spent secondSpent = new Spent("", 2, coordinator, "Second spent");
		new PageBase(driver).gotoProject(webTest);
		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		RequestNewPage nrp = mip.clickNewCR();
		mip = nrp.createNewCR(testR);
		RequestViewPage rvp = mip.clickToRequest(testR.getId());
		RequestDonePage rdp = rvp.completeRequest();
		rvp = rdp.complete("DoneBefore", "0.1", firstSpent);
		RequestRejectPage rrp = rvp.rejectRequest();
		rvp = rrp.reject("Reject request for test");
		rdp = rvp.completeRequest();
		rvp = rdp.complete("DoneBefore", "", secondSpent);
		List<Spent> spent = rvp.readSpentRecords();
		Assert.assertEquals(spent.size(),2);
		Assert.assertEquals(spent.get(0).hours + spent.get(1).hours, firstSpent.hours + secondSpent.hours);
	}

	/**
	 * Test creates new Release, include it to Release 0, set filter to show
	 * only Release=0 requests Then get the request back to Journal (exclude
	 * from Release 0) and checks again with the filter
	 * @throws InterruptedException 
	 */
	@Test
	public void resetFieldsOnTransitioningBetweenStates() throws InterruptedException {
		new PageBase(driver).gotoProject(webTest);
		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		Request testR = new Request("TestR" + DataProviders.getUniqueString(),
				"description", Request.getHighPriority(), 10, "Доработка");
		RequestNewPage nrp = mip.clickNewCR();
		mip = nrp.createNewCR(testR);
		RequestViewPage rvp = mip.clickToRequest(testR.getId());
		rvp = rvp.includeToRelease("0");
		mip = rvp.gotoRequests();
		mip.showAll();
		mip.addFilter("release");
		mip.turnOnFilter("0", "Релиз");
		Assert.assertTrue(mip.isRequestPresent(testR.getId()));

		rvp = mip.clickToRequest(testR.getId());
		rvp = rvp.backToJournal("Get it back");
		mip = rvp.gotoRequests();
		mip.showAll();

		rvp = mip.clickToRequest(testR.getId());
		RequestEditPage rep = rvp.gotoEditRequest();
		rep.setRelease("");
		rvp = rep.saveEdited();

		mip = rvp.gotoRequests();
		mip.showAll();
		mip.addFilter("release");
		mip.turnOnFilter("0", "Релиз");
		Assert.assertFalse(mip.isRequestPresent(testR.getId()));
		
		//cleanup
		mip.removeFilter("release");
	}

	/**
	 * Plan request, complete it's task, check the request is complete too. Then
	 * change system settings not too complete a request automatically when it's
	 * task is completed Plan another request, complete it's tasks, check the
	 * request is still planned
	 */
	@Test
	public void testRemoveSystemActionWhenCompletingRequestTask() {
		
		// 1. Complete Request after Task is done turned on
		new PageBase(driver).gotoProject(webTest);
		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		Request testRequest1 = new Request("TestCR-"
				+ DataProviders.getUniqueString(), "dd", "Низкий", 10, "Доработка");
		RequestNewPage nrp = mip.clickNewBug();
		mip = nrp.createNewCR(testRequest1);
		
		RequestViewPage rvp = mip.clickToRequest(testRequest1.getId());
		RequestPlanningPage rpp = rvp.planRequest();
		rpp.hideTaskbox();
		rpp.hideTaskbox();
		rpp.hideTaskbox();
		rpp.hideTaskbox();
		rpp.hideTaskbox();
		RTask testTask = new RTask("Test task"
				+ DataProviders.getUniqueString(),
				coordinator, "Анализ", 2);
		rpp.fillTask(1, testTask.getName(), testTask.getType(),
				testTask.getExecutor(), testTask.getEstimation());
		rvp = rpp.savePlanned();
		testTask.setId(rvp.getLastActivityID('T'));
		TasksPage mtp = rvp.gotoTasks();
		TaskViewPage tep = mtp.clickToTask(testTask.getId());
		TaskCompletePage ctp = tep.completeTask();
		tep = ctp.complete(testTask);
		mip = tep.gotoRequests();
		mip.showAll();
		Assert.assertEquals(
				mip.getRequestProperty(testRequest1.getId(), "state"),
				"Выполнено");

		// Turn OFF "Complete Request after Task is done" system setting
		TasksStatePage tsp = (new SDLCPojectPageBase(driver))
				.gotoTasksStatePage();
		 StateEditPage mdsp = tsp.modifyCompleted();
		mdsp.removeAction("Автоматически выполнить пожелание после выполнения всех задач по нему");
		mdsp.saveChanges();
		
		// 2. Complete Request after Task is done turned off
		mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		Request testRequest2 = new Request("TestCR-"
				+ DataProviders.getUniqueString(), "dd", "Низкий", 10, "Доработка");
		nrp = mip.clickNewBug();
		mip = nrp.createNewCR(testRequest2);
		
		mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		rvp = mip.clickToRequest(testRequest2.getId());
		rpp = rvp.planRequest();
		rpp.hideTaskbox();
		rpp.hideTaskbox();
		rpp.hideTaskbox();
		rpp.hideTaskbox();
		rpp.hideTaskbox();
		testTask = new RTask("Test task" + DataProviders.getUniqueString(),
				coordinator, "Анализ", 2);
		rpp.fillTask(1, testTask.getName(), testTask.getType(),
				testTask.getExecutor(), testTask.getEstimation());
		rvp = rpp.savePlanned();
		testTask.setId(rvp.getLastActivityID('T'));
		mtp = rvp.gotoTasks();
		tep = mtp.clickToTask(testTask.getId());
		ctp = tep.completeTask();
		tep = ctp.complete(testTask);
		mip = tep.gotoRequests();
		mip.showAll();
		Assert.assertEquals(
				mip.getRequestProperty(testRequest2.getId(), "state"),
				"Запланировано");

		// Rollback system settings
		tsp = (new SDLCPojectPageBase(driver)).gotoTasksStatePage();
		mdsp = tsp.modifyCompleted();
		mdsp.addAction("Автоматически выполнить пожелание после выполнения всех задач по нему");
		mdsp.saveChanges();

	}

	/**
	 * Adding new attribute to Bug Request template, unique value is required.
	 * Then creating 2 Requests with the same value of the attribute. Checking
	 * for error message. Then changing the value for second Request and it
	 * should pass. Clean up.
	 */
	@Test
	public void testUniqueUserAttributeOnErrorRequest() {
		new PageBase(driver).gotoProject(webTest);
		AttributeSettingsPage asp = (new SDLCPojectPageBase(driver))
				.gotoAttributeSettings();
		AttributeEntityNewPage naep = asp.addNewAttribute();
		AttributeNewPage nap = naep.selectEntity("request:bug", "Строка текста");
		nap.setDefaultStringValue(DataProviders.getUniqueString());
		String p = DataProviders.getUniqueStringAlphaNum();
		String name = "MyTestAttr" + DataProviders.getUniqueString();
		nap.enterNewAttribute(name, "mta" + p, "Description of "
				+ DataProviders.getUniqueString(), true);
		asp = nap.createNewAttribute();
		RequestsPage mip = asp.gotoRequests();
		Request testRequest = new Request("TestCR-"
				+ DataProviders.getUniqueString(), "dd", "Низкий", 10, null);
		RequestNewPage nrp = mip.clickNewBug();
		mip = nrp.createCRShort(testRequest);
		
		RequestViewPage rv = mip.clickToRequest(testRequest.getId());
		RequestEditPage rep = rv.gotoEditRequest();
		rep.setUserStringAttribute("mta" + p, "Test value");
		rv = rep.saveEdited();
		mip = rv.gotoRequests();
		
		Request testRequest2 = new Request("TestCR-"
				+ DataProviders.getUniqueString(), "dd", "Низкий", 0, null);
		nrp = mip.clickNewBug();
		nrp.setUserStringAttribute("mta" + p, "Test value");
		Assert.assertEquals(nrp.createWithError(testRequest2),
				"Внимание! Значение поля " + "\"" + name + "\" должно быть уникальным");
		// Check it successfully creates with another value
		nrp.setUserStringAttribute("mta" + p, "New value");
		mip = nrp.createNewCR(testRequest2);
		// Clean attributes
		asp = mip.gotoAttributeSettings();
		asp = asp.deleteAttribute("mta" + p);
	}

	/**
	 * Test creates test scenario, and includes it to a request. Then completes
	 * the request, fails the test, rejects the requests, completes again and
	 * pass the test. In the end it checks test results records for correct data
	 */
	@Test
	public void testRequestTestingResults() {
		new PageBase(driver).gotoProject(webTest);
		TestSpecificationsPage tsp = (new SDLCPojectPageBase(driver))
				.gotoTestPlans();
		TestSpecificationNewPage ntsp = tsp.createNewSpecification();
		TestScenario testPlan = new TestScenario("TestPlan"
				+ DataProviders.getUniqueString());
		TestSpecificationViewPage tspecp = ntsp.create(testPlan);
		TestScenario testScenario = new TestScenario("TestScenario"
				+ DataProviders.getUniqueString());
		tspecp.addNewTestScenario(testScenario);
		RequestsPage mip = tspecp.gotoRequests();
		Request testRequest = new Request("TestCR-"	+ DataProviders.getUniqueString(), "dd", "Низкий", 10, "Доработка");
		RequestNewPage nrp = mip.clickNewBug();
		mip = nrp.createNewCR(testRequest);
		RequestViewPage rvp = mip.clickToRequest(testRequest.getId());
		RequestEditPage rep = rvp.gotoEditRequest();
		rep.addTestDoc(testScenario.getName());
		rvp = rep.saveEdited();
		RequestDonePage rdp = rvp.completeRequest();
		rvp = rdp.complete("Some comment", "0.1");
		driver.navigate().refresh();
		TestScenarioTestingPage rtp = rvp.beginTest("0.1", "");
		rtp.failTest(testScenario);
		driver.navigate().refresh();
		mip = rtp.gotoRequests();
		mip.showAll();
		rvp = mip.clickToRequest(testRequest.getId());
		RequestRejectPage rrp = rvp.rejectRequest();
		rvp = rrp.reject("comment");
		Assert.assertEquals(rvp.readState(), "Добавлено");
		rdp = rvp.completeRequest();
		rvp = rdp.complete("Some comment 2", "");
		driver.navigate().refresh();
		rtp = rvp.beginTest("0.1", "");
		rtp.passTest(testScenario);
		driver.navigate().refresh();
		mip = rtp.gotoRequests();
		mip.showAll();
		rvp = mip.clickToRequest(testRequest.getId());
		String[] testResults = rvp.readTestResults();
		Assert.assertEquals(testResults.length, 4);
		Assert.assertTrue(testResults[0].contains(testScenario.getName()));
		Assert.assertTrue(testResults[0].contains("Провален"));
		Assert.assertTrue(testResults[1].contains(testScenario.getName()));
		Assert.assertTrue(testResults[1].contains("Пройден"));
	}
	

	/**The test creates a new Project, than switches to default one and creates a new Request, attaches files,
	 * and duplicate it to another project, previously created. Finally checks that all the information successfully copied.   
	 */
	@Test
	public void duplicateRequest(){
		PageBase page = new PageBase(driver);
		
		//Development Project
		String p = DataProviders.getUniqueString();
		Project devTest = new Project("DevTest"+p, "devtest"+DataProviders.getUniqueStringAlphaNum(),new Template(this.waterfallTemplateName));
		
		//Default Test Project
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		
		//Create a Development Project
		ProjectNewPage pnp = page.createNewProject();
		ProjectPageBase sdlcFirstPage = (ProjectPageBase) pnp.createNew(devTest);
		
		//Go to Default Project
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) sdlcFirstPage.gotoProject(webTest);
		RequestsPage mip = favspage.gotoRequests();
		
		//Creating new Request with an attachment
		Request request = new Request("DoubleRequest-"+DataProviders.getUniqueString(), "This request created by passRequestToDevepolmentProject() test",
				Request.getHighPriority(), Request.getRandomEstimation(), "Доработка");
		RequestNewPage rnp = mip.clickNewCR();
			 
        mip = rnp.createCRShort(request);
		
		RequestViewPage	rvp = mip.clickToRequest(request.getId());
		RequestEditPage	rep = rvp.gotoEditRequest();
		rep.updateRequest(request);
		//Prepare attachment file
		String attachment = "Attachment.png";
		File image = FileOperations.createPNG(attachment);
		request.addAttachments(attachment);
		rep.addAttachment(image);
		
		rvp = rep.saveEdited();
		
		//Create another Request object by clone()
		Request duplicated = request.clone();
		
		//Duplicate the request (id of te clone request will be changed)
		rvp = rvp.duplicateRequest(devTest.getName());
		
		//Open the duplicate request and check it's name and attachments
		favspage = (SDLCPojectPageBase) rvp.gotoProject(devTest);
		duplicated.setId(favspage.getLastActivityID('I'));
		mip = favspage.gotoRequests();
		rvp = mip.clickToRequest(duplicated.getId());
		
		Assert.assertEquals(rvp.readName(), duplicated.getName(), "Incorrect duplicated Request name");
		Assert.assertTrue(rvp.readPriority().contains(duplicated.getPriority()), "Incorrect duplicated Request priority");
		
		//close duplicated request
		RequestDonePage rdp = rvp.completeRequest();
		rvp = rdp.complete("Fixed", "0.1");
		
		//Open the request list in default project and check the data in column
		favspage = (SDLCPojectPageBase) rvp.gotoProject(webTest);
				mip = favspage.gotoRequests();
		        mip.addColumn("Links");
		       String linked = mip.getRequestProperty(request.getId(), "linked issues");
		       Assert.assertEquals(linked, duplicated.getId());
		       Assert.assertTrue(mip.isLinkedIssueCompleted(request.getId(), duplicated.getId()));
		       mip.gotoProject(webTest);
	}
	

	/**The test creates a new Project, than switches to default one and creates a new Request, attaches files,
	 * and moves it to another project, previously created. Finally checks that all the data saved successfully.   
	 */
	@Test
	public void moveRequestToAnotherProject(){
		
		PageBase page = new PageBase(driver);
		
		//Development Project
		String p = DataProviders.getUniqueString();
		Project devTest = new Project("DevTest"+p, "devtest"+DataProviders.getUniqueStringAlphaNum(),
				new Template(this.waterfallTemplateName));
		
		//Default Test Project
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		
		//Create another Project
		ProjectNewPage pnp = page.createNewProject();
		ProjectPageBase sdlcFirstPage = (ProjectPageBase) pnp.createNew(devTest);
		
		//Go to Default Project
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) sdlcFirstPage.gotoProject(webTest);
		RequestsPage mip = favspage.gotoRequests();
		
		//Creating new Request with an attachment
		Request request = new Request("DoubleRequest-"+DataProviders.getUniqueString(), "This request created by passRequestToDevepolmentProject() test",
				Request.getHighPriority(), Request.getRandomEstimation(), "Доработка");
		RequestNewPage rnp = mip.clickNewCR();
		mip = rnp.createCRShort(request);
		
		RequestViewPage	rvp = mip.clickToRequest(request.getId());
		RequestEditPage	rep = rvp.gotoEditRequest();
		rep.updateRequest(request);
		
		//Prepare attachment file
		String attachment = "Attachment.png";
		File image = FileOperations.createPNG(attachment);
		request.addAttachments(attachment);
		rep.addAttachment(image);
		
		rvp = rep.saveEdited();
		
		//Move Request to devTest
		mip = rvp.moveRequest(devTest.getName());
		mip.showAll();
		Assert.assertFalse(mip.isRequestPresent(request.getId()), "The request is still present in the original project");
		
		//Go to devTest and find replaced Request
		favspage = (SDLCPojectPageBase) mip.gotoProject(devTest);
		mip = favspage.gotoRequests();
		mip.showAll();
		Assert.assertTrue(mip.isRequestPresentByName(request.getName()), "Can't find the Request in the target project");
		Request movedRequest = mip.findRequestByName(request.getName());
		
		//Check Request properties
	    rvp = mip.clickToRequest(movedRequest.getId());
		Assert.assertTrue(rvp.readPriority().contains(request.getPriority()), "Incorrect Request Priority");
		Assert.assertTrue(rvp.readAttachments().containsAll(request.getAttachments()), "Some of the attachments are missed");
		Assert.assertTrue(request.getAttachments().containsAll(rvp.readAttachments()), "Redundant attachements detected");
		mip.gotoProject(webTest);
	}
		
	

	/**The test creates a new Request, then adds 2 Tasks to it, then plan the Request to the current Iteration and creates one more Task.
	 *  Checks that all 3 tasks a presented in the Request after planning.
	 */
	@Test
	public void advanceRequestDecomposition(){
		new PageBase(driver).gotoProject(webTest);
		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		Request testRequest = new Request("TestCR-"
				+ DataProviders.getUniqueString(), "Advance Decomposition Request", "Низкий", 10, "Доработка");
		RequestNewPage rnp = mip.clickNewCR();
		mip = rnp.createCRShort(testRequest);
		RequestViewPage rvp = mip.clickToRequest(testRequest.getId());
		RequestEditPage rep = rvp.gotoEditRequest();
		String p = DataProviders.getUniqueString();
		RTask task1 = new RTask("Decomposition"+p+" - 1", coordinator, "Анализ", 1.0);
		RTask task2 = new RTask("Decomposition"+p+" - 2", coordinator, "Проектирование", 1.0);
		RTask task3 = new RTask("Decomposition"+p+" - 3", coordinator, "Тестирование", 1.0);
		rep.addTask(task1);
		rep.addTask(task2);
		rvp = rep.saveEdited();
		RequestPlanningPage rpp = rvp.planRequest();
		Assert.assertEquals(rpp.getTasksNumber(), 4, "Количество существующих задач неверно");
		rpp.hideTaskbox();
		rpp.hideTaskbox();
		rpp.hideTaskbox();
		rpp.hideTaskbox();
		rpp.addTask(task3);
		rvp = rpp.savePlanned();
		List<RTask> readTasks = rvp.readTasks();
		List<RTask> createdTasks = new ArrayList<RTask>();
		createdTasks.add(task1);
		createdTasks.add(task2);
		createdTasks.add(task3);
		
		Assert.assertEquals(readTasks.size(), createdTasks.size(), "В сохраненном Пожелании неправильное число Задач");
		for (int i=0;i<readTasks.size(); i++){
			Assert.assertEquals(readTasks.get(i).getName(), createdTasks.get(i).getName(), "Некорректное имя у Задачи номер " + i);
			Assert.assertEquals(readTasks.get(i).getType(), createdTasks.get(i).getType(), "Некорректный тип у Задачи номер " + i);					
		}		
	}
	
	

	/**The test creates 2 Requests, and completes them using mass operation menu
	 * @throws InterruptedException 
	 */
	@Test
	public void massCompletion() throws InterruptedException{
		Request testRequest1 = createRequest();
		Request testRequest2 = createRequest();
		new PageBase(driver).gotoProject(webTest);
		RequestsPage mip = new RequestsPage(driver);
		mip.checkRequest(testRequest1.getId());
		mip.checkRequest(testRequest2.getId());
		mip = mip.massComplete("0.1", "Mass completion test");
		mip = mip.selectFilterValue("state", "Не завершено");
		Assert.assertFalse(mip.isRequestPresent(testRequest1.getId()), "Пожелание "+testRequest1.getId()+" обнаружено в списке невыполненных");
		Assert.assertFalse(mip.isRequestPresent(testRequest2.getId()), "Пожелание "+testRequest2.getId()+" обнаружено в списке невыполненных");
	}
	
	private Request createRequest() throws InterruptedException{
	RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		
			Request testRequest = new Request("TestCR-"
					+ DataProviders.getUniqueString(),
					"some description for my test change request",
					Request.getHighPriority(), Request.getRandomEstimation(),
					"Доработка");
			testRequest.setOriginator(coordinator);
			RequestNewPage ncrp = mip.clickNewCR();
			
			mip = ncrp.createCRShort(testRequest);
			FILELOG.debug("Created Request: " + testRequest.getId());
			
			RequestViewPage	rv = mip.clickToRequest(testRequest.getId());
			RequestEditPage	rep = rv.gotoEditRequest();
			rep.updateRequest(testRequest);
			rv = rep.saveEdited();
			mip = rv.gotoRequests();
			return testRequest;
	}
	
	
	/**Создает Пожелание, дублирует его в проект разработки, меняет состояние Пожелания на Запланировано
	 * и проверяет, изменилось ли состояние дубликата, согласно настройкам системных действий
	 */
	 @Test
	public void checkSystemActionChangeDuplicateState() {
		PageBase page = new PageBase(driver);
		
		//Development Project
		String p = DataProviders.getUniqueString();
		Project devTest = new Project("DevTest"+p, "devtest"+DataProviders.getUniqueStringAlphaNum(),new Template(this.waterfallTemplateName));
		
		//Support Project
		Project supportProject = new Project("SupportTest"+p, "support_test"+DataProviders.getUniqueStringAlphaNum(),
				new Template(this.supportTemplateName));
		
		//Tasks
		String index = DataProviders.getUniqueString();
		Request srequest = new Request("SupportRequest" + index);
			
		//Create a Development Project
		ProjectNewPage pnp = page.createNewProject();
		pnp.createNew(devTest);
		
		//Create Support Project
		ProjectNewPage npp = (new PageBase(driver)).createNewProject();
		SupportPageBase firstPage = (SupportPageBase) npp
				.createNew(supportProject);
		FILELOG.debug("Created new project " + supportProject.getName());
		
		//Create new Support Request
		SupportRequestsPage srp = firstPage.gotoRequests();
		srp = srp.addNewRequestShort(srequest);
	    
		//Duplicate Task
		srp = srp.duplicateInProject(srequest, devTest.getName());
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) srp.gotoProject(devTest);
		RequestsPage mip = favspage.gotoRequests();
		Request duplicated = mip.findRequestByName(srequest.getName());
	    
	    //Set system action for planned
	    RequestsStatePage rsp =  mip.gotoRequestsStatePage();
        StateEditPage rsep = rsp.editState("Запланировано");
		rsep.addSystemAction("Выполнить действие над исходными пожеланиями: Взять в работу");
		rsep.saveSystemAction();
		rsp = new RequestsStatePage(driver);
		rsep = rsp.editState("Выполнено");
		rsep.addSystemAction("Выполнить действие над исходными пожеланиями: Завершить");
		rsep.saveSystemAction();
		rsp = new RequestsStatePage(driver);	
		
		//Plan duplicate
		mip = rsp.gotoRequests();
		mip.showAll();
		RequestViewPage rvp = mip.clickToRequest(duplicated.getId());
		RequestPlanningPage rpp = rvp.planRequest();
		rpp.hideTaskbox();
		rpp.hideTaskbox();
		rpp.hideTaskbox();
		rpp.hideTaskbox();
		rpp.hideTaskbox();
		RTask testTask = new RTask("Test task"
				+ DataProviders.getUniqueString(),
				coordinator, "Анализ", 2);
		rpp.fillTask(1, testTask.getName(), testTask.getType(),
				testTask.getExecutor(), testTask.getEstimation());
		rvp = rpp.savePlanned();
	    
		//Go to Support Project and check the task state
		firstPage = (SupportPageBase) rvp.gotoProject(supportProject);
		SupportActivitiesPage sap = firstPage.gotoActivities();
		rvp = sap.clickToTask(srequest.getId());
		Assert.assertEquals(rvp.readState(),"В работе", "Неправильное состояние дубликата");
		
		//Goto development Project and complete the task
		favspage = (SDLCPojectPageBase) rvp.gotoProject(devTest);
		mip = favspage.gotoRequests();
		mip.showAll();
		rvp = mip.clickToRequest(duplicated.getId());
		rvp = rvp.completeRequest("0.1");
		
		//Goto duplicate and check it's state
		firstPage = (SupportPageBase) rvp.gotoProject(supportProject);
		sap = firstPage.gotoActivities();
		rvp = sap.clickToTask(srequest.getId());
		Assert.assertEquals(rvp.readState(),"Выполнено", "Неправильное состояние дубликата");
	}	
	
	@Test(description="S-1960")
	public void massDuplicate() {
      PageBase page = new PageBase(driver);
        //Default Test Project
    		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
    				new Template(this.waterfallTemplateName));
		//"Duplicate to" Project
		String p = DataProviders.getUniqueString();
		Project duplTest = new Project("DuplTest"+p, "dupltest"+DataProviders.getUniqueStringAlphaNum(),new Template(this.waterfallTemplateName));
		
	    ProjectNewPage pnp = page.createNewProject();
		pnp.createNew(duplTest);
		
		FILELOG.debug("Created new project " + duplTest.getName());
		
		//Creating 2 new Requests 
		Request request1 = new Request("DuplRequest1-"+p);
		Request request2 = new Request("DuplRequest2-"+p);
		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		RequestNewPage rnp = mip.clickNewCR();
		mip = rnp.createCRShort(request1);
		rnp = mip.clickNewCR();
		mip = rnp.createCRShort(request2);
		mip.checkRequest(request1.getId());
		mip.checkRequest(request2.getId());
		
		mip = mip.massDuplicateInProject(webTest.getName());
		Assert.assertTrue(mip.isRequestPresent(request1.getId()), "В исходном проекте не обнаружено Пожелание " + request1);
		Assert.assertTrue(mip.isRequestPresent(request2.getId()), "В исходном проекте не обнаружено Пожелание " + request2);
		
		mip.gotoProject(webTest);
		mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		mip = mip.showAll();
		Assert.assertTrue(mip.isRequestPresentByName(request1.getName()), "В исходном проекте не обнаружено Пожелание " + request1);
		Assert.assertTrue(mip.isRequestPresentByName(request2.getName()), "В исходном проекте не обнаружено Пожелание " + request2);
	}
	
	@Test(description="S-1960")
	public void massMoveToProject() {
      PageBase page = new PageBase(driver);
   
		//"Duplicate to" Project
		String p = DataProviders.getUniqueString();
		Project moveTest = new Project("MoveTest"+p, "movetest"+DataProviders.getUniqueStringAlphaNum(),new Template(this.waterfallTemplateName));
		
	    ProjectNewPage pnp = page.createNewProject();
		pnp.createNew(moveTest);
		
		FILELOG.debug("Created new project " + moveTest.getName());
		
		//Сделать пользователя и добавить его как участника
		ActivitiesPage ap = pnp.goToAdminTools();
		UsersListPage ulp = ap.gotoUsers();
		User executor = new User(p, true);
		ulp = ulp.addNewUser(executor, false);
		FILELOG.debug("Created: " + executor.getUsername());
		
		ProjectMembersPage pmp = ((SDLCPojectPageBase)ulp.gotoProject(moveTest)).gotoMembers();
		pmp = pmp.gotoAddMember().addUserToProject(executor, "Разработчик", 2,
				"Дайджест об изменениях в проекте: ежедневно");
		
		//Creating 2 new Requests 
		Request request1 = new Request("MoveRequest1-"+p);
		Request request2 = new Request("MoveRequest2-"+p);
		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		RequestNewPage rnp = mip.clickNewCR();
		mip = rnp.createCRShort(request1);
		rnp = mip.clickNewCR();
		mip = rnp.createCRShort(request2);
		
		LoginPage lp = mip.logOut();
		FavoritesPage fp = lp.loginAs(executor.getUsername(), executor.getPass());
		fp.gotoProject(moveTest);
		mip = new SDLCPojectPageBase(driver).gotoRequests();
		RequestViewPage rvp = mip.clickToRequest(request1.getId());
		Spent spent = new Spent("", 1.0, executor.getUsernameLong(), "Затрачено");
		rvp.addSpentTimeRecord(spent);
		mip = rvp.gotoRequests();
		rvp = mip.clickToRequest(request2.getId());
		rvp.addSpentTimeRecord(spent);
		lp = mip.logOut();
		fp = lp.loginAs(username, password);
		fp.gotoProject(moveTest);
		mip = new SDLCPojectPageBase(driver).gotoRequests();
		
		mip.checkRequest(request1.getId());
		mip.checkRequest(request2.getId());
		
		mip = mip.massChangeProject(webTest.getName());
		Assert.assertFalse(mip.isRequestPresent(request1.getId()), "В исходном проекте обнаружено перемещенное Пожелание " + request1);
		Assert.assertFalse(mip.isRequestPresent(request2.getId()), "В исходном проекте обнаружено перемещенное Пожелание " + request2);
		
		mip.gotoProject(webTest);
		mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		mip = mip.showAll();
		Assert.assertTrue(mip.isRequestPresentByName(request1.getName()), "В исходном проекте не обнаружено Пожелание " + request1);
		Assert.assertTrue(mip.isRequestPresentByName(request2.getName()), "В исходном проекте не обнаружено Пожелание " + request2);
		
		rvp = mip.clickToRequest(request1.getId());
		List<Spent> spentRecords = rvp.readSpentRecords();
		Assert.assertEquals(spentRecords.size(), 1, "В перенесеном пожелании 1 должна быть одна запись о списанном времени");
		Assert.assertEquals(spentRecords.get(0).hours, 1.0, "В перенесеном пожелании 1 списанное время должно быть 1 час");
		
		mip = rvp.gotoRequests();
		rvp = mip.clickToRequest(request2.getId());
		spentRecords = rvp.readSpentRecords();
		Assert.assertEquals(spentRecords.size(), 1, "В перенесеном пожелании 2 должна быть одна запись о списанном времени");
		Assert.assertEquals(spentRecords.get(0).hours, 1.0, "В перенесеном пожелании 2 списанное время должно быть 1 час");
	}
	

	/**
	 */
	@Test
	public void importRequestsFromExcel() throws InterruptedException {
        String excelFile = ("resources/requests.xml");
        String requestName1 = "Импортируемое пожелание 1";
        String requestName2 = "Импортируемое пожелание 2";
        String requestName3 = "Импортируемое пожелание 3";
        String requestDescription1 = "Описание 1";
        String requestDescription2 = "Описание 2";
        String requestDescription3 = "Описание 3";
        String requestPriority1 = "Высокий";
        String requestPriority2 = "Обычный";
        String requestPriority3 = "Низкий";
        String requestType1 = "Доработка";
        String requestType2 = "Ошибка";
        String requestType3 = "Доработка";
        
    	new PageBase(driver).gotoProject(webTest);
		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
	    RequestsImportPage rip = mip.gotoImportRequests();
	    rip.loadFile(excelFile);
	    
	    //Проверяем в режиме просмотра
	    List<Request> requests = rip.readRequestsFromPreview();
	    
	        Assert.assertEquals(requests.get(0).getName(), requestName1, "Просмотр: Не верное поле Имя для Пожелания 1");
		    Assert.assertEquals(requests.get(1).getName(), requestName2, "Просмотр: Не верное поле Имя для Пожелания 2");
		    Assert.assertEquals(requests.get(2).getName(), requestName3, "Просмотр: Не верное поле Имя для Пожелания 3");
		    Assert.assertEquals(requests.get(0).getDescription(), requestDescription1, "Просмотр: Не верное поле Описание для Пожелания 1");
		    Assert.assertEquals(requests.get(1).getDescription(), requestDescription2, "Просмотр: Не верное поле Описание для Пожелания 2");
		    Assert.assertEquals(requests.get(2).getDescription(), requestDescription3, "Просмотр: Не верное поле Описание для Пожелания 3");
	        Assert.assertEquals(requests.get(0).getPriority(), requestPriority1, "Просмотр: Не верное поле Приоритет для Пожелания 1");
		    Assert.assertEquals(requests.get(1).getPriority(), requestPriority2, "Просмотр: Не верное поле Приоритет для Пожелания 2");
		    Assert.assertEquals(requests.get(2).getPriority(), requestPriority3, "Просмотр: Не верное поле Приоритет для Пожелания 3");
		    Assert.assertEquals(requests.get(0).getType(), requestType1, "Просмотр: Не верное поле Тип для Пожелания 1");
		    Assert.assertEquals(requests.get(1).getType(), requestType2, "Просмотр: Не верное поле Тип для Пожелания 2");
		    Assert.assertEquals(requests.get(2).getType(), requestType3, "Просмотр: Не верное поле Тип для Пожелания 3");
		    Assert.assertEquals(requests.get(0).getOriginator(), user, "Просмотр: Не верное поле Автор для Пожелания 1");
		    Assert.assertEquals(requests.get(1).getOriginator(), user, "Просмотр: Не верное поле Автор для Пожелания 2");
		    Assert.assertEquals(requests.get(2).getOriginator(), user, "Просмотр: Не верное поле Автор для Пожелания 3");
	    
	    rip.clickImport();
	    mip = rip.gotoRequests();
	    Request r1 = null;
	    Request r2 = null;
	    Request r3 = null;
	    try {
	    r1 = mip.findRequestByName(requestName1);
	    FILELOG.info("Найдено пожелание: " + r1);
	    r2 = mip.findRequestByName(requestName2);
	    FILELOG.info("Найдено пожелание: " + r2);
	    r3 = mip.findRequestByName(requestName3);
	    FILELOG.info("Найдено пожелание: " + r3);
	    }
	    catch (NotFoundException e) {
	    	Assert.fail("Пожелания не найдены после импорта");
	    }
	    
	    //Проверим поля
	    Assert.assertTrue(r1.getPriority().contains(requestPriority1), "Не верное поле Приоритет для Пожелания 1");
	    Assert.assertTrue(r2.getPriority().contains(requestPriority2), "Не верное поле Приоритет для Пожелания 2");
	    Assert.assertTrue(r3.getPriority().contains(requestPriority3), "Не верное поле Приоритет для Пожелания 3");
	    Assert.assertEquals(r1.getType(), requestType1, "Не верное поле Тип для Пожелания 1");
	    Assert.assertEquals(r2.getType(), requestType2, "Не верное поле Тип для Пожелания 2");
	    Assert.assertEquals(r3.getType(), requestType3, "Не верное поле Тип для Пожелания 3");
	    
	    //Проверим автора одного из пожеланий и описание
	    RequestViewPage rvp = mip.clickToRequest(r1.getId());
	    Assert.assertEquals(rvp.readOriginator(), user, "Не верное поле Автор");
	    Assert.assertEquals(rvp.readDescription(), requestDescription1, "Не верное поле Описание");
	}
	
	

	/**
	 */
	@Test (priority = 20)
	public void checkRequiredSpentFieldInComplete(){
		
	        //Default Test Project
	    		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
	    				new Template(this.waterfallTemplateName));
			//Test Project
			String p = DataProviders.getUniqueString();
			Project testProject = new Project("Project"+p, "project"+DataProviders.getUniqueStringAlphaNum(),new Template(this.waterfallTemplateName));
			
		    ProjectNewPage pnp = new PageBase(driver).createNewProject();
			pnp.createNew(testProject);
			
			FILELOG.debug("Created new project " + testProject.getName());
			
		
		RequestsStatePage rsp = (new SDLCPojectPageBase(driver)).gotoRequestsStatePage();
		
		StateEditPage sep =  rsp.editState("Выполнено");
		sep.removeAttribute("Затрачено");
		sep.addAttribute("Затрачено", true, true);
		sep.saveChanges();
		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		Request testRequest = new Request("TestCR-"
				+ DataProviders.getUniqueString(), "", "Низкий", 10, "Доработка");
		RequestNewPage rnp = mip.clickNewCR();
		mip = rnp.createCRShort(testRequest);
		FILELOG.debug("Created new Request " + testRequest.getId());
		
		RequestViewPage rvp = mip.clickToRequest(testRequest.getId());
		RequestDonePage rdp = rvp.completeRequest();
		rdp.tryToComplete("Комментарий", "0");
		Assert.assertTrue(rdp.isErrorAlert(), "Нет предупреждения об ошибке");
	    rvp = rdp.cancel();
		rvp.gotoProject(webTest);
		}
	
	
}
