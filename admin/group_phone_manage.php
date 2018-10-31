<?php

/**
 * ECSHOP 手机预约用户
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

/* 权限判断 */
admin_priv('group_phone_manage');
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
	$smarty->assign('ur_here',     "团购预约用户");
    $smarty->assign('card_list',   $card_list['list']);
	$smarty->assign('select_list',   select_list());
    $smarty->assign('filter',       $card_list['filter']);
    $smarty->assign('record_count', $card_list['record_count']);
    $smarty->assign('page_count',   $card_list['page_count']);
    $smarty->assign('full_page',        1);
	$smarty->assign('start_date',   local_date('Y-m-d', $start_date));
    $smarty->assign('end_date',     local_date('Y-m-d', $end_date));
	//	/* 显示页面 */
    assign_query_info();
    $smarty->display('group_phone_list.htm');
   //函数如下，仅作为参考

}elseif ($_REQUEST['act'] == 'query')
{
    $card_list = card_list();
 
    $smarty->assign('card_list',   $card_list['list']);
	$smarty->assign('ur_here',     "团购预约用户");
	$smarty->assign('select_list',   select_list());
    $smarty->assign('filter',       $card_list['filter']);
    $smarty->assign('record_count', $card_list['record_count']);
    $smarty->assign('page_count',   $card_list['page_count']);
	$smarty->assign('start_date',   local_date('Y-m-d', $start_date));
    $smarty->assign('end_date',     local_date('Y-m-d', $end_date));
 
	make_json_result($smarty->fetch('group_phone_list.htm'), '', array('filter' => $card_list['filter'], 'page_count' => $card_list['page_count']));
}elseif($_REQUEST['act'] == 'remove')
{
	$id   = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : '';
	if($id)
	{
	  $sql = "DELETE FROM ".$GLOBALS['ecs']->table('subscribe_group') ." WHERE `subscribe_group_id` = '$id'";
	  $db->query($sql);
	  header("Location: group_phone_manage.php"); 
	  exit;
	}
}
/*------------------------------------------------------ */
//-- 编辑预约信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{

    $_REQUEST['id'] = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;


    /* 获取管理员信息 */
    $sql = "SELECT * FROM " .$ecs->table('subscribe_group').
           " WHERE subscribe_group_id = '".$_REQUEST['id']."'";
    $user_info = $db->getRow($sql);


    /* 模板赋值 */
    $smarty->assign('ur_here',     "预约用户管理");
    $smarty->assign('action_link', array('text' => $_LANG['admin_list'], 'href'=>'group_phone_manage.php?act=list'));
    $smarty->assign('user',        $user_info);
	//$smarty->assign('select_list',  select_list());

    
    $smarty->assign('form_act',    'update');
    $smarty->assign('action',      'edit');

    assign_query_info();
    $smarty->display('group_phone_info.htm');
}
/*------------------------------------------------------ */
//-- 更新预约信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'update')
{

    /* 变量初始化 */
    $subscribe_group_id    = !empty($_POST['id'])        ? intval($_POST['id'])   : 0;
    $is_check  = !empty($_POST['is_check']) ? intval($_POST['is_check']) : 0;
    $note = !empty($_POST['data'])     ? trim($_POST['data'])     : '';
	$reply_time = gmtime();

    if($is_check == 1)
	{
	  //更新预约信息
	  $sql = "UPDATE " .$ecs->table('subscribe_group'). " SET ".
			 "reply_time = '$reply_time',note = '$note', is_check = '$is_check' ".
			 " WHERE subscribe_group_id = '$subscribe_group_id'";
	 //var_dump($sql);exit;
	 $db->query($sql);
   }
   /* 记录管理员操作 */
   //admin_log($_POST['user_name'], 'edit', 'admin_role_manage');

    $g_link ="group_phone_manage.php?act=list";
    $msg = "信息更改成功！";

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
        $where .= " AND mobile_phone LIKE '%" . mysql_like_quote($filter['keywords']) . "%' ";
    }
    if ($filter['msg_type'] != -1)
    {
        $where .= " AND act_id = '$filter[msg_type]' ";
    }
	

	$result = get_filter();
    if ($result === false)
    {
        /* 分页大小 */
        $filter = array();
 
        /* 记录总数以及页数 */
        $sql = "SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('subscribe_group') ."WHERE subscribe_group_id > 0" . $where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);
 
        $filter = page_and_size($filter);
 
        /* 查询记录 */
        $sql = "SELECT *"."FROM ". $GLOBALS['ecs']->table('subscribe_group') .' WHERE subscribe_group_id > 0 '. $where.' order by subscribe_group_id ASC LIMIT ' . $filter['start'] . ',' . $filter['page_size'];
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
		$all[$key]['goods_name'] = $GLOBALS['db']->getOne("SELECT goods_name FROM". $GLOBALS['ecs']->table('goods') ."WHERE goods_id =".$val['group_id']);
		if($val['reply_time'])
		{$all[$key]['reply_time'] = local_date($GLOBALS['_CFG']['time_format'], $val['reply_time']);}
    }  
 
    return array('list' => $all, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
function select_list()
{
	$sql = "SELECT act_id , act_name FROM ". $GLOBALS['ecs']->table('goods_activity') ;
	$res = $GLOBALS['db']->getAll($sql);
	return $res;
}
?>