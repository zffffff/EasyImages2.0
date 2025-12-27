<?php
/**
 * Roxyweal 自定义登录背景
 * 说明：不再抓取 Bing 图片，固定使用 cache 目录下的指定图片
 */
include_once __DIR__ . '/function.php';
include_once APP_ROOT . '/config/config.php';

// 获取缓存文件夹路径 (通常是 i/cache/)
$path = APP_ROOT . $config['path'] . $config['delDir']; 

// 强制指定你想要的文件名
$filename = 'meishi.jpg'; 

if (file_exists($path . $filename)) {
    // 如果文件存在，直接输出
    header("Content-type: image/jpeg");
    exit(file_get_contents($path . $filename, true));
} else {
    // 如果图片意外丢失，提示 404
    header("HTTP/1.1 404 Not Found");
    exit("Background image ($filename) not found in cache folder.");
}