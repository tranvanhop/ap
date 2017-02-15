<?php

namespace Auth\Utility;

use Auth\Constant\Define;
use Auth\Constant\Key;

class Utility
{

    public function getMenus($session, $controllerName = '', $actionName = '')
    {
        $menus = array(
            'Home' => array(
                'classActive' => '',
                'class' => 'dropdown',
                'href' => '/',
                'caption' => 'Home',
            ),
            'Post' => array(
                'classActive' => '',
                'class' => 'dropdown',
                'href' => '/post',
                'caption' => 'Post'
            ),
//            'Tools' => array(
//                'classActive' => '',
//                'class' => 'dropdown',
//                'href' => '/tools',
//                'caption' => 'Tools'
//            ),
            'Account' => array(
                'classActive' => '',
                'class' => 'dropdown',
                'href' => '/account/log-in',
                'caption' => 'Account',
            ),
            'Contact' => array(
                'classActive' => '',
                'class' => 'dropdown',
                'href' => '/contact',
                'caption' => 'Contact'
            ),
        );

        if($session->offsetExists(Key::ID))
        {
            $menus = array(
                'Home' => array(
                    'classActive' => '',
                    'class' => 'dropdown',
                    'href' => '/',
                    'caption' => 'Home',
                ),
                'Post' => array(
                    'classActive' => '',
                    'class' => 'dropdown',
                    'href' => '/post',
                    'caption' => 'Post'
                ),
//                'Tools' => array(
//                    'classActive' => '',
//                    'class' => 'dropdown',
//                    'href' => '/tools',
//                    'caption' => 'Tools'
//                ),
                'Account' => array(
                    'classActive' => '',
                    'class' => 'dropdown',
                    'href' => '/account/profile',
                    'caption' => 'Account',
                ),
                'Contact' => array(
                    'classActive' => '',
                    'class' => 'dropdown',
                    'href' => '/contact',
                    'caption' => 'Contact'
                ),
            );
        }

        if(isset($menus[$controllerName]))
        {
            $menus[$controllerName]['classActive'] = 'active';
            if(isset($menus[$controllerName]['sub_menu']) &&  isset($menus[$controllerName]['sub_menu'][$actionName]))
            {
                $menus[$controllerName]['sub_menu'][$actionName]['classActive'] = 'active';
            }
        }

        return $menus;
    }

    public function getMenuAccount($session, $actionName = '')
    {
        $menus = array(
            'logIn' => array(
                'classActive' => '',
                'href' => '/account/log-in',
                'caption' => 'Login',
            ),
            'registration' => array(
                'classActive' => '',
                'href' => '/account/registration',
                'caption' => 'Registration'
            ),
            'forgotPassword' => array(
                'classActive' => '',
                'href' => '/account/forgot-password',
                'caption' => 'Forgot password'
            ),
        );

        if($session->offsetExists(Key::ID))
        {
            $menus = array(
                'profile' => array(
                    'classActive' => '',
                    'href' => '/account/profile',
                    'caption' => 'My account',
                ),
                'update' => array(
                    'classActive' => '',
                    'href' => '/account/update',
                    'caption' => 'Update profile',
                ),
            );
        }

        if(isset($menus[$actionName]))
        {
            $menus[$actionName]['classActive'] = 'active';
        }

        return $menus;
    }

    public function getPullRight($session)
    {
        $pullRight = array(
            'profile' => array(
                'href' => '/account/profile',
                'caption' => 'My account'
            ),
            'logOut' => array(
                'href' => '/account/log-out',
                'caption' => 'Log out'
            )
        );

        if(!$session->offsetExists(Key::ID))
        {
            $pullRight = array(
                'logIn' => array(
                    'href' => '/account/log-in',
                    'caption' => 'Login'
                ),
                'registration' => array(
                    'href' => '/account/registration',
                    'caption' => 'Registration'
                )
            );
        }
        return $pullRight;
    }

    public function getPagination($array, $limit, $offset)
    {
        $pagination = array();

        if(is_array($array))
        {
            $start = ($offset - 1) * $limit;
            $end = $offset * $limit;
            for($i = $start; isset($array[$start]), $i < $end; $i++)
            {
                if(isset($array[$i]))
                    $pagination['items'][] = $array[$i];
            }
        }

        $pagination['pageCount'] = count($array);
        $pagination['current'] = $offset;

        if(isset($array[($offset - 2) * $limit]))
            $pagination['previous'] = $offset - 1;

        if(isset($array[($offset) * $limit]))
            $pagination['next'] = $offset + 1;

        $pagesInRange = [];
        for($i = 0; isset($array[$i]), $i < count($array); $i += $limit)
        {
            $pagesInRange[] = $i/$limit + 1;
        }

        $pagination['pagesInRange'] = $pagesInRange;

        return $pagination;
    }

    public function limitWords($string)
    {
        // strip tags to avoid breaking any html
        $string = strip_tags($string);

        $words = explode(" ", trim($string));
        if(count($words) > Define::LIMIT_WORDS)
        {
            $string = '';

            for($i = 0; $i < Define::LIMIT_WORDS; $i++)
                $string .= $words[$i].' ';

            $string .= '...';
        }

        return $string;
    }

    public function getDateTimeQuery($startDate = '', $endDate = '')
    {
        if(empty($startDate) || empty($endDate))
        {
            return array(
                'start_date' => '1993-10-10 00:00:00',
                'end_date' => '2050-10-10 00:00:00'
            );
        }

        return array(
            'start_date' => $startDate,
            'end_date' => $endDate
        );
    }

    public function random()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $length = 32;
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function removeQuery($query)
    {
        $query = str_replace('select', '', $query);
        $query = str_replace('from', '', $query);
        $query = str_replace('where', '', $query);
        $query = str_replace('delete', '', $query);
        $query = str_replace('update', '', $query);

        return $query;
    }


    // Admin
    public function getMenusAdmin($session, $controllerName = '', $actionName = '')
    {
        $menus = array(
            'Home' => array(
                'classActive' => '',
                'class' => 'dropdown',
                'href' => '/',
                'caption' => 'Home',
            ),
            'Post' => array(
                'classActive' => '',
                'class' => 'dropdown',
                'href' => '/post',
                'caption' => 'Post'
            ),
//            'Tools' => array(
//                'classActive' => '',
//                'class' => 'dropdown',
//                'href' => '/tools',
//                'caption' => 'Tools'
//            ),
            'Account' => array(
                'classActive' => '',
                'class' => 'dropdown',
                'href' => '/account/log-in',
                'caption' => 'Account',
            ),
            'Contact' => array(
                'classActive' => '',
                'class' => 'dropdown',
                'href' => '/contact',
                'caption' => 'Contact'
            ),
        );

        if($session->offsetExists(Key::ID))
        {
            $menus = array(
                'Home' => array(
                    'classActive' => '',
                    'class' => 'dropdown',
                    'href' => '/',
                    'caption' => 'Home',
                ),
                'Post' => array(
                    'classActive' => '',
                    'class' => 'dropdown',
                    'href' => '/post',
                    'caption' => 'Post'
                ),
//                'Tools' => array(
//                    'classActive' => '',
//                    'class' => 'dropdown',
//                    'href' => '/tools',
//                    'caption' => 'Tools'
//                ),
                'Account' => array(
                    'classActive' => '',
                    'class' => 'dropdown',
                    'href' => '/account/profile',
                    'caption' => 'Account',
                ),
                'Contact' => array(
                    'classActive' => '',
                    'class' => 'dropdown',
                    'href' => '/contact',
                    'caption' => 'Contact'
                ),
            );
        }

        if(isset($menus[$controllerName]))
        {
            $menus[$controllerName]['classActive'] = 'active';
            if(isset($menus[$controllerName]['sub_menu']) &&  isset($menus[$controllerName]['sub_menu'][$actionName]))
            {
                $menus[$controllerName]['sub_menu'][$actionName]['classActive'] = 'active';
            }
        }

        return $menus;
    }
}