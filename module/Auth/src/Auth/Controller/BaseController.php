<?php
namespace Auth\Controller;

use Auth\Constant\Define;
use Auth\Constant\Key;
use Auth\Utility\Utility;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\View\Model\ViewModel;

class BaseController extends AbstractActionController
{
    protected $_folder;
    protected $_fileNameLog;
    protected $_log;
    protected $_view;
    protected $_variableLayout;
    protected $_utility;
    protected $_define;
    protected $_commonDAO;
    protected $_session;
    protected $_cookie;
    protected $_request;
    protected $_action;
    protected $_controller;

    public function __construct()
    {
        $this->_folder = $_SERVER['DOCUMENT_ROOT'] . '/logs/';
        $this->_fileNameLog = $this->_folder . 'LOG-'.date('Y-m-d').'.txt';

        $this->_log = '['. $_SERVER['REMOTE_ADDR'] . '] ['. date('d/m/Y H:i:s'). '] ['.$_SERVER['HTTP_USER_AGENT'].'] ';

        $this->_view = new ViewModel();
        $this->_utility = new Utility();
        $this->_session = new Container('user');
        $this->_cookie = $_COOKIE;
    }
    public function setValueDefine($controller, $action)
    {
        $this->_commonDAO = $this->getServiceLocator()->get('CommonDAO');
        $defines = $this->_commonDAO->executeQuery('DEFINE_GET_ALL', array());
        $this->_define = $this->convertArrayToKeyValue($defines);
        $this->_define[Key::TITLE] .= ' | '.$controller;

        $this->_variableLayout = array(
            'define' => $this->_define,
            'menus' => $this->_utility->getMenus($this->_session, $controller, $action),
            'pullRights' => $this->_utility->getPullRight($this->_session)
        );
        $this->_view->setVariable('_utility', $this->_utility);
    }
    public function convertArrayToKeyValue($array)
    {
        $result = array();
        foreach($array as $a)
        {
            $result[$a['key']] = $a['value'];
        }
        return $result;
    }
    public function getBaseUrl()
    {
        $uri = $this->getRequest()->getUri();
        return sprintf('%s://%s', $uri->getScheme(), $uri->getHost());
    }
    public function init()
    {
        $this->_request = $this->getRequest();

        $this->_action = $this->params('action');
        list($module, $controller, $this->_controller) = explode("\\", $this->params('controller'));

        $this->setValueDefine($this->_controller, $this->_action);
        $this->layout()->setVariables($this->_variableLayout);
        $this->_view->setVariable('menuAccount', $this->_utility->getMenuAccount($this->_session, $this->_action));

        $method = $this->_request->getMethod();
        $this->_log .='['.$this->_controller.'Controller] ['.$this->_action.'Action] [Method : '.$method.'] ';

        switch($method)
        {
            case 'GET':
                if($_GET)
                    $this->_log .= '[Params : '.json_encode($_GET).'] ';
                break;
            case 'POST':
                if($_POST)
                    $this->_log .= '[Params : '.json_encode($_POST).'] ';
                break;
        }
        $this->setToken();
    }
    public function writeLog()
    {
        exec('echo "'.$this->_log.'" >> '.$this->_fileNameLog, $output);
    }
    public function getLogin()
    {
        if($this->_session->offsetExists(Key::ID))
            return $this->_session->offsetGet(Key::ID);

        return false;
    }
    public function getToken()
    {
        if(isset($this->_cookie[Key::TOKEN]))
            return $this->_cookie[Key::TOKEN];

        return '';
    }
    public function setToken()
    {
        if(!isset($this->_cookie[Key::TOKEN]) || !$this->getLogin())
        {
            $token = sha1($_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
            setcookie(Key::TOKEN, $token, time() + Define::EXPIRES);
        }
    }
}