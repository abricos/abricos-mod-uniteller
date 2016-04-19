<?php
/**
 * @package Abricos
 * @subpackage Uniteller
 * @copyright 2016 Alexander Kuzmin
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Alexander Kuzmin <roosit@abricos.org>
 */

class UnitellerRequest extends AbricosModel {
    protected $_structModule = 'uniteller';
    protected $_structName = 'Request';
}

class UnitellerRequestList extends AbricosModelList {
}

/**
 * Class UnitellerConfig
 *
 * @property string $shopid Идентификатор точки продажи Shop_ID
 */
class UnitellerConfig extends AbricosModel {
    protected $_structModule = 'uniteller';
    protected $_structName = 'Config';
}


?>