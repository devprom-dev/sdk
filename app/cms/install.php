<?php

  require_once('common.php');
  require_once('design.php');
  require_once('c_model.php');

  //////////////////////////////////////////////////////////////////////////////////////
  $model = new MetaModel;
  
  beginPage('���������');
?>
	<table width=100%>
		<tr>
			<td align=left>
			<?
				  $model->Install();
			?>
				������� ���������� ��������� ������� �����������.
			</td>
		</tr>
    </table>
<?
	endPage();
?>
