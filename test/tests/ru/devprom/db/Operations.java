package ru.devprom.db;

import java.io.BufferedReader;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;
import java.util.Random;

public class Operations {
     private static String[] wikiPageColums;
     private static int columnID;
     private static int columnOrderNum;
     private static int columnCaption;
     private static int columnContent;
     private static int columnParentPage;
     private static int columnParentPath;
     private static int columnSectionNumber;
     private static int columnDocumentId;
	 private static int columnIsDocument;
     private static int columnSortIndex;
     private static int columnVPD;
     private static int columnUID;
     //Запомним отдельно список документов (родительского уровня) для таблицы cms_snapshotitem
     public static List<String[]> documents = new ArrayList<String[]>();
     public static List<String[]> requests = new ArrayList<String[]>();
     public static List<String[]> tasks = new ArrayList<String[]>();
     private static ResultSet snapshotStructure;
     private static ResultSet snapshotItemStructure;
     private static ResultSet snapshotItemValueStructure;
     private static ResultSet requestStructure;
	private static ResultSet crLinkStructure;
	private static ResultSet attachmentStructure;
	private static ResultSet activityStructure;
	private static ResultSet commentStructure;
	private static ResultSet taskStructure;
     
     
     
 	public static List<String[]> generateRequestsTable(int count, String projectId, String VPD) throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException{
		requests = new ArrayList<String[]>();
		requestStructure = getLastRequest();
		int columnCount = requestStructure.getMetaData().getColumnCount();
		String[] data = new String[columnCount]; 
		
		while(requestStructure.next()){
			for (int i=0;i<columnCount;i++){
				data[i]=requestStructure.getString(i+1);
			}
			}
		
		int id = Integer.parseInt(data[requestStructure.findColumn("pm_ChangeRequestId")-1])+1;
		for (int i=0;i<count;i++){
			
			String[] row = new String[columnCount];
			System.arraycopy(data, 0, row, 0, columnCount);
			row[requestStructure.findColumn("pm_ChangeRequestId")-1] = String.valueOf(id);
			row[requestStructure.findColumn("Caption")-1] = "StressTestRequest-" + String.valueOf(id);
			row[requestStructure.findColumn("Description")-1] = "For Stress Test";
			row[requestStructure.findColumn("Priority")-1] = getRandomRequestPriority();
			row[requestStructure.findColumn("Project")-1] = projectId;
			row[requestStructure.findColumn("VPD")-1] = VPD;
			row[requestStructure.findColumn("Function")-1] = null;
			row[requestStructure.findColumn("Estimation")-1] = "10"; 
			row[requestStructure.findColumn("Owner")-1] = null; 
			row[requestStructure.findColumn("Type")-1] = null; 
			row[requestStructure.findColumn("PlannedRelease")-1] = null; 
			row[requestStructure.findColumn("SubmittedVersion")-1] = null; 
			row[requestStructure.findColumn("ClosedInVersion")-1] = null; 
			row[requestStructure.findColumn("State")-1] = getRandomRequestState(); 
			row[requestStructure.findColumn("LifecycleDuration")-1] = null; 
			row[requestStructure.findColumn("FinishDate")-1] = null; 
			row[requestStructure.findColumn("RecordVersion")-1] = "0"; 
			row[requestStructure.findColumn("EstimationLeft")-1] = null; 
			row[requestStructure.findColumn("StateObject")-1] = null; 
			requests.add(row);
			id++;
		}
		return requests;
	}
     

    
 	public static List<String[]> generateTasksTable(String VPD) throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException{
		tasks = new ArrayList<String[]>();
		taskStructure = getLastTask();
		int columnCount = taskStructure.getMetaData().getColumnCount();
		String[] data = new String[columnCount]; 
		
		while(taskStructure.next()){
			for (int i=0;i<columnCount;i++){
				data[i]=taskStructure.getString(i+1);
			}
			}
		
		int id = Integer.parseInt(data[taskStructure.findColumn("pm_TaskId")-1])+1;
		for (String[] req:requests){
			
			String[] row = new String[columnCount];
			System.arraycopy(data, 0, row, 0, columnCount);
			row[taskStructure.findColumn("pm_TaskId")-1] = String.valueOf(id);
			row[taskStructure.findColumn("Caption")-1] = "StressTestTask-" + String.valueOf(id);
			row[taskStructure.findColumn("ChangeRequest")-1] = req[requestStructure.findColumn("pm_ChangeRequestId")-1];
			row[taskStructure.findColumn("Release")-1] = null;
			row[taskStructure.findColumn("State")-1] = "planned";
			row[taskStructure.findColumn("VPD")-1] = VPD;
			row[taskStructure.findColumn("Comments")-1] = null;
			row[taskStructure.findColumn("RecordVersion")-1] = "0"; 
			row[taskStructure.findColumn("PrecedingTask")-1] = null; 
			row[taskStructure.findColumn("StateObject")-1] = null; 
			tasks.add(row);
			id++;
		}
		return tasks;
	}
     
    private static String getRandomRequestState(){
    	String[] select = new String[] { "submitted", "planned", "analysis", "development", "intesting", "deployment", "resolved"};
		return select[new Random(System.nanoTime()).nextInt(select.length - 1)];
    }
     
    private static String getRandomRequestPriority(){
    	String[] select = new String[] { "1", "2", "3", "4", "5" };
		return select[new Random(System.nanoTime()).nextInt(select.length - 1)];
    }
     
     

 	public static List<String[]> generateCommentsTable(String VPD) throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException{
		List<String[]> comments = new ArrayList<String[]>();
		commentStructure = getLastComment();
		int columnCount = commentStructure.getMetaData().getColumnCount();
		String[] data = new String[columnCount]; 
		
		while(commentStructure.next()){
			for (int i=0;i<columnCount;i++){
				data[i]=commentStructure.getString(i+1);
			}
			}
		
		int id = Integer.parseInt(data[commentStructure.findColumn("CommentId")-1])+1;
		for (String[] req:requests){
			
			String[] row = new String[columnCount];
			System.arraycopy(data, 0, row, 0, columnCount);
			row[commentStructure.findColumn("CommentId")-1] = String.valueOf(id);
			row[commentStructure.findColumn("VPD")-1] = VPD;
			row[commentStructure.findColumn("Caption")-1] = "Comment for stress test";
			row[commentStructure.findColumn("ObjectId")-1] = req[requestStructure.findColumn("pm_ChangeRequestId")-1];
			row[commentStructure.findColumn("ObjectClass")-1] = "Request";
			row[commentStructure.findColumn("RecordVersion")-1] = "0"; 
			row[commentStructure.findColumn("PrevComment")-1] = null; 
			comments.add(row);
			id++;
		}
		return comments;
	}
     
 	
 	public static List<String[]> generateActivitiesTable(String VPD) throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException{
		List<String[]> activities = new ArrayList<String[]>();
		activityStructure = getLastActivity();
		int columnCount = activityStructure.getMetaData().getColumnCount();
		String[] data = new String[columnCount]; 
		
		while(activityStructure.next()){
			for (int i=0;i<columnCount;i++){
				data[i]=activityStructure.getString(i+1);
			}
			}
		
		int id = Integer.parseInt(data[activityStructure.findColumn("pm_ActivityId")-1])+1;
		for (String[] task:tasks){
			
			String[] row = new String[columnCount];
			System.arraycopy(data, 0, row, 0, columnCount);
			row[activityStructure.findColumn("pm_ActivityId")-1] = String.valueOf(id);
			row[activityStructure.findColumn("VPD")-1] = VPD;
			row[activityStructure.findColumn("Caption")-1] = null;
			row[activityStructure.findColumn("Task")-1] = task[taskStructure.findColumn("pm_TaskId")-1];
			row[activityStructure.findColumn("Completed")-1] = "N";
			row[activityStructure.findColumn("Capacity")-1] = "1"; 
			row[activityStructure.findColumn("RecordVersion")-1] = "0"; 
			activities.add(row);
			id++;
		}
		return activities;
	}
     
 	public static List<String[]> generateAttachmentTable(String VPD) throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException{
		List<String[]> attachments = new ArrayList<String[]>();
		attachmentStructure = getLastAttachment();
		int columnCount = attachmentStructure.getMetaData().getColumnCount();
		String[] data = new String[columnCount]; 
		
		while(attachmentStructure.next()){
			for (int i=0;i<columnCount;i++){
				data[i]=attachmentStructure.getString(i+1);
			}
			}
		
		int id = Integer.parseInt(data[attachmentStructure.findColumn("pm_AttachmentId")-1])+1;
		for (String[] req:requests){
			
			String[] row = new String[columnCount];
			System.arraycopy(data, 0, row, 0, columnCount);
			row[attachmentStructure.findColumn("pm_AttachmentId")-1] = String.valueOf(id);
			row[attachmentStructure.findColumn("VPD")-1] = VPD;
			row[attachmentStructure.findColumn("ObjectId")-1] = req[requestStructure.findColumn("pm_ChangeRequestId")-1];
			row[attachmentStructure.findColumn("ObjectClass")-1] = "Request";
			row[attachmentStructure.findColumn("RecordVersion")-1] = "0"; 
			attachments.add(row);
			id++;
		}
		return attachments;
	}
     
 	public static List<String[]> generateCRLinkTable(String VPD) throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException{
		List<String[]> crLinks = new ArrayList<String[]>();
		crLinkStructure = getLastCRLink();
		int columnCount = crLinkStructure.getMetaData().getColumnCount();
		String[] data = new String[columnCount]; 
		
		while(crLinkStructure.next()){
			for (int i=0;i<columnCount;i++){
				data[i]=crLinkStructure.getString(i+1);
			}
			}
		
		int id;
		if (data[crLinkStructure.findColumn("pm_ChangeRequestLinkId")-1]==null) id = 1; else 
		id = Integer.parseInt(data[crLinkStructure.findColumn("pm_ChangeRequestLinkId")-1])+1;
		for (String[] req:requests){
			
			String[] row = new String[columnCount];
			System.arraycopy(data, 0, row, 0, columnCount);
			row[crLinkStructure.findColumn("pm_ChangeRequestLinkId")-1] = String.valueOf(id);
			row[crLinkStructure.findColumn("RecordCreated")-1] = getCurrentTimeStamp();
			row[crLinkStructure.findColumn("RecordModified")-1] = getCurrentTimeStamp();
			row[crLinkStructure.findColumn("OrderNum")-1] = "10";
			row[crLinkStructure.findColumn("SourceRequest")-1] = req[requestStructure.findColumn("pm_ChangeRequestId")-1];
			row[crLinkStructure.findColumn("VPD")-1] = VPD;
			row[crLinkStructure.findColumn("TargetRequest")-1] = String.valueOf(Integer.parseInt(req[requestStructure.findColumn("pm_ChangeRequestId")-1])-1);
			row[crLinkStructure.findColumn("LinkType")-1] = "1";
			row[crLinkStructure.findColumn("RecordVersion")-1] = "0"; 
			crLinks.add(row);
			id++;
		}
		return crLinks;
	}
     
 	
	public static List<String[]> generateSnapshotsTable() throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException{
		List<String[]> snapshots = new ArrayList<String[]>();
		snapshotStructure = getLastSnapshot();
		int columnCount = snapshotStructure.getMetaData().getColumnCount();
		String[] data = new String[columnCount]; 
		
		while(snapshotStructure.next()){
			for (int i=0;i<columnCount;i++){
				data[i]=snapshotStructure.getString(i+1);
			}
			}
		
		int id = Integer.parseInt(data[snapshotStructure.findColumn("cms_SnapshotId")-1])+1;
		int orderNum = Integer.parseInt(data[snapshotStructure.findColumn("OrderNum")-1])+10;
		for (String[] doc:documents){
			
			String[] row = new String[columnCount];
			System.arraycopy(data, 0, row, 0, columnCount);
			row[snapshotStructure.findColumn("cms_SnapshotId")-1] = String.valueOf(id);
			row[snapshotStructure.findColumn("VPD")-1] = doc[columnVPD];
			row[snapshotStructure.findColumn("OrderNum")-1] = String.valueOf(orderNum);
			row[snapshotStructure.findColumn("Caption")-1] = "Version 1";
			row[snapshotStructure.findColumn("SystemUser")-1] = "1";
			row[snapshotStructure.findColumn("ListName")-1] = "Requirement:"+doc[columnID];
			row[snapshotStructure.findColumn("ObjectId")-1] = doc[columnID];
			row[snapshotStructure.findColumn("ObjectClass")-1] = "Requirement";
			row[snapshotStructure.findColumn("Description")-1] = "Stress test version"; 
			row[snapshotStructure.findColumn("RecordVersion")-1] = "0"; 
			row[snapshotStructure.findColumn("Type")-1] = null; 
			
			snapshots.add(row);
			id++;
			orderNum = orderNum+10;
		}
		return snapshots;
	}
	
	public static List<String[]> generateSnapshotItemsTable(List<String[]> requirements) throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException{
		List<String[]> snapshotItems = new ArrayList<String[]>();
		snapshotItemStructure = getLastSnapshotItem();
		int columnCount = snapshotItemStructure.getMetaData().getColumnCount();
		String[] data = new String[columnCount]; 
		
		while(snapshotItemStructure.next()){
			for (int i=0;i<columnCount;i++){
				data[i]=snapshotItemStructure.getString(i+1);
			}
			}
		
		int id = Integer.parseInt(data[snapshotItemStructure.findColumn("cms_SnapshotItemId")-1])+1;
		int orderNum = Integer.parseInt(data[snapshotItemStructure.findColumn("OrderNum")-1])+10;
		for (String[] req:requirements){
			
			String[] row = new String[columnCount];
			System.arraycopy(data, 0, row, 0, columnCount);
			row[snapshotItemStructure.findColumn("cms_SnapshotItemId")-1] = String.valueOf(id);
			row[snapshotItemStructure.findColumn("VPD")-1] = req[columnVPD];
			row[snapshotItemStructure.findColumn("OrderNum")-1] = String.valueOf(orderNum);
			row[snapshotItemStructure.findColumn("Snapshot")-1] = getSnapsotIdByWikiId(req[columnID]);
			row[snapshotItemStructure.findColumn("ObjectId")-1] = req[columnID];
			row[snapshotItemStructure.findColumn("ObjectClass")-1] = "Requirement";
			row[snapshotItemStructure.findColumn("RecordVersion")-1] = "0"; 
			snapshotItems.add(row);
			id++;
			orderNum = orderNum+10;
		}
		return snapshotItems;
	}
	
	public static List<String[]> generateSnapshotItemValuesTable(List<String[]> requirements) throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException{
		List<String[]> snapshotItemValues = new ArrayList<String[]>();
		snapshotItemValueStructure = getLastSnapshotItemValue();
		String[] captions = {"Название","Содержание",null,null,"Родительская страница","Путь к родительской странице","Номер раздела",null,"Пользовательское поле 3",null};
		String[] referenceNames = {"Caption","Content","DocumentId","IsDocument","ParentPage","ParentPath","SectionNumber","SortIndex","UserField3"};
		String[] values = new String[referenceNames.length];
		
		int columnCount = snapshotItemValueStructure.getMetaData().getColumnCount();
		String[] data = new String[columnCount]; 
		
		while(snapshotItemValueStructure.next()){
			for (int i=0;i<columnCount;i++){
				data[i]=snapshotItemValueStructure.getString(i+1);
			}
			}
		
		int id = Integer.parseInt(data[snapshotItemValueStructure.findColumn("cms_SnapshotItemValueId")-1])+1;
		int orderNum = Integer.parseInt(data[snapshotItemValueStructure.findColumn("OrderNum")-1])+10;
		for (String[] req:requirements){
			
			values[0]=req[columnCaption];
			values[1]=req[columnContent];
			values[2]=req[columnDocumentId];
			values[3]=req[columnIsDocument];
			values[4]=req[columnParentPage];
			values[5]=req[columnParentPath];
			values[6]=req[columnSectionNumber];
			values[7]=req[columnSortIndex];
			values[8]="";
				
			   for (int i=0;i<values.length;i++) {
			String[] row = new String[columnCount];
			System.arraycopy(data, 0, row, 0, columnCount);
			row[snapshotItemValueStructure.findColumn("cms_SnapshotItemValueId")-1] = String.valueOf(id);
			row[snapshotItemValueStructure.findColumn("VPD")-1] = req[columnVPD];
			row[snapshotItemValueStructure.findColumn("OrderNum")-1] = String.valueOf(orderNum);
			row[snapshotItemValueStructure.findColumn("SnapshotItem")-1] = getSnapsotItemIdByObjectId(req[columnID]);
			row[snapshotItemValueStructure.findColumn("RecordVersion")-1] = "0"; 
			row[snapshotItemValueStructure.findColumn("Caption")-1] =captions[i]; 
			row[snapshotItemValueStructure.findColumn("ReferenceName")-1] = referenceNames[i];
			row[snapshotItemValueStructure.findColumn("Value")-1] = values[i];
			snapshotItemValues.add(row);
			id++;
			orderNum = orderNum+10;
			   }
		}
		return snapshotItemValues;
	}

	private static ResultSet getLastCRLink() throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException {
		ResultSet rs = null;
		try {
			rs = executeQuery("SELECT * FROM devprom.pm_changerequestlink ORDER by pm_ChangeRequestLinkId desc LIMIT 0,1;");
			int rowcount = 0;
			if (rs.last()) {
			  rowcount = rs.getRow();
			  rs.beforeFirst(); // not rs.first() because the rs.next() below will move on, missing the first element
			}
			System.out.println("Found rows: " + rowcount);
		} catch (SQLException e) {
			System.out.println("Can't execute Query");
			throw new SQLException(e);
		}
		return rs;
	}

	private static ResultSet getLastComment() throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException {
		ResultSet rs = null;
		try {
			rs = executeQuery("SELECT * FROM devprom.comment ORDER by CommentId desc LIMIT 0,1;");
			int rowcount = 0;
			if (rs.last()) {
			  rowcount = rs.getRow();
			  rs.beforeFirst(); // not rs.first() because the rs.next() below will move on, missing the first element
			}
			System.out.println("Found rows: " + rowcount);
		} catch (SQLException e) {
			System.out.println("Can't execute Query");
			throw new SQLException(e);
		}
		return rs;
	}

	private static ResultSet getLastAttachment() throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException {
		ResultSet rs = null;
		try {
			rs = executeQuery("SELECT * FROM devprom.pm_attachment ORDER by pm_AttachmentId desc LIMIT 0,1;");
			int rowcount = 0;
			if (rs.last()) {
			  rowcount = rs.getRow();
			  rs.beforeFirst(); // not rs.first() because the rs.next() below will move on, missing the first element
			}
			System.out.println("Found rows: " + rowcount);
		} catch (SQLException e) {
			System.out.println("Can't execute Query");
			throw new SQLException(e);
		}
		return rs;
	}
	
	private static ResultSet getLastActivity() throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException {
		ResultSet rs = null;
		try {
			rs = executeQuery("SELECT * FROM devprom.pm_activity ORDER by pm_ActivityId desc LIMIT 0,1;");
			int rowcount = 0;
			if (rs.last()) {
			  rowcount = rs.getRow();
			  rs.beforeFirst(); // not rs.first() because the rs.next() below will move on, missing the first element
			}
			System.out.println("Found rows: " + rowcount);
		} catch (SQLException e) {
			System.out.println("Can't execute Query");
			throw new SQLException(e);
		}
		return rs;
	}
	
	private static ResultSet getLastTask() throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException {
		ResultSet rs = null;
		try {
			rs = executeQuery("SELECT * FROM devprom.pm_task ORDER by pm_TaskId desc LIMIT 0,1;");
			int rowcount = 0;
			if (rs.last()) {
			  rowcount = rs.getRow();
			  rs.beforeFirst(); // not rs.first() because the rs.next() below will move on, missing the first element
			}
			System.out.println("Found rows: " + rowcount);
		} catch (SQLException e) {
			System.out.println("Can't execute Query");
			throw new SQLException(e);
		}
		return rs;
	}
	
	
	private static ResultSet getLastRequest() throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException {
		ResultSet rs = null;
		try {
			rs = executeQuery("SELECT * FROM devprom.pm_changerequest ORDER by pm_ChangeRequestId desc LIMIT 0,1;");
			int rowcount = 0;
			if (rs.last()) {
			  rowcount = rs.getRow();
			  rs.beforeFirst(); // not rs.first() because the rs.next() below will move on, missing the first element
			}
			System.out.println("Found rows: " + rowcount);
		} catch (SQLException e) {
			System.out.println("Can't execute Query");
			throw new SQLException(e);
		}
		return rs;
	}
	
	
	private static ResultSet getLastSnapshot() throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException {
		ResultSet rs = null;
		try {
			rs = executeQuery("SELECT * FROM devprom.cms_snapshot  order by cms_SnapshotId desc LIMIT 0,1;");
			int rowcount = 0;
			if (rs.last()) {
			  rowcount = rs.getRow();
			  rs.beforeFirst(); // not rs.first() because the rs.next() below will move on, missing the first element
			}
			System.out.println("Found rows: " + rowcount);
		} catch (SQLException e) {
			System.out.println("Can't execute Query");
			throw new SQLException(e);
		}
		return rs;
	}
	
	private static ResultSet getLastSnapshotItem() throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException {
		ResultSet rs = null;
		try {
			rs = executeQuery("SELECT * FROM devprom.cms_snapshotitem  order by cms_SnapshotItemId desc LIMIT 0,1;");
			int rowcount = 0;
			if (rs.last()) {
			  rowcount = rs.getRow();
			  rs.beforeFirst(); // not rs.first() because the rs.next() below will move on, missing the first element
			}
			System.out.println("Found rows: " + rowcount);
		} catch (SQLException e) {
			System.out.println("Can't execute Query");
			e.printStackTrace();
			throw new SQLException(e);
		}
		return rs;
	}
	
	private static ResultSet getLastSnapshotItemValue() throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException {
		ResultSet rs = null;
		try {
			rs = executeQuery("SELECT * FROM devprom.cms_snapshotitemvalue order by cms_SnapshotItemValueId desc LIMIT 0,1;");
			int rowcount = 0;
			if (rs.last()) {
			  rowcount = rs.getRow();
			  rs.beforeFirst(); // not rs.first() because the rs.next() below will move on, missing the first element
			}
			System.out.println("Found rows: " + rowcount);
		} catch (SQLException e) {
			System.out.println("Can't execute Query");
			e.printStackTrace();
			throw new SQLException(e);
		}
		return rs;
	}

	public static String[] readTemplateArray(String caption) throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException{
		ResultSet rs = null;
		String[] data = null;
		try {
			rs = executeQuery("SELECT * FROM devprom.wikipage WHERE Caption='"+caption+"';");
			int rowcount = 0;
			if (rs.last()) {
			  rowcount = rs.getRow();
			  rs.beforeFirst(); // not rs.first() because the rs.next() below will move on, missing the first element
			}
			System.out.println("Found rows: " + rowcount);
		} catch (SQLException e) {
			System.out.println("Can't execute Query");
			throw new SQLException(e);
		}
		
		try {
			int columnCount =  rs.getMetaData().getColumnCount();
			//Заполняем массив имен полей таблицы
			wikiPageColums = new String[columnCount];
			for (int i=0;i<columnCount;i++){
				wikiPageColums[i]=rs.getMetaData().getColumnName(i+1);
			}
			//Запоминаем в константы номера колонок
		    columnID = rs.findColumn("WikiPageId")-1;
		    columnOrderNum = rs.findColumn("OrderNum")-1;
		    columnCaption = rs.findColumn("Caption")-1;
		    columnContent = rs.findColumn("Content")-1;
		    columnParentPage = rs.findColumn("ParentPage")-1;
		    columnParentPath = rs.findColumn("ParentPath")-1;
		    columnSectionNumber = rs.findColumn("SectionNumber")-1;
		    columnDocumentId = rs.findColumn("DocumentId")-1;
		    columnIsDocument = rs.findColumn("IsDocument")-1;
		    columnSortIndex = rs.findColumn("SortIndex")-1;
		    columnVPD =  rs.findColumn("VPD")-1;
		    columnUID =  rs.findColumn("UID")-1;
			//Заполняем массив данных. На самом деле предполагается, что запись возвращена одна, поэтому цикл while - формальность
			data = new String[columnCount]; 
			while(rs.next()){
				for (int i=0;i<columnCount;i++){
					data[i]=rs.getString(i+1);
				}
				}
			
		} catch (SQLException e) {
			System.out.println("Error processing query results");
			e.printStackTrace();
			throw new SQLException (e);
		}
		return data;
	}
	
	

	public static List<String[]> createRequirementsTree(String[] template, int parentPagesCount, int secondLevelItemsCount, int thirdLevelItemsCount) {
		List<String[]> requirements = new ArrayList<String[]>();
		int id = Integer.parseInt(template[0]) + 1;
		int orderNum =  Integer.parseInt(template[1]) + 10;
		String content = readContentFromFile();
		int parentId;
		int secondId;
		
		//Главный цикл - родительские объекты
		 for (int i=1;i<=parentPagesCount;i++) {
		 String[] parent = new String[template.length];
		 System.arraycopy(template, 0, parent, 0, template.length);
		 parent[columnID] = String.valueOf(id);
		 parent[columnOrderNum] = String.valueOf(orderNum);
		 parent[columnCaption] = "StressTestParent"+i;
		 parent[columnContent] = content;
		 parent[columnParentPage] = null;
		 parent[columnParentPath] = ","+id+",";
		 parent[columnSectionNumber] = "1";
		 parent[columnDocumentId] = String.valueOf(id);
		 parent[columnIsDocument] = "1";
		 long sortIndex =  orderNum + 10000000000L;
		 String parentSortIndex = String.valueOf(sortIndex).substring(1);
		 parent[columnSortIndex] = parentSortIndex;
		 requirements.add(parent);	
		 documents.add(parent);
		 parentId = id;
		 id++;
		orderNum = orderNum + 10;
			 //Второй цикл - дочерние элементы
			 for (int k=1;k<=secondLevelItemsCount;k++) {
				 String[] second = new String[template.length];
				 System.arraycopy(template, 0, second, 0, template.length);
				 second[columnID]= String.valueOf(id);
				 second[columnOrderNum]=String.valueOf(orderNum);
				 second[columnCaption]= "StressTestSecondLevel"+i+"."+k;
				 second[columnContent] = content;
				 second[columnParentPage] = String.valueOf(parentId);
				 second[columnParentPath]  = ","+parentId+","+id+",";
				 second[columnSectionNumber] = "1."+k;
				 second[columnDocumentId] = String.valueOf(parentId);
				 second[columnIsDocument] = "0";
				 long sortIndex2 =  orderNum + 10000000000L;
				 String secondSortIndex = String.valueOf(sortIndex2).substring(1);
				 second[columnSortIndex] = parentSortIndex+","+secondSortIndex;
				 requirements.add(second);
				 
				 secondId = id;
				   id++;
					orderNum = orderNum + 10;
				 //Третий цикл - страницы нижнего уровня
				 for (int n=1;n<=thirdLevelItemsCount;n++) {
					 String[] third = new String[template.length];
					 System.arraycopy(template, 0, third, 0, template.length);
					 third[columnID] = String.valueOf(id);
					 third[columnOrderNum] = String.valueOf(orderNum);
					 third[columnCaption] = "StressTestThirdLevel"+i+"."+k+"."+n;
					 third[columnContent] = content;
					 third[columnParentPage] = String.valueOf(secondId);
					 third[columnParentPath] = ","+parentId+"," + secondId+ ","+id+",";
					 third[columnSectionNumber] = "1."+k +"." + n;
					 third[columnDocumentId] = String.valueOf(parentId);
					 third[columnIsDocument] = "0";
					 long sortIndex3 =  orderNum + 10000000000L;
					 third[columnSortIndex] = parentSortIndex+","+secondSortIndex+","+String.valueOf(sortIndex3).substring(1);
					 requirements.add(third);
					   id++;
					   orderNum = orderNum + 10;
				 }
			 }
		 }
		 return requirements;
	}
	
	
	
	public static String generateInsertSQLString(String[] data, String[] columns, String tableName){
		StringBuffer sb = new StringBuffer("INSERT INTO `devprom`.`"+tableName+"` (");
		boolean isFirst = true;
		for (int i=0;i<data.length;i++){
			if ( i == columnUID ) continue;
			if (data[i]!=null){
				if (isFirst) 	{
					sb.append("`"+columns[i]+"`");
					isFirst = false;
				}
				else	sb.append(", `"+columns[i]+"`");
			}
		}
		sb.append(") VALUES (");
		isFirst = true;
		for (int k=0;k<data.length;k++){
			if ( k == columnUID ) continue;
			if (data[k]!=null){
				if (isFirst) 	{
					sb.append("'"+data[k]+"'");
					isFirst = false;
				}
				else	sb.append(", '"+data[k]+"'");
			}
			}
		sb.append(");");	
		return  sb.toString();
	}
	
		 
	private static String readContentFromFile() {
		StringBuilder sb = null;
		BufferedReader br;
		try {
			br = new BufferedReader(new FileReader("resources/content.txt"));
		} catch (FileNotFoundException e) {
			System.out.println("Can't read file content.txt");
			e.printStackTrace();
			return "File not found";
		}
		    try {
		        sb = new StringBuilder();
		        String line = br.readLine();

		        while (line != null) {
		            sb.append(line);
		            sb.append(System.lineSeparator());
		            line = br.readLine();
		        }
		    } 
		    catch (IOException e2) {
		    	System.out.println("Can't read file");
		    	e2.printStackTrace();
		    }
		    finally {
		        try {
					br.close();
				} catch (IOException e) {
					e.printStackTrace();
				}
		    }
		    return  sb.toString();
	}

	public static ResultSet executeQuery(String query) throws SQLException, InstantiationException, IllegalAccessException, ClassNotFoundException {
		Statement st = null;
		Connection c = Connect.getConnection();
		 try {
			st = c.createStatement();
		} catch (SQLException e) {
			System.out.println("Can't create statement");
			throw new SQLException (e);
		}
		 return st.executeQuery(query);
	}
	
	public static int executeUpdate(String query) throws SQLException, InstantiationException, IllegalAccessException, ClassNotFoundException {
		Statement st = null;
		Connection c = Connect.getConnection();
		 try {
			st = c.createStatement();
		} catch (SQLException e) {
			System.out.println("Can't create statement");
			throw new SQLException (e);
		}
		 return st.executeUpdate(query);
	}
	
	private static String getCurrentTimeStamp() {
		 
		java.util.Date dt = new java.util.Date();

		java.text.SimpleDateFormat sdf = 
		     new java.text.SimpleDateFormat("yyyy-MM-dd HH:mm:ss");

		return sdf.format(dt);
	}


	
	public static String generateInsertCRLinkSQLString(String[] data) throws SQLException {
		int columnCount =  crLinkStructure.getMetaData().getColumnCount();
		String[] columns = new String[columnCount];
		for (int i=0;i<columnCount;i++){
			columns[i]=crLinkStructure.getMetaData().getColumnName(i+1);
		}
		return generateInsertSQLString(data,columns,"pm_changerequestlink");
	}
	
	
	
	public static String generateInsertAttachmentSQLString(String[] data) throws SQLException {
		int columnCount =  attachmentStructure.getMetaData().getColumnCount();
		String[] columns = new String[columnCount];
		for (int i=0;i<columnCount;i++){
			columns[i]=attachmentStructure.getMetaData().getColumnName(i+1);
		}
		return generateInsertSQLString(data,columns,"pm_attachment");
	}
	
	
	
	public static String generateInsertActivitySQLString(String[] data) throws SQLException {
		int columnCount =  activityStructure.getMetaData().getColumnCount();
		String[] columns = new String[columnCount];
		for (int i=0;i<columnCount;i++){
			columns[i]=activityStructure.getMetaData().getColumnName(i+1);
		}
		return generateInsertSQLString(data,columns,"pm_activity");
	}
	
	
	
	public static String generateInsertCommentSQLString(String[] data) throws SQLException {
		int columnCount =  commentStructure.getMetaData().getColumnCount();
		String[] columns = new String[columnCount];
		for (int i=0;i<columnCount;i++){
			columns[i]=commentStructure.getMetaData().getColumnName(i+1);
		}
		return generateInsertSQLString(data,columns,"comment");
	}
	
	
	public static String generateInsertTaskSQLString(String[] data) throws SQLException {
		int columnCount =  taskStructure.getMetaData().getColumnCount();
		String[] columns = new String[columnCount];
		for (int i=0;i<columnCount;i++){
			columns[i]=taskStructure.getMetaData().getColumnName(i+1);
		}
		return generateInsertSQLString(data,columns,"pm_task");
	}
	
	public static String generateInsertRequestSQLString(String[] data) throws SQLException {
		int columnCount =  requestStructure.getMetaData().getColumnCount();
		String[] columns = new String[columnCount];
		for (int i=0;i<columnCount;i++){
			columns[i]=requestStructure.getMetaData().getColumnName(i+1);
		}
		return generateInsertSQLString(data,columns,"pm_changerequest");
	}
	
	public static String generateInsertWikiPageSQLString(String[] data) {
		return generateInsertSQLString(data,wikiPageColums,"wikipage");
	}
	
	public static String generateInsertSnapshotSQLString(String[] data) throws SQLException {
		int columnCount =  snapshotStructure.getMetaData().getColumnCount();
		String[] columns = new String[columnCount];
		for (int i=0;i<columnCount;i++){
			columns[i]=snapshotStructure.getMetaData().getColumnName(i+1);
		}
		return generateInsertSQLString(data,columns,"cms_snapshot");
	}
	
	public static String generateInsertSnapshotItemSQLString(String[] data) throws SQLException {
		int columnCount =  snapshotItemStructure.getMetaData().getColumnCount();
		String[] columns = new String[columnCount];
		for (int i=0;i<columnCount;i++){
			columns[i]=snapshotItemStructure.getMetaData().getColumnName(i+1);
		}
		return generateInsertSQLString(data,columns,"cms_snapshotitem");
	}
	
	public static String generateInsertSnapshotItemValueSQLString(String[] data) throws SQLException {
		int columnCount =  snapshotItemValueStructure.getMetaData().getColumnCount();
		String[] columns = new String[columnCount];
		for (int i=0;i<columnCount;i++){
			columns[i]=snapshotItemValueStructure.getMetaData().getColumnName(i+1);
		}
		return generateInsertSQLString(data,columns,"cms_snapshotitemvalue");
	}

	private static String getSnapsotIdByWikiId(String wikiId) throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException{
		ResultSet rs = executeQuery("SELECT DocumentId FROM devprom.wikipage WHERE WikiPageId = '"+wikiId+"'");
		String docId = null;
		String snapshotId = null;
		while(rs.next()){
			docId=rs.getString(1);
			}
		ResultSet rs2 = executeQuery("SELECT cms_SnapshotId FROM devprom.cms_snapshot WHERE ObjectId = '"+docId+"'");
		while(rs2.next()){
			snapshotId=rs2.getString(1);
			}
		return snapshotId;
	}
	

	private static String getSnapsotItemIdByObjectId(String objectId) throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException{
		ResultSet rs = executeQuery("SELECT cms_SnapshotItemId FROM devprom.cms_snapshotitem WHERE ObjectId = '"+objectId+"'");
		String snapshotItemId = null;
		while(rs.next()){
			snapshotItemId=rs.getString(1);
			}
		return snapshotItemId;
	}

	public static String[] getProjectIdByName(String name) throws InstantiationException, IllegalAccessException, ClassNotFoundException, SQLException {
		String[] result = new String[2];
		ResultSet rs = executeQuery("SELECT pm_ProjectId, VPD FROM devprom.pm_project WHERE Caption = '"+name+"'");
		while(rs.next()){
			result[0]=rs.getString(1);
			result[1]=rs.getString(2);
			}
		return result;
	}
	
}
