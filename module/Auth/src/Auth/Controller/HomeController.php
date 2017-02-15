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

    public function getMyFriendAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        if ($this->_request->isGet())
        {
            $friends = $this->_commonDAO->executeQuery('MY_FRIEND_GET_ALL', array());
            $this->_view->setVariable('friends', $friends);
        }

        $this->_view->setTemplate('application/home/template/my-friend.phtml');
        $this->writeLog();
        return $this->_view;
    }

    public function getSkillsAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        if ($this->_request->isGet())
        {
            $skills = $this->_commonDAO->executeQuery('SKILLS_GET_ALL', array());
            $this->_view->setVariable('skills', $skills);
        }

        $this->_view->setTemplate('application/home/template/skills.phtml');
        $this->writeLog();
        return $this->_view;
    }

    public function getPostsLatestAction()
    {
        $this->init();
        $this->_view->setTerminal(true);

        if($this->_request->isGet())
        {
            $dateTimeQuery = $this->_utility->getDateTimeQuery('', '');
            $search = array(
                (int) $this->params()->fromQuery('id', 0),
                (int) $this->params()->fromQuery('category_id', 0),
                $this->params()->fromQuery('title', ''),
                $this->params()->fromQuery('summary', ''),
                $this->params()->fromQuery('description', ''),
                $this->params()->fromQuery('thumbnail', ''),
                $this->params()->fromQuery('start_date', $dateTimeQuery['start_date']),
                $this->params()->fromQuery('end_date', $dateTimeQuery['end_date']),
                (int) $this->params()->fromQuery('create_by', 1),
                $this->params()->fromQuery('tags', ''),
                (int) $this->params()->fromQuery('limit', Define::LIMIT_HOME),
                (int) $this->params()->fromQuery('offset', 0)
            );

            $posts = $this->_commonDAO->executeQuery('POST_SEARCH', $search);
            $this->_view->setVariable('posts', $posts);
        }
        $this->_view->setTemplate('application/home/template/posts-latest.phtml');
        $this->writeLog();
        return $this->_view;
    }
}