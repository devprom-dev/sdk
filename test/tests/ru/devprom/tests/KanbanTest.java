package ru.devprom.tests;

import org.testng.Assert;
import org.testng.annotations.BeforeClass;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.items.KanbanTask;
import ru.devprom.items.Project;
import ru.devprom.items.Template;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.kanban.KanbanAddSubtaskPage;
import ru.devprom.pages.kanban.KanbanPageBase;
import ru.devprom.pages.kanban.KanbanTaskEditPage;
import ru.devprom.pages.kanban.KanbanTaskExecutePage;
import ru.devprom.pages.kanban.KanbanTaskNewPage;
import ru.devprom.pages.kanban.KanbanTaskStateEditPage;
import ru.devprom.pages.kanban.KanbanTaskViewPage;
import ru.devprom.pages.kanban.KanbanTasksPage;
import ru.devprom.pages.kanban.KanbanTasksStatesPage;

public class KanbanTest extends ProjectTestBase {
	Project kanbanProject;
	
	
	/**
	 * Метод создает новый проект Kanban, который будет использоваться в тестах класса.
	 */
	@BeforeClass
	public void createKanbanProject(){
	    createNewKanbanProject(true);
		(new KanbanPageBase(driver)).gotoMethodology().uncheckIsTasks().save();
	}
	
	/**
	 * Тест создает новую задачу, назначает её на текущего пользователя,
	 * проверяет наличие созданной задачи в списке задач текущего пользователя
	 */
	@Test
	public void taskAssignTest(){
		(new PageBase(driver)).gotoProject(kanbanProject);
		KanbanTasksPage ktp = (new KanbanPageBase(driver)).gotoKanbanTasks();
		KanbanTaskNewPage ktnp = ktp.addNewTask();
		KanbanTask task = new KanbanTask("TestTask"+DataProviders.getUniqueString());
		task.setOwner(user);
		ktp = ktnp.createTask(task);
		ktp = (new KanbanPageBase(driver)).gotoKanbanTasks();
		Assert.assertTrue(ktp.isTaskPresent(task.getId()), "Задача не найдена в списке задач пользователя " + user);
	}
	
	
	
    /**
     * Тест создает новую задачу, создает на её основе шаблон, и проверяет возможность создания новых задач по этому шаблону, 
     * а также корретность предлагаемых по шаблону начальных данных.
     * Поля, задействованные в шаблоне: Описание, 
     */
	@Test
	public void taskTemplatesTest(){
			//параметры Задачи
			String description = "Описание, которое будет отображаться в шаблоне";
            String template = "Task Template "+DataProviders.getUniqueString();
			//создаем новую Задачу
            (new PageBase(driver)).gotoProject(kanbanProject);
			KanbanTasksPage ktp = (new KanbanPageBase(driver)).gotoKanbanTasks();
			KanbanTaskNewPage ktnp = ktp.addNewTask();
			KanbanTask task = new KanbanTask("TestTask"+DataProviders.getUniqueString());
			task.setDescription(description);
			task.setPriority(KanbanTask.getRandomPriority());
			task.setOwner(user);
			ktp = ktnp.createTask(task);
			ktp = (new KanbanPageBase(driver)).gotoKanbanTasks();
			
			//Создаем Шаблон на основе новой задачи
			KanbanTaskViewPage ktvp = ktp.clickToTask(task.getId());
			ktvp = ktvp.saveTemplate(template);
			
			//Создаем новую Задачу по шаблону, считываем параметры по умолчанию
			ktp = ktvp.gotoKanbanTasks();
			//TODO разобраться почему не читает список шаблонов
			//Assert.assertTrue(ktp.getTemplatesList().contains(template), "Не найден шаблон с именем "+template);
			ktnp = ktp.addNewTaskUserType(template);
			String descriptionT = ktnp.readDescription();
			String priorityT = ktnp.readPriority();
			String ownerT = ktnp.readOwner();
		    KanbanTask templateTask = new KanbanTask(ktnp.readName());
			ktnp.saveTask(templateTask);
			Assert.assertEquals(templateTask.getName(), task.getName(), "В шаблоне неверно сохранено имя Задачи");
			Assert.assertEquals(descriptionT, task.getDescription(), "В шаблоне неверно сохранено Описание");
			Assert.assertEquals(priorityT, task.getPriority(), "В шаблоне неверно сохранен Приоритет");
			Assert.assertEquals(ownerT, task.getOwner(), "В шаблоне неверно сохранен Исполнитель");
	}
	
	
	
    /**
     * Тест создает задачу, затем подзадачу для неё. Затем выполняет подзадачу с формы задачи.
     */
	@Test (priority = 10)
	public void assignTaskToMultipleOwners(){
		
		//Включаем поздадачи в проекте
		(new KanbanPageBase(driver)).gotoMethodology().checkIsTasks().save();
		
		//Создаем новую задачу 
		KanbanTasksPage ktp = (new KanbanPageBase(driver)).gotoKanbanTasks();
		KanbanTaskNewPage ktnp = ktp.addNewTask();
		KanbanTask task = new KanbanTask("TestTask"+DataProviders.getUniqueString());
		ktp = ktnp.createTask(task);
		ktp = (new KanbanPageBase(driver)).gotoKanbanTasks();
		ktp.showAll();
		
		//Создаем подзадачу
		KanbanTaskViewPage ktvp = ktp.clickToTask(task.getId());
		KanbanAddSubtaskPage kasp = ktvp.actionAddSubtask();
		KanbanTask subtask = new KanbanTask("Subtask" + DataProviders.getUniqueString());
		subtask.setType("Разработка");
		ktvp = kasp.createSubtask(subtask);
		
		//Выполняем подзадачу
        KanbanTaskExecutePage ktep = ktvp.executeSubtask(subtask.getName());
        ktvp = ktep.subtaskExecute(1.0);
        
        //Проверяем статус подзадачи
        boolean isExecuted = ktvp.getSubTaskState(subtask.getName()).equals("Выполнена");
        
        //Выключаем подзадачи в проекте
        ktvp.gotoMethodology().uncheckIsTasks().save();
        Assert.assertTrue(isExecuted, "Статус задачи не 'Выполнена'");
	}
	

	@Test(description="S-1779")
	public void obligatoryFieldsSetUp(){
		createNewKanbanProject(false);
		(new KanbanPageBase(driver)).gotoMethodology().uncheckIsTasks().save();
		//параметры Задачи
		KanbanTask task = new KanbanTask("TestTask"+DataProviders.getUniqueString());
		
		//создаем новую Задачу
		KanbanTasksPage ktp = (new KanbanPageBase(driver)).gotoKanbanTasks();
		KanbanTaskNewPage ktnp = ktp.addNewTask();
		ktp = ktnp.createTask(task);
		
		//переходим в Настройки/Состояния
		KanbanTasksStatesPage ktsp = ktp.gotoTasksStates();
		KanbanTaskStateEditPage ktsep = ktsp.editTaskState("Новые");
		ktsep.addAttribute("Тип", true, true);
		ktsp = ktsep.saveChanges();
		
		//создаем задачу
		ktp = ktsp.gotoKanbanTasks();
		ktnp = ktp.addNewTask();
		boolean isRequired = ktnp.isRequired("Тип");
		ktp = ktnp.cancel();
		ktp.showAll();
		Assert.assertTrue(isRequired, "Создание новой задачи: поле Тип не является обязательным");
		KanbanTaskViewPage ktvp = ktp.clickToTask(task.getId());
		KanbanTaskEditPage ktep = ktvp.editTask();
		isRequired = ktnp.isRequired("Тип");
		ktep.cancel();
		Assert.assertTrue(isRequired, "Редактировани задачи: поле Тип не является обязательным");
	}
	
	private Project createNewKanbanProject(boolean isGlobal){
		ProjectNewPage npp = (new PageBase(driver)).createNewProject();
		Template kanbanTemplate = new Template(this.kanbanTemplateName);
		String p = DataProviders.getUniqueString();
		 Project project = new Project("Kanban" + p, "kanban" + p, kanbanTemplate);
		if (isGlobal) this.kanbanProject = project;
		KanbanPageBase firstPage = (KanbanPageBase) npp
				.createNew(project);
		FILELOG.debug("Created new project " + project.getName());
		Assert.assertEquals(firstPage.getProjectTitle(),
				project.getName());
		return project;
	}
	
}
