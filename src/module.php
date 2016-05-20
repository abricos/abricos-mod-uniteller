<?php
/**
 * @package Abricos
 * @subpackage Uniteller
 * @copyright 2016 Alexander Kuzmin
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Alexander Kuzmin <roosit@abricos.org>
 */

/**
 * Class UnitellerModule
 */
class UnitellerModule extends Ab_Module {

    public function __construct(){
        $this->version = "0.1.0";
        $this->name = "uniteller";

        $this->permission = new UnitellerPermission($this);
    }

    public function Bos_IsMenu(){
        return true;
    }
}

class UnitellerAction {
    const VIEW = 10;
    const WRITE = 30;
    const ADMIN = 50;
}

class UnitellerPermission extends Ab_UserPermission {

    public function __construct(UnitellerModule $module){
        $defRoles = array(
            new Ab_UserRole(UnitellerAction::VIEW, Ab_UserGroup::GUEST),
            new Ab_UserRole(UnitellerAction::VIEW, Ab_UserGroup::REGISTERED),
            new Ab_UserRole(UnitellerAction::VIEW, Ab_UserGroup::ADMIN),

            new Ab_UserRole(UnitellerAction::WRITE, Ab_UserGroup::ADMIN),

            new Ab_UserRole(UnitellerAction::ADMIN, Ab_UserGroup::ADMIN)
        );
        parent::__construct($module, $defRoles);
    }

    public function GetRoles(){
        return array(
            UnitellerAction::VIEW => $this->CheckAction(UnitellerAction::VIEW),
            UnitellerAction::WRITE => $this->CheckAction(UnitellerAction::WRITE),
            UnitellerAction::ADMIN => $this->CheckAction(UnitellerAction::ADMIN)
        );
    }
}

Abricos::ModuleRegister(new UnitellerModule());
