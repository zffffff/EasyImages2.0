<?php
// 配置
$dir = '../i/'; 
$allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// 获取所有图片函数
function getImages($path, $exts) {
    $images = [];
    if (!is_dir($path)) return $images;
    
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array(strtolower($file->getExtension()), $exts)) {
            // 1. 获取预览图 URL (例如 /i/2025/12/26/z46nx3.jpg)
            $url = str_replace('../', '/', $file->getPathname());
            
            // 2. 核心修复：根据你的 show.php 源码对齐跳转公式
            // 参数必须是 img，路径必须完整且包含后缀
            $viewUrl = '/app/show.php?img=' . urlencode($url);
            
            // 3. 获取日期
            $date = date('Y-m-d', $file->getMTime());
            
            $images[] = [
                'url' => $url,
                'view_url' => $viewUrl, 
                'date' => $date,
                'time' => $file->getMTime()
            ];
        }
    }
    usort($images, function($a, $b) { return $b['time'] - $a['time']; });
    return $images;
}

$all_images = getImages($dir, $allowed_ext);
$total_count = count($all_images);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery | Roxyweal</title>
    <style>
        :root { --bg: #0a0a0a; --card: #111; --text-dim: #444; }
        body { background: var(--bg); color: #fff; font-family: -apple-system, sans-serif; margin: 0; padding: 20px; }
        .header { text-align: center; margin: 60px 0 40px; }
        h1 { font-weight: 200; letter-spacing: 10px; font-size: 2.5rem; margin: 0; }
        .stats-bar { margin-top: 20px; font-size: 0.85rem; color: var(--text-dim); letter-spacing: 1px; }
        .btn-upload { display: inline-block; margin-top: 20px; padding: 8px 20px; border: 1px solid #333; color: #888; text-decoration: none; font-size: 0.75rem; transition: 0.3s; border-radius: 20px; }
        .btn-upload:hover { border-color: #fff; color: #fff; background: #111; }
        
        .waterfall { column-count: 4; column-gap: 20px; width: 100%; max-width: 1400px; margin: 0 auto; }
        .item { 
            background: var(--card); margin-bottom: 25px; break-inside: avoid; border-radius: 6px; 
            overflow: hidden; opacity: 0; transform: translateY(20px); transition: all 0.6s ease;
            border: 1px solid #1a1a1a;
        }
        .item.show { opacity: 1; transform: translateY(0); }
        .item:hover { border-color: #555; }
        .item img { width: 100%; display: block; transition: 0.5s; }
        .item:hover img { filter: brightness(0.8); }
        
        /* 日期标签样式 */
        .item-info { padding: 10px 15px; font-size: 0.7rem; color: #333; display: flex; justify-content: space-between; transition: 0.3s; }
        .item:hover .item-info { color: #888; }

        #load-more { display: block; width: 180px; margin: 50px auto; padding: 12px; background: none; border: 1px solid #222; color: #555; cursor: pointer; letter-spacing: 2px; transition: 0.3s; }
        #load-more:hover { border-color: #666; color: #ccc; }
        #load-more.hidden { display: none; }
        .back-nav { position: absolute; top: 20px; left: 20px; font-size: 0.8rem; color: #333; text-decoration: none; }
        
        @media (max-width: 1024px) { .waterfall { column-count: 3; } }
        @media (max-width: 768px) { .waterfall { column-count: 2; } }
        @media (max-width: 480px) { .waterfall { column-count: 1; } }
    </style>
</head>
<body>
    <a href="https://roxyweal.work" class="back-nav">← PORTAL</a>
    <div class="header">
        <h1>GALLERY</h1>
        <div class="stats-bar">TOTAL: <?php echo $total_count; ?> IMAGES</div>
        <a href="../index.php" class="btn-upload">UPLOAD NEW IMAGE</a>
    </div>
    <div class="waterfall" id="gallery-container"></div>
    <button id="load-more">LOAD MORE</button>

    <script>
        const images = <?php echo json_encode($all_images); ?>;
        const container = document.getElementById('gallery-container');
        const btn = document.getElementById('load-more');
        let currentIndex = 0;
        const initialCount = 30;
        const stepCount = 20;

        function renderImages(count) {
            const nextBatch = images.slice(currentIndex, currentIndex + count);
            nextBatch.forEach((img, index) => {
                const div = document.createElement('div');
                div.className = 'item';
                // 确保使用 target="_blank"
                div.innerHTML = `
                    <a href="${img.view_url}" target="_blank">
                        <img src="${img.url}" loading="lazy">
                        <div class="item-info">
                            <span>IMAGE</span>
                            <span>${img.date}</span>
                        </div>
                    </a>
                `;
                container.appendChild(div);
                setTimeout(() => div.classList.add('show'), index * 60);
            });
            currentIndex += count;
            if (currentIndex >= images.length) btn.classList.add('hidden');
        }

        renderImages(initialCount);
        btn.onclick = () => renderImages(stepCount);
    </script>
</body>
</html>