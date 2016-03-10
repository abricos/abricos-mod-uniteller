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
 *
 * @property UnitellerManager $manager
 */
class UnitellerApp extends AbricosApplication {

    protected function GetClasses(){
        return array(
            'Request' => 'UnitellerRequest',
            'RequestList' => 'UnitellerRequestList',
            'Config' => 'UnitellerConfig'
        );
    }

    protected function GetStructures(){
        return 'Request,Config';
    }

    public function ResponseToJSON($d){
        switch ($d->do){
            case "config":
                return $this->ConfigToJSON();
            case "configSave":
                return $this->ConfigSaveToJSON($d->config);

        }
        return null;
    }

    public function RequestSendToJSON($d){
        $ret = $this->RequestSend($d);
        return $this->ResultToJSON('requestSend', $ret);
    }

    public function RequestSend($d){
        if (!$this->manager->IsViewRole()){
            return 403;
        }

        $utmf = Abricos::TextParser(true);

        $d->requestType = intval($d->requestType);
        $d->formType = intval($d->formType);
        $d->email = $utmf->Parser($d->email);
        if (is_object($d->arguments)){
            $d->arguments = json_encode($d->arguments);
        }else{
            return 500;
        }

        UnitellerQuery::RequestAppend($this->db, $d);

        $ret = new stdClass();
        $ret->success = true;
        return $ret;
    }

    public function RequestListToJSON(){
        $ret = $this->RequestList();
        return $this->ResultToJSON('requestList', $ret);
    }

    public function RequestList(){
        if (!$this->manager->IsAdminRole()){
            return 403;
        }

        /** @var RequestList $list */
        $list = $this->models->InstanceClass('RequestList');

        $rows = UnitellerQuery::RequestList($this->db);
        while (($d = $this->db->fetch_array($rows))){
            $list->Add($this->models->InstanceClass('Request', $d));
        }
        return $list;
    }

    public function ConfigToJSON(){
        $res = $this->Config();
        return $this->ResultToJSON('config', $res);
    }

    /**
     * @return UnitellerConfig
     */
    public function Config(){
        if (isset($this->_cache['Config'])){
            return $this->_cache['Config'];
        }

        if (!$this->manager->IsViewRole()){
            return 403;
        }

        $phrases = Abricos::GetModule('uniteller')->GetPhrases();

        $d = array();
        for ($i = 0; $i < $phrases->Count(); $i++){
            $ph = $phrases->GetByIndex($i);
            $d[$ph->id] = $ph->value;
        }

        if (!isset($d['page_count'])){
            $d['page_count'] = 20;
        }

        return $this->_cache['Config'] = $this->models->InstanceClass('Config', $d);
    }

    public function ConfigSaveToJSON($sd){
        $this->ConfigSave($sd);
        return $this->ConfigToJSON();
    }

    public function ConfigSave($sd){
        if (!$this->manager->IsAdminRole()){
            return 403;
        }

        /** @var UnitellerModule $module */

        $phs = Abricos::GetModule('uniteller')->GetPhrases();
        /*
        $phs->Set("page_count", intval($sd->page_count));
        /**/

        Abricos::$phrases->Save();
    }

}

?>