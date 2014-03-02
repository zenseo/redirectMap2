<?php
/**
 * RedirectMap
 * Плагин редиректов для MODX Evolution
 *
 * @category 	plugin
 * @version 	2.0.1
 * @author      Agel_Nash <modx@agel-nash.ru>
 * @internal	@events         OnPageNotFound
 * @internal	@properties     &saveParams=Сохранять GET параметры при редиректе;list;true,false;true &findWithParams=Искать правила с учетом GET параметров;list;true,false;false
 * @internal	@category       API
 * @internal    @code           include MODX_BASE_PATH."assets/modules/RedirectMap/plugin.RedirectMap.php";
 */
$uri = $_SERVER['REQUEST_URI'];
$findWithParams = (isset($findWithParams) && $findWithParams=='true') ? true : false;
$saveParams = (isset($saveParams) && $saveParams=='true') ? true : false;
$params = '';

if( ! $findWithParams){
    $uri = parse_url($uri, PHP_URL_PATH); //PHP_URL_QUERY);
}
$sql = "SELECT page FROM ".$modx->getFullTableName('redirect_map')." WHERE `active`=1 AND `uri`='".$modx->db->escape($uri)."'";
$q = $modx->db->query($sql);
$page = $modx->db->getValue($q);
if(!empty($page)){
    if( $saveParams){
        $params = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
    }
    $url = $modx->makeUrl($page, '', $params, 'full');
    $modx->sendRedirect($url, 0, 'REDIRECT_HEADER', 'HTTP/1.1 301 Moved Permanently');
}