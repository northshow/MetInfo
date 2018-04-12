<?php defined('IN_MET') or exit('No permission'); ?>
<?php
    $cid = 0;
    if($cid == 0){
        $cid = $data['classnow'];
    }
    $num = $c['met_news_list'];
    $order = "no_order";
    $news = load::sys_class('label', 'new')->get('news');
    $news->page_num = $num;
    $result = $news->get_list_page($cid, $data['page']);
    $sub = count($result);
     foreach($result as $index=>$v):
        $v['sub']      = $sub;
        $v['_index']   = $index;
        $v['_first']   = $index == 0 ? true:false;
        $v['_last']    = $index == (count($result)-1) ? true : false;
?>
<li class="media media-lg border-bottom1">
    <?php if($lang['news_imgok']){ ?>
	<div class="media-left">
		<a href="<?php echo $v['url'];?>" title="<?php echo $v['title'];?>" <?php echo $v['urlnew'];?>>
			<img class="media-object" src="<?php echo thumb($v['imgurl'],$c['met_newsimg_x'],$c['met_newsimg_y']);?>" alt="<?php echo $v['title'];?>" height='100'></a>
	</div>
<?php } ?>
	<div class="media-body">
		<h4>
			<a href="<?php echo $v['url'];?>" <?php echo $v['urlnew'];?> title="<?php echo $v['title'];?>" target='_self'><?php echo $v['title'];?></a>
		</h4>
		<p class="des font-weight-300">
			<?php echo $v['description'];?>
		</p>
		<p class="info font-weight-300">
			<span><?php echo $v['updatetime'];?></span>
			<span><?php echo $v['issue'];?></span>
			<span>
				<i class="icon wb-eye m-r-5 font-weight-300" aria-hidden="true"></i>
				<?php echo $v['hits'];?>
			</span>
		</p>
	</div>
</li>
<?php endforeach;?>