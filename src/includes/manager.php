<?php
/**
 * @package Abricos
 * @subpackage Uniteller
 * @copyright 2016 Alexander Kuzmin
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Alexander Kuzmin <roosit@abricos.org>
 */

/**
 * Class UnitellerManager
 */
class UnitellerManager extends Ab_ModuleManager {

    public function IsAdminRole(){
        return $this->IsRoleEnable(UnitellerAction::ADMIN);
    }

    public function IsWriteRole(){
        if ($this->IsAdminRole()){
            return true;
        }
        return $this->IsRoleEnable(UnitellerAction::WRITE);
    }

    public function IsViewRole(){
        if ($this->IsWriteRole()){
            return true;
        }
        return $this->IsRoleEnable(UnitellerAction::VIEW);
    }

    public function GetApp(){
        Abricos::GetApp('payments');
        return parent::GetApp();
    }

    public function AJAX($d) {
        return $this->GetApp()->AJAX($d);
    }

    public function Bos_MenuData(){
        if (!$this->IsAdminRole()){
            return null;
        }
        $i18n = $this->module->I18n();
        return array(
            array(
                "name" => "uniteller",
                "title" => $i18n->Translate('title'),
                "icon" => "/modules/uniteller/images/cp_icon.gif",
                "url" => "uniteller/wspace/ws",
                "parent" => "controlPanel"
            )
        );
    }
}
