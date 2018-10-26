package ru.devprom.tests;

import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import org.openqa.selenium.WebElement;
import org.testng.Assert;
import org.testng.annotations.BeforeClass;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Blogpost;
import ru.devprom.items.ProductFunction;
import ru.devprom.items.Project;
import ru.devprom.items.RTask;
import ru.devprom.items.Request;
import ru.devprom.items.Spent;
import ru.devprom.items.Template;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.VersioningPage;
import ru.devprom.pages.project.blogs.BlogPage;
import ru.devprom.pages.project.functions.FunctionNewPage;
import ru.devprom.pages.project.functions.FunctionsPage;
import ru.devprom.pages.project.requests.RequestEditPage;
import ru.devprom.pages.project.requests.RequestNewPage;
import ru.devprom.pages.project.requests.RequestPlanningPage;
import ru.devprom.pages.project.requests.RequestViewPage;
import ru.devprom.pages.project.requests.RequestsBoardPage;
import ru.devprom.pages.project.requests.RequestsPage;
import ru.devprom.pages.project.settings.MethodologyPage;
import ru.devprom.pages.project.tasks.TaskViewPage;
import ru.devprom.pages.project.tasks.TasksBoardPage;

public class RequestBoardTest extends ProjectTestBase {
    int rcount = 3; 
	private List<Request> requests = new ArrayList<Request>();  
	  
	@BeforeClass
	public void prepare(){
		 PageBase page = new PageBase(driver);
		 String p = DataProviders.getUniqueString();
		 Project project = new Project("SDLCProject"+p, "sdlc"+DataProviders.getUniqueStringAlphaNum(),new Template(this.waterfallTemplateName));
			
		 ProjectNewPage pnp = page.createNewProject();
		SDLCPojectPageBase sdlc =  (SDLCPojectPageBase)pnp.createNew(project);
   		FILELOG.debug("Created new project " + project.getName());
   		
   		//
   		MethodologyPage mp = sdlc.gotoMethodologyPage();
   		mp.selectPlanning("Планирование по релизам и итерациям");
   		mp = mp.saveChanges();
   		
   		//Создаем Пожелания
   		for (int i=0;i<rcount;i++){
   		RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
   		Request testRequest = new Request("TestCR-"+ p+i, "for RequestBoardTest", "Высокий", 10.0,	"Доработка");
   		RequestNewPage ncrp = mip.clickNewCR();
		
		mip = ncrp.createCRShort(testRequest);
		FILELOG.debug("Created Request: " + testRequest.getId());
		requests.add(testRequest);
		
		RequestViewPage	rv = mip.clickToRequest(testRequest.getId());
		RequestEditPage	rep = rv.gotoEditRequest();
		rep.updateRequest(testRequest);
		rv = rep.saveEdited();
   		}
	}
	
	@Test(description="S-1966")
	public void addComment() throws InterruptedException {
		RequestsBoardPage rbp = (new SDLCPojectPageBase(driver)).gotoRequestsBoard();
		String comment = "Для отправки почты используйте следующий код\nusing System.Net;\nusing System.Net.Mail;\n" + 
				"SmtpClient Smtp = new SmtpClient(\"адрес smtp сервера с которого отправляете\", порт сервера);\n" + 
			    "Smtp.EnableSsl = true;\nSmtp.Credentials = new NetworkCredential(\"логин\", \"пароль\");\n" +
		        "MailMessage Message = new MailMessage(\"адрес отправителя\",\"адрес получателя\",\"тема\",\"сообщение\");\n" + 
		        "Smtp.SendAsync(Message, \"t\");";
		/*String commentFormatted = "Для отправки почты используйте следующий код<br><br>using System.Net;<br>using System.Net.Mail;<br><br>" + 
		"SmtpClient Smtp = new SmtpClient(\"адрес smtp сервера с которого отправляете\", порт сервера);<br>" + 
	    "Smtp.EnableSsl = true;<br>Smtp.Credentials = new NetworkCredential(\"логин\", \"пароль\");<br>" +
        "MailMessage Message = new MailMessage(\"адрес отправителя\",\"адрес получателя\",\"тема\",\"сообщение\");<br>" + 
        "Smtp.SendAsync(Message, \"t\");";*/
		rbp.addComment(requests.get(0).getNumericId(), comment);
		RequestViewPage rvp = rbp.clickToRequest(requests.get(0).getId());
		String readComment = rvp.readComment(rvp.readFirstInPageCommentNumber());
		Assert.assertEquals(readComment, comment, "Нет комментария, либо он отображается неверно");
	}
	
	@Test(description="S-1967")
	public void createTask() throws InterruptedException {
		RTask task = new RTask("Задача-1", user, "Анализ", 6.0);
		task.setPriority("Высокий");
		RequestsBoardPage rbp = (new SDLCPojectPageBase(driver)).gotoRequestsBoard();
		rbp = rbp.addTask(requests.get(0).getNumericId(), task);
		List<String> tasks = rbp.readTasksLinks(requests.get(0).getNumericId());
		FILELOG.debug("Найденные ссылки на задачу:");
		 for (String t:tasks){
			 FILELOG.debug(t);
		 }
		Assert.assertTrue(tasks.contains("P"), "Не найдена ссылка на созданную задачу");
		
		RequestViewPage rvp = rbp.clickToRequest(requests.get(0).getId());
		List<RTask> readTasks = rvp.readTasks();
		Assert.assertFalse(readTasks.isEmpty(), "В режиме просмотра Пожелания не отображается ссылка на Задачу");
		Assert.assertEquals(readTasks.get(0).getName(), task.getName(), "В режиме просмотра Пожелания нет Задачи с именем: " + task.getName());
		
		TasksBoardPage tbp = rvp.gotoTasksBoard();
		Assert.assertTrue(tbp.isTaskPresent(readTasks.get(0).getId()), "Добавленная Задача не найдена на Доске Задач");
		
		TaskViewPage tvp = tbp.clickToTask(readTasks.get(0).getId());
		Assert.assertEquals(tvp.readName(), task.getName(), "На странице просмотра Задачи отображается неправильное имя");
		Assert.assertEquals(tvp.readType(), task.getType(), "На странице просмотра Задачи отображается неправильный тип");
		Assert.assertTrue(tvp.readRequest().contains(requests.get(0).getId()), "На странице просмотра Задачи отображается неправильное Пожелание");
		Assert.assertEquals(tvp.readOwner(), user, "На странице просмотра Задачи отображается неправильный Исполнитель");
	}
	
	@Test(description="S-1968")
	public void writeOffTime() throws InterruptedException {
           Spent spent = new Spent("",2.0, user,"описание для задачи");
       	   RequestsBoardPage rbp = (new SDLCPojectPageBase(driver)).gotoRequestsBoard();
       	   rbp = rbp.writeOffSpentTime(requests.get(1).getNumericId(), spent);
       	driver.navigate().refresh();
       	    List<String> spents = rbp.readSpentLinks(requests.get(1).getNumericId());
       	 Assert.assertTrue(spents.contains("2.0"), "Не найдена ссылка на списанное время");	
       	RequestViewPage rvp = rbp.clickToRequest(requests.get(1).getId());
       	List<Spent> readSpent = rvp.readSpentRecords();
        Assert.assertTrue(readSpent.size()==1, "В режиме просмотра Пожелания должна быть одна запись о списанном времени");
       	Assert.assertEquals(readSpent.get(0), spent, "Время списано не правильно");
       	
       	List<RTask> readTasks = rvp.readTasks();
    	Assert.assertFalse(readTasks.isEmpty(), "В режиме просмотра Пожелания не отображается ссылка на Задачу");
    	
    	TasksBoardPage tbp = rvp.gotoTasksBoard();
    	tbp.showAll();
		Assert.assertTrue(tbp.isTaskPresent(readTasks.get(0).getId()), "Добавленная Задача не найдена на Доске Задач");
		
		TaskViewPage tvp = tbp.clickToTask(readTasks.get(0).getId());
		Assert.assertTrue(tvp.readRequest().contains(requests.get(1).getId()), "На странице просмотра Задачи отображается неправильное Пожелание");
	}
	
	/**
	 * [S-1986]
	 * @throws InterruptedException 
	 */
	//@Test  (priority=4)
	public void commonGroupAttributes() throws InterruptedException {
		
		ProductFunction pfunction = new ProductFunction("Функция");
		FunctionsPage fp = (new SDLCPojectPageBase(driver)).gotoFunctions();
		FunctionNewPage fnp = fp.clickNewFunction();
		fp = fnp.createNewFunction(pfunction);
		
		String attach = "resources/config.properties";
		Request request = requests.get(2);
		request.setName("Пожелание-1");
		request.setDescription("Первый вариант описания для Пожелания-1");
		request.setType("Ошибка");
		request.setPriority("Высокий");
		request.setPfunction(pfunction.getId());
		request.setOriginator(user);
		request.setVersion("0.1");
		request.setClosedVersion("0.1");
		RTask task = new RTask("Задача для Пожелания", user, "Анализ", 2);
		request.addTask(task);
		request.addAttachments(attach);
		request.addTag("Тег1");
		request.addlinkedReq(requests.get(0));
		request.addWatcher(user);
		
		
		
		RequestsBoardPage rbp = (new SDLCPojectPageBase(driver)).gotoRequestsBoard();
		RequestEditPage rep = rbp.editRequest(request.getNumericId());
	  rep.updateRequest(request);
	  rep.addTask(task);
	  rep.setVersion(request.getVersion());
	  rep.addFunction(pfunction);
     /* for (String attachment:request.getAttachments()) {
      	rep.addAttachment(new File(attachment));
      }*/
    
      for (String tag:request.getTags()) {
      	rep.addTag(tag);
      }
      for (String watcher:request.getWatchers()) {
      	rep.addWatcher(watcher);
      }
      for (Request linkedR:request.getLinkedRequests()) {
      	rep.addLinkedReqs(linkedR.getId(),"Дубликат");
      }
      
    
      rep.saveEdited();
      
      rbp = new RequestsBoardPage(driver);
      
      rbp.turnOfGrouping();
      
      rbp = rbp.moveToAnotherSection(request.getNumericId(), "0", "В релизе");
      
      rbp = rbp.moveToCompleted(request.getNumericId(), "0.1", null, "");
      rbp = rbp.showSpecificAttributes("UID", "Author", "ClosedInVersion", "RecentComment", "Watchers", "Caption", "SubmittedVersion", "Description", "Attachment", "Priority", "Project", "PlannedRelease", "Type", "Tags", "Function", "Iterations");
      rbp = rbp.showSpecificAttributes("Links");
      Request readRequest = rbp.readCompletedRequest(request.getId());
     
      Assert.assertEquals(readRequest.getName(), request.getName(), "Неправильное имя Пожелания");
      Assert.assertEquals(readRequest.getDescription(), request.getDescription(), "Неправильное описание Пожелания");
      Assert.assertEquals(readRequest.getEstimation(), request.getEstimation(), "Неправильные трудозатраты");
      Assert.assertEquals(readRequest.getRelease(), "0", "Неправильный релиз");
      Assert.assertTrue(rbp.findTextInRequestCard(request.getNumericId(), user), "Неправильный автор");
      Assert.assertEquals(readRequest.getType(), request.getType(), "Неправильный тип Пожелания");
      Assert.assertEquals(readRequest.getPriority(), request.getPriority(), "Неправильный приоритет Пожелания");
      Assert.assertEquals(readRequest.getVersion(), request.getVersion(), "Неправильная версия");
      Assert.assertEquals(readRequest.getWatchers(), request.getWatchers(), "Неправильный список Наблюдателей");
      Assert.assertEquals(readRequest.getTags(), request.getTags(), "Неправильный список Тэгов");
      Assert.assertEquals(readRequest.getVersion(), request.getVersion(), "Неправильный параметр 'Обнаружено в версии'");
      Assert.assertEquals(readRequest.getClosedVersion(), request.getClosedVersion(), "Неправильный параметр 'Выполнено в версии'");
      Assert.assertEquals(readRequest.getPfunction(), request.getPfunction(), "Нет функции, или она не правильная");
      
      //Проверка ссылок
      SDLCPojectPageBase page = rbp.clickToEmbeddedLink(request.getNumericId(), true, "Функция:");  
      Assert.assertTrue(page.isTextPresent(pfunction.getName()), "Не работает ссылка на функцию. Текущий адрес: " + driver.getCurrentUrl());
      driver.navigate().back();
      rbp = new RequestsBoardPage(driver);
      
      page = rbp.clickToEmbeddedLink(request.getNumericId(), true, "Связи:");
      Assert.assertTrue(page.isTextPresent(request.getLinkedRequests().get(0).getName()), "Не работает ссылка на Связанные пожелания. Текущий адрес: " + driver.getCurrentUrl());
      driver.navigate().back();
      rbp = new RequestsBoardPage(driver);
      
      rbp.clickToEmbeddedLink(request.getNumericId(), true, "Проект:");  
      Assert.assertFalse(page.isTextPresent("404/Not Found") || page.isTextPresent("500 / Internal Server Error"), "Не работает ссылка на Проект. Текущий адрес: " + driver.getCurrentUrl());
	}	
	
	@Test  (priority=5, description="S-1988")
	public void dateAttributes() throws InterruptedException {
		Request request = requests.get(1);
		RequestsBoardPage rbp = (new SDLCPojectPageBase(driver)).gotoRequestsBoard();
		  rbp = rbp.showSpecificAttributes("RecordModified", "StartDate", "FinishDate", "RecordCreated", "IterationStartDate", "IterationFinishDate", "IterationEstimatedStart", "IterationEstimatedFinish", "ReleaseStartDate", "ReleaseFinishDate" , "ReleaseEstimatedStart" , "ReleaseEstimatedFinish" , "DeliveryDate");
          List<String> datesStepOne = rbp.readDatesTitles(request.getNumericId());
	      Assert.assertEquals(datesStepOne.size(), 2, "На данном этапе ожидается 2 записи типа Дата");
	      Assert.assertTrue(datesStepOne.contains("Дата создания"), "Отсутствует Дата создания");
	      Assert.assertTrue(datesStepOne.contains("Дата изменения"), "Отсутствует Дата изменения");
          
	      rbp = rbp.moveToAnotherSection(request.getNumericId(), "0", "В релизе");
	      List<String> datesStepTwo = rbp.readDatesTitles(request.getNumericId());
	      Assert.assertEquals(datesStepTwo.size(), 4, "На данном этапе ожидается 4 записей типа Дата");
	      Assert.assertTrue(datesStepTwo.contains("Дата создания"), "Отсутствует Дата создания");
	      Assert.assertTrue(datesStepTwo.contains("Дата изменения"), "Отсутствует Дата изменения");
	      Assert.assertTrue(datesStepTwo.contains("Дата начала"), "Отсутствует Дата начала");
	      
	      RTask task = new RTask("Задача", user, "Разработка", 2.0);
	      RequestPlanningPage rpp = rbp.moveToPlanned(request.getNumericId());
      
	      rpp.fillTask(1, task.getName(), task.getType(), task.getExecutor(), task.getEstimation());
	      rbp = rpp.savePlannedOnBoard();
	      
	      List<String> datesStepThree = rbp.readDatesTitles(request.getNumericId());
	      Assert.assertEquals(datesStepThree.size(), 4, "На данном этапе ожидается 4 записи типа Дата");
	      Assert.assertTrue(datesStepThree.contains("Дата создания"), "Отсутствует Дата создания");
	      Assert.assertTrue(datesStepThree.contains("Дата изменения"), "Отсутствует Дата изменения");
	      Assert.assertTrue(datesStepThree.contains("Дата начала"), "Отсутствует Дата начала");
	      
	      String milestone = "Срок сдачи";
	      Spent spent = new Spent("", 4, user, "Описание");
	      RequestEditPage rep =  rbp.editRequest(request.getNumericId());
	      rep.addNewDeadline(milestone, spent);
	      rep.saveEditedFromBoard();
	      
	      rbp = new RequestsBoardPage(driver);
	      rbp.showAll();
	      
	      List<String> datesStepFour = rbp.readDatesTitles(request.getNumericId());
	      Assert.assertEquals(datesStepFour.size(), 4, "На данном этапе ожидается 4 записи типа Дата");
	      Assert.assertTrue(datesStepFour.contains("Дата создания"), "Отсутствует Дата создания");
	      Assert.assertTrue(datesStepFour.contains("Дата изменения"), "Отсутствует Дата изменения");
	      Assert.assertTrue(datesStepFour.contains("Дата начала"), "Отсутствует Дата начала");
	      Assert.assertTrue(datesStepFour.contains("Оценка завершения"), "Отсутствует Оценка завершения");
	      
	      rbp = rbp.moveToCompleted(request.getNumericId(), "", null, "Релиз: 0");
	      List<String> datesStepFive = rbp.readDatesTitles(request.getNumericId());
	      Assert.assertEquals(datesStepFive.size(), 5, "На данном этапе ожидается тринадцать записей типа Дата");
	      Assert.assertTrue(datesStepFive.contains("Дата создания"), "Отсутствует Дата создания");
	      Assert.assertTrue(datesStepFive.contains("Дата изменения"), "Отсутствует Дата изменения");
	      Assert.assertTrue(datesStepFive.contains("Дата начала"), "Отсутствует Дата начала");
	      Assert.assertTrue(datesStepFive.contains("Оценка завершения"), "Отсутствует Оценка завершения");
	      Assert.assertTrue(datesStepFive.contains("Дата окончания"), "Отсутствует Дата окончания");
	}
	
	@Test  (priority=6, description="S-1989")
	public void estimatesAttributes() throws InterruptedException {
		
		    RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
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
			
			RequestsBoardPage rbp = (new SDLCPojectPageBase(driver)).gotoRequestsBoard();
			 rbp = rbp.showSpecificAttributes("Fact", "EstimationLeft", "Estimation");
			   RequestEditPage rep =  rbp.editRequest(request.getNumericId());    
			rep.editEstimation(22.0);
			  rep.saveEditedFromBoard();
			  driver.navigate().refresh();
		      rbp = new RequestsBoardPage(driver);
		      double readEst = rbp.readEstimation(request.getNumericId());
		      Assert.assertEquals(readEst, 22.0, "Неправильное значение атрибута Трудоемкость");
		      String est = rbp.readAttributeByName(request.getNumericId(), "Оставшаяся трудоемкость");
		      est = est.replace(",",".").replace("ч", "");
		      double readEstLeft = Double.parseDouble(est);
		      Assert.assertEquals(readEstLeft, 22.0, "Неправильное значение атрибута Оставшаяся трудоемкость");
		      
		      Spent spent = new Spent("",5.0, user,"описание для задачи");
		      rbp = rbp.writeOffSpentTime(request.getNumericId(), spent);
		      driver.navigate().refresh();
		      
		      List<String> spents = rbp.readSpentLinks(request.getNumericId());
		      Assert.assertTrue(spents.contains("5.0"), "Не найдена ссылка на списанное время");	
		      
		      rbp = rbp.setupGrouping("Priority");
		      Assert.assertTrue(rbp.isRequestInSection(request.getNumericId(), "Обычный", "Добавлено"), "Пожелание не было перемещено строку Приоритет: обычный");
		      
		      rbp = rbp.changePriorityInContextMenu(request2.getNumericId(), "Низкий");
		      Assert.assertTrue(rbp.isRequestInSection(request2.getNumericId(), "Низкий", "Добавлено"), "Пожелание не было перемещено строку Приоритет: Низкий (через контекстное меню)");
		      
		      rbp = rbp.moveToAnotherSection(request.getNumericId(), "Низкий", "Добавлено");
		      Assert.assertTrue(rbp.isRequestInSection(request.getNumericId(), "Низкий", "Добавлено"), "Пожелание не было перемещено строку Приоритет: Низкий (через перетаскивание)");

	          RequestViewPage rvp = rbp.clickToRequest(request.getId());
	          Assert.assertTrue(rvp.readPriority().contains("Низкий"), "В режиме просмотра пожелания неверный приоритет");
	}
	
	@Test (priority=10, description="S-1992")
	public void lifycycleAttributes() {
		String comment = "Какой-то комментарий на переход";
        int moveCardLimitInSeconds = 10;
        
	    RequestsPage mip = (new SDLCPojectPageBase(driver)).gotoRequests();
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
		
		RequestsBoardPage rbp = (new SDLCPojectPageBase(driver)).gotoRequestsBoard();
		rbp.showAll();
		rbp = rbp.showSpecificAttributes("RecentComment", "State");
		
		Assert.assertTrue(rbp.readAllCardAttributesAsString(request.getNumericId()).contains("Добавлено"), "Не отображается состояние Пожеланий");
		
		rbp = rbp.moveToCompletedUsingMenu(request.getNumericId(), "0.1", comment, null);
		Assert.assertTrue(rbp.readAllCardAttributesAsString(request.getNumericId()).contains("Выполнено"), "Не отображается состояние выполннных Пожеланий");
/*		
		rbp = rbp.setupGrouping("ClosedInVersion");
		rbp = rbp.turnOffFilter("state");
		   
		List<String> groups = rbp.getAllGroupingSections();
		Assert.assertTrue(groups.contains("Выполнено в версии: 0.1"), "Нет группы с именем 'Выполнено в версии: 0.1'");
		
		Date date1 = new Date();
		rbp  = rbp.moveToAnotherSection(request2.getNumericId(), "Выполнено в версии: 0.1", "Добавлено");
		Date date2 = new Date();
		int seconds = (int)((date1.getTime()-date2.getTime())/1000);
		Assert.assertTrue(seconds<moveCardLimitInSeconds, "Пожелание не обнаружено в группе 'Выполнено в версии: 0.1'");
*/		
	}
	
	
}
