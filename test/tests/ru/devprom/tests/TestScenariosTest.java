package ru.devprom.tests;

import java.util.List;

import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.DataProviders;
import ru.devprom.helpers.ScreenshotsHelper;
import ru.devprom.items.Project;
import ru.devprom.items.Requirement;
import ru.devprom.items.Template;
import ru.devprom.items.TestScenario;
import ru.devprom.items.User;
import ru.devprom.pages.FavoritesPage;
import ru.devprom.pages.LoginPage;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.admin.ActivitiesPage;
import ru.devprom.pages.admin.UsersListPage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.requirements.RequirementAddToBaselinePage;
import ru.devprom.pages.project.requirements.RequirementEditPage;
import ru.devprom.pages.project.requirements.RequirementNewPage;
import ru.devprom.pages.project.requirements.RequirementSaveVersionPage;
import ru.devprom.pages.project.requirements.RequirementViewPage;
import ru.devprom.pages.project.requirements.RequirementsPage;
import ru.devprom.pages.project.settings.ProjectMembersPage;
import ru.devprom.pages.project.testscenarios.TestScenarioAddToBaselinePage;
import ru.devprom.pages.project.testscenarios.TestScenarioEditPage;
import ru.devprom.pages.project.testscenarios.TestScenarioNewPage;
import ru.devprom.pages.project.testscenarios.TestScenarioViewPage;
import ru.devprom.pages.project.testscenarios.TestScenariosPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationNewPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationViewPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationsPage;

public class TestScenariosTest extends ProjectTestBase {

	
	/**This method creates Requirement, saves it's version, creates new Test Scenario and trace it on the saved version of the Requirement.
	 * Then creates Analyst user, that make changes in current version of the Requirement and saves the new version.
	 * The check is - default user shouldn't see the change notification in Test Scenarios list.
	 *  */
	@Test
		public void traceOnRequirementVersion() {
		
		//Test constants 
        String p = DataProviders.getUniqueString();
		String content = "Изначальное содержание Требования";
		String newContent = "Изначальное содержание плюс новый текст";
		String veryNewContent = "Изначальное содержание плюс новый текст и еще немножко для версии";
		String version1 = "Итерация 1";
		String version2 = "Итерация 2";
		User analyst = new User("Analyst"+p, "1", "Analyst"+p, "analyst"+p+"@mail.com", false, true);
		Requirement testRequirement = new Requirement("TestR"+p, content);
		TestScenario testScenario = new TestScenario("TestScenario" + p);
		TestScenario testScenario2 = new TestScenario("TestScenario2" + p);
		
		//Creating Analyst and making him the member of the project
		PageBase page = new PageBase(driver);
		ActivitiesPage ap = page.goToAdminTools();
		UsersListPage ulp = ap.gotoUsers();
		ulp = ulp.addNewUser(analyst, false);
		
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) ulp.gotoProject(webTest);
		ProjectMembersPage pmp = favspage.gotoMembers();
		
		pmp = pmp.gotoAddMember().addUserToProject(analyst, "Аналитик", 10, "");
		
		//Create a Requirement
		RequirementsPage rp = pmp.gotoRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
		RequirementViewPage rvp = nrp.create(testRequirement);
		
		TestSpecificationsPage tscp = (new SDLCPojectPageBase(driver))
				.gotoTestPlans();
		TestSpecificationNewPage ntsp = tscp.createNewSpecification();
		TestScenario testPlan = new TestScenario("TestPlan"
				+ DataProviders.getUniqueString());
		TestSpecificationViewPage tspecp = ntsp.create(testPlan);
		
		// Create Test Scenario and trace it to the Requirement
		TestScenariosPage tsp = tspecp.gotoTestScenarios();
		TestScenarioNewPage tsnp = tsp.clickNewTestScenario();
		tsnp.addRequirement(testRequirement.getId(), "");
		
		
		TestScenarioViewPage tsvp = tsnp.createNewScenarioShort(testScenario, testPlan);

		
		rp = tsvp.gotoRequirements();
		rvp = rp.clickToRequirement(testRequirement.getId());
		RequirementEditPage rep = rvp.editRequirement();
		rep.addContent(newContent);
		rvp = rep.saveChanges();
		
		tsp = favspage.gotoTestScenarios();
		Assert.assertTrue(tsp.isNotification(testScenario.getId()), "В тестовом сценарии отсутствует оповещение");
		
		//Save version
		rp = tsp.gotoRequirements();
		rvp = rp.clickToRequirement(testRequirement.getId());
		RequirementSaveVersionPage rsvp = rvp.saveVersion();
		rvp = rsvp.saveVersion(version1, "Первичная версия требования");
		
		//Create another Test Scenario and trace it to the Requirement's saved version
		tsp = rvp.gotoTestScenarios();
		tsnp = tsp.clickNewTestScenario();
		tsnp.addRequirement(testRequirement.getId(), version1);
		tsvp = tsnp.createNewScenarioShort(testScenario2, testPlan);				
		
		//Login as Analyst
		LoginPage lp = tsvp.logOut();
		FavoritesPage fp = lp.loginAs(analyst.getUsername(), analyst.getPass());
		favspage = (SDLCPojectPageBase) fp.gotoProject(webTest);
		
		//Change the Requirement's content and save the new version
		rp = favspage.gotoRequirements();
		rvp = rp.clickToRequirement(testRequirement.getId());
		rvp = rvp.editContent(testRequirement.getClearId(), veryNewContent);
		rsvp = rvp.saveVersion();
		rvp = rsvp.saveVersion(version2, "Версия Аналитика");
		
		//Login as a default user
		lp = rvp.logOut();
		fp = lp.loginAs(Configuration.getUsername(), Configuration.getPassword());
		favspage = (SDLCPojectPageBase) fp.gotoProject(webTest);
		
		//Check the Test Scenarios page for notifications
		tsp = favspage.gotoTestScenarios();
		Assert.assertFalse(tsp.isNotification(testScenario2.getId()), "В тестовом сценарии присутствует оповещение");
		
	}
	

	/**This method creates a Requirement, adds it to Baseline. Then creates Test Scenario links to the Requirement baseline version.
	 * Then adds the Requirement to another Baseline and checks changes in the Test Scenario.
	 * @throws InterruptedException 
	 *  */
	@Test
		public void addTestSpecificationToBaseline() throws InterruptedException {
		
		String index = DataProviders.getUniqueString();
		Requirement testRequirement = new Requirement("addTestSpecificationToBaseline"+index, "Тестовое содержание");
		TestScenario testScenario = new TestScenario("addTestSpecificationToBaseline" + index);
		String baseline1 = "Бейзлайн один";
		String baseline2 = "Бейзлайн два";
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		
		//Create new Requirement
		RequirementsPage rp = favspage.gotoRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
		RequirementViewPage rvp = nrp.create(testRequirement);
		
		//Add To Baseline
		RequirementAddToBaselinePage ratb = rvp.addToBaseline();
		Requirement testRequirementBaseline1 = testRequirement.clone();
		rvp = ratb.addToBaseline(testRequirementBaseline1, baseline1, false);

		//Create Test Scenario and trace it to the Requirement's saved version
		TestSpecificationsPage tscp = (new SDLCPojectPageBase(driver))
				.gotoTestPlans();
		TestSpecificationNewPage ntsp = tscp.createNewSpecification();
		TestScenario testPlan = new TestScenario("addTestSpecificationToBaseline"
				+ DataProviders.getUniqueString());
		TestSpecificationViewPage tspecp = ntsp.create(testPlan);
		
		TestScenariosPage tsp = tspecp.gotoTestScenarios();
		TestScenarioNewPage tsnp = tsp.clickNewTestScenario();
		tsnp.addRequirement(testRequirementBaseline1.getName(), "");
		TestScenarioViewPage tsvp = tsnp.createNewScenarioShort(testScenario, testPlan);
	
		//Add Requirement to  baseline 2 
		rp = tsvp.gotoRequirements();
		rvp = rp.clickToRequirement(testRequirementBaseline1.getId());
		Requirement testRequirementBaseline2 = testRequirement.clone();
		ratb = rvp.addToBaseline();
		rvp = ratb.addToBaseline(testRequirementBaseline2, baseline2);
		
		//Add Scenario to Baseline 2
		tsp = rvp.gotoTestScenarios();
		tsvp = tsp.clickToTestScenario(testScenario.getId());
		TestScenario testScenario2 = testScenario.clone();
		TestScenarioAddToBaselinePage tsatbp = tsvp.addToBaseline();
		tsvp =	tsatbp.addToBaseline(testScenario2, baseline2);
		tsp = tsvp.gotoTestScenarios();
		tsvp = tsp.clickToTestScenario(testScenario2.getId());
		
		//Read Properties
		TestScenarioEditPage tspp = tsvp.edit();
		List<String> requirements = tspp.readLinkedRequirements();
		List<String> scenarios = tspp.readOriginalScenarios();
		tspp.close();
		Assert.assertTrue(requirements.contains(testRequirementBaseline2.getId()), "Не найдена ссылка на покрытое Требование");
		Assert.assertTrue(scenarios.contains(testScenario.getId()), "Не найдена ссылка на исходный Сценарий");
	}
	

	@Test(description="S-1668")
		public void updateBaselineWithSpecificationSections() {
		String p = DataProviders.getUniqueString();
		TestScenario specification = new TestScenario("TestSpec" + p);
		TestScenario specificationInBaseline = specification.clone();
		TestScenario section = new TestScenario("TestScenario" + p);
		section.setContent("Содержание раздела");
		specification.setContent("Содержание тестовой спецификации");
		String baseline = "Бейзлайн один";
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		
		//Create new Test Specification
		TestSpecificationsPage tsp = favspage.gotoTestPlans();
		TestSpecificationNewPage tsnp = tsp.createNewSpecification();
		TestSpecificationViewPage tsvp = tsnp.create(specification);
		
		//Add to Baseline
		TestScenarioAddToBaselinePage tsatb = tsvp.addToBaseline();
		tsatb.addToBaseline(specificationInBaseline, baseline);
		
		//Add section to the source specification
		tsp = tsvp.gotoTestPlans();
		tsvp = tsp.clickToSpecification(specification.getId());
		TestScenarioNewPage tnp = tsvp.addSection();
		tnp.createNewScenario(section);
		
		//Compare baseline version with the source
		tsp = tsvp.gotoTestPlans();
		tsvp = tsp.clickToSpecification(specificationInBaseline.getId());
		tsvp = tsvp.showBaseline(baseline);
		tsvp = tsvp.compareWithVersion(specification.getName());
		Assert.assertTrue(tsvp.isAlertPresent(), "Нет предупреждающего об изменениях знака");
		Assert.assertTrue(tsvp.isTextPresent(section.getName()), "В режиме сравнения не видно секции исходной версии");
		tsvp = tsvp.copySection();
		
		//Check new content
		tsp = tsvp.gotoTestPlans();
		tsvp = tsp.clickToSpecification(specificationInBaseline.getId());
		try {
			Thread.sleep(2000);
		} catch (InterruptedException e) {
		}
		Assert.assertTrue(tsvp.isTextPresent(section.getName()), "Секция не отображается в версии бейзлайна");
	}
	
	@Test(description="S-2498")
		public void massAddToTestPlan() {
		String p = DataProviders.getUniqueString();
		TestScenario testPlanOriginal = new TestScenario("TestPlanOriginal" + p);
		TestScenario testPlanNew = new TestScenario("TestPlanNew" + p);
		TestScenario testScenario1 = new TestScenario("TestScenario1" + p);
		testScenario1.setContent("Содержимое первого сценария");
		TestScenario testScenario2 = new TestScenario("TestScenario2" + p);
		testScenario2.setContent("Содержимое второго сценария");
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		
		//Create new Test Specification
		TestSpecificationsPage tsp = favspage.gotoTestPlans();
		TestSpecificationNewPage tsnp = tsp.createNewSpecification();
		TestSpecificationViewPage tsvp = tsnp.create(testPlanOriginal);
		TestScenarioViewPage tscvp = tsvp.addNewTestScenario(testScenario1);
		tsp = tscvp.gotoTestPlans();
		tsnp = tsp.createNewSpecification();
		tsvp = tsnp.create(testPlanNew);
		TestScenariosPage tscp = tsvp.gotoTestScenarios();
		TestScenarioNewPage tscnp = tscp.clickNewTestScenario();
		tscvp = tscnp.createNewScenarioShort(testScenario2, testPlanOriginal);
		
		tscp = tscvp.gotoTestScenarios();
		tscp.showNRows("all");
		tscp.checkTestScenario(testScenario1.getId());
		tscp.checkTestScenario(testScenario2.getId());
		tscp.massIncludeToTestPlan(testPlanNew.getId());
		Assert.assertTrue(tscvp.isChildScenarioPresent(testScenario1.getName()), "В новом родительском тест плане отсутствует тестовый сценарий " + testScenario1.getId());
		Assert.assertTrue(tscvp.isChildScenarioPresent(testScenario2.getName()), "В новом родительском тест плане отсутствует тестовый сценарий " + testScenario2.getId());

	}
	
}
