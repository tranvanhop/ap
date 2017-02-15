<?php
namespace Auth\Controller;

use Auth\Constant\Define;
use Auth\Constant\Key;
use Auth\Constant\ResponseCode;
use Auth\Form\Filter\ForgotPasswordFilter;
use Auth\Form\Filter\LoginFilter;
use Auth\Form\Filter\NewPasswordFilter;
use Auth\Form\Filter\RegistrationFilter;
use Auth\Form\Filter\UpdateProfileFilter;
use Auth\Form\ForgotPasswordForm;
use Auth\Form\LoginForm;
use Auth\Form\NewPasswordForm;
use Auth\Form\RegistrationForm;
use Auth\Form\UpdateProfileForm;
use Auth\Utility\Security;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class AccountController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {
        $this->init();
        $this->writeLog();
        return $this->_view;
    }

    public function logInAction()
    {
        $this->init();

        $ua = parse_user_agent();
        setcookie(Key::PLATFORM, $ua[Key::PLATFORM], time() + Define::EXPIRES);
        setcookie(Key::BROWSER, $ua[Key::BROWSER], time() + Define::EXPIRES);
        setcookie(Key::VERSION, $ua[Key::VERSION], time() + Define::EXPIRES);
        setcookie(Key::USER_AGENT, $_SERVER['HTTP_USER_AGENT'], time() + Define::EXPIRES);

        if(!isset($this->_cookie[Key::ERROR_COUNT]))
            setcookie(Key::ERROR_COUNT, 0, time() + Define::EXPIRES);

        $security = new Security();

        $loginForm = new LoginForm('loginForm');

        if ($this->_request->isPost() && $this->_cookie[Key::ERROR_COUNT] < intval($this->_define[Key::ERROR_COUNT_MAX]))
        {
            $data = $this->_request->getPost();
            $loginForm->setData($data);
            $u = str_replace(' ', '', strtoupper($data['user_name']));
            $password = str_replace(' ', '', str_replace('-', '', str_replace('/', '', str_replace('0', '',$data['password']))));
            $p = $security->create($password);

            if($loginForm->isValid())
            {
                if(isset($ua[Key::PLATFORM])
                    && (strcmp($ua[Key::PLATFORM], Define::iPhone) === 0)
                    ||
                    (strcmp($ua[Key::PLATFORM], Define::iPad) === 0)
                )
                {
                    unset($this->_cookie[Key::ERROR_COUNT]);
                }

                $arrParams = array($u, $p);
                $user = $this->_commonDAO->executeQueryFirst('USER_LOGIN', $arrParams);
                if($user)
                {
                    $this->flashMessenger()->addMessage(array(
                        'success' => 'Đăng nhập thành công'
                    ));
                    $this->_session->offsetSet(Key::ID, $user['id']);
                    $this->writeLog();
                    return $this->redirect()->toUrl(Define::URL_REDIRECT_LOGIN_SUCCESS);
                }

                $this->flashMessenger()->addMessage(array(
                    'danger' => 'Sai tên hoặc ngày sinh, vui lòng đăng nhập lại'
                ));
                setcookie(Key::ERROR_COUNT, ++$this->_cookie[Key::ERROR_COUNT], time() + Define::EXPIRES);

                if($this->_cookie[Key::ERROR_COUNT] == $this->_define[Key::ERROR_COUNT_WARNING])
                {
                    $this->flashMessenger()->addMessage(array(
                        'danger' => 'Còn '
                            . (intval($this->_define[Key::ERROR_COUNT_MAX]) - intval($this->_define[Key::ERROR_COUNT_WARNING]))
                            .' lần đăng nhập nữa'
                    ));
                }

                if($this->_cookie[Key::ERROR_COUNT] > $this->_define[Key::ERROR_COUNT_WARNING]
                    && $this->_cookie[Key::ERROR_COUNT] < $this->_define[Key::ERROR_COUNT_MAX])
                {
                    $this->flashMessenger()->addMessage(array(
                        'danger' => 'Có cố gắng, nhưng rất tiếc không phải'
                    ));
                }

                if($this->_cookie[Key::ERROR_COUNT] == $this->_define[Key::ERROR_COUNT_MAX])
                {
                    $this->flashMessenger()->addMessage(array(
                        'danger' => 'Vui lòng không đăng nhập nữa, không phải rồi !!!'
                    ));
                }

                $this->writeLog();
                return $this->redirect()->toUrl(Define::URL_REDIRECT_LOGIN_FAIL);
            }
        }
        $this->_view->setVariable('loginForm', $loginForm);
        $this->writeLog();
        return $this->_view;
    }

    public function registrationAction()
    {
        $this->init();

        $registrationForm = new RegistrationForm('registrationForm');
        $registrationForm->setInputFilter(new RegistrationFilter());

        if ($this->_request->isPost())
        {
            $data = $this->_request->getPost();
            $registrationForm->setData($data);

            // check phone is exists !
            $isExistsPhone = false;
            if(strlen($data['phone']) > 0)
                $isExistsPhone = $this->_commonDAO->executeQuery('USER_GET_BY_PHONE', array($data['phone']));

            if($isExistsPhone)
            {
                $this->flashMessenger()->addMessage(array(
                    'danger' => Define::MESSAGE_PHONE_EXISTS
                ));
                $this->writeLog();
                return $this->redirect()->toUrl(Define::URL_REDIRECT_REGISTRATION_FAIL);
            }

            // check email is exists !
            $isExistsEmail = $this->_commonDAO->executeQueryFirst('USER_GET_BY_EMAIL', array($data['email']));
            if($isExistsEmail)
            {
                $this->flashMessenger()->addMessage(array(
                    'danger' => Define::MESSAGE_EMAIL_EXISTS
                ));
                $this->writeLog();
                return $this->redirect()->toUrl(Define::URL_REDIRECT_REGISTRATION_FAIL);
            }

            // valid form : first_name, last_name, email, password is not null. password == confirm_password ?
            if($registrationForm->isValid())
            {
                $avatar = Define::URL_AVATAR_DEFAULT;
                if(!empty($_FILES['avatar']))
                {
                    $filename = time() .'_'. $_FILES['avatar']['name'];
                    $avatar = Define::PATH_UPLOAD_IMAGES.$filename;
                    move_uploaded_file($_FILES['avatar']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] .$avatar);
                }

                $security = new Security();
                $password = $security->create($data['password']);

                $arrParams = array(
                    $data['first_name'],
                    $data['last_name'],
                    $data['email'],
                    $password,
                    $data['phone'],
                    $avatar,
                    date('Y-m-d H:i:s'),
                    $_SERVER['REMOTE_ADDR'],
                    $_SERVER['HTTP_USER_AGENT']
                );
                $user = $this->_commonDAO->executeQuery_returnID('USER_INSERT', $arrParams);
                if($user)
                {
                    $this->flashMessenger()->addMessage(array(
                        'success' => Define::MESSAGE_REGISTRATION_SUCCESS
                    ));
                    $this->writeLog();
                    $this->_session->offsetSet(Key::ID, $user['id']);

                    return $this->redirect()->toUrl(Define::URL_REDIRECT_REGISTRATION_SUCCESS);
                }
            }
        }
        $this->_view->setVariable('registrationForm', $registrationForm);
        $this->writeLog();
        return $this->_view;
    }

    public function forgotPasswordAction()
    {
        $this->init();

        $forgotPasswordForm = new ForgotPasswordForm('forgotPasswordForm');
        $forgotPasswordForm->setInputFilter(new ForgotPasswordFilter());

        if ($this->_request->isPost())
        {
            $data = $this->_request->getPost();
            $forgotPasswordForm->setData($data);

            if($forgotPasswordForm->isValid())
            {
                $user = $this->_commonDAO->executeQueryFirst('USER_GET_BY_EMAIL', array($data['email']));
                if($user)
                {
                    $data = $forgotPasswordForm->getData();
                    $token = $this->_utility->random();
                    $expireDate = date('Y-m-d H:i:s', strtotime('+3 days'));
                    $arrParam = array(
                        $user['id'],
                        $token,
                        $expireDate
                    );
                    $this->_commonDAO->executeQuery_returnID('USER_FORGOT_PASSWORD_INSERT', $arrParam);
                    $transport = $this->getServiceLocator()->get('mail');
                    $message = new Message();

                    $render = $this->getServiceLocator()->get('ViewRenderer');
                    $params = array(
                        'token' => $token,
                        'base_url' => $this->getBaseUrl(),
                        'expire_date' => date('H:i:s d/m/Y', strtotime('+3 days')),
                        'user' => $user
                    );
                    $content = $render->render('layout/email/forgot-password', $params);

                    $html = new MimePart($content);
                    $html->type = "text/html";
                    $body = new MimeMessage();
                    $body->setParts(array($html));

                    $message->addTo($data['email'])
                        ->addFrom(Define::EMAIL_USERNAME)
                        ->setSubject(Define::EMAIL_SUBJECT .' : Forgot Password')
                        ->setBody($body);
                    $transport->send($message);

                    $this->flashMessenger()->addMessage(array(
                        'success' => Define::MESSAGE_FORGOT_PASSWORD_SUCCESS .$data['email'].'. Bạn vui lòng truy cập hộp thư và bấm vào link kích hoạt để tiếp tục.'
                    ));
                    return $this->redirect()->tourl(Define::URL_REDIRECT_FORGOT_PASSWORD_SUCCESS);
                }
                else
                {
                    $this->flashMessenger()->addMessage(array(
                        'danger' => Define::MESSAGE_FORGOT_PASSWORD_FAIL
                    ));
                    return $this->redirect()->tourl(Define::URL_REDIRECT_FORGOT_PASSWORD_FAIL);
                }
            }
        }
        $this->_view->setVariable('forgotPasswordForm', $forgotPasswordForm);
        $this->writeLog();
        return $this->_view;
    }

    public function forgotPasswordConfirmAction()
    {
        $this->init();

        if($this->params()->fromQuery('token'))
        {
            $token = $this->params()->fromQuery('token');
            $forgot = $this->_commonDAO->executeQueryFirst('USER_FORGOT_PASSWORD_GET_BY_TOKEN', array($token));

            if($forgot)
            {
                $newPasswordForm = new NewPasswordForm('newPasswordForm');
                $newPasswordForm->setInputFilter(new NewPasswordFilter());
                $newPasswordForm->setData(array('token' => $token));

                $request = $this->getRequest();
                if($request->isPost())
                {
                    $dataRequest = $request->getPost();
                    $newPasswordForm->setData($dataRequest);

                    if(strcmp($dataRequest['token'], $token) != 0)
                    {
                        $this->flashMessenger()->addMessage(array(
                            'danger' => Define::MESSAGE_INVALID_TOKEN
                        ));
                        return $this->redirect()->toUrl(Define::URL_REDIRECT_NEW_PASSWORD_FAIL);
                    }

                    if($newPasswordForm->isValid())
                    {
                        $data = $newPasswordForm->getData();
                        $userPassword = new Security();
                        $password = $userPassword->create($data['password']);
                        $result = $this->_commonDAO->executeNonQuery('USER_UPDATE_CHANGE_PASSWORD', array($forgot['user_id'], $password));
                        if($result)
                        {
                            $this->_commonDAO->executeNonQuery('USER_FORGOT_PASSWORD_UPDATE', array($forgot['id'], true));
                            $this->flashMessenger()->addMessage(array(
                                'success' => Define::MESSAGE_NEW_PASSWORD_SUCCESS
                            ));
                            return $this->redirect()->toUrl(Define::URL_REDIRECT_NEW_PASSWORD_SUCCESS);
                        }
                        else
                        {
                            $this->flashMessenger()->addMessage(array(
                                'danger' => Define::MESSAGE_NEW_PASSWORD_FAIL
                            ));
                        }
                    }
                }
                $this->_view->setVariable('newPasswordForm', $newPasswordForm);
            }
            else
            {
                $this->flashMessenger()->addMessage(array(
                    'danger' => Define::MESSAGE_INVALID_TOKEN
                ));
                return $this->redirect()->toUrl(Define::URL_REDIRECT_NEW_PASSWORD_FAIL);
            }
        }
        else
        {
            $this->flashMessenger()->addMessage(array(
                'danger' => Define::MESSAGE_NOT_FOUNT_TOKEN
            ));
            return $this->redirect()->toUrl(Define::URL_REDIRECT_NEW_PASSWORD_FAIL);
        }

        $this->writeLog();
        return $this->_view;
    }

    public function profileAction()
    {
        $this->init();

        $userId = $this->getLogin();
        if($userId)
        {
            $user = $this->_commonDAO->executeQueryFirst('USER_GET_BY_ID', array($userId));
            $this->_view->setVariable('user', $user);
        }
        else
        {
            return $this->redirect()->toUrl(Define::URL_REDIRECT_LOGIN_FAIL);
        }

        $this->writeLog();
        return $this->_view;
    }

    public function updateAction()
    {
        $this->init();

        $userId = $this->getLogin();
        if($userId)
        {
            $user = $this->_commonDAO->executeQueryFirst('USER_GET_BY_ID', array($userId));
            $this->_view->setVariable('user', $user);

            $updateProfileForm = new UpdateProfileForm('updateProfileForm');
            $updateProfileForm->setInputFilter(new UpdateProfileFilter());
            $updateProfileForm->setData($user);

            if ($this->_request->isPost())
            {
                $data = $this->_request->getPost();
                $updateProfileForm->setData($data);

                // check phone is exists !
                $isExistsPhone = false;
                if(strlen($data['phone']) > 0 && strcmp($user['phone'], $data['phone']) !== 0)
                    $isExistsPhone = $this->_commonDAO->executeQueryFirst('USER_GET_BY_PHONE', array($data['phone']));

                if($isExistsPhone)
                {
                    $this->flashMessenger()->addMessage(array(
                        'danger' => Define::MESSAGE_PHONE_EXISTS
                    ));
                    $this->writeLog();
                    return $this->redirect()->toUrl(Define::URL_REDIRECT_UPDATE_PROFILE_FAIL);
                }

                // check email is exists !
                $isExistsEmail = false;
                if(strlen($data['email']) > 0 && strcmp($user['email'], $data['email']) !== 0)
                    $isExistsEmail = $this->_commonDAO->executeQueryFirst('USER_GET_BY_EMAIL', array($data['email']));

                if($isExistsEmail)
                {
                    $this->flashMessenger()->addMessage(array(
                        'danger' => Define::MESSAGE_EMAIL_EXISTS
                    ));
                    $this->writeLog();
                    return $this->redirect()->toUrl(Define::URL_REDIRECT_UPDATE_PROFILE_FAIL);
                }

                // valid form : first_name, last_name, email, password is not null. password == confirm_password ?
                if($updateProfileForm->isValid())
                {
                    $avatar = $user['avatar'];
                    if(!empty($_FILES['avatar']['name']))
                    {
                        $filename = time() .'_'. $_FILES['avatar']['name'];
                        $avatar = Define::PATH_UPLOAD_IMAGES.$filename;
                        move_uploaded_file($_FILES['avatar']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/' .$avatar);
                    }

                    $arrParams = array(
                        $user['id'],
                        $data['first_name'],
                        $data['last_name'],
                        $data['email'],
                        $data['phone'],
                        $avatar
                    );
                    $user = $this->_commonDAO->executeNonQuery('USER_UPDATE', $arrParams);
                    if($user)
                    {
                        $this->flashMessenger()->addMessage(array(
                            'success' => Define::MESSAGE_UPDATE_PROFILE_SUCCESS
                        ));
                        $this->writeLog();

                        return $this->redirect()->toUrl(Define::URL_REDIRECT_UPDATE_PROFILE_SUCCESS);
                    }
                }
            }
            $this->_view->setVariable('updateProfileForm', $updateProfileForm);
        }
        else
            return $this->redirect()->toUrl(Define::URL_REDIRECT_LOGIN_FAIL);

        $this->writeLog();
        return $this->_view;
    }

    public function logOutAction()
    {
        $this->init();
        $this->_session->getManager()->destroy();
        $this->writeLog();
        return $this->redirect()->toUrl(Define::URL_REDIRECT_LOGOUT);
    }

    public function isExistsEmailAction()
    {
        $this->_log .= '[isExistsEmail] ';
        $this->_log .= '[params : '.json_encode($_GET).'] ';
        if($this->params()->fromQuery('email'))
        {
            $email = str_replace(' ', '',$this->params()->fromQuery('email'));
            $id = intval($this->params()->fromQuery('id', 0));

            $commonDAO = $this->getServiceLocator()->get('CommonDAO');
            $user = $commonDAO->executeQueryFirst('USER_GET_BY_EMAIL', array($email));

            if($user && $user['id'] != $id)
            {
                $result = array(
                    'error_code' => ResponseCode::EXIST,
                    'message' => Define::MESSAGE_EMAIL_EXISTS
                );
            }
            else
            {
                $result = array(
                    'error_code' => ResponseCode::SUCCESS
                );
            }
        }
        else
        {
            $result = array(
                'error_code' => ResponseCode::WRONG_FORMAT,
                'message' => ''
            );
        }

        $this->response->setContent(json_encode($result));
        $this->_log .= '[response : '.json_encode($result).'] ';
        exec('echo "'.$this->_log.'" >> '.$this->_fileNameLog, $output);
        return $this->response;
    }

    public function isExistsPhoneAction()
    {
        $this->_log .= '[isExistsPhone] ';
        $this->_log .= '[params : '.json_encode($_GET).'] ';
        if($this->params()->fromQuery('phone'))
        {
            $phone = str_replace(' ', '',$this->params()->fromQuery('phone'));
            $id = intval($this->params()->fromQuery('id', 0));

            $commonDAO = $this->getServiceLocator()->get('CommonDAO');
            $user = $commonDAO->executeQueryFirst('USER_GET_BY_PHONE', array($phone));

            if($user && $user['id'] != $id)
            {
                $result = array(
                    'error_code' => ResponseCode::EXIST,
                    'message' => Define::MESSAGE_PHONE_EXISTS
                );
            }
            else
            {
                $result = array(
                    'error_code' => ResponseCode::SUCCESS
                );
            }
        }
        else
        {
            $result = array(
                'error_code' => ResponseCode::WRONG_FORMAT,
                'message' => ''
            );
        }

        $this->response->setContent(json_encode($result));
        $this->_log .= '[response : '.json_encode($result).'] ';
        exec('echo "'.$this->_log.'" >> '.$this->_fileNameLog, $output);
        return $this->response;
    }

}