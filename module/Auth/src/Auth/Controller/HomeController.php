<?php
namespace Auth\Controller;

use Auth\Constant\Define;
use Auth\Constant\Key;

class HomeController extends BaseController
{
    protected $_commonDAO;

    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {
        $this->init();
        $this->_view->setVariable('define', $this->_variableLayout['define']);
        $this->writeLog();
        return $this->_view;
    }

    public function getCaoAnhPhuongAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        if ($this->_request->isGet())
        {

        }

        $this->_view->setTemplate('auth/home/template/cao-anh-phuong.phtml');
        $this->writeLog();
        return $this->_view;
    }
}