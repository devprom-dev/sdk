package ru.devprom.tests;

import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Project;
import ru.devprom.items.RTask;
import ru.devprom.items.Request;
import ru.devprom.items.Template;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requests.RequestNewPage;
import ru.devprom.pages.project.requests.RequestPlanningPage;
import ru.devprom.pages.project.requests.RequestViewPage;
import ru.devprom.pages.project.requests.RequestsBoardPage;
import ru.devprom.pages.project.requests.RequestsPage;
import ru.devprom.pages.project.requests.RequestsStatePage;
import ru.devprom.pages.project.requests.RequestsTypesPage;
import ru.devprom.pages.project.settings.StateNewPage;
import ru.devprom.pages.project.settings.TransitionEditPage;
import ru.devprom.pages.project.settings.TransitionNewPage;

public class WorkflowTest extends ProjectTestBase{

	/** Test adds new Request State and 2 Transitions, then pass a Request between the states*/
	@Test 
	public void newRequestStateTest(){
		String newStateName = "Очередь";
		String firstTransitionName = "Включить в релиз";
		String secondTransitionName = "Поставить в очередь";
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		page.gotoProject(webTest);
		
		//Go to Requests State page and create new State and 2 Transitions	
		RequestsStatePage rsp = (new SDLCPojectPageBase(driver)).gotoRequestsStatePage();
		StateNewPage rsnp = rsp.addState();
		rsp = rsnp.createNewState(newStateName);
		TransitionNewPage rtnp = rsp.clickAddTransition(newStateName);
		rtnp.createNewTransition(firstTransitionName, "В релизе");
		rsp = new RequestsStatePage(driver);
		rtnp = rsp.clickAddTransition("Добавлено");
		rtnp.createNewTransition(secondTransitionName, newStateName);
		rsp = new RequestsStatePage(driver);
		
		//Go to Request, create new one and pass it through the new state
		RequestsPage mip = rsp.gotoRequests();
		RequestNewPage rnp = mip.clickNewCR();
		Request testRequest = new Request("TestCR-"
				+ DataProviders.getUniqueString(), "For WorkflowTest", "Низкий", 10, "Доработка");
		mip = rnp.createNewCR(testRequest);
		RequestViewPage rvp = mip.clickToRequest(testRequest.getId());
		Assert.assertEquals(rvp.readState(),"Добавлено");
		rvp = rvp.applySimpleTransition(secondTransitionName);
		Assert.assertEquals(rvp.readState(),newStateName);
		rvp = rvp.includeToRelease("0");
		Assert.assertTrue(rvp.readState().contains("В релизе"));
		
		//Clean changes. 
		//First find requests in newly added state and delete them, then delete the state
		mip = rvp.gotoRequests();
		mip.showAll();
		mip = mip.selectFilterValue("state", newStateName);
		mip = mip.deleteAll();
		rsp = mip.gotoRequestsStatePage();
		rsp.deleteState(newStateName);
	}
	
	@Test(description="S-2007") 
	public void conditionsToTransit(){
		String p = DataProviders.getUniqueString();
		String userRequestTypeName = "UserRequestType"+p;
  	   Project sdlcProject= new Project("sdlcProject"+p, "sdlcproject"+p,
					new Template(this.waterfallTemplateName));
  	   Request request = new Request("Доработка"+p, "", Request.getHighPriority(), 10.0, "Доработка");
	   Request bug = new Request("Ошибка"+p, "", Request.getHighPriority(), 10.0, "Ошибка");
	   Request userRequest = new Request("u"+p);
	        ProjectNewPage pnp = new PageBase(driver).createNewProject();
			pnp.createNew(sdlcProject);
			FILELOG.debug("Created new project " + sdlcProject.getName());

			RequestsStatePage rsp = (new SDLCPojectPageBase(driver)).gotoRequestsStatePage();
			TransitionEditPage rtep = rsp.clickChangeTransition("Добавлено", "Запланировать > Запланировано");
			rtep = rtep.addPrecondition("Тип пожелания: Ошибка");
			rtep.saveChanges();
			rsp = new RequestsStatePage(driver);
			RequestsPage rp = rsp.gotoRequests();
			RequestNewPage rnp = rp.clickNewCR();
		    rnp.createNewCR(request);	
		    RequestsBoardPage rbp = rnp.gotoRequestsBoard();
		    String errorMessage = rbp.tryToMoveDenied(request.getNumericId(), "Релиз: нет", "Запланировано");
		    Assert.assertTrue(errorMessage.length() > 10, "Не получено правильное сообщение о невозможности запланировать пожелание");
		   
		    rp = rsp.gotoRequests();
		    rnp = rp.clickNewBug();
		    rnp.createNewCR(bug);
		    rbp = rnp.gotoRequestsBoard();
		    RequestPlanningPage rpp =  rbp.moveToPlanned(bug.getNumericId());
		    RTask task1 = new RTask("Задача"+DataProviders.getUniqueString(), user, "Разработка", 2.0);
		    rpp.hideTaskbox(); 
			rpp.hideTaskbox();
			rpp.hideTaskbox();
			rpp.hideTaskbox();
			rpp.hideTaskbox();
	         rpp.fillTask(1, task1.getName(), task1.getType(),
	    		  task1.getExecutor(), task1.getEstimation());
	        rbp = rpp.savePlannedOnBoard();
	        driver.navigate().refresh();
	        RequestsTypesPage rtp = rbp.gotoRequestsTypes();
	        rtp = rtp.addNewType(userRequestTypeName);
	        
	        rsp = rtp.gotoRequestsStatePage();
			rtep = rsp.clickChangeTransition("Добавлено", "Запланировать > Запланировано");
			rtep = rtep.removePrecondition("Тип пожелания: Ошибка");
			rtep = rtep.addPrecondition("Тип пожелания: " + userRequestTypeName);
			rtep.saveChanges();
			rsp =  new RequestsStatePage(driver);
			rp = rsp.gotoRequests();
			rnp = rp.clickNewRequestUserType(userRequestTypeName);
			rp = rnp.createCRShort(userRequest);
			rbp = rp.gotoRequestsBoard();
			Assert.assertTrue(rbp.isMenuItemAccessible(userRequest.getNumericId(), "Запланировать"));
			
			rpp = rbp.moveToPlannedUsingMenu(userRequest.getNumericId());
			rpp.hideTaskbox(); 
				rpp.hideTaskbox();
				rpp.hideTaskbox();
				rpp.hideTaskbox();
				rpp.hideTaskbox();
		        rpp.fillTask(1, task1.getName(), task1.getType(),
		    		  task1.getExecutor(), task1.getEstimation());
		    rbp = rpp.savePlannedOnBoard();
			
	}
	
	
}
