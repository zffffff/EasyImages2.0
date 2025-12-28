#!/bin/bash

# 定义颜色
G='\033[0;32m' # 绿色
B='\033[0;34m' # 蓝色
R='\033[0;31m' # 红色
NC='\033[0m'   # 无颜色

# 获取当前脚本所在目录
BASE_DIR="/root/data/easyimage/EasyImages2.0"
cd $BASE_DIR

echo -e "${B}🚀 开始执行 Roxyweal 全网同步任务...${NC}"

# --- 第一步：同步代码到 GitHub ---
echo -e "\n${B}[1/3] 同步代码至 GitHub (不含大图)...${NC}"
git add .
# 检查是否有文件变动
if git diff-index --quiet HEAD --; then
    echo -e "${G}✅ 代码无变动，跳过提交。${NC}"
else
    git commit -m "update: site maintenance $(date '+%Y-%m-%d %H:%M:%S')"
    if git push origin master; then
        echo -e "${G}✅ 代码推送成功！${NC}"
    else
        echo -e "${R}❌ GitHub 推送失败，请检查网络或冲突。${NC}"
    fi
fi

# --- 第二步：同步全量数据到 Cloudflare R2 ---
echo -e "\n${B}[2/3] 备份全量数据至 Cloudflare R2...${NC}"
# 使用 -P 显示实时进度
if rclone sync $BASE_DIR easyimage-r2:easyimage-backup -P --exclude-from .gitignore; then
    echo -e "${G}✅ R2 存储桶同步完成。${NC}"
else
    echo -e "${R}❌ R2 同步过程中出现错误。${NC}"
fi

# --- 第三步：同步全量数据到 Google Drive ---
echo -e "\n${B}[3/3] 备份全量数据至 Google Drive...${NC}"
if rclone sync $BASE_DIR gdrive-backup:EasyImageBackup -P --exclude-from .gitignore; then
    echo -e "${G}✅ Google Drive 备份完成。${NC}"
else
    echo -e "${R}❌ Google Drive 同步过程中出现错误。${NC}"
fi

echo -e "\n${G}✨ 所有任务已圆满完成！你的站点现在很安全。${NC}"