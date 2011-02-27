<?php
/*
 *  This file is part of FansubCMS.
 *
 *  FansubCMS is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  FansubCMS is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with FansubCMS.  If not, see <http://www.gnu.org/licenses/>
 */
class Devtools_DoctrineController extends FansubCMS_Controller_Action
{
    public function init ()
    {
        if (APPLICATION_ENV != 'development') {
            throw new Zend_Controller_Dispatcher_Exception(
            'Invalid module specified (devtools)');
            die();
        }
        $front = Zend_Controller_Front::getInstance();
        $front->unregisterPlugin('ZFDebug_Controller_Plugin_Debug');
        $this->_helper->layout()->disableLayout();
        set_time_limit(0);
    }
    public function preDispatch ()
    {
        $this->view->partialName = $this->getRequest()->getParam('action');
    }

    public function indexAction ()
    {
        if (! isset($this->view->title)) {
            $this->view->title = "Main page";
        }
        if (! isset($this->view->showMenu)) {
            $this->view->showMenu = true;
        }
    }
    public function migrationAction ()
    {
        $pdata = new stdClass();
        $api = new Devtools_Api_DoctrineTool();
        $changes = $api->generateMigration(true);
        $changeCount = 0;
        $migration = $api->getMigration();
        $currentVersion = $migration->getCurrentVersion();
        $latestVersion = $migration->getLatestVersion();
        $pdata->migrationNeeded = ($latestVersion > $currentVersion);
        $pdata->currentVersion = $currentVersion;
        $pdata->latestVersion = $latestVersion;
        // FirePHP_Init::init()->fb($pdata);
        foreach ($changes as $changeType => $arr) {
            $changeCount += count($arr);
        }
        if ($changeCount > 0) {
            $pdata->hasChanges = true;
            $pdata->changeCount = $changeCount;
            $pdata->changes = $changes;
        } else {
            $pdata->hasChanges = false;
        }
        $this->view->partialData = $pdata;
        $this->_forward('index');
    }
    
    public function generatemigrationsAction ()
    {
        $api = new Devtools_Api_DoctrineTool();
        $pdata = new stdClass();
        $this->view->title = "Generate Migration";
        $generate = ($this->getRequest()->getParam('generate', 0) == 1);
        $pdata->generate = $generate;
        if (! $generate) {
            $changes = $api->generateMigration(true);
            $migrationAllowed = true;
            $changeCount = 0;
            foreach ($changes as $changeType => $arr) {
                $changeCount += count($arr);
            }
            if ($changeCount == 0) {
                $migrationAllowed = false;
            }
            $pdata->changes = $changes;
            $pdata->changeCount = $changeCount;
            $pdata->migrationAllowed = $migrationAllowed;
        } else {
            $api->generateMigration(false);
        }
        $this->view->partialData = $pdata;
        $this->_forward('index');
    }
    
    public function generatemodelsAction ()
    {
        $this->view->partialData = new stdClass();
        $this->view->title = "Generate models";
        $api = new Devtools_Api_DoctrineTool();
        $submit = $this->getRequest()->getParam('submit', NULL);
        if (is_null($submit)) {
            $this->view->partialData->state = 1;
            $this->_forward('index');
            return;
        }
        $this->view->partialData->state = 2;
        $api->generateModels();
        $this->view->title = "Generate models";
        $this->view->message = "Models were generated.";
        $ref = $this->getRequest()->getParam('ref', null);
        if ($ref == 'migration') {
            $this->_forward('migration');
        } else {
            $this->_forward('index');
        }
    }
    
    public function setmigrationversionAction() {
        $this->view->title = "Set the migration version of the databse";
        
        $version = $this->getRequest()->getParam('version', null);

        if(is_null($version)) {
            $this->view->partialData = new stdClass();
            $this->view->message = 'Please provide parameter "version". If you provide "current" it means latest version.';
            $this->_forward('index');
            return;
        }

        elseif($version == 'current') {
            Devtools_Api_DoctrineTool::setMigrationVersionToCurrent();
            $this->view->message = 'Migration version was set to latest version available.';
        }
        else {
            Devtools_Api_DoctrineTool::setMigrationVersion($version);
            $this->view->message = "Migration version was set to $version.";
        }

        $this->view->partialData = new stdClass();
        $this->_forward('index');
    }
}