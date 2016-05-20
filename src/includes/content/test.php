<?php
/**
 * @package Abricos
 * @subpackage Payments
 * @copyright 2016 Alexander Kuzmin
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @author Alexander Kuzmin <roosit@abricos.org>
 */

$brick = Brick::$builder->brick;
$p = &$brick->param->param;
$v = &$brick->param->var;

/** @var PaymentsApp $app */
$app = Abricos::GetApp('payments');

if (!$app->manager->IsAdminRole()){
    return;
}

// http://example.com/payments/test/uniteller/fddfc109a420516bb93bf22c6104343c/paid/

$dir = Abricos::$adress->dir;

/** @var UnitellerApp $unitellerApp */
$unitellerApp = Abricos::GetApp('uniteller');
$config = $unitellerApp->Config();

$orderid = isset($dir[3]) ? $dir[3] : '';
$status = isset($dir[4]) ? $dir[4] : '';

$signature = strtoupper(md5($orderid.$status.$config->password));

$brick->content = Brick::ReplaceVarByData($brick->content, array(
    "brickid" => $brick->id,
    "orderid" => $orderid,
    "status" => $status,
    "signature" => $signature
));
