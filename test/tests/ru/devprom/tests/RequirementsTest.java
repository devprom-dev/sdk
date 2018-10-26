package ru.devprom.tests;

import java.io.File;
import java.io.IOException;
import java.util.List;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.xpath.XPathExpressionException;

import org.junit.Ignore;
import org.openqa.selenium.By;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.WebDriverWait;
import org.testng.Assert;
import org.testng.annotations.Test;
import org.xml.sax.SAXException;

import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.DataProviders;
import ru.devprom.helpers.FileOperations;
import ru.devprom.items.KnowledgeBase;
import ru.devprom.items.ProductFunction;
import ru.devprom.items.Project;
import ru.devprom.items.Request;
import ru.devprom.items.Requirement;
import ru.devprom.items.Template;
import ru.devprom.items.User;
import ru.devprom.pages.CKEditor;
import ru.devprom.pages.FavoritesPage;
import ru.devprom.pages.LoginPage;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.admin.ActivitiesPage;
import ru.devprom.pages.admin.UsersListPage;
import ru.devprom.pages.project.ProjectCommonSettingsPage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.functions.FunctionNewPage;
import ru.devprom.pages.project.functions.FunctionsPage;
import ru.devprom.pages.project.kb.KBNewPage;
import ru.devprom.pages.project.kb.KnowledgeBasePage;
import ru.devprom.pages.project.requests.RequestEditPage;
import ru.devprom.pages.project.requests.RequestNewPage;
import ru.devprom.pages.project.requests.RequestViewPage;
import ru.devprom.pages.project.requests.RequestsPage;
import ru.devprom.pages.project.requirements.RequirementAddToBaselinePage;
import ru.devprom.pages.project.requirements.RequirementChangesPage;
import ru.devprom.pages.project.requirements.RequirementEditPage;
import ru.devprom.pages.project.requirements.RequirementNewPage;
import ru.devprom.pages.project.requirements.RequirementSaveVersionPage;
import ru.devprom.pages.project.requirements.RequirementViewPage;
import ru.devprom.pages.project.requirements.RequirementsImportPage;
import ru.devprom.pages.project.requirements.RequirementsNewTypePage;
import ru.devprom.pages.project.requirements.RequirementsPage;
import ru.devprom.pages.project.requirements.RequirementsTypesPage;
import ru.devprom.pages.project.requirements.TraceMatrixPage;
import ru.devprom.pages.project.settings.NewTextTemplatePage;
import ru.devprom.pages.project.settings.ProjectMembersPage;
import ru.devprom.pages.project.settings.TextTemplatesPage;
import ru.devprom.pages.requirement.RequirementBasePage;

public class RequirementsTest extends ProjectTestBase {

	/** Test creates 2 simple Requirements and then edit the second set the first as a parent page for it */
	@Test
	public void testChangeParentPage() {
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		RequirementsPage rp = favspage.gotoRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
		Requirement rParent = new Requirement("TestR"+DataProviders.getUniqueString());
		RequirementViewPage rvp = nrp.createSimple(rParent);
		FILELOG.info("Requirement created: "+rParent.getId()+" : " + rParent.getName());
		rp = rvp.gotoRequirements();
		nrp = rp.createNewRequirement();
		Requirement rChild = new Requirement("TestR"+DataProviders.getUniqueString());
		FILELOG.info("Requirement created: "+rChild.getId()+" : " + rChild.getName());
		rvp = nrp.createSimple(rChild);
		RequirementEditPage rep = rvp.editRequirement();
		rep.addParentPage(rParent.getName());
		rvp = rep.saveChanges();
		rp = favspage.gotoRequirements();
		rp.clickToRequirement(rChild.getId());
		Assert.assertEquals("R-"+rvp.readParentPage(rChild), rParent.getId());
		
		
	}
	
	/** Test creates Requirement, then creates Request adding the Requirement to it, and then checks what we have displayed*/
	@Test
	public void testCreateEnhancementOnRequirement() {
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		RequirementsPage rp = favspage.gotoRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
		Requirement requirement = new Requirement("TestR"+DataProviders.getUniqueString());
		RequirementViewPage rvp = nrp.createSimple(requirement);
		FILELOG.info("Requirement created: "+requirement.getId()+" : " + requirement.getName());
		RequestsPage mip = rvp.gotoRequests();
		RequestNewPage nr = mip.clickNewCR();
		Request request = new Request("RequirementTestRequest"+DataProviders.getUniqueString());
		FILELOG.info("Request created: "+request.getId()+" : " + request.getName());
		request.setType("Доработка");
		request.addRequirements(requirement.getName());
		mip = nr.createCRShort(request);
		RequestViewPage rv = mip.clickToRequest(request.getId());
		RequestEditPage rep = rv.gotoEditRequest();
		rep.addRequirements(requirement.getName());
		rv = rep.saveEdited();
	    Requirement resultRequirement = rv.readRequirements()[0]; 
		Assert.assertEquals(resultRequirement, requirement);
		
	}
	
	
	/** Test creates Requirement, then creates Defect adding the Requirement to it, and then checks what we have displayed*/
	@Test
	public void testCreateDefectOnRequirement() {
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		RequirementsPage rp = favspage.gotoRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
		Requirement requirement = new Requirement("TestR"+DataProviders.getUniqueString());
		RequirementViewPage rvp = nrp.createSimple(requirement);
		FILELOG.info("Requirement created: "+requirement.getId()+" : " + requirement.getName());
		RequestsPage mip = rvp.gotoRequests();
		RequestNewPage nr = mip.clickNewCR();
		Request request = new Request("RequirementTestRequest"+DataProviders.getUniqueString());
		request.setType("Ошибка");
		request.addRequirements(requirement.getName());
		mip = nr.createCRShort(request);
		FILELOG.info("Request created: "+request.getId()+" : " + request.getName());
		RequestViewPage rv = mip.clickToRequest(request.getId());
		RequestEditPage rep = rv.gotoEditRequest();
		rep.addRequirements(requirement.getName());
		rv = rep.saveEdited();
	    Requirement resultRequirement = rv.readRequirements()[0]; 
		Assert.assertEquals(resultRequirement, requirement);
	}
	
	/**This test creates 2 Requirements, Parent and Child. Exports them to Excel doc, reads this doc to array,
	 *  removes the Requirements from the System, Imports them from the Excel doc and checks the equality.
	 * @throws InterruptedException 
	 * @throws IOException 
	 * @throws SAXException 
	 * @throws ParserConfigurationException 
	 * @throws XPathExpressionException  */
	@Test
	public void testExportImportRequirementsInExcel() throws XPathExpressionException, ParserConfigurationException, SAXException, IOException, InterruptedException {
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		
		RequirementsPage rp = favspage.gotoRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
		Requirement parentR = new Requirement("TestRParent"+DataProviders.getUniqueString(), "Some content");
		RequirementViewPage rvp = nrp.create(parentR);
		FILELOG.info("Requirement created: "+parentR.getId()+" : " + parentR.getName());
		nrp = rvp.addChildRequirement();
		Requirement childR = new Requirement("TestRChild"+DataProviders.getUniqueString(), "Some content");
		childR.setParentPage(parentR);
		rvp = nrp.createChild(childR);
		FILELOG.info("Requirement created: "+childR.getId()+" : " + childR.getName());
		rp = rvp.gotoRequirements();
		rp.addColumn("ParentPage");
		rp.showAll();
		rp.checkRequirement(parentR.getId());
		rp.checkRequirement(childR.getId());
		Requirement[] exportedRequirements = rp.exportToExcel(new String[]{"Родительская страница"});
		for (Requirement r:exportedRequirements){
			FILELOG.debug(r);;
		}
		rp = rp.deleteSelected();
		RequirementsImportPage irp = rp.clickImport();
		irp.importRequirements(new File(Configuration.getDownloadPath()+"\\Реестр требований.xls"));
		rp = irp.gotoRequirements();
		rp.showAll();
		Requirement parentImported = rp.findRequirementByName(parentR.getName());
		Requirement childImported = rp.findRequirementByName(childR.getName());
		rvp = rp.clickToRequirement(parentImported.getId());
		parentImported.setContent(rvp.readContent(parentImported.getNumericId()));
		rp = rvp.gotoRequirements();
		rp.showAll();
		rvp = rp.clickToRequirement(childImported.getId());
		childImported.setContent(rvp.readContent(childImported.getNumericId()));
		Assert.assertEquals(parentImported.getContent(),parentR.getContent());
		Assert.assertEquals(childImported.getContent(),childR.getContent());
	}


	/**This test creates 2 Requirements, main and nested. Put a link on the nested to main's body. Then check's this link.	 */
	@Ignore
	@Test
	public void testNestedRequirementPages() {
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		
		RequirementsPage rp = favspage.gotoRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
		Requirement nestedR = new Requirement("TestR"+DataProviders.getUniqueString(), "Some very very specific content");
		RequirementViewPage rvp = nrp.create(nestedR);
		FILELOG.info("Requirement created: "+nestedR.getId()+" : " + nestedR.getName());
		rp = rvp.gotoRequirements();
		nrp = rp.createNewRequirement();
		Requirement mainR = new Requirement("TestR"+DataProviders.getUniqueString(), "Link to: "+"{{" +nestedR.getId()+"}}" );
		rvp = nrp.create(mainR);
		FILELOG.info("Requirement created: "+mainR.getId()+" : " + mainR.getName());
		Assert.assertTrue(rvp.readNestedContent().contains(nestedR.getContent()));
		
	}
	
	/**This test creates test Requirement, do some actions on it and verify each iteration statuses.
	 * Then it scan Life Cycle frame for all the statuses the requirement has had before	 */
	@Test
	public void testRequirementsAgreement() {
		
		String reason = "Тестовые действия";
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		
		RequirementsPage rp = favspage.gotoRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
		Requirement testR = new Requirement("TestR"+DataProviders.getUniqueString(), "Content text");
		RequirementViewPage rvp = nrp.create(testR);
		FILELOG.info("Requirement created: "+testR.getId()+" : " + testR.getName());
		Assert.assertEquals(rvp.readRequirementStatus(), "В работе");
		rvp = rvp.completeRequirement();
		Assert.assertEquals(rvp.readRequirementStatus(), "Готово");
		rvp = rvp.signRequirement();
		Assert.assertEquals(rvp.readRequirementStatus(), "Реализовано");
		rvp = rvp.getBackRequirement(reason);
		Assert.assertEquals(rvp.readRequirementStatus(), "В работе");
	}
	
	
	

	/**This test creates test Requirement, do some actions on it and verify each iteration statuses.
	 * Then it scan Life Cycle frame for all the statuses the requirement has had before	 */
	@Test
	public void testRequirementsChangesControl() {
		String originaltext = "Оригинальный текст";
		String finaltext = "Текст после редактирования";
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		
		RequirementsPage rp = favspage.gotoRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
		Requirement testR = new Requirement("TestR"+DataProviders.getUniqueString(), originaltext);
		RequirementViewPage rvp = nrp.create(testR);
		FILELOG.info("Requirement created: "+testR.getId()+" : " + testR.getName());
		RequirementEditPage rep = rvp.editRequirement();
		rep.addContent(finaltext);
		rvp = rep.saveChanges();
		rvp.waitForContent(testR.getNumericId(), finaltext);
		Assert.assertTrue(true);
	}
	

	/** Test creates 2 Templates, 2 Requirements Types (based on the templates) and 2 Requirements of these types */
	@Test
	public void testRequirementTemplate() {
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		//Create 2 templates
		String template1 = "TestReqTemplate"+DataProviders.getUniqueString();
		String template2 = "TestReqTemplate"+DataProviders.getUniqueString();
		String content1 = "Content for the first template";
		String content2 = "Content for the second template";
		TextTemplatesPage rtp = favspage.gotoTextTemplates();
		NewTextTemplatePage ntp = rtp.createNewTemplate();
		rtp = ntp.create(template1, content1, "Требование", false);
		rtp = rtp.gotoTextTemplates();
		ntp = rtp.createNewTemplate();
		rtp = ntp.create(template2, content2, "Требование", false);
		
		//Create 2 requirement types
		String type1 = "TestReqType"+DataProviders.getUniqueString();
		String type2 = "TestReqType"+DataProviders.getUniqueString();
		RequirementsTypesPage rtyp = rtp.gotoRequirementsTypes();
		RequirementsNewTypePage nrtp = rtyp.createNewRequirementType();
		rtyp = nrtp.createNewRequirementType(type1, type1, "Description for Type 1", template1, "", "");
		nrtp = rtyp.createNewRequirementType();
		rtyp = nrtp.createNewRequirementType(type2, type2, "Description for Type 2", template2, "", "");
		
		//Create 2 requirements
		Requirement testR1 = new Requirement("TestR"+DataProviders.getUniqueString());
		Requirement testR2 = new Requirement("TestR"+DataProviders.getUniqueString());
		RequirementsPage rp = rtyp.gotoRequirements();
		RequirementNewPage nrp = rp.createRequirementWithType(type1);
		RequirementViewPage rvp = nrp.create(testR1);
		FILELOG.info("Requirement created: "+testR1.getId()+" : " + testR1.getName());
		Assert.assertEquals(rvp.readType(), type1);
		Assert.assertEquals(rvp.readContent(testR1.getNumericId()), content1);
		
		rp = rvp.gotoRequirements();
		nrp = rp.createRequirementWithType(type2);
		rvp = nrp.create(testR2);
		FILELOG.info("Requirement created: "+testR2.getId()+" : " + testR2.getName());
		Assert.assertEquals(rvp.readType(), type2);
		Assert.assertEquals(rvp.readContent(testR2.getNumericId()), content2);
	}
	
	/**This method will check CKEditor editor is correctly applied when creating a new requirement type */
	@Test
	public void testCKEditorOnAddingNewRequirementType() {
		
		String text = "Bold text for requirement";
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		
		//Create Requirement Type set up Wiki Editor
		String type = "ForWikiTest"+DataProviders.getUniqueString();
		RequirementsTypesPage rtyp = favspage.gotoRequirementsTypes();
		RequirementsNewTypePage nrtp = rtyp.createNewRequirementType();
		rtyp = nrtp.createNewRequirementType(type, type, "Description for Type 1", "", "WikiRtfCKEditor", "");
		
		//Create new Requirement of a new type
		RequirementsPage rp = favspage.gotoRequirements();
		RequirementNewPage nrp = rp.createRequirementWithType(type);
		Requirement testR = new Requirement("TestR"+DataProviders.getUniqueString());
		
		//Enter WikiEditor zone and add formatted text
		CKEditor we = new CKEditor(driver);
		we.boldOnOff();
		we.typeText(text);
		
		//complete Requirement creating
		RequirementViewPage rvp = nrp.create(testR);
		FILELOG.info("Requirement created: "+testR.getId()+" : " + testR.getName());
		//check the text decoration - strong (bold)
		List<String> decorated = rvp.getStyleTagsForText(testR.getNumericId(), text);
		Assert.assertTrue(decorated.contains("strong"));
		//Check the Requirement type
		Assert.assertEquals(rvp.readType(), type);
	}
	
	
	/**This method checks WIKI Editor, than checks how items created in WIKI Editor are displayed in CKEditor. 
	 * NOT thread-safe*/
	@Test(enabled = false)
	public void testUserHistoryDescriptionRedactor() {
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		
		//Go to Common Project Settings and select WIKI Editor
		ProjectCommonSettingsPage csp = favspage.gotoCommonSettings();
		csp.selectWikiEditor("WikiSyntaxEditor");
		favspage = csp.saveChanges();
		
		RequestsPage mip = favspage.gotoRequests();
		Request testR = new Request("WIKITestCR"+DataProviders.getUniqueString(), "WIKI test *bold* _italic_ and _*bold and italic*_ and +underlined+", Request.getHighPriority(), 2.0, "Доработка");
		RequestNewPage ncrp = mip.clickNewCR();
		
		//Add description String and check how it is displayed
		ncrp.addWIKIDescription(testR.getDescription());
		mip = ncrp.createCRShort(testR);
		FILELOG.info("Request created: "+testR.getId()+" : " + testR.getName());
		RequestViewPage rvp = mip.clickToRequest(testR.getId());
		
		String testRDescription = rvp.readDescription();
		Assert.assertTrue(rvp.getStyleTagsForText("bold").contains("b"));
		Assert.assertTrue(rvp.getStyleTagsForText("italic").contains("i"));
		Assert.assertTrue(rvp.getStyleTagsForText("bold and italic").contains("b"));
		Assert.assertTrue(rvp.getStyleTagsForText("bold and italic").contains("i"));
		Assert.assertTrue(rvp.getStyleTagsForText("underlined").contains("u"));
		
		//Duplicate the Request, check it's description. Using "1" when selecting Project to duplicate in. 
		//This method should be changed in future to allow selecting by project name
		Request duplicateR = new Request(testR.getName(), testR.getDescription(), testR.getPriority(), testR.getEstimation(), testR.getType());
		rvp.duplicateRequest(webTest.getName());
		favspage = (SDLCPojectPageBase) rvp.gotoProject(webTest);
		duplicateR.setId(favspage.getLastActivityID('I'));
		mip = favspage.gotoRequests();
		rvp = mip.clickToRequest(duplicateR.getId());
		FILELOG.info("Duplicate Request created: "+duplicateR.getId()+" : " + duplicateR.getName());
		Assert.assertEquals(rvp.readDescription(), testRDescription);
		Assert.assertTrue(rvp.getStyleTagsForText("bold").contains("b"));
		Assert.assertTrue(rvp.getStyleTagsForText("italic").contains("i"));
		Assert.assertTrue(rvp.getStyleTagsForText("bold and italic").contains("b"));
		Assert.assertTrue(rvp.getStyleTagsForText("bold and italic").contains("i"));
		Assert.assertTrue(rvp.getStyleTagsForText("underlined").contains("u"));
		
		//Change Editor
		mip = rvp.gotoRequests();
		csp = mip.gotoCommonSettings();
		csp.selectWikiEditor("WikiRtfCKEditor");
		favspage = csp.saveChanges();
		mip = favspage.gotoRequests();
		rvp = mip.clickToRequest(testR.getId());
		//Check the description looks raw
		Assert.assertEquals(rvp.readDescription(), testR.getDescription());
		//Check there is no text decoration
		Assert.assertFalse(rvp.getStyleTagsForText("bold").contains("b"));
		Assert.assertFalse(rvp.getStyleTagsForText("italic").contains("i"));
		Assert.assertFalse(rvp.getStyleTagsForText("bold and italic").contains("b"));
		Assert.assertFalse(rvp.getStyleTagsForText("bold and italic").contains("i"));
		Assert.assertFalse(rvp.getStyleTagsForText("underlined").contains("u"));
	}
	
	/**This method will check if attachments are added and displayed correctly when editing a requirement 
	 * @throws IOException */
	@Test
	public void testAttachmentsWhenEditingRequirement() throws IOException
	{
		String attachement = "Attachment.png";
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);

		//Go to Common Project Settings and select CKEditor
		ProjectCommonSettingsPage csp = favspage.gotoCommonSettings();
		csp.selectWikiEditor("WikiRtfCKEditor");
			favspage = csp.saveChanges();
			
		//Create PNG file
		File image = FileOperations.createPNG(attachement);
		long sourceImageSize = image.length();

		//Create a Requirement with an attachment
		RequirementsPage rp = favspage.gotoRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
		Requirement testR = new Requirement("TestR"+DataProviders.getUniqueString());
		CKEditor cke = new CKEditor(driver);
		cke.loadAttachementToRequirement(image);
		RequirementViewPage rvp = nrp.createSimple(testR);
		FILELOG.info("Requirement created: "+testR.getId()+" : " + testR.getName());

		//Check is there an image in Content
		Assert.assertTrue(rvp.isImageContained(testR.getNumericId()));
		Assert.assertTrue(rvp.isImageSrcCorrect(testR.getNumericId()), "Ссылка на картинку содержит: cms_TempFile");
		
		long bytes = rvp.getAttachmentSize(testR.getNumericId(), image.getName());
		Assert.assertEquals(bytes, sourceImageSize, "Неверный размер файла приложения");
	}
	
	/**This method will check saved version of the requirement */
	@Test
	public void saveVersionTest() {
		
		String content = "Изначальное содержание Требования";
		String newContent = "Текст требования изменен на этот";
		String version = "Версия 1";
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		
		//Create new Requirement
		RequirementsPage rp = favspage.gotoRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
		Requirement testRequirement = new Requirement("TestR"+DataProviders.getUniqueString(), content);
		RequirementViewPage rvp = nrp.create(testRequirement);
		
		//Save version
		RequirementSaveVersionPage rsvp = rvp.saveVersion();
		rvp = rsvp.saveVersion(version, "Первичная версия требования");
		
		//Edit current version
		rvp.showBaseline(testRequirement.getName());
		rvp.editContent(testRequirement.getClearId(), newContent);
		
		//Open saved version and read content
		rvp.showVersion(version);
		Assert.assertEquals(rvp.readSavedVersionContent(), content, "Не найдено исходное содержание Требования");
		
		
	}
		

	/**This method creates Requirement, saves it's version, 
	 * then creates Analyst user, that make changes in current version of the Requirement and saves the new version.
	 * Then default user opens first version of the Requirement and compares it with the saved by Analyst */
	@Test
			public void checkChangesInVersionTest() {
				
		      //Test constants 
		        String p = DataProviders.getUniqueString();
				String content = "Изначальное содержание Требования";
				String newContent = "Изначальное содержание плюс новый текст";
				String version1 = "Итерация 1";
				String version2 = "Итерация 2";
				User analyst = new User("Analyst"+p, "1", "Analyst"+p, "analyst"+p+"@mail.com", false, true);
				Requirement testRequirement = new Requirement("TestR"+p, content);
				
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
				FILELOG.debug("Created Requirement " + testRequirement.getId());
				//Save version
				RequirementSaveVersionPage rsvp = rvp.saveVersion();
				rvp = rsvp.saveVersion(version1, "Первичная версия требования");
				
				//Login as Analyst
				LoginPage lp = rvp.logOut();
				FavoritesPage fp = lp.loginAs(analyst.getUsername(), analyst.getPass());
				favspage = (SDLCPojectPageBase) fp.gotoProject(webTest);
				
				//Change the Requirement's content and save the new version
				rp = favspage.gotoRequirements();
				rvp = rp.clickToRequirement(testRequirement.getId());
				rvp = rvp.editContent(testRequirement.getClearId(), newContent);
				rsvp = rvp.saveVersion();
				rvp = rsvp.saveVersion(version2, "Версия Аналитика");
				
				//Login as a default user
				lp = rvp.logOut();
				fp = lp.loginAs(Configuration.getUsername(), Configuration.getPassword());
				favspage = (SDLCPojectPageBase) fp.gotoProject(webTest);
				
				//Compare the Requirement's versions
				rp = favspage.gotoRequirements();
				rvp = rp.clickToRequirement(testRequirement.getId());
				rvp = rvp.showVersion(version1);
				rvp = rvp.compareWithVersion(version2);
				Assert.assertEquals(rvp.getAddedText(), "плюс новый текст", "Удаленный текст не соответствует ожиданиям");
				Assert.assertEquals(rvp.getRemovedText(), "Требования", "Добавленный текст не соответствует ожиданиям");
	}
		
	
				
	/**This method creates Requirement,add it's to Baseline without making a copy. 
	 * Then checks that the Requirement is shown in Baseline
	 *  */
	@Test
		public void addToBaseline() {
			
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		
		//Create new Requirement
		RequirementsPage rp = favspage.gotoRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
		Requirement testRequirement = new Requirement("TestR"+DataProviders.getUniqueString());
		RequirementViewPage rvp = nrp.create(testRequirement);
		
		//Add To Baseline
		RequirementAddToBaselinePage ratb = rvp.addToBaseline();
		rvp = ratb.addToBaseline(testRequirement, "Бейзлайн один", "Тестовое добавление в бейзлайн без создания копии", false, "", "");
		Assert.assertEquals(rvp.readCurrentBaseline(), "Бейзлайн один","Нет пометки о принадлежности к бейзлайну");
	}
	
	/**This method creates Requirement,add it's to 2 Baselines. Than changes the Requirement in first Baseline,
	 * get the change notification for second Baseline, opens Requirement and apply changes. 
	 *  */
	@Test
		public void manageChangesInRequirement() {
		String content = "Изначальное содержание Требования";
		String newContent = "Изначальное содержание плюс новый текст";
		String baseline1 = "Бейзлайн один";
		String baseline2 = "Бейзлайн два";
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		
		//Create new Requirement
		RequirementsPage rp = favspage.gotoRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
		Requirement testRequirement = new Requirement("TestR"+DataProviders.getUniqueString());
		testRequirement.setContent(content);
		RequirementViewPage rvp = nrp.create(testRequirement);
		
		//Add To Baselines 
		RequirementAddToBaselinePage ratb = rvp.addToBaseline();
		Requirement testRequirementBaseline1 = testRequirement.clone();
		rvp = ratb.addToBaseline(testRequirementBaseline1, baseline1);
		ratb = rvp.addToBaseline();
		Requirement testRequirementBaseline2 = testRequirement.clone();
		rvp = ratb.addToBaseline(testRequirementBaseline2, baseline2);
		
		//Do changes in Baseline 1
		rvp = rvp.showBaseline(baseline1);
		RequirementEditPage rep = rvp.editRequirement();
		rep.addContent(newContent);
		rvp = rep.saveChanges();
		
		//Go to Change Matrix and search changes warning
		TraceMatrixPage tmp = rvp.gotoTraceMatrix();
		Assert.assertTrue(tmp.isRequirementHasAlert(testRequirementBaseline2.getId()), "Нет сообщения об изменениях Требования");
		
		//Apply changes made in previous baseline
		RequirementChangesPage rchp = tmp.clickToAlertOnRequirement(testRequirementBaseline2.getId());
		rvp = rchp.useText();
		rp = favspage.gotoRequirements();
		rvp = rp.clickToRequirement(testRequirementBaseline2.getId());
		Assert.assertEquals(rvp.readContent(testRequirementBaseline2.getNumericId()), newContent, "Содержание не включает изменения, сделанные в "+baseline1);
	}	
	
	
	/**This method creates a several objects, link them to a Requirement, then add the Requirement to baseline creating a copy,
	 * and checks that the new Requirement has all the links like the original one. 
	 *  */
	@Test
		public void copyLinksWhenAddingToBaseline() {
		
		String baseline1 = "Бейзлайн один";
		String index = DataProviders.getUniqueString();
		Request testRequest = new Request("TestCR-"+ index,
				"some description for my test change request",
				Request.getHighPriority(), Request.getRandomEstimation(),
				"Доработка");
		String attachement = "Attachment.png";
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		
		//Create new Request
		RequestsPage mip = favspage.gotoRequests();
		RequestNewPage ncrp = mip.clickNewCR();
		mip = ncrp.createNewCR(testRequest);
		FILELOG.debug("Created Request: " + testRequest.getId());
		
		//Create new Requirement
		RequirementsPage rp = mip.gotoRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
		Requirement testRequirement = new Requirement("TestR"+DataProviders.getUniqueString(), "Тестовое содержание");
		testRequirement.addTag("Test_tag_" + index);
		testRequirement.addRequest(testRequest.getId());
				
		RequirementViewPage rvp = nrp.create(testRequirement);
		FILELOG.debug("Created Requirement: " + testRequirement.getId());

		//Create PNG file
		File image = FileOperations.createPNG(attachement);
		RequirementEditPage rep = rvp.editRequirement();
		rep.addAttachment(image);
		rep.saveChanges();
		
		//Add Requirement to Baseline
		RequirementAddToBaselinePage ratb = rvp.addToBaseline();
		Requirement testRequirementBaseline1 = testRequirement.clone();
		rvp = ratb.addToBaseline(testRequirementBaseline1, baseline1);
		
		//Read Properties
		RequirementEditPage rpp = rvp.editRequirement();
		List<String> attachments = rpp.getAttachments();
		List<String> tags = rpp.readTags();
		List<String> requests = rpp.readLinkedRequests();
		rpp.close();
		Assert.assertTrue(attachments.contains(attachement), "Не найдена ссылка на прикрепленное изображение");
		Assert.assertTrue(tags.containsAll(testRequirement.getTags()), "Не найдены теги исходного Требования");
		Assert.assertFalse(requests.containsAll(testRequirement.getRequests()), "Найдена ссылка на Пожелание");
	}
	
	/**This method creates Requirement and a child Requirement for it.
	 * Then save the version. Then removes the child Requirement from the initial version.
	 * Then compare current Requirement with it's saved version, checks that the child Requirement is visible.
	 *  */
	@Test
		public void controlChangesInRequirementStructure() {
		
		String index = DataProviders.getUniqueString();
		Requirement parentR = new Requirement("TestRParent"+index, "Содержание требования");
		Requirement childR = new Requirement("TestRChild"+index, "Содержание раздела требования");
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		
		//Create parent Requirement
		RequirementsPage rp = favspage.gotoRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
		
		RequirementViewPage rvp = nrp.create(parentR);
		FILELOG.info("Requirement created: "+parentR.getId()+" : " + parentR.getName());

		//Create Partition - child requirement
		nrp = rvp.addPartition();
		rvp = nrp.createChild(childR);
		FILELOG.info("Requirement created: "+childR.getId()+" : " + childR.getName());

		rp = rvp.gotoRequirements();
		rvp = rp.clickToRequirement(parentR.getId());
		
		//Save version
		RequirementSaveVersionPage rsvp = rvp.saveVersion();
		rvp = rsvp.saveVersion("Версия 1");
		
		//Remove child Requirement
		rp = rvp.gotoRequirements();
		rp.checkRequirement(childR.getId());
		rp = rp.deleteSelected();
		
		//Open parent Requirement
		rvp = rp.clickToRequirement(parentR.getId());
		rvp = rvp.compareWithVersion("Версия 1");
		Assert.assertTrue(rvp.isTextPresent(childR.getName()), "Не найдено имя удаленного раздела");
		Assert.assertTrue(rvp.isTextPresent(childR.getContent()), "Не найдено содержание удаленного раздела");
	}
	
	

	/**  S-1796
	 *  */
	@Test
		public void createBaselineBasedOnDocumentVersion() {
		String index = DataProviders.getUniqueString();
		String baseline1 = "Бейзлайн один";
		String baseline2 = "Бейзлайн два";
		String version = "Версия 0.1";
		String content = "Текст, который надо будет найти";
		Requirement document = new Requirement("TestRParent"+index, "Содержание требования");
		Requirement childA = new Requirement("TestRChildA"+index, content);
		Requirement childB = new Requirement("TestRChildB"+index, "Содержание раздела требования");
		
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		
		//Create parent Requirement
		RequirementsPage rp = favspage.gotoRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
		
		RequirementViewPage rvp = nrp.create(document);
		FILELOG.info("Requirement created: "+document.getId()+" : " + document.getName());

		//Create Partition - child requirement
		nrp = rvp.addPartition();
		rvp = nrp.createChild(childA);
		FILELOG.info("Requirement created: "+childA.getId()+" : " + childA.getName());

		rp = rvp.gotoRequirements();
		rvp = rp.clickToRequirement(document.getId());
		nrp = rvp.addPartition();
		rvp = nrp.createChild(childB);
		FILELOG.info("Requirement created: "+childB.getId()+" : " + childB.getName());
		
		rp = rvp.gotoRequirements();
		rvp = rp.clickToRequirement(document.getId());
		
		//Add Requirement to Baseline
		RequirementAddToBaselinePage ratb = rvp.addToBaseline();
		rvp = ratb.addToBaseline(document, baseline1, "", false, "", "");
		
		//Save version
		RequirementSaveVersionPage rsvp = rvp.saveVersion();
		rvp = rsvp.saveVersion(version);

		//Remove one of the child requirements
		rp = rvp.gotoRequirements();
		rp = rp.showAll();
		rp.checkRequirement(childA.getId());
		rp = rp.deleteSelected();
		
		//Add Document to another baseline
		rvp = rp.clickToRequirement(document.getId());
		ratb = rvp.addToBaseline();
		List<String> versions = ratb.getVersionsList();
		rvp = ratb.addToBaseline(document, baseline2, "", true, version, "");
		
		Assert.assertEquals(versions.size(), 2, "В списке должно быть две версии");
	    Assert.assertTrue(rvp.isTextPresent(content), "Удаленный раздел не виден в новом бейзлайне");
		
	}
		

	/**
	 * Создаем два простых Требования
	 * Меню "массовые операции" -> создать Доработки
	 * Проверяем, что доработки(пожелания) были созданы. 
      */
	@Test
	public void massRequestCreationForRequirements() {
		Requirement firstR = new Requirement("TestR"+DataProviders.getUniqueString());
		Requirement secondR = new Requirement("TestR"+DataProviders.getUniqueString());
		
        PageBase basePage = new PageBase(driver);
        basePage.clickLink();
        ProjectNewPage newProjectPage = basePage.clickNewProject();
        RequirementBasePage favspage = (RequirementBasePage) newProjectPage.createNew(
				new Project(
						"Требования" + DataProviders.getUniqueStringAlphaNum(), 
						"requirement" + DataProviders.getUniqueStringAlphaNum(),
						new Template(this.requirementTemplateName)
					)
				);

        RequirementsPage rp = favspage.gotoReestrRequirements();
		RequirementNewPage nrp = rp.createNewRequirement();
	
		RequirementViewPage rvp = nrp.createSimple(firstR);
		FILELOG.info("Requirement created: "+firstR.getId()+" : " + firstR.getName());
		rp = rvp.gotoRequirements();
		nrp = rp.createNewRequirement();
		rvp = nrp.createSimple(secondR);
		FILELOG.info("Requirement created: "+secondR.getId()+" : " + secondR.getName());

		rp = rvp.gotoRequirements();
		rp.showAll();
		rp.checkRequirement(firstR.getId());
		rp.checkRequirement(secondR.getId());
		rp = rp.massCreateRequests();
		rp.showAll();
		
		rvp = rp.clickToRequirement(firstR.getId());
		RequirementEditPage rpp = rvp.editRequirement();
		List<String> requestsFirst = rpp.readLinkedEnhancements();
		Assert.assertEquals(requestsFirst.size(), 1, "Должна быть одна доработка для Требования " + firstR.getId());
		rpp.close();
		
		rp = rvp.gotoRequirements();
		rp.showAll();
		rvp = rp.clickToRequirement(secondR.getId());
		rpp = rvp.editRequirement();
		List<String> requestsSecond = rpp.readLinkedEnhancements();
		Assert.assertEquals(requestsSecond.size(), 1, "Должна быть одна доработка для Требования " + secondR.getId());
		rpp.close();
	}
	
	
	
}
