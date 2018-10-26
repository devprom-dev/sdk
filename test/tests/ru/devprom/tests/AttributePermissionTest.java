package ru.devprom.tests;

import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Project;
import ru.devprom.items.Request;
import ru.devprom.items.Spent;
import ru.devprom.items.Template;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.project.PermissionsPage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requests.RequestNewPage;
import ru.devprom.pages.project.requests.RequestViewPage;
import ru.devprom.pages.project.requests.RequestsPage;

public class AttributePermissionTest extends ProjectTestBase {
	private String admin = "WebTestUser";
	
	/** The test changes current user role permissions, not allowing to see Request Spent time records and edit Request Description*/
	@Test 
	public void requestAttributePermissionTest(){
		String description = "This is a test Request used in requestAttributePermissionTest"; 
		int estimation = 10;
		
		PageBase page = new PageBase(driver);
		String p = DataProviders.getUniqueString();
		Project testProject = new Project("DevTest"+p, "devtest"+DataProviders.getUniqueStringAlphaNum(),
				new Template(this.waterfallTemplateName)); 
		ProjectNewPage pnp = page.createNewProject();
		SDLCPojectPageBase sdlcFirstPage = (SDLCPojectPageBase) pnp.createNew(testProject);
		
		RequestsPage mip = sdlcFirstPage.gotoRequests();
		Request testRequest = new Request("TestCR-"
				+ DataProviders.getUniqueString(), description, "Низкий",
				estimation, "Доработка");
		
		//Creating new Request
		RequestNewPage nrp = mip.clickNewCR();
		mip = nrp.createNewCR(testRequest);
		RequestViewPage rvp = mip.clickToRequest(testRequest.getId());
		
		//Add spent time record to the Request
		Spent spent = new Spent("", 2.0, admin,"test description");
		rvp.addSpentTimeRecord(spent);
		
		mip = sdlcFirstPage.gotoRequests();
		rvp = mip.clickToRequest(testRequest.getId());
		
		//Check that we have expected data in Description and Spent time fields
		Assert.assertEquals(rvp.readDescription(),description, "Created Request has incorrect description");
		Assert.assertEquals(rvp.readSpentRecords().get(0),spent, "Spent time record saved incorrectly");
		
		//Goto Permissions page and setup permissions limitation for the current user role
		PermissionsPage pp = rvp.gotoPermissionsPage();
		pp.showAll();
		pp.setRight("Пожелание.Затрачено", "none");
		pp.setRight("Пожелание.Описание", "view");
		
		//Check the changes in Requests attributes accessibility
		mip = pp.gotoRequests();
		rvp = mip.clickToRequest(testRequest.getId());
		
	    Assert.assertFalse(rvp.isSpentTimeVisible(), "User can see Spent time record");
	    Assert.assertFalse(rvp.isDescriptionEditable(), "User can edit Description");
	}
	
	
}
