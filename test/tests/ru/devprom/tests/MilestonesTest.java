package ru.devprom.tests;

import org.junit.Assert;
import org.testng.annotations.Test;

import ru.devprom.helpers.DataProviders;
import ru.devprom.helpers.DateHelper;
import ru.devprom.items.Milestone;
import ru.devprom.items.Project;
import ru.devprom.items.Template;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.project.SDLCPojectPageBase;
import ru.devprom.pages.project.milestones.MilestoneEditPage;
import ru.devprom.pages.project.milestones.MilestoneNewPage;
import ru.devprom.pages.project.milestones.MilestonesPage;

public class MilestonesTest extends ProjectTestBase
{
	public void createMilestone(){
		//Создаем простой объект Вехи
		Milestone milestone = new Milestone(DateHelper.getDayAfter(5), "TestMilestone"+DataProviders.getUniqueString());
		
		PageBase page = new PageBase(driver);
		Project webTest = new Project("DEVPROM.WebTest", "devprom_webtest",
				new Template(this.waterfallTemplateName));
		SDLCPojectPageBase favspage = (SDLCPojectPageBase) page.gotoProject(webTest);
		
		//Переходим в меню "Вехи" и создаем новую Веху в системе
		MilestonesPage msp = favspage.gotoMilestones();
		MilestoneNewPage mnp = msp.addNewMilestone();
		msp = mnp.createMilestone(milestone);
		
		//Открываем Веху для редактирования и добавляем любое Пожелание из существующих в системе		
		MilestoneEditPage mep = msp.editMilestone(milestone.getId());
		mep.addAnyRequest();
		msp = mep.gotoMilestones();
		//Проверяем, что у тестируемой Вехи есть связанное Пожелание
		Assert.assertFalse(msp.getLinkedRequestId(milestone.getId()).isEmpty());
		
		//Открываем Веху для редактирования и ставим ей статус "Пройдена"
		mep = msp.editMilestone(milestone.getId());
		mep = mep.passMilestone();
		msp = mep.gotoMilestones();
		
		//Убеждаемся, что теперь Вехи нет в списке
		Assert.assertFalse(msp.isMilestonePresent(milestone.getId()));
		
	}
	
}
