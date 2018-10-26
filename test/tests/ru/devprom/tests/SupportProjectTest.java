package ru.devprom.tests;

import java.util.List;

import org.testng.Assert;
import org.testng.annotations.BeforeClass;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Project;
import ru.devprom.items.Request;
import ru.devprom.items.Spent;
import ru.devprom.items.Template;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.project.requests.RequestDonePage;
import ru.devprom.pages.project.requests.RequestNewPage;
import ru.devprom.pages.project.requests.RequestViewPage;
import ru.devprom.pages.project.requests.RequestsPage;
import ru.devprom.pages.support.SupportPageBase;
import ru.devprom.pages.support.SupportRequestsPage;

public class SupportProjectTest extends ProjectTestBase {
 
	private Project myTestProject;
	
	@BeforeClass
	public void createSupportProject(){
		ProjectNewPage npp = (new PageBase(driver)).createNewProject();
		Template SDLC = new Template(
				this.supportTemplateName);
		String p = DataProviders.getUniqueString();
		this.myTestProject = new Project("SupportProject" + p, "supportproject" + DataProviders.getUniqueStringAlphaNum(), SDLC);
		npp.createNew(myTestProject);
		FILELOG.debug("Created new project " + myTestProject.getName());
	}
	
	@Test
	public void completRequest() {
        SupportRequestsPage srp = (new SupportPageBase(driver)).gotoRequests();
        RequestNewPage rnp = srp.clickNewRequest();
        Request request = new Request("Request"+DataProviders.getUniqueString());
        RequestsPage rp =  rnp.createCRShort(request);
        RequestViewPage rvp = rp.clickToRequest(request.getId());
        Spent spent = new Spent("",2.0, user,"Тест проекта поддержки");
        RequestDonePage rdp = rvp.completeRequest();
		rvp = rdp.complete("Тест", null, spent);
		Assert.assertEquals(rvp.readState(), "Выполнено", "Неверный статус после выполнения");
		
		List<Spent> sprec = rvp.readSpentRecords();
		Assert.assertEquals(sprec.size(), 1, "Неверное число записей 'Затрачено'");
		Assert.assertEquals(sprec.get(0).hours, 2.0, "Неверное количество списанного времени");
		
	}
	
}
