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

class Cms_Bootstrap extends Zend_Application_Module_Bootstrap {
    public $frontController;
    public $settings;

    protected function _initModule() {
        $this->settings = new Zend_Config_Ini(dirname(__FILE__).'/configs/module.ini');
        $this->frontController = Zend_Controller_Front::getInstance();
    }

    protected function _initRoute() {
        if($this->settings->routes->router != null) {
            $router = $this->frontController->getRouter();
            $router->addConfig($this->settings->routes->router, 'routes');
        }
    }
}