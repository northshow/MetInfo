<?php defined('IN_MET') or exit('No permission'); ?>
<tag action='search.list'>
<li class="list-group-item">
	<h4><a href="{$v.url}" title='{$v.ctitle}' target="{$lang.met_listurlblank}" >{$v.title}</a></h4>
    <p>{$v.content}</p>
	<a class="search-result-link" href="{$v.url}" target="{$lang.met_listurlblank}">{$v.url}</a>
</li>
</tag>