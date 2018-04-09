package ru.devprom.items;

public class Project {

	private String name;
	private String codeName;
	private Template template;
	private String coordinator;
        private boolean demoData;
	
	public Project(String name, String codeName, Template template) {
		this.name = name;
		this.codeName = codeName;
		this.template = template;
	}

	public String getName() {
		return name;
	}

	public void setName(String name) {
		this.name = name;
	}

	public String getCodeName() {
		return codeName;
	}

	public void setCodeName(String codeName) {
		this.codeName = codeName;
	}

	public Template getTemplate() {
		return template;
	}

	public void setTemplate(Template template) {
		this.template = template;
	}

	public String getCoordinator() {
		return coordinator;
	}

	public void setCoordinator(String coordinator) {
		this.coordinator = coordinator;
	}
        
        public void setDemoData(boolean isDemoData){
            this.demoData = isDemoData;
        }
        
        public boolean getDemoData(){
            return demoData;
        }

}
