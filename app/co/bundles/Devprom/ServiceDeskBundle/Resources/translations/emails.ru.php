<?php
/**
 * ������� �����
 *
 * @author Kosta Korenkov <7r0ggy@gmail.com>
 */
return array(
    'issue.created.subject' => '[I-%id%] ���� ������ �������� � ������ ���������',
    'issue.created.body' => '
        <p>������������!</p>

        <p>�� �������� ������ "<a href="%link%">%title%</a>":</p>

        <blockquote>%description%</blockquote>
        <br/>
        <p><b>���:</b> %type%</p>
        <p><b>�������:</b> %product%</p>
        <p><b>���������:</b> %priority%</p>
        <br/>
        <p>����������� ������ ��������� ��� ���������� ����� ������� � �������� � ���� � ��������� �����.</p>
        <br/>
        <hr>
        <p>�� ������ ����������� ������ ������ � �������� ���� ����������� �� ������: <a href="%link%">%link%</a></p>
    ',

    'issue.updated.subject' => '[I-%id%] ���� ������ ���� ��������',
    'issue.updated.body' => '
        <p>���a�c������!</p>
        <p>���� ������ "<a href="%link%">%title%</a>" ���� ��������� ����������� ������ ���������:</p>
        %changes%
        <br/>
        <hr>
        <p>�� ������ ����������� ������ ������ � �������� ���� ����������� �� ������: <a href="%link%">%link%</a></p>
    ',

    'issue.commented.subject' => '[I-%id%] � ����� ������ �������� �����������',
    'issue.commented.body' => '
        <p>���a�c������!</p>
        <p>��������� ������ ��������� ������� ����������� � ����� ������ "<a href="%link%">%title%</a>":</p>
        <blockquote>%comment%</blockquote>
        <br/>
        <hr>
        <p>�� ������ ����������� ������ ������ � �������� ���� ����������� �� ������: <a href="%link%">%link%</a></p>
    ',

    'issue.resolved.subject' => '[I-%id%] ���� ������ ���������',
    'issue.resolved.body' => '
        <p>���a�c������!</p>
        <p>���� ������ "<a href="%link%">%title%</a>" ���� ������� ����������� ������ ���������</p>
        <p><b>�����������:</b></p>
        <blockquote>%comment%</blockquote>
        <br/>
        <hr>
        <p>���� �� ��������, ��� ������ �� ��������� ��� ��������� �����������, �� �������� ���� ����������� �� ������: <a href="%link%">%link%</a></p>
    ',

    'resetting.email.subject' => '����� ������',
    'resetting.email.message' => '
            <p>������������!</p>

            <p>�� �������� ��� ������, ������ ��� ���-�� �������� �������������� ������ �� ����� ������ ��������� %clientName%.</p>
            <p>��� ������ ������ ���������� �������� �� ������: <a href="%confirmationUrl%">%confirmationUrl%</a></p>

            <p>� ���������� �����������,<br>
            ������ ��������� %clientName%</p>
            <a href="%link%">%link%</a>',

    'registration.email.subject' => '�������� ����������� � �������',
    'registration.email.message' => '
            <p>������������!</p>

            <p>���������� ��� �� ����������� �� ����� ������ ��������� %clientName%.</p>
            <p>��� ����� ��� ����� �� ����: <i>%login%</i></p>
            <p>��� ������: <i>%password%</i></p>

            <p>� ���������� �����������,<br>
            ������ ��������� %clientName%</p>
            <a href="%link%">%link%</a>',

);
?>
