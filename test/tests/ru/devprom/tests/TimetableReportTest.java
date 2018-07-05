package ru.devprom.tests;

import java.io.IOException;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.xpath.XPathExpressionException;

import org.testng.Assert;
import org.testng.annotations.Test;
import org.xml.sax.SAXException;

import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Project;
import ru.devprom.items.RTask;
import ru.devprom.items.Request;
import ru.devprom.items.Spent;
import ru.devprom.items.Template;
import ru.devprom.items.TimetableItem;
import ru.devprom.items.User;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.admin.ActivitiesPage;
import ru.devprom.pages.admin.UsersListPage;
import ru.devprom.pages.project.AddMemberPage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.TimetablePage;
import ru.devprom.pages.project.requests.RequestNewPage;
import ru.devprom.pages.project.requests.RequestDonePage;
import ru.devprom.pages.project.requests.RequestViewPage;
import ru.devprom.pages.project.requests.RequestsPage;
import ru.devprom.pages.project.settings.ProjectMembersPage;
import ru.devprom.pages.project.tasks.TaskCompletePage;
import ru.devprom.pages.project.tasks.TaskNewPage;
import ru.devprom.pages.project.tasks.TaskViewPage;
import ru.devprom.pages.project.tasks.TasksPage;

public class TimetableReportTest extends ProjectTestBase {
        
	private String executor = "WebTestUser";
	

	/** This method checks export of Participant Spent Time (Участники -> Затраченное время) as it is displayed by default 
	 * @throws InterruptedException 
	 * @throws IOException 
	 * @throws SAXException 
	 * @throws ParserConfigurationException 
	 * @throws XPathExpressionException */
	@Test
	public void testParticipantsSpentTimeExportExcelByTasks() throws XPathExpressionException, ParserConfigurationException, SAXException, IOException, InterruptedException {
		
		//create Task and complete (to be sure we have at least one element in the timetable)
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		TasksPage mtp = favspage.gotoTasks();
		RTask testTask = new RTask("TestTask"+DataProviders.getUniqueString(), executor, RTask.getRandomType(), RTask.getRandomEstimation());
		testTask.setPriority(RTask.getRandomPriority());
		TaskNewPage ntp = mtp.createNewTask();
	    mtp = ntp.createTask(testTask);
	    FILELOG.debug("Task created: " + testTask);
		TaskViewPage tvp = mtp.clickToTask(testTask.getId());
		TaskCompletePage ctp = tvp.completeTask();
		tvp = ctp.complete(testTask, 1.0, "Задача выполнена");
		
		//move to Timetable Page, set "Задачи" mode and read the table
		TimetablePage ttp = tvp.gotoTimetablePage();
		ttp.setMode("tasks");
		TimetableItem[] table = ttp.readTimetable();
	//	Arrays.sort(table);
		FILELOG.debug("Данные со страницы:");
		for (TimetableItem t:table){
			FILELOG.debug(t);
		}
		//export to Excel and read the file
		TimetableItem[] tableExcel = ttp.exportToExcel(ttp.readTimetableType());
		//Arrays.sort(tableExcel);
		FILELOG.debug("Данные из файла:");
		for (TimetableItem t:tableExcel){
			FILELOG.debug(t);
		}
		
		//verify the data
		Assert.assertEquals(tableExcel, table);
	}
	

	/** This method checks export of Participant Spent Time (Участники -> Затраченное время) displayed in Requests View 
	 * @throws InterruptedException 
	 * @throws IOException 
	 * @throws SAXException 
	 * @throws ParserConfigurationException 
	 * @throws XPathExpressionException */
	@Test (priority=3)
	public void testParticipantsSpentTimeExportExcelByRequests() throws XPathExpressionException, ParserConfigurationException, SAXException, IOException, InterruptedException {
		//create Request and complete (to be sure we have at least one element in the timetable)
			Request testRequest = new Request("TestCR-"
				+ DataProviders.getUniqueString(), "dd", "Низкий", 10, "Доработка");
		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
		RequestNewPage nrp = mip.clickNewBug();
		mip = nrp.createNewCR(testRequest);
	    FILELOG.debug("Task created: " + testRequest);
		RequestViewPage rvp = mip.clickToRequest(testRequest.getId());
		RequestDonePage rdp = rvp.completeRequest();
		rvp = rdp.complete("DoneBefore", "0", new Spent("",2.0, executor, "complete"));
		
		//move to Timetable Page, set "Пожелания" mode and read the table
		TimetablePage ttp = rvp.gotoTimetablePage();
		ttp.setMode("issues");
		TimetableItem[] table = ttp.readTimetable();
	//	Arrays.sort(table);
		FILELOG.debug("Данные со страницы:");
		for (TimetableItem t:table){
			FILELOG.debug(t);
		}
		//export to Excel and read the file
		TimetableItem[] tableExcel = ttp.exportToExcel(ttp.readTimetableType());
		//Arrays.sort(tableExcel);
		FILELOG.debug("Данные из файла:");
		for (TimetableItem t:tableExcel){
			FILELOG.debug(t);
		}
		
		//verify the data
		Assert.assertEquals(tableExcel, table);
	}
	
	
	/** This method checks export of Participant Spent Time (Участники -> Затраченное время) displayed in Requests View 
	 * @throws InterruptedException 
	 * @throws IOException 
	 * @throws SAXException 
	 * @throws ParserConfigurationException 
	 * @throws XPathExpressionException */
	@Test (priority=4)
	public void testParticipantsSpentTimeExportExcelByProjects() throws XPathExpressionException, ParserConfigurationException, SAXException, IOException, InterruptedException {
		//move to Timetable Page, set "Проекты" mode and read the table
		SDLCPojectPageBase page = new SDLCPojectPageBase(driver);
		TimetablePage ttp =  page.gotoTimetablePage();
		ttp.setMode("projects");
		TimetableItem[] table = ttp.readTimetable();
	//	Arrays.sort(table);
		FILELOG.debug("Данные со страницы:");
		for (TimetableItem t:table){
			FILELOG.debug(t);
		}
		//export to Excel and read the file
		TimetableItem[] tableExcel = ttp.exportToExcel(ttp.readTimetableType());
		//Arrays.sort(tableExcel);
		FILELOG.debug("Данные из файла:");
		for (TimetableItem t:tableExcel){
			FILELOG.debug(t);
		}
		
		//verify the data
		Assert.assertEquals(tableExcel, table);
	}
	
	
	/** This method checks export of Participant Spent Time (Участники -> Затраченное время) displayed in Requests View 
	 * @throws InterruptedException 
	 * @throws IOException 
	 * @throws SAXException 
	 * @throws ParserConfigurationException 
	 * @throws XPathExpressionException */
	@Test (priority=5)
	public void testParticipantsSpentTimeExportExcelByMembers() throws XPathExpressionException, ParserConfigurationException, SAXException, IOException, InterruptedException {
		//move to Timetable Page, set "Участники" mode and read the table
		SDLCPojectPageBase page = new SDLCPojectPageBase(driver);
		TimetablePage ttp =  page.gotoTimetablePage();
		ttp.setMode("participants");
		TimetableItem[] table = ttp.readTimetable();
	//	Arrays.sort(table);
		FILELOG.debug("Данные со страницы:");
		for (TimetableItem t:table){
			FILELOG.debug(t);
		}
		//export to Excel and read the file
		TimetableItem[] tableExcel = ttp.exportToExcel(ttp.readTimetableType());
		//Arrays.sort(tableExcel);
		FILELOG.debug("Данные из файла:");
		for (TimetableItem t:tableExcel){
			FILELOG.debug(t);
		}
		
		//verify the data
		Assert.assertEquals(tableExcel, table);
	}
	
	@Test (priority=6)
	public void FilterByMemberAndRole() throws InterruptedException {
		//move to Timetable Page, set "Участники" mode and read the table
		SDLCPojectPageBase page = new SDLCPojectPageBase(driver);
		ActivitiesPage ap = page.goToAdminTools();
		UsersListPage ulp = ap.gotoUsers();
		User member = new User(DataProviders.getUniqueString(), true);
		ulp = ulp.addNewUser(member, false);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		ProjectMembersPage pmp = favspage.gotoMembers();
		AddMemberPage amp = pmp.gotoAddMember();
		pmp = amp.addUserToProject(member, "Заказчик", 2, "");
		
		TimetablePage ttp =  pmp.gotoTimetablePage();
		ttp.addFilterRole();
		ttp.addFilterParticipant();
		
		ttp = ttp.setFilterRole("Заказчик");
		TimetableItem[] table = ttp.readTimetable();
		Assert.assertEquals(table.length, 0, "В таблице присутствуют записи при фильтрации по роли Заказчик");
		ttp.removeFilterRole();
		ttp.setFilterParticipant(member.getUsernameLong());
		table = ttp.readTimetable();
		Assert.assertEquals(table.length, 0, "В таблице присутствуют записи при фильтрации по имени нового участника");
		ttp.removeFilterParticipant();
		
	}
	
	
}
