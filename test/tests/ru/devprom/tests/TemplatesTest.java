package ru.devprom.tests;

import java.util.List;

import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Project;
import ru.devprom.items.Template;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.project.LoadTemplatePage;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.settings.SaveTemplatePage;
import ru.devprom.pages.project.settings.TransitionEditPage;
import ru.devprom.pages.project.tasks.TasksStatePage;

public class TemplatesTest extends ProjectTestBase
{
	@Test(description="S-2206")
	public void transitionSettingsTest() throws InterruptedException{
		String p = DataProviders.getUniqueString();
		Template template = new Template("TransitionTestSDLC" +p, "Шаблон для теста настроек перехода", "trtest"+DataProviders.getUniqueStringAlphaNum(), Template.Lang.russian);
		
		ProjectNewPage npp = (new PageBase(driver)).createNewProject();
		Template SDLC = new Template(
				this.waterfallTemplateName);
		Project protoripeProject = new Project("TemplatePrototipeProject" + p, "tpproject" + DataProviders.getUniqueStringAlphaNum(), SDLC);
		npp.createNew(protoripeProject);
		FILELOG.debug("Created new project " + protoripeProject.getName());
		
		TasksStatePage tsp = (new SDLCPojectPageBase(driver)).gotoTasksStatePage();
	    TransitionEditPage tep = tsp.clickChangeTransition("Добавлена", "Выполнить > Выполнена");
		tep.checkIsNeedReasonForTransitionBox();
		tep.addObligatoryField("Исполнитель");
		tep.addProjectRole("Архитектор");
		tep.addPrecondition("Тип задачи: Проектирование");
		tep.addResetField("Осталось");
		tep.saveChanges();
		
		SaveTemplatePage stp = new SDLCPojectPageBase(driver).gotoSaveTemplatePage();
		stp.saveTemplate(template);
		
		stp.createNewProject();
		Project inheritProject = new Project("InheritSettingsProject" + p, "isproject" + DataProviders.getUniqueStringAlphaNum(), SDLC);
		npp.createNew(inheritProject);
		FILELOG.debug("Created new project " + inheritProject.getName());
		
		tsp = (new SDLCPojectPageBase(driver)).gotoTasksStatePage();
		tep = tsp.clickChangeTransition("Добавлена", "Выполнить > Выполнена");
		tep.uncheckIsNeedReasonForTransitionBox();
		tep.addObligatoryField("Приоритет");
		tep.addProjectRole("Заказчик");
		tep.addPrecondition("Тип задачи: Дизайн тестов");
		tep.addResetField("Затрачено");
		tep.saveChanges();
		
		LoadTemplatePage ltp = tep.gotoLoadTemplatePage();
		ltp.uncheckAll();
		ltp.checkImportDictionariesSettings();
		ltp.checkImportStateSettings();
		ltp.importTemplate(template.getFullName());
		
		tsp = ltp.gotoTasksStatePage();
		tep = tsp.clickChangeTransition("Добавлена", "Выполнить > Выполнена");
		
		List<String> obligatoryFields = tep.getObligatoryFields();
		List<String> projectRoles = tep.getProjectRoles();
		List<String> removePreconditions = tep.getRemovePreconditions();
		List<String> resetFields = tep.getResetFields();
		Boolean isChecked = tep.isNeedReasonForTransition();
		tep.saveChanges();
		
		Assert.assertTrue(isChecked, "Чекбокс 'Необходимо указать причину перехода' должен быть установлен" );
		Assert.assertTrue(obligatoryFields.contains("Исполнитель"), "Настройки перехода не включают обязательным полем 'Исполнитель'" );
		Assert.assertTrue(projectRoles.contains("Архитектор"), "Настройки перехода не включают обязательным полем 'Архитектор'" );
		Assert.assertTrue(removePreconditions.contains("Тип задачи: Проектирование"), "Настройки перехода не включают обязательным полем 'Тип задачи: Проектирование'" );
		Assert.assertTrue(resetFields.contains("Осталось"), "Настройки перехода не включают обязательным полем 'Оставшаяся трудоемкость, ч.'" );
		
		
		Assert.assertFalse(obligatoryFields.contains("Приоритет"), "Настройки перехода включают обязательным полем 'Приоритет' установленное до применения шаблона" );
		Assert.assertFalse(projectRoles.contains("Заказчик"), "Настройки перехода включают обязательным полем 'Заказчик' установленное до применения шаблона" );
		Assert.assertFalse(removePreconditions.contains("Тип задачи: Дизайн тестов"), "Настройки перехода включают обязательным полем 'Тип задачи: Запустить модульные тесты' установленное до применения шаблона" );
		Assert.assertFalse(resetFields.contains("Затрачено"), "Настройки перехода  включают обязательным полем 'Фактическая трудоемкость, ч.' установленное до применения шаблона" );
		
		
	}
	
	
	
}
