<?php
namespace Auth\Constant;

class Define
{
    const LIMIT_MAX = 999999;
    const LIMIT = 10;
    const LIMIT_HOME = 12;
    const EXPIRES = 31536000; // 365 day
    const LIMIT_WORDS = 20;
    const LIMIT_RECENT = 3;
    static $IMAGE_TYPE_ALLOWS = ['image/png', 'image/jpeg', 'image/gif'];
    const UPLOAD_MAX_FILE_SIZE = 838860800; // 100MB
    const TOOLS_ITEM_SIZE = 3;

    const GB = 1000000000;
    const MB = 1000000;
    const KB = 1000;
    const B = 1;

    const URL_REDIRECT_LOGIN_SUCCESS = '/';
    const URL_REDIRECT_LOGIN_FAIL = '/account/log-in';
    const URL_REDIRECT_REGISTRATION_SUCCESS = '/account/log-in';
    const URL_REDIRECT_REGISTRATION_FAIL = '/account/registration';
    const URL_REDIRECT_UPDATE_PROFILE_SUCCESS = '/account/profile';
    const URL_REDIRECT_UPDATE_PROFILE_FAIL = '/account/update';
    const URL_REDIRECT_LOGOUT = '/';
    const URL_REDIRECT_CONTACT_SUCCESS = '/contact';
    const URL_REDIRECT_CONTACT_FAIL = '/contact';
    const URL_REDIRECT_FORGOT_PASSWORD_SUCCESS = '/';
    const URL_REDIRECT_FORGOT_PASSWORD_FAIL = '/account/forgot-password';
    const URL_REDIRECT_NEW_PASSWORD_SUCCESS = '/account/log-in';
    const URL_REDIRECT_NEW_PASSWORD_FAIL = '/';
    const URL_REDIRECT_COMMENT_SUCCESS = '/post/detail/';
    const URL_REDIRECT_COMMENT_FAIL = '/post/detail/';

    const URL_AVATAR_DEFAULT = 'theme/assets/frontend/pages/img/people/img2-large.jpg';

    const MESSAGE_LOGIN_SUCCESS = 'Login successful !';
    const MESSAGE_LOGIN_FAIL = 'Login unsuccessful !';
    const MESSAGE_REGISTRATION_SUCCESS = 'Registration successful !';
    const MESSAGE_REGISTRATION_FAIL = 'Registration unsuccessful !';
    const MESSAGE_UPDATE_PROFILE_SUCCESS = 'Update successful !';
    const MESSAGE_UPDATE_PROFILE_FAIL = 'Update unsuccessful !';
    const MESSAGE_EMAIL_EXISTS = 'Email is exists !';
    const MESSAGE_PHONE_EXISTS = 'Phone is exists !';
    const MESSAGE_LOGOUT = 'Good bye !';
    const MESSAGE_CONTACT_SEND_SUCCESS = 'Send message successful !';
    const MESSAGE_CONTACT_SEND_FAIL = 'Send message unsuccessful !';
    const MESSAGE_FORGOT_PASSWORD_SUCCESS = 'LEEHOP đã gửi đường link kích hoạt yêu cầu lấy lại mật khẩu đăng nhập tài khoản tới địa chỉ email ';
    const MESSAGE_FORGOT_PASSWORD_FAIL = 'Email does not exist !';
    const MESSAGE_INVALID_TOKEN = 'Invalid token';
    const MESSAGE_NOT_FOUNT_TOKEN = 'Token not found';
    const MESSAGE_NEW_PASSWORD_SUCCESS = 'Create new password success !';
    const MESSAGE_NEW_PASSWORD_FAIL = 'Create new password unsuccessful !';
    const MESSAGE_COMMENT_SUCCESS = 'Post a comment successful !';
    const MESSAGE_COMMENT_FAIL = 'Post a comment unsuccessful !';

    const EMAIL_USERNAME = 'leehop.blog@gmail.com';
    const EMAIL_SUBJECT = 'LEEHOP - BLOG';

    const PATH_UPLOAD_IMAGES = 'upload/images/';

    const iPhone = 'iPhone';
    const iPad = 'iPad';
}