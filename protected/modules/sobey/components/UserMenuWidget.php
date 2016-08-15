<?php

class UserMenuWidget extends CWidget {

    public function run() {
        // 菜单
        $menu_tree = Menu::model()->getTree();
        // 用户信息
        $user = Yii::app()->session['user'];
        // 不是系统最高权限 则根据权限过滤菜单
        if ($user['role_id'] != 'super') {
            $cacheName = md5("widget_userMenuWidget_roleId_" . $user['role_id']);
            $menuList = Yii::app()->memcache->get($cacheName);
            if (!$menuList) {
                // 所有权限条件
                $acaList = Aca::model()->getAcaList();
                // 获取角色权限
                $roleAca = RoleAca::model()->getAcaIdsByRole($user['role_id']);
                // 获取角色没有的权限
                $roleNoAca = array();
                foreach ($acaList as $key => $val) {
                    if (!in_array($key, $roleAca)) {
                        $roleNoAca[$key] = Yii::app()->createUrl($val['controller'] . '/' . $val['action']);
                    }
                }
                // 过滤当前角色没有权限菜单
                foreach ($menu_tree as $key => $val) {
                    foreach ($val['child'] as $cKey => $cVal) {
                        if (in_array($cVal['url'], $roleNoAca)) {
                            unset($menu_tree[$key]['child'][$cKey]);
                        }
                    }
                    // 当所有的二级菜单都没有权限 则过滤当前主菜单
                    if (empty($menu_tree[$key]['child'])) {
                        unset($menu_tree[$key]);
                    }
                }
                Yii::app()->memcache->set($cacheName, $menu_tree, 600);
            } else {
                $menu_tree = $menuList;
            }
        }
        $set = array('menu_tree' => $menu_tree);
        $this->render('usermenu', $set);
    }

}