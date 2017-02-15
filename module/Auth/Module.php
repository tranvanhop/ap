<?php
namespace Auth;

use Auth\Constant\Key;
use Auth\Model\CommonDAO;
use Zend\Authentication\Adapter\DbTable as DbAuthAdapter;
use Zend\Mail\Transport\Smtp;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;

date_default_timezone_set("Asia/Ho_Chi_Minh");

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(MvcEvent::EVENT_DISPATCH, array(
            $this,
            'boforeDispatch'
        ), 100);

        $e->getApplication()->getEventManager()->getSharedManager()->attach('Zend\Mvc\Controller\AbstractController', 'dispatch', function ($e) {
            $controller = $e->getTarget();
            $controllerClass = get_class($controller);
            $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
            $config = $e->getApplication()->getServiceManager()->get('config');
            if (isset($config['module_layouts'][$moduleNamespace])) {
                $controller->layout($config['module_layouts'][$moduleNamespace]);
            }
        }, 100);

        $this->initTranslator($e);
    }

    protected function initTranslator(MvcEvent $event)
    {
        $serviceManager = $event->getApplication()->getServiceManager();

        // Zend\Session\Container
        $session = new Container('language');

        $translator = $serviceManager->get('translator');
        if($session->offsetExists('language'))
        {
            $translator
                ->setLocale($session->offsetGet('language'))
                ->setFallbackLocale('en_US');
        }
    }

    function boforeDispatch(MvcEvent $event)
    {
        $response = $event->getResponse();

        $whiteList = array(
            'Auth\Controller\Account-log-in',
            'Auth\Controller\Account-log-out'
        );

        $controller = $event->getRouteMatch()->getParam('controller');
        $action = $event->getRouteMatch()->getParam('action');
        $requestedResource = $controller . "-" . $action;

        $session = new Container('user');

        if ($session->offsetExists(Key::ID))
        {
            if ($requestedResource == 'Auth\Controller\Account-log-in' || in_array($requestedResource, $whiteList))
            {
                $url = '/home/index';
                $response->setHeaders($response->getHeaders()
                    ->addHeaderLine('Location', $url));
                $response->setStatusCode(302);
            }
        }
        else
        {
            if ($requestedResource != 'Auth\Controller\Account-log-in' && ! in_array($requestedResource, $whiteList)) {
                $url = '/';
                $response->setHeaders($response->getHeaders()
                    ->addHeaderLine('Location', $url));
                $response->setStatusCode(302);
            }
            $response->sendHeaders();
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'CommonDAO' => function ($serviceManager) {
                    return new CommonDAO($serviceManager->get('Zend\Db\Adapter\Adapter'));
                },
                'mail' => function ($serviceManager) {
                    $config = $serviceManager->get('Config');
                    $transport = new Smtp();
                    $transport->setOptions(new SmtpOptions($config['mail']['transport']['options']));
                    return $transport;
                },
            )
        );
    }
}