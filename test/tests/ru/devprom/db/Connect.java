package ru.devprom.db;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

public class Connect {
	private static Connection conn = null;
	private static String userName = "root";
	private static String password = "";
	private static String url = "jdbc:mysql://localhost:3306/devprom"; 
	 
	
	public static Connection getConnection() throws SQLException, InstantiationException, IllegalAccessException, ClassNotFoundException{
		if (conn==null || !conn.isValid(2)) return 	openConnection();
		else return conn;
	}
	
	public static void close(){
		 closeConnection(conn);
	}
	
	 private static Connection openConnection() throws SQLException, InstantiationException, IllegalAccessException, ClassNotFoundException{
		 try
		 {
		
		 Class.forName ("com.mysql.jdbc.Driver").newInstance ();
		 conn = DriverManager.getConnection (url, userName, password);
		 System.out.println ("Database connection established");
		 }
		 catch (SQLException e)
		 {
		 System.err.println ("Cannot connect to database server");
		 throw new SQLException (e);
		 }
		 return conn;
	 }
	 
	 private static void closeConnection(Connection c) {
	  if (c != null)
	   {
	    try
         {
	 c.close ();
	 System.out.println ("Database connection terminated"); 
         }	
	 catch (Exception e) { }
	   }
 	}
}
