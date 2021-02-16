<?php
include 'DashboardTable.php';
include 'DashboardItemForm.php';

class DashboardPage extends PMPage
{
    public function getObject() {
        return getFactory()->getObject('DashboardItem');
    }

    public function getTable() {
		return new DashboardTable($this->getObject());
    }

    public function getEntityForm() {
        return new DashboardItemForm($this->getObject());
    }
}
