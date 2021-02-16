package ru.devprom.tests;

import java.io.File;
import java.util.List;

import org.openqa.selenium.WebElement;
import org.testng.Assert;
import org.testng.annotations.AfterClass;
import org.testng.annotations.Test;

import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.DataProviders;
import ru.devprom.helpers.FileOperations;
import ru.devprom.helpers.SCMHelper;
import ru.devprom.items.Commit;
import ru.devprom.items.Project;
import ru.devprom.items.RTask;
import ru.devprom.items.Request;
import ru.devprom.items.Spent;
import ru.devprom.items.Template;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.admin.AdminPageBase;
import ru.devprom.pages.admin.SystemTasksPage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.repositories.ChangesPage;
import ru.devprom.pages.project.repositories.CommitPage;
import ru.devprom.pages.project.repositories.RepositoryCommitsPage;
import ru.devprom.pages.project.repositories.RepositoryConnectPage;
import ru.devprom.pages.project.repositories.RepositoryCreatedPage;
import ru.devprom.pages.project.repositories.RepositoryFilesPage;
import ru.devprom.pages.project.repositories.RepositoryNewPage;
import ru.devprom.pages.project.requests.RequestNewPage;
import ru.devprom.pages.project.requests.RequestViewPage;
import ru.devprom.pages.project.requests.RequestsPage;
import ru.devprom.pages.project.tasks.TaskNewPage;
import ru.devprom.pages.project.tasks.TaskViewPage;
import ru.devprom.pages.project.tasks.TasksPage;

/** Class that will hold all test related to SCM functionality */
public class SCMTest extends ProjectTestBase {

	
/**  This test will check if request is correctly updated when committing a file using SVN with specific comment 
 * @throws InterruptedException */
	@Test
	public void testRequestCompletedOnCommit() throws InterruptedException {
		
		// Configure SCM connection
		String repositoryUrl = Configuration.getSVNUrl();
		String repositoryPath = Configuration.getSVNPath();
		String repositoryFullPath;
		 if (repositoryPath.equals("")) repositoryFullPath = repositoryUrl;
		 else repositoryFullPath = repositoryUrl +"/" + repositoryPath;
		String repositoryUserName = Configuration.getSVNUser();
		String repositoryUserPassword = Configuration.getSVNPass();
		String repositoryName = "SVNLocal"+DataProviders.getUniqueString();
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		RepositoryConnectPage rcp = favspage.gotoRepositoryConnectPage();
		RepositoryNewPage rnp = rcp.addNewConnection();
		rnp.createConnection("svn", repositoryUrl, repositoryPath, repositoryName, repositoryUserName, repositoryUserPassword);
		rnp.addUserMapping(user, repositoryUserName, repositoryUserPassword);
		RepositoryCreatedPage rcrp = rnp.saveConnection();
		rcp = rcrp.gotoRepositoryConnectPage();
		FILELOG.debug("Created new repository connection: " + repositoryName);
		
		
		// Check that Files from linked SVN are displayed
		List<String> filesFromSVN = SCMHelper.getFilesList(repositoryFullPath, repositoryUserName, repositoryUserPassword);
		for (String s:filesFromSVN){
		   FILELOG.debug("Files obtained from SVN:");
		   FILELOG.debug(s);
		}
		RepositoryFilesPage rfp = rcp.gotoFiles(repositoryName);
		List<String> filesFromDevprom = rfp.getTestFilesList();
		for (String s:filesFromDevprom){
			FILELOG.debug("Files obtained from DEVPROM page:");
			FILELOG.debug(s);
		}
		Assert.assertTrue(filesFromDevprom.containsAll(filesFromSVN), "Some of the files found in the repository are not displayed in DEVPROM");
	//	Assert.assertTrue(filesFromSVN.containsAll(filesFromDevprom), "There are some redundant files in DEVPROM view");

		// Check that working copy folder exists and a checkout has been made to it
		String workingCopyPath = Configuration.getWorkingCopy();
		SCMHelper.setWorkingCopy(repositoryFullPath, workingCopyPath, repositoryUserName, repositoryUserPassword);
		Assert.assertTrue(SCMHelper.isWorkingCopy(workingCopyPath), "Can't create Working Copy");
	
		//Create new Request for commit comment
		RequestsPage rp = (new SDLCPojectPageBase(driver)).gotoRequests();
		RequestNewPage reqnp = rp.clickNewCR();
		Request request = new Request("SCMTest Request"+DataProviders.getUniqueString(),"Request for SCM Test", Request.getHighPriority(), 10.0, "Ошибка");
		reqnp.createNewCR(request);
		FILELOG.debug("Created new Request: " + request);
		
		// Create a file with random name and content
		File txtFile = FileOperations.createTxt(workingCopyPath+"//FileToBeCommited"+DataProviders.getUniqueStringAlphaNum()+".php", "Some content");
		FILELOG.debug("Created file: " + txtFile.getAbsolutePath());
		
		 //Add & commit file to SVN with special comment
		String textComment = "SCM Test commit";
		String spentTime = "1";
		String comment = request.getId()+" #resolve #time "+spentTime+"h #comment " + textComment;
		SCMHelper.addFile(txtFile.getAbsolutePath(), repositoryUserName, repositoryUserPassword);
		String revisionNumber = SCMHelper.commitFile(txtFile.getAbsolutePath(), comment, repositoryUserName, repositoryUserPassword);
		
        //Run synchronization task
		AdminPageBase apb = reqnp.goToAdminTools();
		SystemTasksPage stp = apb.gotoSystemTasks();
		stp = stp.runSystemTask("Синхронизация с системой контроля версий");
		favspage = (SDLCPojectPageBase) stp.gotoProject(webTest);
		
		// Check that table has a link to automatically completed request
		RepositoryCommitsPage rcomp = favspage.gotoRepositoryCommitsPage();
		rcomp= rcomp.update();
	//	rcomp = rcomp.setConnectionFilter(repositoryName);
		String readComment = rcomp.readComment(revisionNumber);
		Assert.assertTrue(readComment.contains("["+request.getId()+"]"), "There is no link to the Request in the commit comments");
		
		try {
			Thread.sleep(7000);
		} catch (InterruptedException e) {
			e.printStackTrace();
		}
		
		// Open the request
		List<WebElement> requestsLinks = rcomp.readRequestLinksFromComment(revisionNumber);
		List<Commit> commits = rcomp.readCommitsByVersion(revisionNumber);
		RequestViewPage rvp;
		for (WebElement requestLink:requestsLinks){
			requestLink.click();
			rvp = new RequestViewPage(driver);
			//Check State
			Assert.assertEquals(rvp.readState(), "Выполнено", "Incorrect state of Request " + request.getId());
			//Check Commits list
			List<Commit> commitsFromRequest = rvp.readCommitRecords();
			Assert.assertTrue(commitsFromRequest.containsAll(commits), "Some of commits are not displayed in Request");
			//Check Comment
			Assert.assertEquals(rvp.readComment(rvp.readFirstInPageCommentNumber()),textComment);
			//Check Time estimates
            List<Spent> timeEstimates = rvp.readSpentRecords();
            Assert.assertEquals(timeEstimates.size(), 2);
            Assert.assertEquals(timeEstimates.get(0).hours, Double.parseDouble(spentTime));
		}
	}
	
	
	/**  This test will check if task is correctly updated when committing a file using SVN with specific comment */
	@Test
	public void testTaskCompletedOnCommit() {

		// Configure SCM connection
		String repositoryUrl = Configuration.getSVNUrl();
		String repositoryPath = Configuration.getSVNPath();
		String repositoryFullPath;
		 if (repositoryPath.equals("")) repositoryFullPath = repositoryUrl;
		 else repositoryFullPath = repositoryUrl +"/" + repositoryPath;
		String repositoryUserName = Configuration.getSVNUser();
		String repositoryUserPassword = Configuration.getSVNPass();
		String repositoryName = "SVNLocal"+DataProviders.getUniqueString();
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		RepositoryConnectPage rcp = favspage.gotoRepositoryConnectPage();
		RepositoryNewPage rnp = rcp.addNewConnection();
		rnp.createConnection("svn", repositoryUrl, repositoryPath, repositoryName, repositoryUserName, repositoryUserPassword);
		rnp.addUserMapping(user, repositoryUserName, repositoryUserPassword);
		RepositoryCreatedPage rcrp = rnp.saveConnection();
		rcp = rcrp.gotoRepositoryConnectPage();
		FILELOG.debug("Created new repository connection: " + repositoryName);
		
		
		// Check that Files from linked SVN are displayed
		List<String> filesFromSVN = SCMHelper.getFilesList(repositoryFullPath, repositoryUserName, repositoryUserPassword);
		for (String s:filesFromSVN){
		   FILELOG.debug("Files obtained from SVN:");
		   FILELOG.debug(s);
		}
		RepositoryFilesPage rfp = rcp.gotoFiles(repositoryName);
		List<String> filesFromDevprom = rfp.getTestFilesList();
		for (String s:filesFromDevprom){
			FILELOG.debug("Files obtained from DEVPROM page:");
			FILELOG.debug(s);
		}
		Assert.assertTrue(filesFromDevprom.containsAll(filesFromSVN), "Some of the files found in the repository are not displayed in DEVPROM");
	//	Assert.assertTrue(filesFromSVN.containsAll(filesFromDevprom), "There are some redundant files in DEVPROM view");
		
		// Check that working copy folder exists and a checkout has been made to it
		String workingCopyPath = Configuration.getWorkingCopy();
		SCMHelper.setWorkingCopy(repositoryFullPath, workingCopyPath, repositoryUserName, repositoryUserPassword);
		Assert.assertTrue(SCMHelper.isWorkingCopy(workingCopyPath), "Can't create Working Copy");
	
		//Create new Task for commit comment
		TasksPage tp =  (new SDLCPojectPageBase(driver)).gotoTasks();
		TaskNewPage tnp = tp.createNewTask();
		RTask task = new RTask("SCMTest Task"+DataProviders.getUniqueString(), user, "Разработка" ,10.0);
        tp = tnp.createTask(task);
        FILELOG.debug("Created new Task: " + task);

		// Create a file with random name and content
		File txtFile = FileOperations.createTxt(workingCopyPath+"//FileToBeCommited"+DataProviders.getUniqueStringAlphaNum()+".txt", "Some content");
		FILELOG.debug("Created file: " + txtFile.getAbsolutePath());
		
		 //Add & commit file to SVN with special comment
		String textComment = "SCM Test commit";
		String spentTime = "1";
		String comment = task.getId()+" #resolve #time "+spentTime+"h #comment " + textComment;
		SCMHelper.addFile(txtFile.getAbsolutePath(), repositoryUserName, repositoryUserPassword);
		String revisionNumber = SCMHelper.commitFile(txtFile.getAbsolutePath(), comment, repositoryUserName, repositoryUserPassword);
		
        //Run synchronization task
		AdminPageBase apb = tp.goToAdminTools();
		SystemTasksPage stp = apb.gotoSystemTasks();
		stp = stp.runSystemTask("Синхронизация с системой контроля версий");
		favspage = (SDLCPojectPageBase) stp.gotoProject(webTest);
		
		// Check that table has a link to automatically completed task
		RepositoryCommitsPage rcomp = favspage.gotoRepositoryCommitsPage();
		rcomp= rcomp.update();
		//Фильтрация временно отключена
		//rcomp = rcomp.setConnectionFilter(repositoryName);
		String readComment = rcomp.readComment(revisionNumber);
		Assert.assertTrue(readComment.contains("["+task.getId()+"]"), "There is no link to the Task in the commit comments");
		
		
		// Open the task
		List<WebElement> tasksLinks = rcomp.readTasksLinksFromComment(revisionNumber);
		List<Commit> commits = rcomp.readCommitsByVersion(revisionNumber);
		TaskViewPage tvp;
		for (WebElement taskLink:tasksLinks){
			taskLink.click();
			tvp = new TaskViewPage(driver);
			//Check State
			Assert.assertEquals(tvp.readStatus(), "Выполнена", "Incorrect state of Task " + task.getId());
			//Check Commits list
			List<Commit> commitsFromTask = tvp.readCommitRecords();
			Assert.assertTrue(commitsFromTask.containsAll(commits), "Some of commits are not displayed in Request");
		}
	}
		
	
	/**  Тест проверяет отображение изменений в коде файлов после коммита*/
	@Test
	public void testCodeReview() {
		
		String contentBefore = "Содержимое файла после его создания";
		String contentAfter = "Содержимое файла после его изменения";
		String filename = "file"+DataProviders.getUniqueStringAlphaNum()+".php";
		
		// Configure SCM connection
		String repositoryUrl = Configuration.getSVNUrl();
		String repositoryPath = Configuration.getSVNPath();
		String repositoryFullPath;
		 if (repositoryPath.equals("")) repositoryFullPath = repositoryUrl;
		 else repositoryFullPath = repositoryUrl +"/" + repositoryPath;
		String repositoryUserName = Configuration.getSVNUser();
		String repositoryUserPassword = Configuration.getSVNPass();
		String repositoryName = "SVNLocal"+DataProviders.getUniqueString();
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		RepositoryConnectPage rcp = favspage.gotoRepositoryConnectPage();
		RepositoryNewPage rnp = rcp.addNewConnection();
		rnp.createConnection("svn", repositoryUrl, repositoryPath, repositoryName, repositoryUserName, repositoryUserPassword);
		rnp.addUserMapping(user, repositoryUserName, repositoryUserPassword);
		RepositoryCreatedPage rcrp = rnp.saveConnection();
		rcp = rcrp.gotoRepositoryConnectPage();
		FILELOG.debug("Created new repository connection: " + repositoryName);
		
	    // Check that working copy folder exists and a checkout has been made to it
		String workingCopyPath = Configuration.getWorkingCopy();
		SCMHelper.setWorkingCopy(repositoryFullPath, workingCopyPath, repositoryUserName, repositoryUserPassword);
		Assert.assertTrue(SCMHelper.isWorkingCopy(workingCopyPath), "Can't create Working Copy");
	
		// Create a file with random name and content
		File phpFile = FileOperations.createTxt(workingCopyPath+"//"+filename, contentBefore);
		FILELOG.debug("Created file: " + phpFile.getAbsolutePath());
		
		 //Add & commit file to SVN 
		String textComment = "Commit new file";
		SCMHelper.addFile(phpFile.getAbsolutePath(), repositoryUserName, repositoryUserPassword);
		SCMHelper.commitFile(phpFile.getAbsolutePath(), textComment, repositoryUserName, repositoryUserPassword);
		
        //Run synchronization task
		AdminPageBase apb =  (new SDLCPojectPageBase(driver)).goToAdminTools();
		SystemTasksPage stp = apb.gotoSystemTasks();
		stp = stp.runSystemTask("Синхронизация с системой контроля версий");
		
		//Edit file and commit
		phpFile = FileOperations.editTxt(workingCopyPath+"//"+filename, contentAfter);
		textComment = "Commit edited file";
		SCMHelper.addFile(phpFile.getAbsolutePath(), repositoryUserName, repositoryUserPassword);
		SCMHelper.commitFile(phpFile.getAbsolutePath(), textComment, repositoryUserName, repositoryUserPassword);
		
		//Run synchronization task
		apb =  (new SDLCPojectPageBase(driver)).goToAdminTools();
		stp = apb.gotoSystemTasks();
		stp = stp.runSystemTask("Синхронизация с системой контроля версий");
		
		favspage = (SDLCPojectPageBase) stp.gotoProject(webTest);
		
		//Check the changes
		RepositoryCommitsPage rcomp = favspage.gotoRepositoryCommitsPage();
		rcomp= rcomp.update();
		CommitPage cp = rcomp.clickToTheLastCommitCommentedAs(textComment);
		ChangesPage chp = cp.seeChanges(filename);
		Assert.assertTrue(chp.isFileChanged(), "Изменения в файле не отображаются");
	}
}
