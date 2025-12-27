#!/bin/bash

# ====================================================
# Roxyweal 站点一键同步脚本 (GitHub + R2 + GDrive)
# ====================================================

# 1. 进入项目根目录
cd /root/data/easyimage/EasyImages2.0/

echo "---------------------------------------"
echo "🚀 开始执行全网同步任务..."
echo "---------------------------------------"

# 2. 同步至 GitHub
echo "📦 [1/3] 正在提交代码至 GitHub..."
git add .
git commit -m "Auto sync at $(date '+%Y-%m-%d %H:%M:%S')"
git push
echo "✅ GitHub 同步完成！"

# 3. 同步至 Cloudflare R2
echo "☁️ [2/3] 正在同步镜像至 Cloudflare R2..."
rclone sync /root/data/easyimage/EasyImages2.0 r2:easyimage --exclude ".git/**"
echo "✅ R2 同步完成！"

# 4. 同步至 Google Drive
echo "💾 [3/3] 正在同步镜像至 Google Drive..."
rclone sync /root/data/easyimage/EasyImages2.0 gdrive:EasyImagesBackup --exclude ".git/**"
echo "✅ Google Drive 同步完成！"

echo "---------------------------------------"
echo "🎉 所有备份与同步任务已圆满完成！"
echo "---------------------------------------"
