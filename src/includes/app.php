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
class UnitellerApp extends PaymentsEngine {

    protected function GetClasses(){
        return array(
            'Config' => 'UnitellerConfig'
        );
    }

    protected function GetStructures(){
        return 'Config';
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

    public function FormFill(PaymentsForm $form){
        $config = $this->Config();

        $form->url = $config->urlPay;

        $p = new stdClass();
        $p->Shop_IDP = strval($config->shopid);
        $p->Order_IDP = $form->order->id;

        $p->Subtotal_P = sprintf('%.2f', $form->order->total);

        $p->Lifetime = intval($config->lifetime);
        if ($p->Lifetime){
            $lifetime = $p->Lifetime;
        } else {
            unset($p->Lifetime);
            $lifetime = '';
        }

        $p->URL_RETURN_OK = $form->urlReturnOk;
        $p->URL_RETURN_NO = $form->urlReturnNo;

        $p->Signature = strtoupper(md5($p->Shop_IDP.$p->Order_IDP.$p->Subtotal_P.$lifetime.$config->password));

        $form->params = $p;
    }

    public function API($action, $p1, $p2, $p3){
        switch ($action){
            case strtolower("orderStatusUpdate"):
                return $this->OrderStatusUpdateByPOST();
        }

        return AbricosResponse::ERR_BAD_REQUEST;
    }

    public function OrderStatusUpdateByPOST(){
        $pOrder_ID = Abricos::CleanGPC('p', 'Order_ID', TYPE_STR);
        $pStatus = Abricos::CleanGPC('p', 'Status', TYPE_STR);
        $pSignature = Abricos::CleanGPC('p', 'Signature', TYPE_STR);

        $logDebugInfo = array(
            'Order_ID' => $pOrder_ID,
            'Status' => $pStatus,
            'Signature' => $pSignature,
        );

        $this->LogDebug('Request payment service to change the order status', $logDebugInfo);

        /** @var PaymentsApp $paymentsApp */
        $paymentsApp = Abricos::GetApp('payments');
        $order = $paymentsApp->Order($pOrder_ID);

        if (AbricosResponse::IsError($order)){
            $this->LogError('Order not found to change the status', array(
                'orderid' => $pOrder_ID
            ));

            return AbricosResponse::ERR_NOT_FOUND;
        }

        $config = $this->Config();

        $password = $config->password;

        $signature = strtoupper(md5($pOrder_ID.$pStatus.$password));

        if ($pSignature !== $signature){
            $this->LogError('Invalid signature order to change the status', $logDebugInfo);
            return AbricosResponse::ERR_BAD_REQUEST;
        }

        $order->status = $pStatus;

        $paymentsApp->OrderStatusUpdateMethod($order);

        $this->LogTrace('End order status update', $logDebugInfo);

        return $order;
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
            return AbricosResponse::ERR_FORBIDDEN;
        }

        $phrases = Abricos::GetModule('uniteller')->GetPhrases();

        $d = array();
        for ($i = 0; $i < $phrases->Count(); $i++){
            $ph = $phrases->GetByIndex($i);
            $d[$ph->id] = $ph->value;
        }

        if (!isset($d['urlPay'])){
            $d['urlPay'] = "https://test.wpay.uniteller.ru/pay/";
        }

        if (!isset($d['urlResult'])){
            $d['urlResult'] = "https://test.wpay.uniteller.ru/results/";
        }

        if (!isset($d['lifetime'])){
            $d['lifetime'] = "3600";
        }

        /** @var UnitellerConfig $config */
        $config = $this->InstanceClass('Config', $d);

        if (empty($config->password)){
            $this->LogWarn('Password not set in Config');
        }

        if (empty($config->shopid)){
            $this->LogWarn('Shop ID not set in Config');
        }

        return $this->_cache['Config'] = $config;
    }

    public function ConfigSaveToJSON($d){
        $this->ConfigSave($d);
        return $this->ConfigToJSON();
    }

    public function ConfigSave($d){
        if (!$this->manager->IsAdminRole()){
            return AbricosResponse::ERR_FORBIDDEN;
        }

        $utmf = Abricos::TextParser(true);

        $phs = Abricos::GetModule('uniteller')->GetPhrases();
        $phs->Set("password", $utmf->Parser($d->password));
        $phs->Set("urlPay", $utmf->Parser($d->urlPay));
        $phs->Set("urlResult", $utmf->Parser($d->urlResult));
        $phs->Set("shopid", $utmf->Parser($d->shopid));
        $phs->Set("lifetime", intval($d->lifetime));

        Abricos::$phrases->Save();
    }

}

?>