package ru.devprom.tests;

import java.util.List;

import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Project;
import ru.devprom.items.RTask;
import ru.devprom.items.Request;
import ru.devprom.items.Requirement;
import ru.devprom.items.SearchResultItem;
import ru.devprom.items.Template;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.SearchResultsPage;
import ru.devprom.pages.project.requests.RequestNewPage;
import ru.devprom.pages.project.requests.RequestViewPage;
import ru.devprom.pages.project.requests.RequestsPage;
import ru.devprom.pages.project.requirements.RequirementNewPage;
import ru.devprom.pages.project.requirements.RequirementViewPage;
import ru.devprom.pages.project.requirements.RequirementsPage;
import ru.devprom.pages.project.tasks.TaskNewPage;
import ru.devprom.pages.project.tasks.TasksPage;

public class SearchTest extends ProjectTestBase {

	
	/** This method create a few requests 
	 * @throws InterruptedException */
	@Test
	public void createRequests() throws InterruptedException {
		String nameKeyword = DataProviders.getUniqueString();
		String contentKeyword = DataProviders.getUniqueString(9);
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(
						this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		
		//Create new Request
		RequestsPage mip = favspage.gotoRequests();
		Request testRequest = new Request("Test-"+ nameKeyword, "Test Request for Search Test, search text: " + contentKeyword, "Низкий", 10, "Доработка");
		RequestNewPage nrp = mip.clickNewBug();
		mip = nrp.createNewCR(testRequest);
		
		//Create new Requirement
		RequirementsPage rp = mip.gotoRequirements();
		RequirementNewPage rnp = rp.createNewRequirement();
		Requirement testRequirement = new Requirement("Test-"+ nameKeyword, "Test Request for Search Test, search text: " + contentKeyword);
		RequirementViewPage rvp = rnp.create(testRequirement);
		
		//Create new Task
		RTask testTask = new RTask("Test-"+ nameKeyword, user, RTask.getRandomType(), RTask.getRandomEstimation());
		testTask.setPriority(RTask.getRandomPriority());
		TasksPage mtp = rvp.gotoTasks();
	    TaskNewPage ntp = mtp.createNewTask();
	    mtp = ntp.createTask(testTask);

	    String searchString = nameKeyword.split("\\.")[1];
		SearchResultsPage srp = favspage.searchByKeyword(searchString);
		List<SearchResultItem> results = srp.readAllResults();
		SearchResultItem foundRequest = null;
		SearchResultItem foundRequirement = null;
		SearchResultItem foundTask = null;
		for (SearchResultItem item:results){
			if (item.getId().equals(testRequest.getId())){
				foundRequest = item;
			}
            if (item.getId().equals(testRequirement.getId())){
            	foundRequirement = item;
            }
            if (item.getId().equals(testTask.getId())){
            	foundTask = item;
			}
		}
		Assert.assertFalse(foundRequest==null, "Search didn't show test Request " +testRequest.getId());
		Assert.assertFalse(foundRequirement==null, "Search didn't show test Requirement " +testRequirement.getId());
		Assert.assertFalse(foundTask==null, "Search didn't show test Task " +testTask.getId());

		Assert.assertTrue(foundRequirement.getFindString().contains("Название:"), "Search area is incorrect - should be 'Название'");
		Assert.assertEquals(foundRequirement.getBold(),searchString, "Can't find search string in Requirement bold text");

		Assert.assertTrue(foundRequest.getFindString().contains("Название:"), "Search area is incorrect - should be 'Название'");
		Assert.assertEquals(foundRequest.getBold(),searchString, "Can't find search string in Request bold text");

		Assert.assertTrue(foundTask.getFindString().contains("Название:"), "Search area is incorrect - should be 'Название'");
		Assert.assertEquals(foundTask.getBold(),searchString, "Can't find search string in Request bold text");

		//Search in content
		searchString = contentKeyword.split("\\.")[1];
		srp = srp.searchByKeyword(searchString);
		results = srp.readAllResults();
		foundRequest = null;
		foundRequirement = null;
		for (SearchResultItem item:results){
			if (item.getId().equals(testRequest.getId())){
				foundRequest = item;
			}
			if (item.getId().equals(testRequirement.getId())){
				foundRequirement = item;
			}
		}
		Assert.assertFalse(foundRequest==null, "Search didn't show test Request " +testRequest.getId());
		Assert.assertFalse(foundRequirement==null, "Search didn't show test Requirement " +testRequirement.getId());

		Assert.assertEquals(foundRequest.getBold(),searchString, "Can't find search string in Requirement bold text");
		Assert.assertEquals(foundRequirement.getBold(),searchString, "Can't find search string in Request bold text");

		//search by Request id
		RequestViewPage revp = mip.searchByRequestId(testRequest.getId());
		Assert.assertEquals(revp.readID(), testRequest.getId());
		Assert.assertEquals(revp.readName(), testRequest.getName());
		Assert.assertEquals(revp.readDescription(), testRequest.getDescription());
		}
	}
