/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package ru.devprom.tests;

import org.openqa.selenium.Alert;
import org.openqa.selenium.Keys;
import org.openqa.selenium.interactions.HasInputDevices;
import org.openqa.selenium.interactions.Keyboard;
import org.testng.annotations.Test;
import ru.devprom.helpers.DataProviders;
import ru.devprom.items.KanbanTask;
import ru.devprom.items.Project;
import ru.devprom.items.Requirement;
import ru.devprom.items.Template;
import ru.devprom.items.TestScenario;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.kanban.KanbanPageBase;
import ru.devprom.pages.kanban.KanbanTaskNewPage;
import ru.devprom.pages.kanban.KanbanTaskViewPage;
import ru.devprom.pages.project.requests.RequestRejectPage;
import ru.devprom.pages.project.requirements.RequirementAddToBaselinePage;
import ru.devprom.pages.project.requirements.RequirementNewPage;
import ru.devprom.pages.project.requirements.RequirementReturnToWorkPage;
import ru.devprom.pages.project.requirements.RequirementSaveVersionPage;
import ru.devprom.pages.project.requirements.RequirementViewPage;
import ru.devprom.pages.project.requirements.RequirementsDocsPage;
import ru.devprom.pages.project.requirements.RequirementsNewTypePage;
import ru.devprom.pages.project.requirements.RequirementsPage;
import ru.devprom.pages.project.requirements.RequirementsTypesPage;
import ru.devprom.pages.project.tasks.TaskCompletePage;
import ru.devprom.pages.project.tasks.TaskNewPage;
import ru.devprom.pages.project.testscenarios.TestScenarioNewPage;
import ru.devprom.pages.project.testscenarios.TestScenarioViewPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationViewPage;
import ru.devprom.pages.project.testscenarios.TestSpecificationsPage;
import ru.devprom.pages.requirement.InboxRequestsPage;
import ru.devprom.pages.requirement.IssueViewPage;
import ru.devprom.pages.requirement.RequirementBasePage;
import static ru.devprom.tests.TestBase.FILELOG;

/**
 *
 * @author лена
 */
public class RequirementsScreenCast extends ProjectTestBase{
    Project newProject;
    int timeOut = 1000;
    int bigTimeOut = 2000;
    
    Requirement subSection2;
    Requirement requirement1;
    Requirement reqCourse;
    String brTemplate = "<table border=\"1\" cellpadding=\"1\" cellspacing=\"1\" style=\"border-collapse: collapse; width: 100%;\"><tbody><tr><td>1</td><td>%text%</td></tr><tr><td>2</td><td>%model%</td></tr></tbody></table>";
    String brText = "Существует реестр курсов, сгруппированных по дисциплинам. За курсами закреплены преподаватели. Студент должен иметь возможность зарегистрироваться на курс обучения.";
    String brModel = "<img alt=\"Y2xhc3MlMjAldTA0MUEldTA0NDMldTA0NDAldTA0NDElMjAlN0IlMEEldTA0MUQldTA0MzAldTA0MzcldTA0MzIldTA0MzAldTA0M0QldTA0MzgldTA0MzUlMEEldTA0MUUldTA0M0YldTA0MzgldTA0NDEldTA0MzAldTA0M0QldTA0MzgldTA0MzUlMEEldTA0MjIldTA0NDAldTA0MzUldTA0MzEldTA0M0UldTA0MzIldTA0MzAldTA0M0QldTA0MzgldTA0NEYlMjAldTA0M0ElMjAldTA0NDEldTA0NDIldTA0NDMldTA0MzQldTA0MzUldTA0M0QldTA0NDIldTA0MzAldTA0M0MlMEElN0QlMEElMEFjbGFzcyUyMCV1MDQxRiV1MDQ0MCV1MDQzNSV1MDQzRiV1MDQzRSV1MDQzNCV1MDQzMCV1MDQzMiV1MDQzMCV1MDQ0MiV1MDQzNSV1MDQzQiV1MDQ0QyUyMCU3QiUwQSV1MDQyNCV1MDQxOCV1MDQxRSUwQSV1MDQxQSV1MDQzRSV1MDQzRCV1MDQ0MiV1MDQzMCV1MDQzQSV1MDQ0MiV1MDQ0QiUwQSU3RCUwQSUwQWNsYXNzJTIwJXUwNDIxJXUwNDQyJXUwNDQzJXUwNDM0JXUwNDM1JXUwNDNEJXUwNDQyJTIwJTdCJTBBJXUwNDI0JXUwNDE4JXUwNDFFJTBBJXUwNDFBJXUwNDNFJXUwNDNEJXUwNDQyJXUwNDMwJXUwNDNBJXUwNDQyJXUwNDRCJTBBJTdEJTBBJTBBJTIyJXUwNDFBJXUwNDQzJXUwNDQwJXUwNDQxJTIyJTIwLS0lMjAlMjIldTA0MUYldTA0NDAldTA0MzUldTA0M0YldTA0M0UldTA0MzQldTA0MzAldTA0MzIldTA0MzAldTA0NDIldTA0MzUldTA0M0IldTA0NEMlMjIlM0ElMjAldTA0MzIldTA0MzUldTA0MzQldTA0MzUldTA0NDIlMEElMjIldTA0MUEldTA0NDMldTA0NDAldTA0NDElMjIlMjAtLSUyMCUyMiV1MDQyMSV1MDQ0MiV1MDQ0MyV1MDQzNCV1MDQzNSV1MDQzRCV1MDQ0MiUyMiUzQSUyMCV1MDQzRiV1MDQzRSV1MDQ0MSV1MDQzNSV1MDQ0OSV1MDQzMCV1MDQzNSV1MDQ0Mg==\" src=\"http://www.plantuml.com/plantuml/img/VP0z2i9058JxFSLZ-rp0sqXQkvGYvASI1Li9654yW4gMX6ZC5URTo5b3C2BQFDWtyytmDfnDeqYGQwQn9Z8tsE6C2bVE5ZMSGO4EjIRlnL5ZE5pm65ow4JIYYQQQeQJMQahbRXR6X7t1_iLqP4egvEXmkEdQLvwmHM6GK-t9Xach4NqO9_ydx72BbI0G-xlOJeHFvrCr_S8-6hsL2Vm3fqkc47W2\">";
    	
     /**
	 * Сценарий обучающих роликов: разработка и управление требованиями
	 */
    @Test(description="S-3327")
	public void runRequirementsScreenCast() throws InterruptedException{
            documentationStage();
            documentSystemStage();
            agreementStage();
            useRequirementStage();
	}

    private void documentationStage() throws InterruptedException {
        RequirementBasePage requirementBasePage = createNewProject(true);
        RequirementsDocsPage requirementsDocsPage = requirementBasePage.gotoRequirementsDocs();
        RequirementViewPage newDocPage = requirementsDocsPage.addDoc();
        newDocPage.editRequirementName("Требования к комплексу дистанционного обучения");
        RequirementNewPage reqNewPage = newDocPage.addChildRequirement();
        Requirement section1 = new Requirement("Словарь терминов");
        section1.setContent("Курс");
        reqNewPage.createFromBoard(section1);
        newDocPage.addChildRequirement();
        Requirement section2 = new Requirement("Бизнес-требования");
        section2.setContent(" ");
        reqNewPage.createFromBoard(section2);
        newDocPage.addChildRequirement();
        subSection2 = new Requirement("Организация курса обучения");
        subSection2.setType("Системное требование");
        reqNewPage.createWithHTML(subSection2,brTemplate.replace("%text%", brText).replace("%model%",brModel));
        newDocPage.addChildRequirement();
        Requirement section3 = new Requirement("Ограничения");
        section3.setContent(" ");
        reqNewPage.createWithTemplate(section3);
        InboxRequestsPage inboxPage = requirementBasePage.gotoInboxRequests();
        KanbanTaskNewPage newTaskPage = inboxPage.addWish();
        KanbanTask wish1 = new KanbanTask("Количество преподавателей на курсе");
        wish1.setDescription("У нас могут быть курсы, где будет участвовать несколько преподавателей. Например, опытный + стажер или еще есть разные варианты.");
        newTaskPage.addName(wish1.getName());
        newTaskPage.addDescription(wish1.getDescription());
        newTaskPage.save();
        wish1.setId(inboxPage.getIDWishByName(wish1.getName()));
        IssueViewPage viewPage = inboxPage.clickOnWish(wish1.getId());
        viewPage.doAnalyse("");
        RequirementNewPage requirementNewPage = viewPage.clickActionCreateRequirement();
        requirement1 = new Requirement("Студенты и преподаватели");
        requirement1.setType("Системное требование");
        requirement1.setContent("Ограничение нужно уточнить, но формула расчета коэффициента известна:\n");
        Requirement parentRequirement = new Requirement(section3.getName());
        requirement1.setParentPage(parentRequirement);
        requirementNewPage.createWithFormula(requirement1, "");
        requirement1.setId(viewPage.getIdRequirement(requirement1.getName()));
        //
        RequirementsPage requirementsPage = requirementBasePage.gotoReestrRequirements();
        subSection2.setId(requirementsPage.getIdByName(subSection2.getName()));
        RequirementViewPage requirementViewPage = requirementsPage.clickToRequirement(subSection2.getId());
        brText += " Число студентов и преподавателей на курсе подчиняется ограничению " + requirement1.getId();
        requirementViewPage.changeHtmlInContent(subSection2.getId().split("-")[1], brTemplate.replace("%text%", brText).replace("%model%",brModel));
        driver.navigate().refresh();
        requirementViewPage.seeChanges(subSection2.getId());
    }

    private RequirementBasePage createNewProject(boolean isGlobal) {
        try{
            Thread.sleep(timeOut);
            PageBase basePage = new PageBase(driver);
            basePage.clickLink();
            ProjectNewPage newProjectPage = basePage.clickNewProject();
            Thread.sleep(timeOut);
            Template requirementTemplate = new Template(this.requirementTemplateName);
            String p = DataProviders.getUniqueString();
            Project project = new Project("Требования", "requirement" + DataProviders.getUniqueStringAlphaNum(), requirementTemplate);
            if (isGlobal) this.newProject = project;
            RequirementBasePage requirementBasePage = (RequirementBasePage) newProjectPage.createNew(project);
            Thread.sleep(timeOut);
            FILELOG.debug("Created new project " + project.getName());
            return requirementBasePage;
        }
            catch(InterruptedException e)
            {
                FILELOG.debug("Error in creating new project" + e);
                return null;
            }
    }

    private void documentSystemStage() throws InterruptedException {
        RequirementsTypesPage requirementsDocsPage = (new RequirementBasePage(driver)).gotoRequirementsTypes();
        RequirementsNewTypePage newTypePage = requirementsDocsPage.createNewRequirementType();
        newTypePage.createNewRequirementType("Нефункциональное требование", "NFR", "", "", "","");
        RequirementsPage requirementsPage = (new RequirementBasePage(driver)).gotoReestrRequirements();
        RequirementViewPage requirementViewPage = requirementsPage.clickToRequirement(subSection2.getId());
        RequirementNewPage newRequirementPage = requirementViewPage.createCoverRequirement();
        reqCourse = new Requirement("Создание нового курса");
        reqCourse.setTemplateName("OpenUP");
        Requirement parentRequirement = new Requirement("Варианты использования");
        reqCourse.setParentPage(parentRequirement);
        reqCourse.setType("Системное требование");
        newRequirementPage.createFromBoard(reqCourse, false);
        Thread.sleep(2000);
        brModel = "<img alt=\"Y2xhc3MlMjAldTA0MUEldTA0NDMldTA0NDAldTA0NDElMjAlN0IlMEEldTA0MUQldTA0MzAldTA0MzcldTA0MzIldTA0MzAldTA0M0QldTA0MzgldTA0MzUlMEEldTA0MUUldTA0M0YldTA0MzgldTA0NDEldTA0MzAldTA0M0QldTA0MzgldTA0MzUlMEEldTA0MjIldTA0NDAldTA0MzUldTA0MzEldTA0M0UldTA0MzIldTA0MzAldTA0M0QldTA0MzgldTA0NEYlMjAldTA0M0ElMjAldTA0NDEldTA0NDIldTA0NDMldTA0MzQldTA0MzUldTA0M0QldTA0NDIldTA0MzAldTA0M0MlMEElN0QlMEElMEFjbGFzcyUyMCV1MDQxRiV1MDQ0MCV1MDQzNSV1MDQzRiV1MDQzRSV1MDQzNCV1MDQzMCV1MDQzMiV1MDQzMCV1MDQ0MiV1MDQzNSV1MDQzQiV1MDQ0QyUyMCU3QiUwQSV1MDQyNCV1MDQxOCV1MDQxRSUwQSV1MDQxQSV1MDQzRSV1MDQzRCV1MDQ0MiV1MDQzMCV1MDQzQSV1MDQ0MiV1MDQ0QiUwQSV1MDQxNyV1MDQzMCV1MDQzQyV1MDQzNSV1MDQzRCV1MDQzMCUwQSV1MDQyMSV1MDQ0MiV1MDQzMCV1MDQzNiV1MDQzNSV1MDQ0MCUwQSU3RCUwQSUwQWNsYXNzJTIwJXUwNDIxJXUwNDQyJXUwNDQzJXUwNDM0JXUwNDM1JXUwNDNEJXUwNDQyJTIwJTdCJTBBJXUwNDI0JXUwNDE4JXUwNDFFJTBBJXUwNDIxJXUwNDQwJXUwNDM1JXUwNDM0JXUwNDNEJXUwNDM4JXUwNDM5JTIwJXUwNDMxJXUwNDMwJXUwNDNCJXUwNDNCJTBBJXUwNDFBJXUwNDNFJXUwNDNEJXUwNDQyJXUwNDMwJXUwNDNBJXUwNDQyJXUwNDRCJTBBJTdEJTBBJTBBJTIyJXUwNDFBJXUwNDQzJXUwNDQwJXUwNDQxJTIyJTIwLS0lMjAlMjIldTA0MUYldTA0NDAldTA0MzUldTA0M0YldTA0M0UldTA0MzQldTA0MzAldTA0MzIldTA0MzAldTA0NDIldTA0MzUldTA0M0IldTA0NEMlMjIlM0ElMjAldTA0MzIldTA0MzUldTA0MzQldTA0MzUldTA0NDIlMEElMjIldTA0MUEldTA0NDMldTA0NDAldTA0NDElMjIlMjAtLSUyMCUyMiV1MDQyMSV1MDQ0MiV1MDQ0MyV1MDQzNCV1MDQzNSV1MDQzRCV1MDQ0MiUyMiUzQSUyMCV1MDQzRiV1MDQzRSV1MDQ0MSV1MDQzNSV1MDQ0OSV1MDQzMCV1MDQzNSV1MDQ0Mg==\" src=\"http://www.plantuml.com/plantuml/img/TP1D2i8m48NtESMGVI_WRQHRTrsAq9-LABXK2bOg7g1AexMrzWflRkGafLWfRWB9c_UzD_4Nyo0Wf9nmo14j5OueyC1DpXOLZ4A63XL7tuSpXp2uuZrCyOvG4qSSSu9Ij9PZKLvgfPJV1voSfnDFAOhuPCAWuQsDlE20J24LjNVMGjeex2t4ub0ev4wvmt0uukRZp14hTtcbg_maALkWGJFD498UjjVaUQJ_LzKpagkb6eudjfycTbG8yck6rq8HmmS0\"> новый текст";
        requirementViewPage.changeHtmlInContent(subSection2.getId().split("-")[1], brTemplate.replace("%text%", brText).replace("%model%",brModel));
        Thread.sleep(10000);
        driver.navigate().refresh();
        requirementViewPage.completeRequirement();
        Thread.sleep(2000);
        TestSpecificationsPage specificationPage = requirementViewPage.clickAttention();
        requirementViewPage.addContent("","При просмотре курса, студенты видят описание преподавателей, а также признак замены");
        specificationPage.clickRepair();
        (new RequirementBasePage(driver)).gotoMatrixTrace();
        Thread.sleep(4000);
    }

    private void agreementStage() throws InterruptedException {
        RequirementsPage requirementsPage = (new RequirementBasePage(driver)).gotoReestrRequirements();
        reqCourse.setId(requirementsPage.getIdByName(reqCourse.getName()));
        RequirementViewPage requirementViewPage = requirementsPage.clickToRequirement(reqCourse.getId());
        requirementViewPage.completeRequirement();
        RequirementReturnToWorkPage returnToworkPage = requirementViewPage.returnToWork();//todo - переделать метод
        returnToworkPage.addComment("Не понятно как система будет формулировать отказ студенту при попытке регистрации на курсе");
        requirementViewPage.addComment("О, да, отличное замечание, обсудили с заказчиком этот момент, см. уточненный альтернативный сценарий №3");
        requirementViewPage.setAtributeStatusHistory();
        requirementViewPage.agreeWithOSA();
        RequirementSaveVersionPage saveVersionPage = requirementViewPage.saveVersion();
        saveVersionPage.saveVersion("Согласованная версия", "Спецификация системных требований прошла процедуру согласования, присутствовали:\nИванова Л.В.\nПетрова С.Н.\nСидорова У.К.");
        requirementViewPage.showBaseline("Варианты использования");
        RequirementAddToBaselinePage addToBaselinePage = requirementViewPage.addToBaseline();
        Thread.sleep(2000);
        Requirement baselineReq = new Requirement("");
        addToBaselinePage.addToBaseline(baselineReq, "Релиз 0");
        requirementViewPage.addContent(baselineReq.getClearId(), "В новом бейзлайне появилось существенное расширение функциональности варианта использования");
        RequirementNewPage newRequirementPage = requirementViewPage.addChildRequirement();
        Requirement requirement2 = new Requirement("Регистрация преподавателя");
        requirement2.setType("Системное требование");
        requirement2.setTemplateName("OpenUP");
        newRequirementPage.createFromBoard(requirement2);
        requirementViewPage.showBaseline("Варианты использования");
        requirementViewPage.compareWithVersion("Релиз 0");
        Thread.sleep(3000);
    }

    private void useRequirementStage() throws InterruptedException {
        RequirementsPage requirementsPage = (new RequirementBasePage(driver)).gotoReestrRequirements();
        RequirementViewPage requirementViewPage = requirementsPage.clickToRequirement(reqCourse.getId());
        requirementViewPage.exportToPDF();
        Thread.sleep(3000);
        KanbanTaskNewPage taskNewPage = requirementViewPage.createRework();
        KanbanTask rework = new KanbanTask("Форма создания курса");
        rework.setDescription("Реализовать форму создания курса по требованиям, реализовать сохранение курса в базе данных");
        taskNewPage.addName(rework.getName());
        taskNewPage.addDescription(rework.getDescription());
        taskNewPage.save();
        Thread.sleep(1000);
        driver.navigate().refresh();
       // taskNewPage.createTask(rework);
        RequestRejectPage rejectPage = requirementViewPage.clickToRework();
        Thread.sleep(1000);
        (new KanbanTaskViewPage(driver)).completeTask();
        (new KanbanTaskViewPage(driver)).openRequirement(reqCourse.getName());
    }
}
