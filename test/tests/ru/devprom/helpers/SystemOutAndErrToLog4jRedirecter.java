package ru.devprom.helpers;

import java.io.PrintStream;

import org.apache.log4j.Logger;

public class SystemOutAndErrToLog4jRedirecter {

	private static final Logger logger = Logger.getLogger("#SYSTEM_OUT");

	public static Logger getLogger() {
		return logger;
	}

	public static void bindSystemOutAndErrToLog4j() {
		System.setOut(createLoggingProxy(System.out));
		System.setErr(createLoggingProxy(System.err));
	}

	private static PrintStream createLoggingProxy(
			final PrintStream realPrintStream) {
		return new PrintStream(realPrintStream) {
			@Override
			public void print(final String str) {
				realPrintStream.print(str);
				if (str.contains("Exception") || str.contains("FAILED"))
					logger.error(str);
				else if (str.contains("\tat ")) {
					if (Configuration.getLoglevel().equals("debug"))
						logger.debug(str);
				} else if (Configuration.getLoglevel().equals("debug")
						|| Configuration.getLoglevel().equals("info"))
					logger.info(str);
			}
		};
	}
}