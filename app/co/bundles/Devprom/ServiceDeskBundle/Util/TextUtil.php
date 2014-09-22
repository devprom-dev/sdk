<?php

namespace Devprom\ServiceDeskBundle\Util;


/**
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
class TextUtil {

    public static function escapeHtml($str) {
        return htmlspecialchars($str, ENT_COMPAT, 'cp1251');
    }

    /**
     * ������� ���� ������������ ������, ����� html-��������, ��������� � �����������, ������������ ������� � ��������.
     * �������� ����� ���������
     */
    public static function escapeForDevpromWysiwygFields($str) {
        return self::escapeHtml(nl2br(self::escapeHtml($str)));
    }


    public static function unescapeHtml($text) {
        return stripslashes(html_entity_decode($text));
    }

}