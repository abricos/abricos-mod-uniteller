<?php
/**
 * @package Abricos
 * @subpackage Uniteller
 * @copyright 2016 Alexander Kuzmin
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Alexander Kuzmin <roosit@abricos.org>
 */


/**
 * Class UnitellerConfig
 *
 * @property string $urlPay
 * @property string $urlResult
 * @property string $shopid Идентификатор точки продажи Shop_ID
 * @property int $lifetime
 */
class UnitellerConfig extends AbricosModel {
    protected $_structModule = 'uniteller';
    protected $_structName = 'Config';
}


?>