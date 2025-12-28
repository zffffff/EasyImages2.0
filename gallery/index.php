<?php
// 配置保持不变
$dir = '../i/'; 
$allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

function getImages($path, $exts) {
    $images = [];
    if (!is_dir($path)) return $images;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array(strtolower($file->getExtension()), $exts)) {
            $url = str_replace('../', '/', $file->getPathname());
            $viewUrl = '../app/show.php?img=' . urlencode($url);
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
        :root { --bg: #0a0a0a; --card: #111; --text-dim: #555; }
        body { background: var(--bg); color: #fff; font-family: -apple-system, sans-serif; margin: 0; padding: 20px; }
        .header { text-align: center; margin: 60px 0 40px; }
        h1 { font-weight: 200; letter-spacing: 10px; font-size: 2.5rem; margin: 0; }
        .stats-bar { margin-top: 20px; font-size: 0.85rem; color: var(--text-dim); letter-spacing: 1px; }
        .btn-upload { display: inline-block; margin-top: 20px; padding: 8px 20px; border: 1px solid #333; color: #888; text-decoration: none; font-size: 0.75rem; transition: 0.3s; border-radius: 20px; }
        .btn-upload:hover { border-color: #fff; color: #fff; background: #111; }
        
        /* 核心修复：横向 Flex 布局 */
        .waterfall { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 12px; 
            width: 100%; 
            max-width: 1600px; 
            margin: 0 auto; 
        }
        
        .item { 
            height: 280px; /* 统一行高 */
            flex-grow: 1; /* 自动填充行宽 */
            background: var(--card); 
            border-radius: 4px; 
            overflow: hidden; 
            opacity: 0; 
            transform: scale(0.95);
            transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1); 
            position: relative;
        }
        
        /* 解决最后一行图片过宽的问题 */
        .waterfall::after {
            content: "";
            flex-grow: 999;
        }

        .item.show { opacity: 1; transform: scale(1); }
        
        .item a { display: block; height: 100%; width: 100%; }
        
        .item img { 
            height: 100%; 
            width: 100%; 
            object-fit: cover; /* 裁剪填充 */
            transition: 0.5s; 
        }
        
        .item:hover img { filter: brightness(0.7); transform: scale(1.05); }
        
        .item-info { 
            position: absolute; bottom: 0; left: 0; right: 0;
            padding: 10px; background: linear-gradient(transparent, rgba(0,0,0,0.8));
            font-size: 0.65rem; color: #888; opacity: 0; transition: 0.3s;
        }
        .item:hover .item-info { opacity: 1; }

        #load-more { display: block; width: 180px; margin: 50px auto; padding: 12px; background: none; border: 1px solid #222; color: #555; cursor: pointer; letter-spacing: 2px; transition: 0.3s; border-radius: 4px; }
        #load-more:hover { border-color: #666; color: #ccc; }
        #load-more.hidden { display: none; }
        
        .back-nav { position: absolute; top: 20px; left: 20px; font-size: 0.8rem; color: #333; text-decoration: none; }
        
        /* 手机端高度调低 */
        @media (max-width: 768px) { .item { height: 180px; } }
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
                div.innerHTML = `
                    <a href="${img.view_url}" target="_blank">
                        <img src="${img.url}" loading="lazy">
                        <div class="item-info">
                            <span>${img.date}</span>
                        </div>
                    </a>
                `;
                container.appendChild(div);
                // 略微延迟实现优雅进场
                setTimeout(() => div.classList.add('show'), index * 50);
            });
            currentIndex += count;
            if (currentIndex >= images.length) btn.classList.add('hidden');
        }

        renderImages(initialCount);
        btn.onclick = () => renderImages(stepCount);
    </script>
</body>
</html>