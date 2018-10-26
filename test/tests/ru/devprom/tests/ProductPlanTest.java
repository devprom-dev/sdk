package ru.devprom.tests;

import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.helpers.DateHelper;
import ru.devprom.items.Iteration;
import ru.devprom.items.Project;
import ru.devprom.items.Release;
import ru.devprom.items.Template;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.project.IterationNewPage;
import ru.devprom.pages.project.ReleaseNewPage;
import ru.devprom.pages.project.ReleasesIterationsPage;
import ru.devprom.pages.project.SDLCPojectPageBase;

public class ProductPlanTest extends ProjectTestBase {


	/** The test creates 2 releases and 2 iterations for each of them*/
	@Test
	public void CreateProductPlan(){
		String p = DataProviders.getUniqueStringAlphaNum();
		
		Release release1 = new Release("1-Р+" + p,"Release for ProductPlanTest",DateHelper.getCurrentDate(),DateHelper.getDayAfter(13));
		Release release2 = new Release("2-Р+" + p,"Release for ProductPlanTest",DateHelper.getDayAfter(14),DateHelper.getDayAfter(27));
		
		Iteration iteration11 = new Iteration("-"+p,"Итерация для теста", DateHelper.getCurrentDate(), DateHelper.getDayAfter(7), release1.getNumber());
		Iteration iteration12 = new Iteration("-1-"+p,"Итерация для теста", DateHelper.getDayAfter(7), DateHelper.getDayAfter(14), release1.getNumber());
	
		Iteration iteration21 = new Iteration("-"+p,"Итерация для теста", DateHelper.getDayAfter(14), DateHelper.getDayAfter(21), release2.getNumber());
		Iteration iteration22 = new Iteration("-1-"+p,"Итерация для теста", DateHelper.getDayAfter(21), DateHelper.getDayAfter(28), release2.getNumber());
	
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(
						this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		ReleasesIterationsPage rip = favspage.gotoReleasesIterations();
		
		//Create first release, velocity = 10
		ReleaseNewPage rnp = rip.addRelease();
		rip = rnp.createRelease(release1); 
		
		//Creating 2 iterations for the release
		IterationNewPage inp = rip.addIteration();
		rip = inp.createIteration(iteration11);
		inp = rip.addIteration();
		rip = inp.createIteration(iteration12);
				
		//Create second release, velocity = 20
		rnp = rip.addRelease();
		rip = rnp.createRelease(release2);
		
		//Creating 2 iterations for the release
			inp = rip.addIteration();
		rip = inp.createIteration(iteration21);
		inp = rip.addIteration();
		rip = inp.createIteration(iteration22);
		
		//Verify displayed releases and iterations
		rip.showAll();
		Assert.assertEquals(rip.readRelease(release1.getNumber()), release1, "Ошибка в данных, отображаемых для Релиза " + release1.getNumber());
		Assert.assertEquals(rip.readRelease(release2.getNumber()), release2, "Ошибка в данных, отображаемых для Релиза " + release2.getNumber());
		Assert.assertEquals(rip.readIteration(iteration11.getReleaseName()+"."+iteration11.getName()), iteration11, "Ошибка в данных, отображаемых для Итерации " + iteration11.getReleaseName()+"."+iteration11.getName());
		Assert.assertEquals(rip.readIteration(iteration12.getReleaseName()+"."+iteration12.getName()), iteration12, "Ошибка в данных, отображаемых для Итерации " + iteration12.getReleaseName()+"."+iteration12.getName());
		Assert.assertEquals(rip.readIteration(iteration21.getReleaseName()+"."+iteration21.getName()), iteration21, "Ошибка в данных, отображаемых для Итерации " + iteration21.getReleaseName()+"."+iteration21.getName());
		Assert.assertEquals(rip.readIteration(iteration22.getReleaseName()+"."+iteration22.getName()), iteration22, "Ошибка в данных, отображаемых для Итерации " + iteration22.getReleaseName()+"."+iteration22.getName());
	}
	
	
}
