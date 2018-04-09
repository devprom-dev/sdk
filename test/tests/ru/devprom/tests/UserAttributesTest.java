package ru.devprom.tests;

import org.openqa.selenium.By;
import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.Messages;
import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Project;
import ru.devprom.items.Request;
import ru.devprom.items.Requirement;
import ru.devprom.items.Template;
import ru.devprom.items.User;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

import ru.devprom.pages.admin.ActivitiesPage;
import ru.devprom.pages.admin.UsersListPage;
import ru.devprom.pages.project.ProjectPageBase;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.attributes.AttributeSettingsPage;
import ru.devprom.pages.project.attributes.AttributeEntityNewPage;
import ru.devprom.pages.project.attributes.AttributeNewPage;
import ru.devprom.pages.project.requests.RequestEditPage;
import ru.devprom.pages.project.requests.RequestIncludeToReleasePage;
import ru.devprom.pages.project.requests.RequestNewPage;
import ru.devprom.pages.project.requests.RequestPlanningPage;
import ru.devprom.pages.project.requests.RequestViewPage;
import ru.devprom.pages.project.requests.RequestsBoardPage;
import ru.devprom.pages.project.requests.RequestsPage;
import ru.devprom.pages.project.requests.RequestsStatePage;
import ru.devprom.pages.project.requirements.RequirementNewPage;
import ru.devprom.pages.project.requirements.RequirementViewPage;
import ru.devprom.pages.project.requirements.RequirementsPage;
import ru.devprom.pages.project.settings.ProjectMembersPage;
import ru.devprom.pages.project.settings.StateEditPage;

public class UserAttributesTest extends ProjectTestBase {


	/** This method tests a new user attribute for Request of "Ошибка" type */
	@Test
	public void testCreateNewErrorAttribute() {
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		page.gotoProject(webTest);
		
	    //Go to Attributes page in Global Settings
		AttributeSettingsPage asp = (new SDLCPojectPageBase(driver))
				.gotoAttributeSettings();
		//Clean up old user attributes
		asp = asp.deleteAll();
		//Create new
		AttributeEntityNewPage naep = asp.addNewAttribute();
		
	    //Create a new attribute for Request:bug objects, type "dictionary"
		AttributeNewPage nap = naep.selectEntity("request:bug", "Справочник");
		int defaultAttributeValue = 2;
		nap.setDefaultStringValue(String.valueOf(defaultAttributeValue));
		String p = DataProviders.getUniqueString();
		nap.enterNewAttribute("Важность" + p, "ref" + p, "Для теста пользовательских атрибутов", false);
		
	    //Create a values map	
		Map<Integer, String> attributeValues = new HashMap <Integer, String>();
		attributeValues.put(1, "Блокер");
		attributeValues.put(2, "Некорректное поведение");
		attributeValues.put(3, "Проблемы Usability");
		
		nap.addValuesRange(attributeValues);
		asp = nap.createNewAttribute();
		
		//Create new Request
		RequestsPage mip = asp.gotoRequests();
		Request testRequest = new Request("TestCR-"
				+ DataProviders.getUniqueString(), "dd", "Низкий", 10, "Ошибка");
		RequestNewPage nrp = mip.clickNewBug();
		Assert.assertTrue(nrp.isAttributePresent("ref" + p), "Важность"+p+" attribute isn't present");
		Assert.assertEquals(nrp.checkSelectAttributeDefaultValue("ref" + p), "Некорректное поведение", "Incorrect default value was displayed for Важность"+p+" attribute when creating request");
		List<String> attributeValuesRead = nrp.getSelectValues(driver.findElement(By.name("ref" + p)));
		FILELOG.debug("had values: ");
		for (String s:attributeValues.values()){
			FILELOG.debug(s);
		}
		FILELOG.debug("read values: ");
		for (String s:attributeValuesRead){
			FILELOG.debug(s);
		}
		Assert.assertTrue(attributeValues.values().containsAll(attributeValuesRead));
		Assert.assertTrue(attributeValuesRead.containsAll(attributeValues.values()));
		
		nrp.setUserOptionAttribute("ref" + p, "3");
		mip = nrp.createNewCR(testRequest);
		FILELOG.info("Request created: "+testRequest.getId()+" : " + testRequest.getName());
		
		 // Re-open request and check attribute's value
		RequestViewPage rvp = mip.clickToRequest(testRequest.getId());
		Assert.assertEquals(rvp.readUserAttribute("Важность"+p),"Проблемы Usability");
		
		// Clean attributes
		asp = rvp.gotoAttributeSettings();
		asp = asp.deleteAttribute("ref" + p);
	
	}
	
	
	
	/**   This method tests a new user attribute for Requirement */
	@Test
	public void testCreateNewRequirementAttribute() {
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
	   page.gotoProject(webTest);
	

	    //Go to Attributes page in Global Settings
		AttributeSettingsPage asp = (new SDLCPojectPageBase(driver))
				.gotoAttributeSettings();
		//Clean up old user attributes
		asp = asp.deleteAll();
		//Create new
		AttributeEntityNewPage naep = asp.addNewAttribute();
		
	    //Create a new attribute for Request:bug objects, type "string"
		AttributeNewPage nap = naep.selectEntity("requirement", "Строка текста");
		String p = DataProviders.getUniqueString();
		nap.enterNewAttribute("Источник" + p, "ref" + p, "Для теста пользовательских атрибутов", false);
		nap.setDefaultStringValue("Default value "+p);
		asp = nap.createNewAttribute();
		
		//create Requirement
		RequirementsPage rp = (new SDLCPojectPageBase(driver)).gotoRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
		
		Assert.assertTrue(nrp.isAttributePresent("ref" + p), "Источник"+p+" attribute isn't present");
		Assert.assertEquals(nrp.checkStringAttributeDefaultValue("ref" + p), "Default value "+p, "Incorrect default value was displayed for Источник"+p+" attribute when creating request");
	
		
		Requirement requirement = new Requirement("TestRForUserAttr"+DataProviders.getUniqueString());
		nrp.setUserStringAttribute("ref" + p, "Some test value " + p);
		RequirementViewPage rvp = nrp.createSimple(requirement);
		FILELOG.info("Requirement created: "+requirement.getId()+" : " + requirement.getName());
		
		 // Check attribute's value
        Assert.assertEquals(rvp.readUserAttribute("Источник"+p),"Some test value " + p);
		
		// Clean attributes
		asp = rvp.gotoAttributeSettings();
		asp = asp.deleteAttribute("ref" + p);
	}
	

	/**   The methods tries creates user attribute with name "type" and checks that 
	 * system doesn't allow to create it, because of it has name used for system attribute  */
	@Test
	public void disallowDuplicateSystemAttributes() {
        String referenceName = "type";
		 
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
	   page.gotoProject(webTest);
	    

	    //Go to Attributes page in Global Settings
		AttributeSettingsPage asp = (new SDLCPojectPageBase(driver))
				.gotoAttributeSettings();
	
		//Create new
		AttributeEntityNewPage naep = asp.addNewAttribute();
		
	    //Create a new attribute for Request:bug objects, type "string"
		AttributeNewPage nap = naep.selectEntity("request", "Строка текста");
		nap.enterNewAttribute("Пользовательский тип", referenceName, "Для теста пользовательских атрибутов", false);
		Assert.assertEquals(nap.createWithError(), Messages.ERROR_MESSAGE_DUPLICATE_ATTRIBUTE.getText(),
				 "Нет правильного сообщения об ошибке при попытке создать атрибут с существующим именем");
		referenceName = "type" + DataProviders.getUniqueString(); 
		nap.enterNewAttribute("Пользовательский тип", referenceName, "Для теста пользовательских атрибутов", false);
		asp = nap.createNewAttribute();
		Assert.assertTrue(asp.isAttribute(referenceName), "В списке нет созданного атрибута");
	  }
	

	/**Создает два новых атрибута для Пожеланий и проверяет настройку видимости пользовательских полей при переходе.
	 * S-1780
	 * @throws Exception 
	 */
	@Test
	public void checkUserFieldsVisibilityOnChangeState() throws Exception {
        PageBase page = new PageBase(driver);
		
		//New SDLC Project
		String p = DataProviders.getUniqueString();
			Project project = new Project("SDLCProject"+p, "sdlcproject"+p,new Template(this.waterfallTemplateName));
		
		//Create a Development Project
		ProjectNewPage pnp = page.createNewProject();
		SDLCPojectPageBase sdlcFirstPage = (SDLCPojectPageBase) pnp.createNew(project);
		FILELOG.debug("Created new project " + project.getName());

		//Go to Attributes settings and create 2 attributes
		AttributeSettingsPage asp = sdlcFirstPage.gotoAttributeSettings();
	    AttributeEntityNewPage aenp = asp.addNewAttribute();
		AttributeNewPage nap = aenp.selectEntity("request", "Строка текста");
		nap.enterNewAttribute("Пользовательский атрибут 1", "userattr1", "Для теста настройки видимости при переходе", false);
		asp = nap.createNewAttribute();
		aenp = asp.addNewAttribute();
		nap = aenp.selectEntity("request", "Строка текста");
		nap.enterNewAttribute("Пользовательский атрибут 2", "userattr2", "Для теста настройки видимости при переходе", false);
		asp = nap.createNewAttribute();
		
		//Go to Request State settings
		RequestsStatePage rsp = asp.gotoRequestsStatePage();
		StateEditPage rsep = rsp.editState("В релизе");
		rsep.addAttribute("Пользовательский атрибут 1", true, true);
		rsep.addAttribute("Пользовательский атрибут 2", true, false);
		rsep.saveSystemAction();
		rsp = new RequestsStatePage(driver);
		
		//Go to Requests and create one
		Request request = new Request("TestRequest"+p);
		RequestsPage rp = rsp.gotoRequests();
		RequestNewPage rnp = rp.clickNewCR();
		rp = rnp.createCRShort(request);
		RequestViewPage rvp = rp.clickToRequest(request.getId());
        RequestIncludeToReleasePage itr = rvp.includeToReleaseEx();
        Assert.assertTrue(itr.isFieldVisibleByLabel("Пользовательский атрибут 1"), "Не виден первый атрибут");
        Assert.assertTrue(itr.isFieldVisibleByLabel("Пользовательский атрибут 2"), "Не виден второй атрибут");
        Assert.assertTrue(itr.isFieldRequired("userattr1"), "Первый атрибут не определен как обязательный");
        itr.fillUserStringField("userattr1", "some text");
        rvp = itr.includeToRelease("0");
        Assert.assertTrue(rvp.readState().contains("В релизе"), "Неверное состояние Пожелания после перевода в релиз");
        
	}
	
	@Test(description="S-1877")
	public void linkTypeAttribute() throws InterruptedException {
		
		String p = DataProviders.getUniqueString();
		User user1 = new User(p+"1",true);
		User user2 = new User(p+"2",true);
		Project project = new Project("SDLCProject"+p,"sdlcproject"+p, new Template(this.waterfallTemplateName));
		String attributeName = "Ответственный за уточнение";
		String attribute = "resplink" + p;
		Request request1 = new Request("Request1"+p);
		Request request2 = new Request("Request2"+p);
		
		
        PageBase page = new PageBase(driver);
        
        //Создаем двух пользователей
        ActivitiesPage ap = page.goToAdminTools();
    	UsersListPage ulp = ap.gotoUsers();
    	ulp = ulp.addNewUser(user1, false);
		FILELOG.debug("Created user " + user1.getUsername());
		ulp = ulp.addNewUser(user2, false);
		FILELOG.debug("Created user " + user2.getUsername());
        
		//Создаем новый проект
		ProjectNewPage npp = ulp.createNewProject();
		SDLCPojectPageBase sdlcFirstPage = (SDLCPojectPageBase) npp.createNew(project);
		FILELOG.debug("Created project " + project.getName());
		ProjectMembersPage pmp = ((SDLCPojectPageBase) (new PageBase(driver))
				.gotoProject(project)).gotoMembers();
		
		pmp = pmp.gotoAddMember().addUserToProject(user1, "Разработчик", 4, "");
		pmp = pmp.gotoAddMember().addUserToProject(user2, "Разработчик", 4,	"");
		
		//Создаем новый атрибут типа Ссылка
		AttributeSettingsPage asp = sdlcFirstPage.gotoAttributeSettings();
	    AttributeEntityNewPage aenp = asp.addNewAttribute();
		AttributeNewPage nap = aenp.selectEntity("request", "Ссылка");
		nap.enterNewAttribute(attributeName, attribute, "Для теста настройки видимости при переходе", false);
		asp = nap.createNewAttribute();
	
		
		//Создаем два пожелания
		RequestsPage rp = asp.gotoRequests();
		RequestNewPage rnp = rp.clickNewCR();
		rnp.setUserAutocompleteAttribute(attribute, user1.getUsernameLong());
		rp = rnp.createCRShort(request1);
		
		rnp = rp.clickNewCR();
		rnp.setUserAutocompleteAttribute(attribute, user2.getUsernameLong());
		rp = rnp.createCRShort(request2);
		
		RequestsBoardPage rbp = rp.gotoRequestsBoard();
		rbp = rbp.addGrouppingByUserAttribute(attribute);
		List<String> firstGroup = rbp.getListOfRequestsInGroup(user1.getUsernameLong());
		Assert.assertTrue(firstGroup.contains(request1.getId()), "Первая группа не содержит первое пожелание");
		Assert.assertFalse(firstGroup.contains(request2.getId()), "Первая группа содержит второе пожелание");
		List<String> secondGroup = rbp.getListOfRequestsInGroup(user2.getUsernameLong());
		Assert.assertTrue(secondGroup.contains(request2.getId()), "Вторая группа не содержит второе пожелание");
		Assert.assertFalse(secondGroup.contains(request1.getId()), "Вторая группа содержит первое пожелание");
		
		rbp.addFilter(attribute);
		rbp.turnOnFilter(user1.getUsernameLong(), attributeName);
		Assert.assertTrue(rbp.isRequestPresent(request1.getId()), "Не видно первое пожелание после фильтрации");
		Assert.assertFalse(rbp.isRequestPresent(request2.getId()), "Видно второе пожелание после фильтрации");		
	}
	
	@Test(description="S-2006")
	public void dragCustomAttributeTOGroup() throws Exception {

		PageBase page = new PageBase(driver);
		//New SDLC Project
		String p = DataProviders.getUniqueString();
			Project project = new Project("SDLCProject"+p, "sdlcproject"+p,new Template(this.waterfallTemplateName));
		
		ProjectNewPage pnp = page.createNewProject();
		SDLCPojectPageBase sdlcFirstPage = (SDLCPojectPageBase) pnp.createNew(project);
		FILELOG.debug("Created new project " + project.getName());

		
		RequestsPage mip = sdlcFirstPage.gotoRequests();
		Request request = new Request("TestCR-"+ DataProviders.getUniqueString(), "for RequestBoardTest",
				"Обычный", 10.0, "Доработка");
   		Request request2 = new Request("TestCR-"+ DataProviders.getUniqueString(), "for RequestBoardTest",
				"Обычный", 10.0, "Доработка");
   		RequestNewPage ncrp = mip.clickNewCR();
		mip = ncrp.createCRShort(request);
		FILELOG.debug("Created Request: " + request.getId());
   		ncrp = mip.clickNewCR();
		mip = ncrp.createCRShort(request2);
		FILELOG.debug("Created Request: " + request2.getId());
		
		
		AttributeSettingsPage asp = mip.gotoAttributeSettings();
	    AttributeEntityNewPage aenp = asp.addNewAttribute();
		AttributeNewPage nap = aenp.selectEntity("request", "Справочник");
		nap.enterNewAttribute("Новый", "new", "Для теста S-2006", false);
		Map<Integer, String> values = new HashMap<Integer, String>();
		values.put(1, "Первое значение");
		values.put(2, "Второе значение");
		nap.addValuesRange(values);
		asp = nap.createNewAttribute();
		
		
		RequestsBoardPage rbp = asp.gotoRequestsBoard();
		RequestEditPage rep = rbp.editRequest(request.getNumericId());
		Assert.assertTrue(rep.isAttributePresent("new"), "На форме редактирования Пожелания нет нового атрибута");
		
		rep.setUserOptionAttributeByVisibleText("new", "Первое значение");
		RequestViewPage rvp = rep.saveEdited();
		rbp = rvp.gotoRequestsBoard();
		
		rbp = rbp.setupGrouping("new");
		List<String> sections = rbp.getAllGroupingSections();
		Assert.assertTrue(sections.contains("Новый: Первое значение"), "На странице нет группы 'Новый: Первое значение'");


		rbp.moveToAnotherSection(request2.getNumericId(), "Новый: Первое значение", "Добавлено");
		Assert.assertTrue(rbp.isRequestInSection(request2.getNumericId(), "Новый: Первое значение", "Добавлено"), "Пожелание 2 не переместилось в секцию 'Новый: Первое значение'");
		
		rvp = rbp.clickToRequest(request2.getId());
		Assert.assertEquals(rvp.readUserAttribute("Новый"), "Первое значение", "В режиме просмотра карточки неправильное значение нового аттрибута");
		
	}
	
	
	
	}
		
	
