package ru.devprom.tests;

import java.sql.SQLException;
import java.util.List;

import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.db.Connect;
import ru.devprom.db.Operations;
import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Project;
import ru.devprom.items.Requirement;
import ru.devprom.items.Template;
import ru.devprom.pages.MyReportsPageBase;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.kanban.KanbanPageBase;
import ru.devprom.pages.project.ProjectPageBase;
import ru.devprom.pages.project.RequirementDocumentsPage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requirements.RequirementNewPage;
import ru.devprom.pages.project.requirements.RequirementViewPage;
import ru.devprom.pages.project.requirements.RequirementsPage;
import ru.devprom.pages.project.requirements.TraceMatrixPage;

public class StressTest extends ProjectTestBase {
   double maxLoadTime = Configuration.getStressTimeout();
   int documentsCount = 50;
   int sectionCount = 5;
   int pagesCount = 100;
		
	@Test
	public void RequirementsStressTest() throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException
	{
		String templateName = "StressTestTemplate" + DataProviders.getUniqueStringAlphaNum();
		System.out.println("Max Load Time set to: " + maxLoadTime/1000 );
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		RequirementsPage rp = favspage.gotoRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
		Requirement template = new Requirement(templateName);
		RequirementViewPage rvp = nrp.createSimple(template);
		
		// Читаем из базы
		String[] templateArray = Operations.readTemplateArray(template.getName());
		
		// Загоняем в базу набор данных для стресс-теста
		//Таблица WikiPage
		List<String[]> tree = Operations.createRequirementsTree(templateArray, documentsCount, sectionCount, pagesCount/sectionCount);
		 for (String[] t:tree){
				String query = Operations.generateInsertWikiPageSQLString(t);			
			  Operations.executeUpdate(query);
			 }		
	    	//Таблица Snapshots
			List<String[]>  snaps = Operations.generateSnapshotsTable();			
			 for (String[] s:snaps){
				 String query = Operations.generateInsertSnapshotSQLString(s);
			     Operations.executeUpdate(query);
			 }
			//Таблица SnapshotItems	 
			 List<String[]>  snapitems = Operations.generateSnapshotItemsTable(tree);
			 for (String[] s:snapitems){					
					String query = Operations.generateInsertSnapshotItemSQLString(s);
					Operations.executeUpdate(query);
				 }
			//Таблица SnapshotItemValues 
			 List<String[]>  snapitemvalues = Operations.generateSnapshotItemValuesTable(tree);
			 for (String[] s:snapitemvalues){
					 String query = Operations.generateInsertSnapshotItemValueSQLString(s);
					Operations.executeUpdate(query);
				 }
			 
			Connect.close();
		 
		 
		// Проверяем время загрузки страницы после наполнения базы
		long time = System.currentTimeMillis();
		RequirementDocumentsPage rdp = rvp.gotoRequirementDocuments();
		long docListLoadTime = (System.currentTimeMillis() - time) / 1000;
		System.out.println("Documents List load time: " + docListLoadTime + " sec.");
		Assert.assertTrue(docListLoadTime<maxLoadTime, "Время загрузки списка Документов " + docListLoadTime + " превышает "+ maxLoadTime + " секунд");
		
		time = System.currentTimeMillis();
		rp = rdp.gotoRequirements();
		long listLoadTime = (System.currentTimeMillis() - time) / 1000;
		System.out.println("RequirementList load time: " + listLoadTime + " sec.");
		Assert.assertTrue(listLoadTime<maxLoadTime, "Время загрузки списка Требований " + listLoadTime + " превышает " + maxLoadTime + " секунд");

		time = System.currentTimeMillis();
		rvp = rp.clickToRequirement("R-"+(Integer.parseInt(template.getNumericId())+3));
		long requirementLoadTime = (System.currentTimeMillis() - time) / 1000;
		System.out.println("Single Requirement load time: " + requirementLoadTime + " sec.");
		Assert.assertTrue(requirementLoadTime<maxLoadTime, "Время загрузки страницы Требования " + requirementLoadTime + " превышает " + maxLoadTime + " секунд");

		time = System.currentTimeMillis();
		TraceMatrixPage tmp = rp.gotoTraceMatrix();	
		long traceMatrixLoadTime = (System.currentTimeMillis() - time) / 1000;
		System.out.println("Trace Matrix load time: " + traceMatrixLoadTime + " sec.");		
		Assert.assertTrue(traceMatrixLoadTime<maxLoadTime, "Время загрузки матрицы трассировки " + traceMatrixLoadTime + " превышает " + maxLoadTime + " секунд");
	}
	
	@Test
	public void KanbanTasksStressTest() throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException{
		int requestsCount = 500;
		
		ProjectNewPage npp = (new PageBase(driver)).createNewProject();
		Template kanbanTemplate = new Template(this.kanbanTemplateName);
		String p = DataProviders.getUniqueString();
		 Project project = new Project("Kanban" + p, "kanban" + DataProviders.getUniqueStringAlphaNum(), kanbanTemplate);
		KanbanPageBase firstPage = (KanbanPageBase) npp
				.createNew(project);
		FILELOG.debug("Created new project " + project.getName());
		Assert.assertEquals(firstPage.getProjectTitle(),
				project.getName());
		String[] projectParams= Operations.getProjectIdByName(project.getName());
		
		// Загоняем в базу набор данных для стресс-теста
		//Таблица pm_changerequest
		List<String[]>  requests = Operations.generateRequestsTable(requestsCount, projectParams[0], projectParams[1]);			
		 for (String[] s:requests){
			 String query = Operations.generateInsertRequestSQLString(s);
		     Operations.executeUpdate(query);
		 }
		  
		// Таблица pm_task
		List<String[]> tasks = Operations.generateTasksTable(projectParams[1]);
		for (String[] s : tasks) {
			String query = Operations.generateInsertTaskSQLString(s);
			Operations.executeUpdate(query);
		}

		// Таблица comment
		List<String[]> comments = Operations
				.generateCommentsTable(projectParams[1]);
		for (String[] s : comments) {
			String query = Operations.generateInsertCommentSQLString(s);
			Operations.executeUpdate(query);
		}

		// Таблица pm_activity
		List<String[]> activities = Operations
				.generateActivitiesTable(projectParams[1]);
		for (String[] s : activities) {
			String query = Operations.generateInsertActivitySQLString(s);
			Operations.executeUpdate(query);
		}
		// Таблица pm_attachment
		List<String[]> attachments = Operations
				.generateAttachmentTable(projectParams[1]);
		for (String[] s : attachments) {
			String query = Operations.generateInsertAttachmentSQLString(s);
			Operations.executeUpdate(query);
		}
		// Таблица pm_ChangeRequestLinkId
		List<String[]> crLinks = Operations
				.generateCRLinkTable(projectParams[1]);
		for (String[] s : crLinks) {
			String query = Operations.generateInsertCRLinkSQLString(s);
			Operations.executeUpdate(query);
		}
		Connect.close();
		
		MyReportsPageBase mrpb = firstPage.gotoMyReports();
		long time = System.currentTimeMillis();
		ProjectPageBase ppb = mrpb.openReport("allissues");
		double allTasksLoadTime = (System.currentTimeMillis() - time) / 1000;
		System.out.println("Все задачи в проекте: " + allTasksLoadTime + " sec.");

		mrpb = ppb.gotoMyReports();
		time = System.currentTimeMillis();
		ppb = mrpb.openReport("kanbanboard");
		double kanbanBoardLoadTime = (System.currentTimeMillis() - time) / 1000;
		System.out.println("Kanban доска: " + kanbanBoardLoadTime + " sec.");
		
		Assert.assertTrue(allTasksLoadTime<maxLoadTime, "Время загрузки отчета 'Все задачи в проекте' " + allTasksLoadTime + " превышает "+ maxLoadTime + " секунд");
		Assert.assertTrue(kanbanBoardLoadTime<maxLoadTime, "Время загрузки отчета 'Kanban доска' " + kanbanBoardLoadTime + " превышает " + maxLoadTime + " секунд");
	}
	
	
}
