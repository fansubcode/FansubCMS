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

class Admin_IndexController extends FansubCMS_Controller_Action {
    public function indexAction() {
        foreach($_SERVER as $k => $v) {
            $server->$k = $v;
        }
        $this->view->system = $server;
        $this->view->isAdmin = Zend_Auth::getInstance()->getIdentity()->hasRole('admin_admin');
    }
}