# neouma Eval

cd /opt/neouma/admin  
npm install

## 管理画面

### 開発用ローカル起動

cd /opt/neouma/admin  
npm run dev  
http://localhost:5173/

### STGでの更新ビルド


nvm install 16.20.1  
export NODE_OPTIONS="--max-old-space-size=1024";cd /opt/neouma/admin;npm run build;


### AmazonLinux2 更新のためか、npmがよく繋がらなくなる

$ nvm install 16.20.1

### ビルドするためのメモリが足らない

スワップ領域を確保したうえで
https://qiita.com/nakamto/items/5e78e9caceeff6b9e2b4

# 2GBのスワップファイルを作成（メモリが少ないのでbsが多いと失敗する）
sudo dd if=/dev/zero of=/swapfile bs=1M count=2048
# 読み書き権限の設定
sudo chmod 600 /swapfile
# スワップ領域のセットアップ
sudo mkswap /swapfile
# スワップ領域を有効化
sudo swapon /swapfile
# スワップ領域の確認
sudo swapon -s

export NODE_OPTIONS="--max-old-space-size=1024"

