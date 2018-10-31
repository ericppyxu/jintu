<?php

/**
 * ECSHOP 数据平台管理用户
 * ============================================================================
 * * 版权所有 2016-2052 孤独三少，并保留所有权利。
 * QQ：122090024；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: 孤独三少 $
 * $Id: user_msg.php 17217 2011-01-19 06:29:08Z 孤独三少 $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
//var_dump(select_list());exit;

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/* 初始化 $exc 对象 */
$exc = new exchange($ecs->table("admin_role"), $db, 'user_id', 'user_name');

/* 权限判断 */
admin_priv('admin_role_manage');
/*------------------------------------------------------ */
//--数据查询
/*------------------------------------------------------ */
/* 时间参数 */
$start_date = $end_date = '';
if (isset($_POST) && !empty($_POST))
{
    $start_date = local_strtotime($_POST['start_date']);
    $end_date = local_strtotime($_POST['end_date']);
}
elseif (isset($_GET['start_date']) && !empty($_GET['end_date']))
{
    $start_date = local_strtotime($_GET['start_date']);
    $end_date = local_strtotime($_GET['end_date']);
}
else
{
    $today  = local_strtotime(local_date('Y-m-d'));
    $start_date = $today - 86400 * 7;
    $end_date   = $today;
}
/*------------------------------------------------------ */
//--手机预约用户列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
	//获取信息列表
	$card_list = card_list();  //求得目标数组的函数
    $smarty->assign('card_list',   $card_list['list']);
	$smarty->assign('ur_here',     "数据平台用户管理");
	$smarty->assign('action_link', array('href'=>'admin_role_manage.php?act=add', 'text' => $_LANG['admin_add']));
	$smarty->assign('select_list',   select_list());
    $smarty->assign('filter',       $card_list['filter']);
    $smarty->assign('record_count', $card_list['record_count']);
    $smarty->assign('page_count',   $card_list['page_count']);
    $smarty->assign('full_page',        1);
	$smarty->assign('start_date',   local_date('Y-m-d', $start_date));
    $smarty->assign('end_date',     local_date('Y-m-d', $end_date));
	//	/* 显示页面 */
    assign_query_info();
    $smarty->display('admin_role_manage_list.htm');
   //函数如下，仅作为参考

}elseif ($_REQUEST['act'] == 'query')
{
    $card_list = card_list();
 
    $smarty->assign('card_list',   $card_list['list']);
	$smarty->assign('ur_here',     "数据平台用户管理");
	$smarty->assign('action_link', array('href'=>'admin_role_manage.php?act=add', 'text' => $_LANG['admin_add']));
	$smarty->assign('select_list',   select_list());
    $smarty->assign('filter',       $card_list['filter']);
    $smarty->assign('record_count', $card_list['record_count']);
    $smarty->assign('page_count',   $card_list['page_count']);
	$smarty->assign('start_date',   local_date('Y-m-d', $start_date));
    $smarty->assign('end_date',     local_date('Y-m-d', $end_date));
 
	make_json_result($smarty->fetch('user_phone_list.htm'), '', array('filter' => $card_list['filter'], 'page_count' => $card_list['page_count']));
}
/*------------------------------------------------------ */
//-- 删除管理员
/*------------------------------------------------------ */
elseif($_REQUEST['act'] == 'remove')
{
	$id   = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : '';
	if($id)
	{
	  $sql = "DELETE FROM ".$GLOBALS['ecs']->table('admin_role') ." WHERE `user_id` = '$id'";
	  $db->query($sql);
	  header("Location: admin_role_manage.php"); 
	  exit;
	}
}
/*------------------------------------------------------ */
//-- 添加管理员页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 检查权限 */
    admin_priv('admin_manage');
	$priv_str =array();

     /* 模板赋值 */
    $smarty->assign('ur_here',     $_LANG['admin_add']);
	$smarty->assign('priv_str',  $priv_str);
    $smarty->assign('action_link', array('href'=>'admin_role_manage.php?act=list', 'text' => $_LANG['admin_list']));
    $smarty->assign('form_act',    'insert');
    $smarty->assign('action',      'add');
    $smarty->assign('select_list',  select_list());

    /* 显示页面 */
    assign_query_info();
    $smarty->display('admin_role_info.htm');
}
/*------------------------------------------------------ */
//-- 添加管理员的处理
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert')
{
    admin_priv('admin_manage');
    if($_POST['token']!=$_CFG['token'])
    {
         sys_msg('add_error', 1);
    }
    /* 判断管理员是否已经存在 */
    if (!empty($_POST['user_name']))
    {
        $is_only = $exc->is_only('user_name', $_POST['user_name']);

        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['user_name_exist'], $_POST['user_name']), 1);
        }
    }

    /* Email地址是否有重复 */
    if (!empty($_POST['email']))
    {
        $is_only = $exc->is_only('email', stripslashes($_POST['email']));

        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['email_exist'], stripslashes($_POST['email'])), 1);
        }
    }

    /* 获取添加日期及密码 */
    $add_time = gmtime();
    
    $password  = md5($_POST['password']);
	//获取复选框值
	$str_tag = "";
	$frm_tag = $_POST['action_list'];
	for($i=0;$i<count($frm_tag);$i++){
	  //echo $frm_tag[$i]."<br>";
	  if($i==0)
	   $str_tag = $frm_tag[$i];
	  else
	   $str_tag = $str_tag.",".$frm_tag[$i];
	}
    $action_list = $str_tag;



    $sql = "INSERT INTO ".$ecs->table('admin_role')." (user_name, email, password, add_time, action_list) ".
           "VALUES ('".trim($_POST['user_name'])."', '".trim($_POST['email'])."', '$password', '$add_time', '$action_list')";

    $db->query($sql);


    /* 记录管理员操作 */
    admin_log($_POST['user_name'], 'add', 'admin_role_manage');
	
	/* 提示信息 */
    $link[] = array('text' => $_LANG['back_admin_list'], 'href'=>'admin_role_manage.php?act=list');  
	
	sys_msg($_LANG['add'] . "&nbsp;" .$_POST['user_name'] . "&nbsp;" . $_LANG['action_succeed'],0, $link);
 }
 
/*------------------------------------------------------ */
//-- 编辑管理员信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    /* 不能编辑demo这个管理员 */
    if ($_SESSION['admin_name'] == 'demo')
    {
       $link[] = array('text' => $_LANG['back_list'], 'href'=>'admin_role_manage.php?act=list');
       sys_msg($_LANG['edit_admininfo_cannot'], 0, $link);
    }

    $_REQUEST['id'] = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

    /* 查看是否有权限编辑其他管理员的信息 */
    if ($_SESSION['admin_id'] != $_REQUEST['id'])
    {
        admin_priv('admin_role_manage');
    }

    /* 获取管理员信息 */
    $sql = "SELECT user_id, user_name, email, password, action_list FROM " .$ecs->table('admin_role').
           " WHERE user_id = '".$_REQUEST['id']."'";
    $user_info = $db->getRow($sql);

    /* 获得该管理员的页面权限数组 */
    $priv_str =array();
	$priv_str = explode(',',$user_info['action_list']);
	//var_dump($priv_str);exit;
	//echo($priv_str);exit;

    /* 模板赋值 */
    $smarty->assign('ur_here',     $_LANG['admin_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['admin_list'], 'href'=>'admin_role_manage.php?act=list'));
    $smarty->assign('user',        $user_info);
	$smarty->assign('priv_str',  $priv_str);
	$smarty->assign('select_list',  select_list());

    
    $smarty->assign('form_act',    'update');
    $smarty->assign('action',      'edit');

    assign_query_info();
    $smarty->display('admin_role_info.htm');
}

/*------------------------------------------------------ */
//-- 更新管理员信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'update')
{

    /* 变量初始化 */
    $admin_id    = !empty($_REQUEST['id'])        ? intval($_REQUEST['id'])      : 0;
    $admin_name  = !empty($_REQUEST['user_name']) ? trim($_REQUEST['user_name']) : '';
    $admin_email = !empty($_REQUEST['email'])     ? trim($_REQUEST['email'])     : '';
    $ec_salt=rand(1,9999);
    $password = !empty($_POST['new_password']) ? ", password = '".md5(md5($_POST['new_password']).$ec_salt)."'"    : '';
	//获取复选框值
	$str_tag = "";
	$frm_tag = $_POST['action_list'];
	for($i=0;$i<count($frm_tag);$i++){
	  //echo $frm_tag[$i]."<br>";
	  if($i==0)
	   $str_tag = $frm_tag[$i];
	  else
	   $str_tag = $str_tag.",".$frm_tag[$i];
	}
	//var_dump($frm_tag);exit;
    //$action_list = $str_tag;
	$action_list = ', action_list = \''.$str_tag.'\'';
    if($_POST['token']!=$_CFG['token'])
    {
         sys_msg('update_error', 1);
    }
    if ($_REQUEST['act'] == 'update')
    {
        /* 查看是否有权限编辑其他管理员的信息 */
        if ($_SESSION['admin_id'] != $_REQUEST['id'])
        {
            admin_priv('admin_manage');
        }
        $g_link = 'admin_role_manage.php?act=list';
        $nav_list = '';
    }

    /* 判断管理员是否已经存在 */
    if (!empty($admin_name))
    {
        $is_only = $exc->num('user_name', $admin_name, $admin_id);
        if ($is_only == 1)
        {
            sys_msg(sprintf($_LANG['user_name_exist'], stripslashes($admin_name)), 1);
        }
    }

    /* Email地址是否有重复 */
    if (!empty($admin_email))
    {
        $is_only = $exc->num('email', $admin_email, $admin_id);

        if ($is_only == 1)
        {
            sys_msg(sprintf($_LANG['email_exist'], stripslashes($admin_email)), 1);
        }
    }

    //如果要修改密码
    $pwd_modified = false;

    if (!empty($_POST['new_password']))
    {

        /* 比较新密码和确认密码是否相同 */
        if ($_POST['new_password'] <> $_POST['pwd_confirm'])
        {
           $link[] = array('text' => $_LANG['go_back'], 'href'=>'javascript:history.back(-1)');
           sys_msg($_LANG['js_languages']['password_error'], 0, $link);
        }
        else
        {
            $pwd_modified = true;
        }
    }

    //更新管理员信息
    if($pwd_modified)
    {
        $sql = "UPDATE " .$ecs->table('admin_role'). " SET ".
               "user_name = '$admin_name', ".
               "email = '$admin_email' ".
               $action_list.
               $password.
               " WHERE user_id = '$admin_id'";
    }
    else
    {
        $sql = "UPDATE " .$ecs->table('admin_role'). " SET ".
               "user_name = '$admin_name', ".
               "email = '$admin_email' ".
               $action_list.
               "WHERE user_id = '$admin_id'";
    }

   $db->query($sql);
   /* 记录管理员操作 */
   admin_log($_POST['user_name'], 'edit', 'admin_role_manage');

   /* 如果修改了密码，则需要将session中该管理员的数据清空 */
   if ($pwd_modified)
   {
       //$sess->delete_spec_admin_session($_SESSION['admin_id']);
       $msg = $_LANG['edit_password_succeed'];
   }
   else
   {
       $msg = $_LANG['edit_profile_succeed'];
   }

   /* 提示信息 */
   $link[] = array('text' => strpos($g_link, 'list') ? $_LANG['back_admin_list'] : $_LANG['modif_info'], 'href'=>$g_link);
   sys_msg("$msg<script>parent.document.getElementById('header-frame').contentWindow.document.location.reload();</script>", 0, $link);

}

function card_list()
{
    //$start_date = $start_date;
	//$end_date = $end_date;
	/* 过滤条件 */
    $filter['keywords']   = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
    if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
    {
        $filter['keywords'] = json_str_iconv($filter['keywords']);
    }
	$filter['msg_type']   = isset($_REQUEST['msg_type']) ? intval($_REQUEST['msg_type']) : -1;
	$where = '';
    if ($filter['keywords'])
    {
        $where .= " AND user_name LIKE '%" . mysql_like_quote($filter['keywords']) . "%' ";
    }
    if ($filter['msg_type'] != -1)
    {
        $where .= " AND topic_id = '$filter[msg_type]' ";
    }
	

	$result = get_filter();
    if ($result === false)
    {
        /* 分页大小 */
        $filter = array();
 
        /* 记录总数以及页数 */
        $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('admin_role') ."WHERE user_id > 0" . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
 
        $filter = page_and_size($filter);
 
        /* 查询记录 */
        $sql = "SELECT *"."FROM ". $GLOBALS['ecs']->table('admin_role') .' WHERE user_id > 0 '. $where.' order by user_id ASC LIMIT ' . $filter['start'] . ',' . $filter['page_size'];
		//$sql = "SELECT *"."FROM ". $GLOBALS['ecs']->table('subscribe') .' WHERE subscribe_id > 0 order by subscribe_id ASC LIMIT ' . $filter['start'] . ',' . $filter['page_size'];
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
 
    $all = $GLOBALS['db']->getAll($sql);
	foreach($all as $key=>$val)
    {
        $all[$key]['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $val['add_time']);
		$all[$key]['last_login'] = local_date($GLOBALS['_CFG']['time_format'], $val['last_login']);

		$all[$key]['title'] = select_title($val['action_list']);
		//$all[$key]['title'] = 0;

    }  
 
    return array('list' => $all, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
function select_list()
{
	$sql = "SELECT topic_id , title FROM ". $GLOBALS['ecs']->table('topic')." WHERE `is_check` =1" ;
	$res = $GLOBALS['db']->getAll($sql);
	return $res;
}

function select_title($res)
{
  /* 获得该管理员的页面权限数组 */
  $priv_str = explode(',',$res);
  for($index=0;$index<count($priv_str);$index++)
  {
	 if($index == 0)
	 $title = $GLOBALS['db']->getOne("SELECT title FROM". $GLOBALS['ecs']->table('topic') ."WHERE topic_id = ".$priv_str[$index]) ;
	 else
	 $title = $title.",". $GLOBALS['db']->getOne("SELECT title FROM". $GLOBALS['ecs']->table('topic') ."WHERE topic_id = ".$priv_str[$index]) ;
  }
  return $title; 	
}

?>