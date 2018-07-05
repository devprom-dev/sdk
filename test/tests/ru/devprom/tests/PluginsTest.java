package ru.devprom.tests;

import org.openqa.selenium.By;
import org.testng.Assert;
import org.testng.annotations.BeforeClass;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Project;
import ru.devprom.items.Template;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.admin.AdminPageBase;
import ru.devprom.pages.admin.PluginsPage;
import ru.devprom.pages.kanban.KanbanPageBase;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class PluginsTest extends AdminTestBase
{
	Project testProject;
	
	@BeforeClass
	public void createProject()
	{
		PageBase page = new PageBase(driver);
		String p = DataProviders.getUniqueString();
		testProject = new Project("PluginsTest"+p, "pluginstest"+p,
				new Template(this.waterfallTemplateName)); 
		ProjectNewPage pnp = page.createNewProject();
		pnp.createNew(testProject);
	}

	/** The test disables plugin "support.php" and checks if "Адреса поддержки" link removed */
	@Test (priority=0)
	public void checkDisablePlugin()
	{
		PageBase page = new PageBase(driver);
		SDLCPojectPageBase project = (SDLCPojectPageBase) page.gotoProject(testProject);
		project.gotoStoryMapping();
		Assert.assertTrue(project.isElementPresent(By.xpath("//a[@uid='storymapping-storyboard']")));
		
		PluginsPage pp = project.goToAdminTools().gotoPlugins();
		pp = pp.disablePlugin("storymapping.php");
		
		project = (SDLCPojectPageBase) pp.gotoProject(testProject);
		project.gotoTraceMatrix();
		Assert.assertFalse(project.isElementPresent(By.xpath("//a[@uid='storymapping-storyboard']")));
	}
	
	/** The test enables plugin "support.php" and checks if "Адреса поддержки" link got back */
	@Test(priority=1)
	public void checkEnablePlugin()
	{
		PageBase page = new PageBase(driver);
		SDLCPojectPageBase project = (SDLCPojectPageBase) page.gotoProject(testProject);
		project.gotoTraceMatrix();
		Assert.assertFalse(project.isElementPresent(By.xpath("//a[@uid='storymapping-storyboard']")));
		
		PluginsPage pp = project.goToAdminTools().gotoPlugins();
		pp = pp.enablePlugin("storymapping.php");
		
		project = (SDLCPojectPageBase) pp.gotoProject(testProject);
		project.gotoStoryMapping();
		Assert.assertTrue(project.isElementPresent(By.xpath("//a[@uid='storymapping-storyboard']")));
	}
}
