package ru.devprom.helpers;

import java.io.File;
import java.io.IOException;
import java.util.HashMap;
import java.util.LinkedHashMap;
import java.util.Map;
import java.util.Set;

import javax.swing.table.DefaultTableModel;
import javax.swing.table.TableModel;
import javax.xml.parsers.DocumentBuilder;
import javax.xml.parsers.DocumentBuilderFactory;
import javax.xml.parsers.ParserConfigurationException;
import javax.xml.xpath.XPath;
import javax.xml.xpath.XPathConstants;
import javax.xml.xpath.XPathExpressionException;
import javax.xml.xpath.XPathFactory;

import org.w3c.dom.NodeList;
import org.xml.sax.SAXException;

import jxl.Cell;
import jxl.Sheet;
import jxl.Workbook;
import jxl.read.biff.BiffException;
import ru.devprom.items.RTask;
import ru.devprom.items.Request;
import ru.devprom.items.Requirement;
import ru.devprom.items.TimetableItem;

public class XLTableParser
{
	public static LinkedHashMap<String, String>[] parseXLS(File file)
				throws ParserConfigurationException, SAXException, IOException,
				XPathExpressionException
	{
		try {
			Workbook workbook = Workbook.getWorkbook(file);
	        Sheet sheet = workbook.getSheets()[0];
			
	        LinkedHashMap<String, String>[] resultTable = new LinkedHashMap[sheet.getRows() - 2];
	        Cell[] header = sheet.getRow(0);

	        TableModel model = new DefaultTableModel(sheet.getRows(), sheet.getColumns());
	        for (int row = 0; row < resultTable.length; row++) {
	        	resultTable[row] = new LinkedHashMap<String, String>();
	            for (int column = 0; column < header.length; column++) {
	                String content = sheet.getCell(column, row + 1).getContents();
	                resultTable[row].put(header[column].getContents(), content);
	            }
	        }
	        return resultTable;
		}
		catch (BiffException e) {
			return new LinkedHashMap[0];
		}
	}
	
/** The method creates Requirements from the table nested in the file.
 * Fields to be fill: ID, Name, State, Content.
 * Additionally you may pass String[] with any other field names you want to search in the table  */
	public static Requirement[] getRequirements(File file, String[] moreFields) throws XPathExpressionException, ParserConfigurationException, SAXException, IOException{
		LinkedHashMap<String, String>[] table = parseXLS(file);
		Requirement[] requirements = new Requirement[table.length];
		
		for (int i=0;i<table.length;i++){
			requirements[i]= new Requirement(table[i].get("Название"),table[i].get("Содержание"));
		    if (table[i].get("Состояние")!=null)  requirements[i].setState(table[i].get("Состояние"));
		    if (table[i].get("UID")!=null)  requirements[i].setId(table[i].get("UID"));
		  // add more fields  
		    if (moreFields.length<0) {
		    for (String s:moreFields) {
		    if (s.equals("Родительская страница") && table[i].get("Родительская страница")!=null)  requirements[i].setParentPage(new Requirement(table[i].get("Родительская страница")));
		  //add as many properties as you want
		    }
		    }
		}
		
		return requirements;
	}
		
	
	public static RTask[] getTasks(File file, String[] moreFields) throws XPathExpressionException, ParserConfigurationException, SAXException, IOException{
		LinkedHashMap<String, String>[] table = parseXLS(file);
		RTask[] tasks = new RTask[table.length];
		
		for (int i=0;i<table.length;i++){
			tasks[i]= new RTask(table[i].get("UID"),table[i].get("Название"),table[i].get("Тип"),table[i].get("Приоритет"),table[i].get("Состояние"));
		}
		
		return tasks;
	}
	
	
	public static Request[] getRequests(File file, String[] moreFields) throws XPathExpressionException, ParserConfigurationException, SAXException, IOException{
		LinkedHashMap<String, String>[] table = parseXLS(file);
		Request[] requests = new Request[table.length];
		
		for (int i=0;i<table.length;i++){
			requests[i]= new Request(table[i].get("UID"),table[i].get("Название").replaceAll("(\\t|\\r?\\n)+", " "),table[i].get("Тип"),table[i].get("Состояние"),table[i].get("Приоритет"));
		}
		
		return requests;
	}
		
	
	public static TimetableItem[] getTimetableItems(File file, String type) throws XPathExpressionException, ParserConfigurationException, SAXException, IOException{
		LinkedHashMap<String, String>[] table = parseXLS(file);
		TimetableItem[] items = new TimetableItem[table.length];
		
		for (int i=0;i<table.length;i++) {
		    String name = table[i].get(type);
		    String sum = table[i].get("Итого").isEmpty() ? "0" : table[i].get("Итого");
		    String[] columnNames = table[i].keySet().toArray(new String[0]);
		    String[] days = new String[table[i].size()-2];
		    for (int j=0; j < days.length; j++){
				String cell = table[i].get(columnNames[j + 1]);
				days[j] = cell.equals("") ? "0" : cell;
		    }
			items[i] = new TimetableItem(name.replaceAll("(\\t|\\r?\\n)+", " "), days, sum);
		}
		
		return items;
	}
	
	
}