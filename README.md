# attendance-app  
  
Attendance モデルrests() メソッドで「1対多」の関係  
Rest モデル attendance() メソッドで「多対1」の逆引き  
Attendance マイグレーション rest_start/end を削除し、rest_duration（合計時間）だけ残した  
Rest マイグレーション attendance_id で親データと紐付け、終了時刻を nullable にしたことで、休憩中の状態を表現  
  
## Laravel をインストール  
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer create-project laravel/laravel .  

## コンテナを動かすための設定ファイルを生成します  
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    php artisan sail:install --with=mysql,redis,mailpit  

## `.env`修正　　 
APP_NAME=AttendanceApp
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE=Asia/Tokyo
APP_LOCALE=ja

**データベース設定（ここを MySQL 用に書き換えます）**
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=attendance_app
DB_USERNAME=sail
DB_PASSWORD=password

**セッション・キャッシュ設定**
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

**Redis設定（Sail用）**
REDIS_HOST=redis

**メール設定（Mailpit用）**
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025

**プロジェクト名の固定（末尾に追加）**
COMPOSE_PROJECT_NAME=attendance-app

## 起動  
./vendor/bin/sail up -d  
## テーブル作成　　 
./vendor/bin/sail artisan migrate  

### メール確認用画面	http://localhost:8025 を開く	Mailpitの画面が出る  

