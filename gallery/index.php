<?php
// 配置图片存放路径（相对于 EasyImage 根目录）
$dir = '../i/'; 
$allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// 递归获取所有图片
function getImages($path, $exts) {
    $images = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array(strtolower($file->getExtension()), $exts)) {
            $images[] = [
                'url' => str_replace('../', '/', $file->getPathname()),
                'time' => $file->getMTime()
            ];
        }
    }
    // 按修改时间倒序排列（最新在前）
    usort($images, function($a, $b) { return $b['time'] - $a['time']; });
    return $images;
}

$images = getImages($dir, $allowed_ext);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery | Roxyweal</title>
    <style>
        body { background: #0a0a0a; color: #fff; font-family: sans-serif; margin: 0; padding: 20px; }
        h1 { text-align: center; font-weight: 200; letter-spacing: 5px; margin: 40px 0; }
        
        /* 响应式瀑布流核心代码 */
        .waterfall {
            column-count: 4; /* 电脑端 4 列 */
            column-gap: 15px;
            width: 100%;
            max-width: 1600px;
            margin: 0 auto;
        }
        
        .item {
            background: #1a1a1a;
            margin-bottom: 15px;
            break-inside: avoid; /* 防止图片被切断 */
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .item:hover { transform: translateY(-5px); }
        
        .item img {
            width: 100%;
            display: block;
            cursor: zoom-in;
        }

        /* 手机端适配 */
        @media (max-width: 1024px) { .waterfall { column-count: 3; } }
        @media (max-width: 768px) { .waterfall { column-count: 2; } }
        @media (max-width: 480px) { .waterfall { column-count: 1; } }
        
        .back-btn { display: block; text-align: center; color: #666; text-decoration: none; margin-bottom: 20px; }
    </style>
</head>
<body>
    <a href="https://roxyweal.work" class="back-btn">← Back to Portal</a>
    <h1>GALLERY</h1>
    <div class="waterfall">
        <?php foreach ($images as $img): ?>
        <div class="item">
            <a href="<?php echo $img['url']; ?>" target="_blank">
                <img src="<?php echo $img['url']; ?>" loading="lazy">
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
