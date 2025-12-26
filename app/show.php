<?php
/** * ROXYWEAL 宽屏画廊布局 - 极简回归版 
 * 优化点：移除冗余硬编码导航，使用系统默认导航，极大化右侧文字
 */
require_once __DIR__ . '/header.php';

$relative_path = isset($_GET['img']) ? $_GET['img'] : '';
$imgUrl = $config['domain'] . $relative_path;

if (empty($relative_path)) {
    header("Location: list.php");
    exit;
}
?>

<style>
    /* 全局背景与基础设定 */
    body { 
        background-color: #000 !important; 
        color: #eee; 
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        margin: 0; padding: 0;
    }

    /* 适配系统默认导航栏的样式调整 */
    .navbar { margin-bottom: 0; border-radius: 0; border-bottom: 1px solid #222; }

    /* 主布局容器：左右分栏 */
    .gallery-flex-container {
        display: flex;
        min-height: calc(100vh - 60px); /* 减去顶部导航高度 */
        padding: 60px 80px;
        gap: 100px;
        align-items: center;
        justify-content: center;
    }

    /* 左侧：图片区域 */
    .gallery-visual { 
        flex: 0 1 auto; 
        max-width: 55%; 
        display: flex; 
        justify-content: flex-end; 
    }
    .gallery-visual img {
        max-height: 85vh; /* 图片适应屏幕高度 */
        width: auto;
        border-radius: 8px;
        box-shadow: 0 30px 100px rgba(0,0,0,1);
        border: 1px solid #222;
    }

    /* 右侧：文字区域 - 特大化处理 */
    .gallery-content {
        flex: 1;
        max-width: 600px;
        text-align: left;
    }

    .author-section { margin-bottom: 60px; }
    .label-meta { color: #555; font-size: 1rem; text-transform: uppercase; letter-spacing: 4px; font-weight: 600; }
    .display-name { font-size: 4.5rem; font-weight: 900; color: #fff; margin-top: 20px; display: block; line-height: 1; }

    .tag-group { display: flex; gap: 15px; margin-bottom: 70px; }
    .tag-pill { 
        background: #111; border: 1px solid #333; color: #ccc; 
        padding: 10px 25px; border-radius: 6px; font-size: 1.2rem; font-weight: 500;
    }

    /* 授权协议区：显著增强易读性 */
    .license-info {
        background: #080808;
        border-left: 6px solid #444;
        padding: 45px;
    }
    .license-info h4 { color: #fff; margin-top: 0; font-size: 1.6rem; margin-bottom: 25px; letter-spacing: 1px; }
    .license-info p { color: #999; font-size: 1.3rem; line-height: 2.2; margin-bottom: 0; }
    .license-info strong { color: #fff; font-weight: 700; border-bottom: 2px solid #555; }

    /* 移动端自适应 */
    @media (max-width: 1300px) {
        .gallery-flex-container { flex-direction: column; padding: 40px 30px; gap: 50px; }
        .gallery-visual { max-width: 100%; justify-content: center; }
        .gallery-content { max-width: 100%; }
        .display-name { font-size: 3rem; }
    }
</style>

<div class="gallery-flex-container">
    <div class="gallery-visual">
        <a href="<?php echo $imgUrl; ?>" target="_blank">
            <img src="<?php echo $imgUrl; ?>" alt="Roxyweal Digital Asset">
        </a>
    </div>

    <div class="gallery-content">
        <div class="author-section">
            <span class="label-meta">Created by</span>
            <span class="display-name">Roxyweal</span>
        </div>

        <div class="tag-group">
            <span class="tag-pill">#手机壁纸</span>
            <span class="tag-pill">#CC0 自由使用</span>
        </div>

        <div class="license-info">
            <h4>授权协议 / LICENSE</h4>
            <p>
                本作品采用 <strong>CC0 1.0 Universal</strong> 公共领域贡献协议释出。
                <br><br>
                您可以自由复制、修改、发行该作品，甚至用于商业目的，<strong>无需经过作者同意，无需支付费用</strong>。
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>