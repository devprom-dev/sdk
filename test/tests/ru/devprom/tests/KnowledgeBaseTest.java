package ru.devprom.tests;

import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.items.KBTemplate;
import ru.devprom.items.KnowledgeBase;
import ru.devprom.items.Project;
import ru.devprom.items.Template;
import ru.devprom.items.User;
import ru.devprom.pages.FavoritesPage;
import ru.devprom.pages.LoginPage;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.admin.ActivitiesPage;
import ru.devprom.pages.admin.UsersListPage;
import ru.devprom.pages.project.AddMemberPage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.kb.KBPermissionsPage;
import ru.devprom.pages.project.kb.KnowledgeBasePage;
import ru.devprom.pages.project.kb.KBNewPage;
import ru.devprom.pages.project.settings.NewTextTemplatePage;
import ru.devprom.pages.project.settings.ProjectMembersPage;
import ru.devprom.pages.project.settings.TextTemplatesPage;

public class KnowledgeBaseTest extends ProjectTestBase {

	/** This method checks access rights set for knowledge base
	 * @throws InterruptedException */
	@Test
	public void testKnowledgeBaseAccessRights() throws InterruptedException {
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(
						this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		
		//Go to KnowledgeBase and create new KB
		KnowledgeBasePage kbp = favspage.gotoKnowledgeBase();
		KBNewPage nkbp = kbp.addKb();
		KnowledgeBase kb = new KnowledgeBase("KB"+DataProviders.getUniqueString());
		nkbp.addContent("For Permissions Test");
		//complete KB creating
		kbp = nkbp.createShort(kb);
		
		FILELOG.info("Knowledge base article created: "+kb.getId()+" : " + kb.getName());
		KBPermissionsPage kbpp = kbp.gotoChangePermissionsPage();
		
		kbpp.changePermissions("Заказчик", 'n');
		
		kbp = kbpp.gotoKnowledgeBase();
		
		kbp = kbp.openKb(kb.getName());
		nkbp = kbp.addChildKb(kb.getId());
		KnowledgeBase kbchild = new KnowledgeBase("KBChild"+DataProviders.getUniqueString());
		nkbp.addContent("Chlid for Permissions Test");
		kbp = nkbp.createShort(kbchild);
		FILELOG.info("Knowledge base child article created: "+kbchild.getId()+" : " + kbchild.getName());
		
		
		Assert.assertTrue(kbp.isKBExists(kb.getName()));
		Assert.assertTrue(kbp.isKBExists(kbchild.getName()));
		
		//create new Member and add it to the Project as "Заказчик" 
		ActivitiesPage ap = kbp.goToAdminTools();
		UsersListPage ulp = ap.gotoUsers();
		String p = DataProviders.getUniqueString();
		User member = new User("KBUser"+p,"1", "KBUser"+p, p+"test@email.com", false, true);
		ulp = ulp.addNewUser(member, false);
		favspage = ulp.gotoSDLCProject(webTest);
		ProjectMembersPage pmp = favspage.gotoMembers();
		AddMemberPage amp = pmp.gotoAddMember();
		pmp = amp.addUserToProject(member, "Заказчик",  2, "");
		
		//log out
		LoginPage loginpage = pmp.logOut();
		
		//login and go to Knowledge Base
		FavoritesPage fp  = loginpage.loginAs(member.getUsername(), member.getPass());
		favspage = fp.gotoSDLCProject(webTest);
		kbp = favspage.gotoKnowledgeBase();
		//Check if the KB articles are invisible for this user
		Assert.assertFalse(kbp.isKBExists(kb.getName()));
		Assert.assertFalse(kbp.isKBExists(kbchild.getName()));
	
		
	      //log out
		loginpage = pmp.logOut();
			
		//login with the main user 
		fp  = loginpage.loginAs(username, password);
		favspage = fp.gotoSDLCProject(webTest);
	}
	
	
	

	/** This method tests creation and using of knowledge base template*/
	@Test
	public void testKnowledgeBaseTemplate() {
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(
						this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		
		//Go to "Шаблоны Базы Знаний" and create new KB Template
		TextTemplatesPage kbtp = favspage.gotoTextTemplates();
		NewTextTemplatePage nkbtp = kbtp.createNewTemplate();
		String templateContent = "test template content";
		nkbtp.create("TestKBTemplate"+DataProviders.getUniqueString(), templateContent, "База знаний", true);
	
		KnowledgeBasePage kbp = favspage.gotoKnowledgeBase();
		KBNewPage nkbp = kbp.addKb();
		KnowledgeBase kb = new KnowledgeBase("KB"+DataProviders.getUniqueString());
		kbp = nkbp.createShort(kb);
		
		//check content after creation
		Assert.assertEquals(kbp.readContent(kb.getNumericId()), templateContent);
	}

}
