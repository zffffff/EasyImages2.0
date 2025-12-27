#!/bin/bash

# ====================================================
# Roxyweal 站点一键同步脚本 (专业加固版)
# ====================================================

# 确保脚本遇到任何错误即停止执行
set -e

cd /root/data/easyimage/EasyImages2.0/

echo "🚀 开始执行全网同步任务..."

# 1. GitHub 同步
echo "📦 [1/3] 提交代码至 GitHub..."
git add .
# 即使没有改动也允许继续执行，不触发 set -e 退出
git commit -m "Auto sync at $(date '+%Y-%m-%d %H:%M:%S')" || echo "没有检测到需要提交的代码改动"
git push || { echo "❌ GitHub 推送失败，请检查 Token 权限"; exit 1; }

# 2. Cloudflare R2 同步
echo "☁️ [2/3] 同步镜像至 Cloudflare R2..."
# 加上 --copy-links 解决你之前的 symlink 警告
rclone sync /root/data/easyimage/EasyImages2.0 r2:easyimage --exclude ".git/**" --copy-links || { echo "❌ R2 同步失败，请核对 Bucket 名称和 API 权限"; exit 1; }

# 3. Google Drive 同步
echo "💾 [3/3] 同步镜像至 Google Drive..."
rclone sync /root/data/easyimage/EasyImages2.0 gdrive:EasyImagesBackup --exclude ".git/**" --copy-links || { echo "❌ Google Drive 同步失败"; exit 1; }

echo "---------------------------------------"
echo "🎉 所有备份与同步任务已圆满完成！"
echo "---------------------------------------"