package ru.devprom.tests;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.testng.Assert;
import org.testng.annotations.Test;

import ru.devprom.items.User;
import ru.devprom.pages.admin.AdminPageBase;
import ru.devprom.pages.admin.UsersListPage;

public class PagingTest extends AdminTestBase {

	@Test
	public void usersPagingTest() {
		int rows = 5;
		boolean isLastLess = false;
		
		UsersListPage ulp = (new AdminPageBase(driver)).gotoUsers();
		ulp.showAll();
	    ulp = new UsersListPage(driver); 
		int allRowsCount = ulp.getDataRowsCount();
		int pagesShouldBe = allRowsCount/5;
		int lastPageRowsCount = allRowsCount%5;
		if (lastPageRowsCount>0) {
			pagesShouldBe++;
			isLastLess = true;
		}
		
		//Считываем список всех пользователей
		 List<User> allUsers = ulp.getAllUsers();
		 Assert.assertEquals(allUsers.size(), allRowsCount, "Ошибка теста - число пользователей не соответствует числу строк");
		 int iterator = 0;
		
		 ulp.showRows(String.valueOf(rows));
		 ulp = new UsersListPage(driver); 
		Assert.assertEquals(ulp.getPagesCount(), pagesShouldBe, "Неверное количество страниц");
		
		for (int i=1;i<=pagesShouldBe;i++) {
	     ulp.showPage(String.valueOf(i));
	     ulp = new UsersListPage(driver); 
	     List<User> usersOnPage = ulp.getAllUsers();
		   if (isLastLess && i==pagesShouldBe) {
			   Assert.assertEquals(ulp.getDataRowsCount(), lastPageRowsCount, "На странице " + i + " количество строк не соответствует ожидаемому");
			   for (int k=0; k < lastPageRowsCount; k++){
				   Assert.assertEquals(usersOnPage.get(k), allUsers.get(iterator), "Не верный пользователь");
				   iterator++;
			   }
		   }
		   else {
			   Assert.assertEquals(ulp.getDataRowsCount(), rows, "На странице " + i + " количество строк не соответствует ожидаемому");
			   for (int k=0; k < rows; k++){
				   Assert.assertEquals(usersOnPage.get(k), allUsers.get(iterator), "Не верный пользователь");
				   iterator++;
			   }
		   }
		}
	}
	
}
