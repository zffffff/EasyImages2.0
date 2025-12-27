#!/bin/bash

# ====================================================
# Roxyweal 站点一键同步脚本 (排除故障版)
# ====================================================

# 确保脚本遇到任何错误即停止执行
set -e

cd /root/data/easyimage/EasyImages2.0/

echo "🚀 开始执行全网同步任务..."

# 1. GitHub 同步
echo "📦 [1/3] 提交代码至 GitHub..."
git add .
git commit -m "Auto sync at $(date '+%Y-%m-%d %H:%M:%S')" || echo "没有检测到需要提交的代码改动"
git push || { echo "❌ GitHub 推送失败"; exit 1; }

# 2. Cloudflare R2 同步
# 核心修正：显式排除坏掉的 application 链接，并使用 --links 而非 --copy-links
echo "☁️ [2/3] 正在同步至 R2 存储桶: easyimage-backup..."
rclone sync /root/data/easyimage/EasyImages2.0 r2:easyimage-backup \
    --exclude ".git/**" \
    --exclude "application" \
    --links \
    --ignore-errors || { echo "❌ R2 同步失败，请检查 R2 桶是否存在及 Token 权限"; exit 1; }

# 3. Google Drive 同步
echo "💾 [3/3] 正在同步镜像至 Google Drive..."
rclone sync /root/data/easyimage/EasyImages2.0 gdrive:EasyImagesBackup \
    --exclude ".git/**" \
    --exclude "application" \
    --links \
    --ignore-errors || { echo "❌ Google Drive 同步失败"; exit 1; }

echo "---------------------------------------"
echo "🎉 所有备份与同步任务已真正圆满完成！"
echo "---------------------------------------"