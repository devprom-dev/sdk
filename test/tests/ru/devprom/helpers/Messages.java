package ru.devprom.helpers;

public enum Messages 
{
	ERROR_MESSAGE_SETUP("Установка обновления невозможна."), 
	ERROR_MESSAGE_FORMAT("Загруженный файл не является файлом обновления Devprom"), 
	ERROR_MESSAGE_UPDATE("же установлено или включено в текущую версию системы"), 
	ERROR_MESSAGE_DEPENDENCY("Не установлены необходимые обновления"), 
	ERROR_MESSAGE_BAD_PROJECT_NAME("В кодовом названии проекта можно использовать только латинские буквы"), 
	ERROR_MESSAGE_DUPLICATE_PROJECT_CODENAME("должно быть уникальным"),
	SUCCESS_MESSAGE_ACTION_DONE("Действие успешно выполнено над выбранными объектами"),
	INFO_MESSAGE_REQUEST_BLOKED("Пожелание заблокировано другим невыполненным пожеланием"), 
	ERROR_MESSAGE_DUPLICATE_ATTRIBUTE("Внимание! Атрибут с таким ссылочным именем уже определен для объекта");

	private String msg;

	private Messages(String msg) {
		this.msg = msg;
	}

	public String getText() {
		return msg;
	}
}
