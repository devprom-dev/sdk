package ru.devprom.tests;

import org.testng.Assert;
import org.testng.annotations.Test;
import ru.devprom.helpers.Configuration;
import ru.devprom.helpers.DataProviders;
import ru.devprom.items.Project;
import ru.devprom.items.Template;
import ru.devprom.items.TestScenario;
import ru.devprom.pages.PageBase;
import ru.devprom.pages.ProjectNewPage;
import ru.devprom.pages.kanban.KanbanPageBase;
import ru.devprom.pages.kanban.KanbanTestPage;
import ru.devprom.pages.project.testscenarios.TestScenarioNewPage;
import ru.devprom.pages.project.testscenarios.TestScenarioViewPage;
import ru.devprom.pages.project.testscenarios.TestScenariosPage;

import java.io.File;
import java.io.IOException;
import java.nio.charset.Charset;
import java.nio.charset.StandardCharsets;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;

public class TestReportImport extends ProjectTestBase
{
    @Test(description = "S-5017 Импорт тестового отчета TestNG")
    public void reportsImportTest () throws InterruptedException, IOException {
        Project webTest = new Project("ImportReportTest" + DataProviders.getUniqueString(),
                "ImportReportTest" + DataProviders.getUniqueStringAlphaNum(),
                new Template(this.kanbanTemplateName));

        // создаем проект для изоляции теста
        PageBase page = new PageBase(driver);
        ProjectNewPage pnp = page.createNewProject();

        KanbanPageBase firstPage = (KanbanPageBase) pnp.createNew(webTest);

        TestScenario test = new TestScenario("ImportReportTestScenario" + DataProviders.getUniqueStringAlphaNum());

        //переходим на страницу Тестовые сценарии и создаем сценарий
        TestScenariosPage tsp = firstPage.gotoTestScenarios();
        TestScenarioNewPage tscnp = tsp.clickNewTestScenario();
        tscnp.createNewScenario(test);
        Thread.sleep(3000);

        //устанавливаем ID
        TestScenarioViewPage tsvp = tscnp.clickToTestScenario(test.getId());
        TestScenariosPage tsp1 = tsvp.gotoTestScenarios();

        //вытаскиваем UID, чтобы записать его в тестовый отчет
        String UID = tsp1.getIDByName(test.getName());

        //открываем файл отчета, и вставляем в него UID нашего тестового сценария
        Path path = Paths.get("C:\\Users\\Дима\\DevpromSVN\\branches\\resources\\TestResult.xml");
        Charset charset = StandardCharsets.UTF_8;
        String content = new String(Files.readAllBytes(path), charset);
        //находим в TestResult.xml первую строку, в которой отсутствует is-config=true (указывается при прохождении теста на браузере IE),
        //и, чтобы наш сценарий получил состояние Протестировано, меняем Description на наш:
        content = content.replace("Description=\"33213\"", "description=\"" + UID + "\"" );
        Files.write(path, content.getBytes(charset));

        //переходим на страницу тестовых отчетов проектов и импортируем файл
        KanbanTestPage testsPage = (new KanbanPageBase(driver)).gotoTests();
        File file = new File(Configuration.getPathToTestReport());
        testsPage.importReport(file);

        Thread.sleep(3000);
        //Assert.assertTrue(testsPage.isReportPresent(UID),"Отчет" + UID + "не находится в статусе Пройден");
        TestScenariosPage tsp2 = firstPage.gotoTestScenarios();
        Thread.sleep(3000);
        tsp2.setFilter("state", "tested");
        Thread.sleep(3000);
        Assert.assertTrue(tsp2.isScenarioPresent(test.getName()),"Сценарий не находится в статусе Протестировано");

    }

}
