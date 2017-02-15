<?php

namespace Auth\Model;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;

class CommonDAO extends AbstractTableGateway implements ServiceLocatorAwareInterface
{

    protected $_serviceLocator;
    public $table = '';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
    }

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->_serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->_serviceLocator;
    }

    /**
     * Thực hiện chức năng insert, update, delete,..
     * @param string spname: Tên procedure
     * @param array arrParameter: mảng đối số truyền vào procedure
     * @return boolean
     */
    public function executeNonQuery($spname, $arrParameter = array())
    {
        $stmt = $this->adapter->createStatement();
        $spname = 'CALL ' . $spname . '(';
        $count = count($arrParameter);
        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $spname = $spname . '?,';
            }
            $spname = substr($spname, 0, -1);
        }

        $spname = $spname . ')';
        $stmt->prepare($spname);

        for ($i = 0; $i < $count; $i++) {
            $stmt->getResource()->bindParam($i + 1, $arrParameter [$i]);
        }

        $bResult = $this->getResultSetPrototype()->initialize($stmt->execute());

        $stmt->getResource()->closeCursor();

        if ($bResult) {
            return true;
        }
        return false;
    }

    /**
     * Chủ yếu thực hiện chức năng select
     * @param string spname: Tên procedure
     * @param array arrParameter: mảng đối số truyền vào procedure
     * @return records
     */
    public function executeQuery($spname, $arrParameter = array())
    {
        $stmt = $this->adapter->createStatement();
        $spname = 'CALL ' . $spname . '(';
        $count = count($arrParameter);
        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $spname = $spname . '?,';
            }
            $spname = substr($spname, 0, -1);
        }

        $spname = $spname . ')';
        $stmt->prepare($spname);

        for ($i = 0; $i < $count; $i++) {
            $stmt->getResource()->bindParam($i + 1, $arrParameter [$i]);
        }

        $arrResult = $this->getResultSetPrototype()->initialize($stmt->execute());

        $records = array();

        foreach ($arrResult as $object) {
            $records [] = $object;
        }

        $stmt->getResource()->closeCursor();

        return $records;
    }

    public function executeQueryFirst($spname, $arrParameter = array())
    {
        $stmt = $this->adapter->createStatement();
        $spname = 'CALL ' . $spname . '(';
        $count = count($arrParameter);
        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $spname = $spname . '?,';
            }
            $spname = substr($spname, 0, -1);
        }

        $spname = $spname . ')';
        $stmt->prepare($spname);

        for ($i = 0; $i < $count; $i++) {
            $stmt->getResource()->bindParam($i + 1, $arrParameter [$i]);
        }

        $arrResult = $this->getResultSetPrototype()->initialize($stmt->execute());

        $records = array();

        foreach ($arrResult as $object) {
            $records [] = $object;
        }

        $stmt->getResource()->closeCursor();

        if(count($records) > 0)
            return $records[0];

        return false;
    }

    /**
     * Thực hiện chức năng insert và trả về last id
     * @param type $spname
     * @param type $arr tham s? cho sp
     * @return int
     */
    public function executeQuery_returnID($spname, $arr = array())
    {
        $stmt = $this->adapter->createStatement();
        $spname = 'CALL ' . $spname . '(';
        $count = count($arr);
        if ($count > 0) {
            for ($i = 0; $i < $count; $i++) {
                $spname = $spname . '?,';
            }
            $spname = substr($spname, 0, -1);
        }
        $spname = $spname . ')';

        $stmt->prepare($spname);
        for ($i = 0; $i < $count; $i++) {
            $stmt->getResource()->bindParam($i + 1, $arr [$i]);
        }
        $result = $this->getResultSetPrototype()->initialize($stmt->execute());
//        $stmt2 = $this->adapter->createStatement();
//        $stmt2->prepare("SELECT @result AS id");
//        $result2 = $stmt2->execute();
        $output = $result->current();
        return (int)$output['id'];
    }

    /**
     * Get list records của 1 trang
     * @param string sp_select_limit: tên procedure
     * @param type $GET : truyền vào đối số $_GET
     * @param type $arr_search : Tham số search nếu có
     * @return list records per 1 page-
     */
    public function getListDataPage($link, $sp_select_limit, $GET, $arr_search = [])
    {
        $arr_sp = array_values($arr_search);
        $link_display = 7;      //Số link hiển thị phân trang
        if (isset($GET['display']) && (int)$GET['display'] >= 0) {
            $display = $GET['display'];  //number per 1 page
        } else {
            $display = 10;
        }
        $start = (isset($GET['start']) && (int)$GET['start'] >= 0) ? $GET['start'] : 0;
        $current = ($start / $display) + 1;
        $next = $start + $display;
        $previous = $start - $display;
        array_push($arr_sp, $display);
        array_push($arr_sp, $start);
        $admin_users = $this->executeQuery($sp_select_limit, $arr_sp);
        if (isset($GET['page']) && (int)$GET['page'] >= 0 && isset($GET['record']) && (int)$GET['record'] >= 0) {
            $page = $GET['page'];
            $record = $GET['record'];
            if ($record > $display) {
                $page = ceil($record / $display);
            } else {
                $page = 1;
            }
        } else {
            $result = $this->executeQuery('SP_FOUND_ROWS');
            $record = $result[0]['SIZE'];
            if ($record > $display) {
                $page = ceil($record / $display);
            } else {
                $page = 1;
            }
        }
        $last = ($page - 1) * $display;
        if ($current >= $link_display) {
            $start_page = $current - 3;
            if ($page > $current + 3) {
                $end_page = $current + 3;
            } else if ($current <= $page && $current > $page - 6) {
                $start_page = $page - 6;
                $end_page = $page;
            } else {
                $end_page = $page;
            }
        } else {
            $start_page = 1;
            if ($page > $link_display) {
                $end_page = $link_display;
            } else {
                $end_page = $page;
            }
        }
        //lấy số thứ tự của trang
        $page_no = $display * ($current - 1) + 1;
        return ['link' => $link, 'list' => $admin_users, 'display' => $display, 'page' => $page, 'record' => $record, 'start_page' => $start_page,
            'end_page' => $end_page, 'current' => $current, 'next' => $next, 'previous' => $previous, 'last' => $last, 'arr_search' => $arr_search, 'page_no' => $page_no];
    }

    /**
     * Get mảng menu n cấp cung cấp cho việc hiển thị menu bên trái
     * @param int intMenuParent: mã menu cha
     * @param int group_id: mã group_id
     * @param array menus: Danh sách các menu id thuộc group_id
     * @param array dataSource: Tham số mạng ban đầu dùng đệ quy
     * @return list records per 1 page-
     */
    public function getDataMenus($intMenuParent, $group_id, $menus, $dataSource)
    {
        $arrResult = $this->executeQuery("ADMIN_MENU_DISPLAY_CHILDREN_BY_GROUP_ID", array($intMenuParent, $group_id));
        $i = -1;
        foreach ($arrResult as $row) {
            $i++;
            if ($row['Count'] > 0) {
                $dataSource[$i] = array('id' => $row['MENU_ID'], 'text' => $row['MENU_NAME'], 'menu_link' => $row['MENU_LINK'], 'menu_icon' => $row['MENU_ICON'], 'menu_level' => $row['MENU_LEVEL'], 'order' => $row['MENU_ORDER'], 'children' => array());
                $dataSource[$i]['children'] = $this->getDataMenus((int)$row['MENU_ID'], $group_id, $menus, $dataSource[$i]['children']);
            } else {
                $arrResultLeaf = $this->executeQuery('ADMIN_MENU_IS_LEAF', array($row['MENU_ID']));
                $bLeaf = $arrResultLeaf[0]['EXIST'];
                if ($bLeaf && in_array($row['MENU_ID'], $menus)) {
                    $dataSource[$i] = array('id' => $row['MENU_ID'], 'text' => $row['MENU_NAME'], 'menu_link' => $row['MENU_LINK'], 'menu_icon' => $row['MENU_ICON'], 'menu_level' => $row['MENU_LEVEL'], 'order' => $row['MENU_ORDER']);
                }
            }
        }
        return $dataSource;
    }

    /**
     * Get mảng menu n cấp cung cấp cho việc hiển thị tree view check box phân quyền
     * @param int intMenuParent: mã menu cha
     * @param array menus: Danh sách các menu id thuộc group_id
     * @param array dataSource: Tham số mạng ban đầu dùng đệ quy
     * @return list records per 1 page-
     */
    //START THANH-LD MODIFIED
    public function getDataMenusForTreeCheckBox($intMenuParent, $dataSource, $menus, $group_id)
    {
        $arrResult = $this->executeQuery("ADMIN_MENU_DISPLAY_CHILDREN", array($intMenuParent));
        $i = -1;
        foreach ($arrResult as $row) {
            $arrFunction = $this->getFunction((int)$row['MENU_ID'], $group_id);
            $i++;
            $dataSource[$i] = array('id' => $row['MENU_ID'], 'text' => $row['MENU_NAME'], 'icon' => '',
                'order' => $row['MENU_ORDER'], 'children' => array());
            if ($row['Count'] > 0) {
                $dataSource[$i]['children'] = $this->getDataMenusForTreeCheckBox((int)$row['MENU_ID'], $dataSource[$i]['children'], $menus, $group_id);
            } else if (count($arrFunction) > 0) {
                $dataSource[$i]['children'] = $arrFunction;
            } else {
                $dataSource[$i]['icon'] = "glyphicon glyphicon-leaf";
            }
        }
        return $dataSource;
    }

    //END THANH-LD MODIFIED
    //START THANH-LD ADD
    public function getFunction($menu_id, $group_id)
    {
        $arrFunction = array();
        $arrFunctionGroup = array();
        $arrFunctionGroupOrg = $this->executeQuery("ADMIN_GROUP_FUNCTION_GET_BY_GROUP", array($group_id));
        for ($i = 0; $i < count($arrFunctionGroupOrg); $i++) {
            $arrFunctionGroup[$i] = $arrFunctionGroupOrg[$i]['FUNCTION_ID'];
        }
        $arrFunctionOrg = $this->executeQuery("ADMIN_FUNCTION_GET_LIST_BY_MENU_ID", array($menu_id));
        if (count($arrFunctionOrg) > 0) {
            for ($i = 0; $i < count($arrFunctionOrg); $i++) {
                if (in_array($arrFunctionOrg[$i]['FUNCTION_ID'], $arrFunctionGroup)) {
                    $arrFunction[$i] = array('id' => $menu_id . '-' . $arrFunctionOrg[$i]['FUNCTION_ID'],
                        'text' => $arrFunctionOrg[$i]['FUNCTION_NAME'], "state" => ["selected" => true], 'order' => 1,
                        'icon' => "glyphicon glyphicon-leaf");
                } else {
                    $arrFunction[$i] = array('id' => $menu_id . '-' . $arrFunctionOrg[$i]['FUNCTION_ID'], 'order' => 1,
                        'text' => $arrFunctionOrg[$i]['FUNCTION_NAME'], 'icon' => "glyphicon glyphicon-leaf");
                }
            }
        }
        return $arrFunction;
    }
    //END THANH-LD ADD
    /**
     * Get mảng menu n cấp cung cấp cho việc hiển thị tree view menu
     * @param int intMenuParent: mã menu cha
     * @param array arrMenuParent: Danh sách menu_id của menu cha
     * @param array dataSource: Tham số mạng ban đầu dùng đệ quy
     * @return list records per 1 page-
     */
    public function getDataMenusForTree($intMenuParent, $dataSource, $arrMenuParent)
    {
        $result = $this->executeQuery("ADMIN_MENU_DISPLAY_CHILDREN", array($intMenuParent));
        $i = -1;
        foreach ($result as $row) {
            $i++;
            if (in_array($row['MENU_ID'], $arrMenuParent)) {
                $dataSource[$i] = array('id' => $row['MENU_ID'], 'text' => $row['MENU_NAME'], 'order' => $row['MENU_ORDER'], 'children' => array(), "state" => ["opened" => true]);
            } else {
                $dataSource[$i] = array('id' => $row['MENU_ID'], 'text' => $row['MENU_NAME'], 'order' => $row['MENU_ORDER'], 'children' => array());
            }

            if ($row['Count'] > 0) {
                $dataSource[$i]['children'] = $this->getDataMenusForTree((int)$row['MENU_ID'], $dataSource[$i]['children'], $arrMenuParent);
            } else {
                $dataSource[$i] = array('id' => $row['MENU_ID'], 'text' => $row['MENU_NAME'], 'order' => $row['MENU_ORDER'], 'icon' => "glyphicon glyphicon-leaf");
            }
        }
        return $dataSource;
    }

    /**
     * Get mảng menu n cấp cung cấp cho việc hiển thị tree view check box phân quyền
     * @param int intMenuParent: mã menu cha
     * @param array menus: Danh sách các menu id thuộc group_id
     * @param array dataSource: Tham số mạng ban đầu dùng đệ quy
     * @return list records per 1 page-
     */
    public function getDataVODsForTreeCheckBox($intMenuParent, $type_id, $dataSource, $selected)
    {
        $arrResult = $this->executeQuery("VOD_CATE_DISPLAY_CHILDREN", array($intMenuParent, $type_id));
        $i = -1;
        foreach ($arrResult as $row) {
            $i++;
            $dataSource[$i] = array('id' => $row['CATE_ID'], 'text' => $row['CATE_NAME'], 'children' => array());
            if ($row['Count'] > 0) {
                $dataSource[$i]['children'] = $this->getDataVODsForTreeCheckBox((int)$row['CATE_ID'], $type_id, $dataSource[$i]['children'], $selected);
            } else if (in_array($row['CATE_ID'], $selected)) {
                $dataSource[$i] = array('id' => $row['CATE_ID'], 'text' => $row['CATE_NAME'], 'icon' => "glyphicon glyphicon-leaf", "state" => ["selected" => true]);
            } else {
                $dataSource[$i] = array('id' => $row['CATE_ID'], 'text' => $row['CATE_NAME'], 'icon' => "glyphicon glyphicon-leaf");
            }
        }
        return $dataSource;
    }

    /**
     * Get mảng menu 1 cấp cung cấp cho việc hiển thị tree view check box channel category
     * @param
     * @param array menus: Danh sách các menu id thuộc group_id
     * @param array dataSource: Tham số mạng ban đầu dùng đệ quy
     * @return list records per 1 page-
     */
    public function getDataMenusForTreeCheckBox_Channel_Cate($dataSource, $arrCategory)
    {
        $result = $this->executeQuery("CHANNEL_CATEGORY_SELECT_ALL", array());
        $i = -1;
        foreach ($result as $row) {
            $i++;
            //$dataSource[$i] = array('id' => $row['CATE_ID'], 'text' => $row['CATE_NAME'], array());
            if (in_array($row['CATE_ID'], $arrCategory)) {
                $dataSource[$i] = array('id' => $row['CATE_ID'], 'text' => $row['CATE_NAME'], 'icon' => "glyphicon glyphicon-leaf", "state" => ["selected" => true]);
            } else {
                $dataSource[$i] = array('id' => $row['CATE_ID'], 'text' => $row['CATE_NAME'], 'icon' => "glyphicon glyphicon-leaf");
            }
        }
        return $dataSource;
    }

    public function getDataMenusForTreeCheckBox_Channel_Profile($dataSource, $arrCategory)
    {
        $result = $this->executeQuery("CHANNEL_PROFILE_SELECT_ALL", array());
        $i = -1;
        foreach ($result as $row) {
            $i++;
            //$dataSource[$i] = array('id' => $row['PROFILE_ID'], 'text' => $row['PROFILE_NAME'], array());
            $profile_name = "";
            if ($row['TYPE_NAME'] == "SD") {
                $profile_name = $row['PROFILE_NAME'] . '- SD';
            } else {
                $profile_name = $row['PROFILE_NAME'] . '- HD';
            }
            if (in_array($row['PROFILE_ID'], $arrCategory)) {
                $dataSource[$i] = array('id' => $row['PROFILE_ID'], 'text' => $profile_name, 'icon' => "glyphicon glyphicon-leaf", "state" => ["selected" => true]);
            } else {
                $dataSource[$i] = array('id' => $row['PROFILE_ID'], 'text' => $profile_name, 'icon' => "glyphicon glyphicon-leaf");
            }
        }
        return $dataSource;
    }

    /**
     * Get mảng đối tượng menu tạo Breadcrumb cho chức năng MenuManagement
     * @param int menu_id: mã menu
     * @param array arrMenu: Tham số mảng ban đầu dùng đệ quy
     * @return list records per 1 page-
     */
    public function getBreadcrumb($menu_id, $arrMenu)
    {
        $arrResult = $this->executeQuery("ADMIN_MENU_GET_BY_ID", array($menu_id));
        $menu = $arrResult[0];
        $arrMenu[] = array('link' => '/admin/menumanagement/index?menu_id=' . $menu['MENU_ID'], 'name' => $menu['MENU_NAME']);
        if ($menu['MENU_PARENT'] != 0) {
            $arrMenu = $this->getBreadcrumb($menu['MENU_PARENT'], $arrMenu);
        }
        return $arrMenu;
    }

    /**
     * Get mảng menu_id của menu cha
     * @param int menu_id: mã menu
     * @param array arrMenu: Tham số mảng ban đầu dùng đệ quy
     * @return list records per 1 page-
     */
    public function getListMenuParent($menu_id, $arrMenu)
    {
        $arrResult = $this->executeQuery("ADMIN_MENU_GET_BY_ID", array($menu_id));
        if (count($arrResult) > 0) {
            $menu = $arrResult[0];
            array_push($arrMenu, $menu_id);
            if ($menu['MENU_PARENT'] != 0) {
                $arrMenu = $this->getListMenuParent($menu['MENU_PARENT'], $arrMenu);
            }
        } else {
            $arrMenu = array();
        }

        return $arrMenu;
    }

}
