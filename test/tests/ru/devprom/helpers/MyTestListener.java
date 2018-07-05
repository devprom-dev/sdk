package ru.devprom.helpers;

import org.apache.log4j.Level;
import org.apache.log4j.LogManager;
import org.apache.log4j.Logger;
import org.testng.ITestContext;
import org.testng.ITestResult;
import org.testng.TestListenerAdapter;

public class MyTestListener extends TestListenerAdapter {
	final Logger FILELOG;

	public MyTestListener() {
		FILELOG = LogManager.getLogger("LISTENERLOG");
		switch (Configuration.getLoglevel()) {
		case "debug":
			FILELOG.setLevel(Level.DEBUG);
			break;
		case "info":
			FILELOG.setLevel(Level.INFO);
			break;
		case "warn":
			FILELOG.setLevel(Level.WARN);
			break;
		case "error":
			FILELOG.setLevel(Level.ERROR);
			break;
		default:
			FILELOG.setLevel(Level.DEBUG);
		}
	}

	@Override
	public void onConfigurationFailure(ITestResult itr) {
		// TODO Auto-generated method stub
		super.onConfigurationFailure(itr);
		FILELOG.error("CONFIGURAION FAILED. CLASS: "
				+ itr.getTestClass().getName() + " METHOD: "
				+ itr.getMethod().getMethodName());
		itr.getThrowable().printStackTrace(System.err);
	}

	@Override
	public void onTestFailure(ITestResult tr) {
		// TODO Auto-generated method stub
		super.onTestFailure(tr);
		FILELOG.error("TEST FAILED. CLASS: " + tr.getTestClass().getName()
				+ " METHOD: " + tr.getMethod().getMethodName());
		tr.getThrowable().printStackTrace(System.err);
	}

	@Override
	public void onTestStart(ITestResult result) {
		// TODO Auto-generated method stub
		super.onTestStart(result);
		FILELOG.info("RUNNING TEST. CLASS: " + result.getTestClass().getName()
				+ " METHOD: " + result.getMethod().getMethodName());
	}

	@Override
	public void onTestSuccess(ITestResult tr) {
		// TODO Auto-generated method stub
		super.onTestSuccess(tr);
		FILELOG.info("TEST SUCCESS. CLASS: " + tr.getTestClass().getName()
				+ " METHOD: " + tr.getMethod().getMethodName());
	}

	@Override
	public void onFinish(ITestContext testContext) {
		// TODO Auto-generated method stub.
		super.onFinish(testContext);
		FILELOG.info("++++++++++++++++++++++++++++++++++++++++++++++++++++++");
		FILELOG.info("                  +++++++++++++++                     ");
		FILELOG.info("        COMPLETED. SEE RESULTS FROM TESTNG.           ");
		FILELOG.info("                  +++++++++++++++                     ");
		FILELOG.info("++++++++++++++++++++++++++++++++++++++++++++++++++++++");

	}

}
