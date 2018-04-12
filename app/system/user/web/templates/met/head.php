<!--<?php
# MetInfo Enterprise Content Management System
# Copyright (C) MetInfo Co.,Ltd (http://www.metinfo.cn). All rights reserved.
defined('IN_MET') or exit('No permission');
$loginbg = $_M['config']['met_member_bgimage']?" background-image:url({$_M['config']['met_member_bgimage']});":'';
echo <<<EOT
--><!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8" />
<title>{$_M['tem_data']['title']}</title>
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
<meta name="generator" content="MetInfo"  data-variable="{$_M[url][site]}|{$_M[lang]}|{$classnow}|{$id}|{$class_list[$classnow][module]}|{$_M[config][met_skin_user]}" />
<link href="{$_M['url']['site']}favicon.ico" rel="shortcut icon" />
<link rel="stylesheet" type="text/css" href="{$_M['url']['pub']}bootstrap/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="{$_M['url']['pub']}font-awesome/css/font-awesome.min.css" />
<link rel="stylesheet" type="text/css" href="{$_M['url']['own_tem']}css/metinfo.css" />
</head>
<style>
body{background-color:{$_M['ui_config']['bodybgcolor']};background-image:url({$_M['ui_config']['bodybgimg']});}
.met-head,
.met-footer,
.met-member,
.login_index{color:{$_M['ui_config']['firstcolor']};}
[type=button],
[type=submit],
[type=reset],
[type=file],
.btn,
.sidebar .sidebar-box a.active{background: {$_M['ui_config']['secondcolor']} !important;}
.btn.btn-default,.met-member-safety .media i{background: none !important;color:{$_M['ui_config']['secondcolor']};}
.btn.btn-default:hover{background: {$_M['ui_config']['secondcolor']} !important;color: #fff;}
[type=button],
[type=submit],
[type=reset],
[type=file],
.btn{border-color: {$_M['ui_config']['secondcolor']} !important;}
.met-member{ background-color:{$_M['config']['met_member_bgcolor']};}
.login_index{ background-color:{$_M['config']['met_member_bgcolor']}; {$loginbg} }
</style>
<body>
<div class="met-head">
	<div class="container">
		<div class="row">
			<div class="col-xs-6 col-sm-6 logo">
				<ul class="list-none">
					<li><a href="{$_M['url']['site']}index.php?lang={$_M['lang']}" class="met-logo"><img src="{$_M['config']['met_logo']}" /></a></li>
<!--
EOT;
if($title){
echo <<<EOT
-->
					<li>{$title}</li>
<!--
EOT;
}
echo <<<EOT
-->
				</ul>
			</div>
			<div class="col-xs-6 col-sm-6 user-info">
				<ol class="breadcrumb pull-right">
<!--
EOT;
if($_M['user']){
echo <<<EOT
-->
				  <li>{$_M['user']['username']}</li>
				  <li><a href="{$_M['url']['login_out']}" title="{$_M['word']['memberIndex10']}">{$_M['word']['memberIndex10']}</a></li>
<!--
EOT;
}
echo <<<EOT
-->
				  <li><a href="{$_M['url']['site']}index.php?lang={$_M['lang']}" title="{$_M['word']['rehomepage']}">{$_M['word']['rehomepage']}</a></li>
				</ol>
			</div>
		</div>
	</div>
</div>
<!--
EOT;
# This program is an open source system, commercial use, please consciously to purchase commercial license.
# Copyright (C) MetInfo Co., Ltd. (http://www.metinfo.cn). All rights reserved.
?>