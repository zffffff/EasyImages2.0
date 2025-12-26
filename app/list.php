<?php

/** 广场页面 - ROXYWEAL 定制版 */
require_once __DIR__ . '/header.php';

/** 顶部广告 */
if ($config['ad_top']) echo $config['ad_top_info'];
?>
<div class="row">
  <div class="col-md-12">
    <?php
    if (!$config['showSwitch'] && !is_who_login('admin')) : ?>
      <div class="alert alert-info">管理员关闭了预览哦~~</div>
      <?php exit(require_once __DIR__ . '/footer.php'); ?>
      <?php else :
      /* 限制GET浏览日期 */
      $listDate = $config['listDate'];                                                                
      $path =  date('Y/m/d/');                                                                          
      if (isset($_GET['date'])) {
        if ($_GET['date'] < date('Y/m/d/', strtotime("- $listDate day"))) {                             
          $path =  date('Y/m/d/');
          echo '
          <script>
            new $.zui.Messager("已超出浏览页数, 返回今日上传列表", {
            type: "info", 
            icon: "exclamation-sign" 
            }).show();
          </script>';
        } else {
          $path = $_GET['date'];                                                                        
        }
      }

      $path = preg_replace("/^d{4}-d{2}-d{2} d{2}:d{2}:d{2}$/s", "", trim($path));                      
      $keyNum = isset($_GET['num']) ? $_GET['num'] : $config['listNumber'];                             
      $keyNum = preg_replace("/[\W]/", "", trim($keyNum));                                              
      $fileType = isset($_GET['search']) ? '*.' . preg_replace("/[\W]/", "", $_GET['search'])  : '*.*'; 
      $fileArr = get_file_by_glob(APP_ROOT . config_path($path) .  $fileType, 'list');                  
      $allUploud = isset($_GET['date']) ? $_GET['date'] : date('Y/m/d/');
      $allUploud = get_file_by_glob(APP_ROOT . $config['path'] . $allUploud, 'number');                 
      $httpUrl = array('date' => $path, 'num' => getFileNumber(APP_ROOT . config_path($path)));         

      if ($config['hide_path']) {
        $config_path = str_replace($config['path'], '/', config_path($path));
      } else {
        $config_path = config_path($path);
      }

      if (empty($fileArr[0])) : ?>
        <div class="alert alert-danger">今天还没有上传的图片哟~~ <br />快来上传第一张吧~!</div>
      <?php else : ?>
        <ul id="viewjs">
          <div class="cards listNum">
            <?php foreach ($fileArr as $key => $value) {
              if ($key < $keyNum) {
                $relative_path = config_path($path) . $value;     
                $imgUrl = $config['domain'] . $relative_path;     
            ?>
                <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                  <div class="card">
                    <a href="show.php?img=<?php echo urlencode($relative_path); ?>" target="_blank">
                       <li><img src="<?php static_cdn(); ?>/public/images/loading.svg" data-image="<?php echo creat_thumbnail_by_list($imgUrl); ?>" data-original="<?php echo $imgUrl; ?>" alt="手机壁纸资源"></li>
                    </a>
                    
                    <div class="bottom-bar">
                      <a href="show.php?img=<?php echo urlencode($relative_path); ?>" target="_blank"><i class="icon icon-picture" data-toggle="tooltip" title="查看详情" style="margin-left:10px;"></i></a>
                      
                      <?php if (is_who_login('admin')) : ?>
                        <a href="#" class="copy" data-clipboard-text="<?php echo $imgUrl; ?>" data-toggle="tooltip" title="复制直链" style="margin-left:10px;"><i class="icon icon-copy"></i></a>
                        <a href="#" onclick="ajax_post('<?php echo $relative_path; ?>','recycle')" data-toggle="tooltip" title="回收文件" style="margin-left:10px;"><i class="icon icon-undo"></i></a>
                        <a href="#" onclick="ajax_post('<?php echo $relative_path; ?>')" data-toggle="tooltip" title="删除文件" style="margin-left:10px;"><i class="icon icon-trash"></i></a>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
            <?php
              }
            }
            ?>
          </div>
        </ul>
    <?php
      endif;
    endif;
    if ($config['ad_bot']) echo $config['ad_bot_info']; ?>
  </div>
  
  <div class="col-md-12" style="margin-bottom: 5em;">
    <hr />
    <div class="col-md-8 col-xs-12" style="padding-bottom:5px">
      <div class="btn-toolbar">
        <div class="btn-group">
          <a class="btn btn-danger btn-mini" href="?<?php echo http_build_query($httpUrl); ?>">当前<?php echo $allUploud; ?></a>
          <a class="btn btn-primary btn-mini" href="list.php">今日<?php echo get_file_by_glob(APP_ROOT . config_path() . '*.*', 'number'); ?></a>
          <?php
          for ($x = 1; $x <= $listDate; $x++)
            echo '<a class="btn btn-mini inline-block" href="?date=' . date('Y/m/d/', strtotime("-$x day"))  .  '">' . date('j号', strtotime("-$x day")) . '</a>';
          ?>
        </div>
      </div>
    </div>
    <div class="col-md-2 col-xs-7">
      <div class="btn-group">
        <a class="btn btn-mini" href="<?php echo '?' . http_build_query($httpUrl) . '&search=jpg'; ?>">JPG</a>
        <a class="btn btn-mini" href="<?php echo '?' . http_build_query($httpUrl) . '&search=png'; ?>">PNG</a>
        <a class="btn btn-mini" href="<?php echo '?' . http_build_query($httpUrl) . '&search=webp'; ?>">Webp</a>
      </div>
    </div>
    <div class="col-md-2 col-xs-5">
      <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="get">
        <div class="input-group">
          <input type="text" class="form-control form-date input-sm" name="date" value="<?php echo date('Y/m/d/'); ?>" readonly="readonly">
          <span class="input-group-btn">
            <button type="submit" class="btn btn-primary input-sm">按日期</button>
          </span>
        </div>
      </form>
    </div>
  </div>

  <link rel="stylesheet" href="<?php static_cdn(); ?>/public/static/EasyImage.css">
  <link rel="stylesheet" href="<?php static_cdn(); ?>/public/static/zui/lib/datetimepicker/datetimepicker.min.css">
  <script type="application/javascript" src="<?php static_cdn(); ?>/public/static/lazyload/lazyload.min.js"></script>
  <script type="application/javascript" src="<?php static_cdn(); ?>/public/static/zui/lib/datetimepicker/datetimepicker.min.js"></script>
  <script>
    // 懒加载
    var lazy = new Lazy({ delay: 300 });

    // 日期选择
    $(".form-date").datetimepicker({
      language:  "zh-CN",
      weekStart: 1,
      todayBtn: 1,
      autoclose: 1,
      todayHighlight: 1,
      startView: 2,
      minView: 2,
      forceParse: 0,
      pickerPosition: "top-right",
      format: "yyyy/mm/dd/",
      endDate: new Date()
    });
  </script>
</div>
<?php require_once __DIR__ . '/footer.php'; ?>