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
            // 获取相对路径用于显示，获取文件名用于 show.php
            $relative_path = str_replace('../', '/', $file->getPathname());
            $img_name = $file->getBasename('.' . $file->getExtension());
            
            $images[] = [
                'url' => $relative_path,
                'view_url' => '../show.php?i=' . basename($file->getPath(), 2) . basename($file->getPath()) . $file->getBasename(), 
                // 注意：EasyImage 的 show.php 传参逻辑视版本而定，通常是文件名或 ID
                // 这里我们统一使用 view_url 处理点击跳转
                'time' => $file->getMTime()
            ];
        }
    }
    usort($images, function($a, $b) { return $b['time'] - $a['time']; });
    return $images;
}

$all_images = getImages($dir, $allowed_ext);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery | Roxyweal</title>
    <style>
        :root { --bg: #0a0a0a; --card-bg: #111; --text: #888; }
        body { background: var(--bg); color: #fff; font-family: -apple-system, sans-serif; margin: 0; padding: 20px; }
        h1 { text-align: center; font-weight: 200; letter-spacing: 8px; margin: 50px 0; font-size: 2rem; }
        
        .waterfall {
            column-count: 4; column-gap: 20px;
            width: 100%; max-width: 1400px; margin: 0 auto;
        }

        .item {
            background: var(--card-bg);
            margin-bottom: 20px;
            break-inside: avoid;
            border-radius: 4px;
            overflow: hidden;
            opacity: 0; /* 初始透明，用于淡入动画 */
            transform: translateY(20px);
            transition: all 0.6s ease;
        }

        /* 淡入动画效果 */
        .item.show { opacity: 1; transform: translateY(0); }
        
        .item img { width: 100%; display: block; transition: filter 0.3s; }
        .item:hover img { filter: brightness(0.8); }

        /* 按钮样式 */
        #load-more {
            display: block; width: 200px; margin: 50px auto; padding: 15px;
            background: none; border: 1px solid #333; color: var(--text);
            cursor: pointer; letter-spacing: 2px; transition: all 0.3s;
            border-radius: 2px;
        }
        #load-more:hover { border-color: #666; color: #fff; }
        #load-more.hidden { display: none; }

        .back-btn { display: block; text-align: center; color: #444; text-decoration: none; font-size: 0.9rem; margin-top: 20px; }

        @media (max-width: 1024px) { .waterfall { column-count: 3; } }
        @media (max-width: 768px) { .waterfall { column-count: 2; } }
        @media (max-width: 480px) { .waterfall { column-count: 1; } }
    </style>
</head>
<body>
    <a href="https://roxyweal.work" class="back-btn">ROXYWEAL.WORK</a>
    <h1>GALLERY</h1>
    
    <div class="waterfall" id="gallery-container">
        </div>

    <button id="load-more">LOAD MORE</button>

    <script>
        // 将 PHP 数组转为 JS 数组
        const images = <?php echo json_encode($all_images); ?>;
        const container = document.getElementById('gallery-container');
        const btn = document.getElementById('load-more');
        
        let currentIndex = 0;
        const initialCount = 30; // 初始 30 张
        const stepCount = 20;    // 每次增加 20 张

        function renderImages(count) {
            const nextBatch = images.slice(currentIndex, currentIndex + count);
            
            nextBatch.forEach((img, index) => {
                const div = document.createElement('div');
                div.className = 'item';
                // 链接改为跳转到 show.php
                div.innerHTML = `
                    <a href="${img.view_url}">
                        <img src="${img.url}" loading="lazy">
                    </a>
                `;
                container.appendChild(div);
                
                // 延迟添加 show 类，实现错落有致的淡入感
                setTimeout(() => {
                    div.classList.add('show');
                }, index * 100);
            });

            currentIndex += count;
            
            // 如果加载完了，隐藏按钮
            if (currentIndex >= images.length) {
                btn.classList.add('hidden');
            }
        }

        // 初始化加载
        renderImages(initialCount);

        // 点击加载更多
        btn.onclick = () => {
            renderImages(stepCount);
        };
    </script>
</body>
</html>