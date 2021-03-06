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
 *  
 */
/**
 * 
 * This class is the api to install the cms
 * @author Hikaru-Shindo <hikaru@fansubcode.org>
 *
 */
class Install_Api_DoctrineBuilder extends Doctrine_Import_Builder
{
    protected function _getBaseClassName($className)
    {
        $parts = explode('_', $className);
        $className = implode('_', array($parts[0], $parts[1], 'Base', $parts[2]));
        return $className;
    }
    
    public function writeDefinition(array $definition)
    {
        
        if(isset($definition['is_main_class']) && $definition['is_main_class']) {
            $definition['inheritance']['extends'] = $this->_getBaseClassName($definition['topLevelClassName']);
        }
        
        parent::writeDefinition($definition);
    }
}