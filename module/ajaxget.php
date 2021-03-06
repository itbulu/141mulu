<?php
if (!defined('IN_HANFOX')) exit('Access Denied');

require(CORE_PATH.'module/webdata.php');

$type = trim($_GET['type']);
/** check site */
if ($type == 'check') {
	$url = trim($_GET['url']);
	
	if (empty($url)) {
		exit('请输入网站域名！');
	} else {
		if (!is_valid_domain($url)) {
			exit('请输入正确的网站域名！');
		}
	}
			
	$query = $DB->query("SELECT web_id FROM ".$DB->table('website')." WHERE web_url='$url'");
	if ($DB->num_rows($query)) {
		echo('该域名已存在，请勿重复提交！');
	} else {
		echo('<a href="javascript: void(0);" onclick="getmeta(\''.$url.'\'); getrank(\''.$url.'\')">自动抓取&raquo;</a>');
	}
	$DB->free_result($query);	
}

/** category */
if ($type == 'category') {
	$key = $_GET['key'];
	$ids = explode(',', $key);
	$cid = array_pop($ids);
	$cid = intval($cid);
	
	$sql = "SELECT cate_id, cate_name FROM ".$DB->table('categories')." WHERE root_id='$cid' ORDER BY cate_id ASC";
	$categories = $DB->fetch_all($sql);
	if (!empty($categories) && is_array($categories)) {
		$temp = array();
		foreach ($categories as $row) {
			$temp[$row['cate_id']]	= $row['cate_name'];
		}
		unset($categories);
		
		echo json_encode($temp);
	}
}

/** crawl */
if ($type == 'crawl') {
	$url = trim($_GET['url']);
	if (empty($url)) {
		exit('请输入网站域名！');
	} else {
		if (!is_valid_domain($url)) {
			exit('请输入正确的网站域名！');
		}
	}
	
	$meta = get_sitemeta($url);	
	echo '<script type="text/javascript">';
	echo '$("#web_name").attr("value", "'.$meta['title'].'");';
	echo '$("#web_tags").attr("value", "'.$meta['keywords'].'");';
	echo '$("#web_intro").attr("value", "'.$meta['description'].'");';
	echo '</script>';
	unset($meta);
}

/** data */
if ($type == 'data') {
	$url = trim($_GET['url']);
	if (empty($url)) {
		exit('请输入网站域名！');
	} else {
		if (!is_valid_domain($url)) {
			exit('请输入正确的网站域名！');
		}
	}
	
	$ip = get_serverip($url);
	$grank = get_pagerank($url);
	$brank = get_baidurank($url);
	$srank = get_sogourank($url);
	$arank = get_alexarank($url);
	echo '<script type="text/javascript">';
	echo '$("#web_ip").attr("value", "'.$ip.'");';
	echo '$("#web_grank").attr("value", "'.$grank.'");';
	echo '$("#web_brank").attr("value", "'.$brank.'");';
	echo '$("#web_srank").attr("value", "'.$srank.'");';
	echo '$("#web_arank").attr("value", "'.$arank.'");';
	echo '</script>';
}
?>
