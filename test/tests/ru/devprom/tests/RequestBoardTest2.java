package ru.devprom.tests;

import java.util.ArrayList;
import java.util.List;

import org.testng.Assert;
import org.testng.annotations.BeforeClass;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.helpers.DateHelper;
import ru.devprom.items.Milestone;
import ru.devprom.items.Project;
import ru.devprom.items.Request;
import ru.devprom.items.Spent;
import ru.devprom.items.Template;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.kanban.KanbanBuildsPage;
import ru.devprom.pages.kanban.KanbanNewBuildPage;
import ru.devprom.pages.kanban.KanbanPageBase;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.attributes.AttributeEntityNewPage;
import ru.devprom.pages.project.attributes.AttributeNewPage;
import ru.devprom.pages.project.attributes.AttributeSettingsPage;
import ru.devprom.pages.project.requests.RequestEditPage;
import ru.devprom.pages.project.requests.RequestNewPage;
import ru.devprom.pages.project.requests.RequestViewPage;
import ru.devprom.pages.project.requests.RequestsBoardPage;
import ru.devprom.pages.project.requests.RequestsPage;

public class RequestBoardTest2 extends ProjectTestBase {

	 Project newTestProject; 
	
	/**
	 * Создадим новый проект, чтобы не загромождать доску новыми задачами
	 */
	@BeforeClass
	public void createNewProject() {
		ProjectNewPage npp = (new PageBase(driver)).createNewProject();
		Template SDLC = new Template(
				this.waterfallTemplateName);
		String p = DataProviders.getUniqueString();
		this.newTestProject = new Project("RBT2Project" + p, "rbt2project" + DataProviders.getUniqueStringAlphaNum(), SDLC);
		npp.createNew(newTestProject);
		FILELOG.debug("Created new project " + newTestProject.getName());
	}
	
	@Test(description="S-1994")
	public void dragAndDropByProperty() throws InterruptedException
	{
		KanbanPageBase kanbanPage = (new KanbanPageBase(driver));
        KanbanBuildsPage buildsPage = kanbanPage.gotoBuilds();
        KanbanNewBuildPage newBuildPage = buildsPage.clickNewBuild();
        newBuildPage.createNewBuild("0.1", "В сборку вошли изменения по всем основным веткам, по которым прошли тесты", "");
		
	int requestsCount = 5;
	List<Request> requests = new ArrayList<Request>();
	RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
	Spent deadline = new Spent(DateHelper.getDayAfter(3), 1.0, user, "Срок выполнения");
	
	//Создадим Пожелания
	 for (int i=0;i<requestsCount;i++){
		 requests.add(new Request("RBT2Request"+DataProviders.getUniqueString()));
		 RequestNewPage ncrp = mip.clickNewCR();
			mip = ncrp.createCRShort(requests.get(i));
			FILELOG.debug("Created Request: " + requests.get(i).getId());
	 }
		
		
	RequestsBoardPage rbp = mip.gotoRequestsBoard();
	RequestViewPage rvp = rbp.clickToRequest(requests.get(0).getId());
	RequestEditPage rep = rvp.gotoEditRequest();
	rep.setVersion("0.1");
    rep.editEstimation(8.0);
	rep.addTag("Тег1Teg");
    rep.addNewDeadline("Deadline", deadline);
	rvp = rep.saveEdited();
	
	rbp = rvp.gotoRequestsBoard();
	rbp.setupGrouping("SubmittedVersion");
	
	 List<String> groups = rbp.getAllGroupingSections();
	 Assert.assertTrue(groups.contains("Обнаружено в версии: 0.1"), "На доске нет группы с именем 'Обнаружено в версии: 0.1'");
	Assert.assertTrue(rbp.isRequestInSection(requests.get(1).getNumericId(), "Обнаружено в версии: нет", "Добавлено"), "Ошибка теста, пожелание " +requests.get(1).getId()+ " изначально находится в секции отличной от Не задано");
	rbp = rbp.moveToAnotherSection(requests.get(1).getNumericId(), "Обнаружено в версии: 0.1", "Добавлено");
	Assert.assertTrue(rbp.isRequestInSection(requests.get(1).getNumericId(), "Обнаружено в версии: 0.1", "Добавлено"), "Пожелание не было перемещено в секцию 'Обнаружено в версии: 0.1'");
	
	rvp = rbp.clickToRequest(requests.get(1).getId());
	Assert.assertEquals(rvp.readUserAttribute("Обнаружено в версии:"), "0.1", "Неверная версия в режиме просмотра");
	rbp = rvp.gotoRequestsBoard();
	
	rbp.setupGrouping("Estimation");
	groups = rbp.getAllGroupingSections();
	Assert.assertTrue(groups.contains("Оценка, ч.: 8"), "На доске нет группы с именем 'Оценка, ч.: 8'");
	Assert.assertTrue(rbp.isRequestInSection(requests.get(3).getNumericId(), "Оценка, ч.:", "Добавлено"), "Ошибка теста, пожелание " +requests.get(3).getId()+ " изначально находится в секции отличной от Оценка, ч.: 0");
	rbp = rbp.moveToAnotherSection(requests.get(3).getNumericId(), "Оценка, ч.: 8", "Добавлено");
	Assert.assertTrue(rbp.isRequestInSection(requests.get(3).getNumericId(), "Оценка, ч.: 8", "Добавлено"), "Пожелание не было перемещено в секцию 'Оценка, ч.: 8'");
	rvp = rbp.clickToRequest(requests.get(3).getId());
	Assert.assertEquals(rvp.readEstimates(), 8.0,"Неверная трудоемкость в режиме просмотра");
	rbp = rvp.gotoRequestsBoard();
	Assert.assertEquals(rbp.readEstimation(requests.get(3).getNumericId()), 8.0, "Неверная трудоемкость в режиме доски");
	
	rbp.setupGrouping("Tags");
	groups = rbp.getAllGroupingSections();
	Assert.assertTrue(groups.contains("Тэг: Тег1Teg"), "На доске нет группы с именем 'Тег1Teg'");
	Assert.assertTrue(rbp.isRequestInSection(requests.get(4).getNumericId(), "Тэги: нет", "Добавлено"), "Ошибка теста, пожелание " +requests.get(4).getId()+ " изначально находится в секции отличной от Тэги: не задано");
	rbp = rbp.moveToAnotherSection(requests.get(4).getNumericId(), "Тег1Teg", "Добавлено");
	Assert.assertTrue(rbp.isRequestInSection(requests.get(4).getNumericId(), "Тег1Teg", "Добавлено"), "Пожелание не было перемещено в секцию 'Тег1Teg'");
	driver.navigate().refresh();
	rvp = rbp.clickToRequest(requests.get(4).getId());
	String[] tags = rvp.getTagsList();
	boolean isTag = false;
	for (String t:tags)	{
		if (t.equals("Тег1Teg")) isTag = true;
	}
	Assert.assertTrue(isTag, "Отсутствует тег Тег1Teg");
	rbp = rvp.gotoRequestsBoard();
	
	}
	
	@Test(description="S-1976")
	public void deleteRequests(){
		int requestsCount = 2;
		List<Request> requests = new ArrayList<Request>();
		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		
		//Создадим Пожелания
		 for (int i=0;i<requestsCount;i++){
			 requests.add(new Request("RBT2Request"+DataProviders.getUniqueString()));
			 RequestNewPage ncrp = mip.clickNewCR();
				mip = ncrp.createCRShort(requests.get(i));
				FILELOG.debug("Created Request: " + requests.get(i).getId());
		 }
			
			
		RequestsBoardPage rbp = mip.gotoRequestsBoard();
		rbp.selectRequest(requests.get(0).getId());
		rbp.selectRequest(requests.get(1).getId());
		rbp = rbp.deleteSelected();
		Assert.assertFalse(rbp.isRequestPresent(requests.get(0).getId()) || rbp.isRequestPresent(requests.get(1).getId()),"Одно или несколько Пожеланий не были удалены");
		
	}
	
	
	/**
	 * Тест массовой операции "Включить в релиз"
	 */
	@Test
	public void massPlanRequests(){
		int requestsCount = 2;
		List<Request> requests = new ArrayList<Request>();
		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		
		//Создадим Пожелания
		 for (int i=0;i<requestsCount;i++){
			 requests.add(new Request("RBT2Request"+DataProviders.getUniqueString()));
			 RequestNewPage ncrp = mip.clickNewCR();
				mip = ncrp.createCRShort(requests.get(i));
				FILELOG.debug("Created Request: " + requests.get(i).getId());
		 }
			
			
		RequestsBoardPage rbp = mip.gotoRequestsBoard();
		rbp.selectRequest(requests.get(0).getId());
		rbp.selectRequest(requests.get(1).getId());
		rbp = rbp.massIncludeInRelease("0");
		Assert.assertTrue(rbp.isRequestInSection(requests.get(0).getNumericId(), "Релиз: 0", "В релизе"), "Пожелание " + requests.get(0).getId() + " не найдено в секции 'В релизе'");
		Assert.assertTrue(rbp.isRequestInSection(requests.get(1).getNumericId(), "Релиз: 0", "В релизе"), "Пожелание " + requests.get(1).getId() + " не найдено в секции 'В релизе'");
	}
	

	/**
	 * Тест пользовательских атрибутов для Пожелания
	 */
	@Test
	public void customAttributesTest(){
		String p = DataProviders.getUniqueStringAlphaNum();
		        String cattr1 = "attr1_" + p;
		        String attrName = DataProviders.getUniqueString();
		        String cattr1value = "Значение пользовательского атрибута 1";
		        
		        
				AttributeSettingsPage asp = (new SDLCPojectPageBase(driver))
						.gotoAttributeSettings();
				AttributeEntityNewPage naep = asp.addNewAttribute();
				AttributeNewPage nap = naep.selectEntity("request", "Строка текста");
			    nap.enterNewAttribute(attrName, cattr1, "Для теста доски", false);
			    asp = nap.createNewAttribute();
				
			RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
			Request request = new Request("RBT2Request"+p);
			 RequestNewPage ncrp = mip.clickNewCR();
				mip = ncrp.createCRShort(request);
				
	 //			
				FILELOG.debug("Created Request: " + request.getId());
				RequestViewPage rvp = mip.clickToRequest(request.getId());
			RequestEditPage rep = rvp.gotoEditRequest();
			rep.setUserStringAttribute(cattr1, cattr1value);
			
			rvp = rep.saveEdited();
			RequestsBoardPage rbp = rvp.gotoRequestsBoard();
			rbp = rbp.showSpecificAttributes(cattr1);
			Assert.assertEquals(rbp.readAttributeByName(request.getNumericId(), attrName),cattr1value, "Неверное значение атрибута 1");
			
			rvp = rbp.clickToRequest(request.getId());
			rep = rvp.gotoEditRequest();
			rep.editDescription("Новое описание");
			rep.saveEdited();
			rbp = rvp.gotoRequestsBoard();
			rbp = rbp.showSpecificAttributes(cattr1);
			Assert.assertEquals(rbp.readAttributeByName(request.getNumericId(), attrName),cattr1value, "Неверное значение атрибута 1 после редактирования пожелания");
			
			rbp = rbp.moveToAnotherSection(request.getNumericId(), "0", "В релизе");
			driver.navigate().refresh();
			rvp = rbp.clickToRequest(request.getId());
			
			Assert.assertEquals(rvp.readUserAttribute(attrName),cattr1value, "Неверное значение атрибута 1 после перевода пожелания в релиз");
	}
	
}
