#!/bin/bash

# ====================================================
# Roxyweal 站点一键同步脚本 (视觉增强 & 分流备份版)
# ====================================================

# 确保脚本遇到任何错误即停止执行
set -e

# 定义颜色
G='\033[0;32m' # 绿色
B='\033[0;34m' # 蓝色
R='\033[0;31m' # 红色
NC='\033[0m'   # 无颜色

BASE_DIR="/root/data/easyimage/EasyImages2.0"
cd $BASE_DIR

echo -e "${B}🚀 开始执行 Roxyweal 全网同步任务...${NC}"

# --- 1. GitHub 同步 (仅代码) ---
# 注意：因为设置了 .gitignore，图片目录 i/ 会被自动忽略
echo -e "\n${B}[1/3] 同步代码至 GitHub (已自动排除大图资产)...${NC}"
git add .
if git diff-index --quiet HEAD --; then
    echo -e "${G}✅ 代码无变动，跳过提交。${NC}"
else
    git commit -m "update: site maintenance $(date '+%Y-%m-%d %H:%M:%S')"
    if git push origin master; then
        echo -e "${G}✅ GitHub 代码推送成功！${NC}"
    else
        echo -e "${R}❌ GitHub 推送失败，请检查网络。${NC}"
        exit 1
    fi
fi

# --- 2. Cloudflare R2 同步 (全量备份) ---
# 注意：备份需要包含图片，所以不使用 gitignore 排除，但排除 .git 目录
echo -e "\n${B}[2/3] 正在同步全量数据至 R2 存储桶: easyimage-backup...${NC}"
if rclone sync $BASE_DIR r2:easyimage-backup \
    --exclude ".git/**" \
    --exclude "application" \
    --links \
    -P \
    --ignore-errors; then
    echo -e "${G}✅ R2 存储桶同步完成。${NC}"
else
    echo -e "${R}❌ R2 同步失败，请检查配置名 'r2' 是否正确。${NC}"
    exit 1
fi

# --- 3. Google Drive 同步 (全量备份) ---
echo -e "\n${B}[3/3] 正在同步全量镜像至 Google Drive...${NC}"
if rclone sync $BASE_DIR gdrive:EasyImagesBackup \
    --exclude ".git/**" \
    --exclude "application" \
    --links \
    -P \
    --ignore-errors; then
    echo -e "${G}✅ Google Drive 备份完成。${NC}"
else
    echo -e "${R}❌ Google Drive 同步失败，请检查配置名 'gdrive' 是否正确。${NC}"
    exit 1
fi

echo -e "\n${G}---------------------------------------"
echo -e "🎉 所有备份与同步任务已真正圆满完成！"
echo -e "---------------------------------------${NC}"