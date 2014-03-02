<?php namespace RedirectMap;
class Action{
    protected static $modx = null;
    public static $TPL = null;
    protected static $_tplObj = null;
    const TABLE = "redirect_map";

    public static function init(\DocumentParser $modx, Template $tpl){
        self::$modx = $modx;
        self::$_tplObj = $tpl;
        self::$TPL = Template::showLog();
    }

    protected static function _checkObj($id){
        $q = self::$modx->db->select('id', self::$modx->getFullTableName(self::TABLE), "id = ".$id);
        return (self::$modx->db->getRecordCount($q)==1);
    }

    protected static function _getValue($field, $id){
        $q = self::$modx->db->select($field, self::$modx->getFullTableName(self::TABLE), "id = ".$id);
        return self::$modx->db->getValue($q);
    }

    protected static function _workValue($callback, $data = null){
        self::$TPL = 'ajax/getValue';
        if(is_null($data)){
            $data = Helper::jeditable('data');
        }
        $out = array();
        if(!empty($data)){
            $modSEO = new modRedirectMap(self::$modx);
            $modSEO->edit($data['id']);
            if($modSEO->getID() && ((is_object($callback) && ($callback instanceof \Closure)) || is_callable($callback))){
                $out = call_user_func($callback, $data, $modSEO);
            }
        }
        return $out;
    }

    public static function saveValue(){
        return self::_workValue(function($data, $modSEO){
            $out = array();
            if(isset($_POST['value']) && is_scalar($_POST['value'])){
                if($modSEO->set($data['key'], $_POST['value'])->save()){
                    $out['value'] = $modSEO->get($data['key']);
                }
            }
            return $out;
        });
    }

    public static function getValue(){
        return self::_workValue(function($data, $modSEO){
            return array(
                'value' => $modSEO->get($data['key'])
            );
        });
    }
    public static function addUri(){
        $out = array();
        if($_SERVER['REQUEST_METHOD']=="POST" && isset($_POST['page']) && !empty($_POST['uri'])){
            $modRedirect = new modRedirectMap(self::$modx);
            $flag = $modRedirect->create($_POST)->save();
            if($flag){
                $out['log'] = 'Добавлено новое правило';
            }else{
                $out['uriField'] = $_POST['uri'];
                $out['pageField'] = $_POST['page'];
                $log = $modRedirect->getLog();
                if(isset($log['UniqueUri'])){
                    $out['log'] = 'Правило для заданного URI уже есть в базе';
                }else{
                    $out['log'] = 'Во время добавления нового правила произошла ошибка';
                }
            }
        }else{
            $out['log'] = 'Не удалось получить данные для нового правила';
        }
        return $out;
    }
    public static function checkUniq(){
        return self::_workValue(function($data, $modSEO){
            $out = array();
            if(isset($_POST['value']) && is_scalar($_POST['value'])){
                if($modSEO->isUniq($_POST['value'])){
                    $out['value'] = 'true';
                }else{
                    $out['value'] = 'Вы пытаетесь сохранить ключ который уже есть в базе. Удалите эту запись если она лишная.';
                }
            }else{
                $out['value'] = 'Не установлено значение';
            }
            return $out;
        });
    }

    public static function isactive(){
        $data = array();
        $dataID = (int)Template::getParam('docId', $_GET);
        if($dataID>0 && self::_checkObj($dataID)){
            $oldValue = self::_getValue('active', $dataID);
            if(self::_getValue('page', $dataID)>0){
                $q = self::$modx->db->update(array(
                        'active' => !$oldValue
                    ), self::$modx->getFullTableName(self::TABLE), "id = ".$dataID);
            }else{
                $q = false;
            }
            if($q){
                $data['log'] = $oldValue ? 'Правило с ID '.$dataID.' отключено' : 'Правило с ID '.$dataID.' активировано';
            }else{
                $data['log'] = $oldValue ? 'Не удалось отключить правило с ID '.$dataID : 'Не удалось активировать правило с ID '.$dataID;
            }
        }else{
            $data['log'] = 'Не удалось определить обновляемое правило';
        }
        return $data;
    }

    public static function lists(){
        self::$TPL = 'ajax/lists';
    }
    public static function fullDelete(){
        $data = array();
        $dataID = (int)Template::getParam('docId', $_GET);
        if($dataID>0 && self::_checkObj($dataID)){
            $modRedirect = new modRedirectMap(self::$modx);
            $modRedirect->delete($dataID);
            if(!self::_checkObj($dataID)){
                $data['log'] = 'Удалена запись с ID: <strong>'.$dataID.'</strong>';
            }else{
                $data['log'] = 'Не удалось удалить запись с ID: <strong>'.$dataID.'</strong>';
            }
        }else{
            $data['log'] = 'Не удалось определить обновляему запись';
        }
        return $data;
    }

    protected static function _prepareResponse($json, $function, array $params = array()){
        require_once(MODX_BASE_PATH."assets/snippets/DocLister/lib/jsonHelper.class.php");
        $data = array();
        if($json){
            $json = \jsonHelper::jsonDecode($json,array('assoc'=>true));
            if(empty($json)){
                $data['log'][] = 'Ошибка в полученном ответе от APIShops';
            }else{
                if(isset($json['items'])){
                    if((is_object($function) && ($function instanceof \Closure)) || is_callable($function)){
                        $data = call_user_func($function, $json, $params);
                    }else{
                        $data['flag'] = true;
                        $data['log'][] = 'Запрос к APIShops успешно обработан';
                    }
                }
                if(isset($json['error'])){
                    $data['log'][] = "<strong>APIShops вернул ошибку</strong>: ".$json['error'];
                    $data['flag'] = false;
                }
            }
        }else{
            $data['log'][] = 'Ошибка соединения с API-сервером APIShops';
        }
        return $data;
    }
}