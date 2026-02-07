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



# COACHTECH_FRIMA  


## 使用技術・実行環境  
- **Backend** : PHP 8.5.0 / Laravel 12.43.1
- **Frontend** : JavaScript (Vanilla JS), Tailwind CSS v4.0.0, Vite v7.0.7
- **Database** : MySQL 8.4.7
- **Infrastructure** : Laravel Sail (Docker環境)
- **External APIs** : Stripe API (決済)
- **Tooling** : Node.js v24.11.1, npm
- **Web Server** : Laravel Sail (PHP 8.5.0 Built-in Server) ※将来的にNginx導入予定

---  
## 環境構築

### 1. リポジトリのクローン  
- ターミナルでプロジェクトを作成する場所に移動
```bash
cd ...
```
- リポジトリのクローン  
```bash
#リポジトリのクローン
git clone git@github.com:haruki-saitou/coachtech-frima.git
cd coachtech-frima
```  
  
### 2.Laravel環境構築  
```bash
#`.env`をコピー
cp .env.example .env
```
テキストエディタでプロジェクトを開く  
```bash
code .
```  
下記の内容に`.env`の環境変数を変更  
```bash
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```  
#### `.env`の以下の項目を環境に合わせて変更してください。   
`WWWGROUP=ユーザーID`   
`WWWUSER=グループID`  
- ユーザーID（WWWUSER）を調べる  
```Bash
#ユーザーID（WWWUSER）を調べる
id -u
```  
- グループID（WWWGROUP）を調べる  
```Bash
#グループID（WWWGROUP）を調べる
id -g
```
  
---  
### 3.Sail本体のインストール（初回必須）  
クローン直後は vendor ディレクトリがないため、  
一時的なコンテナを使用して依存関係を解消します。  
```Bash
#一時的なコンテナを作成とSailのインストール
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```
> [!TIP]
※フォルダの中に vendor という名前のフォルダが新しくできているか確認してください。  
※ .env ファイル内の APP_URL が http://localhost になっていることを確認してください。  
  
---  
### 4.コンテナの起動と初期化
  
> ※Apple Silicon (M1/M2/M3) 及び Intel Mac/Windows の両方に対応。
  
Dockerコンテナをバックグラウンドで起動。  
```bash
#Docker起動
./vendor/bin/sail up -d
```  
アプリの初期設定を一括で行います。(キー生成・リンク作成・DB構築)  
```bash
#キー生成・リンク作成・DB構築
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan storage:link
./vendor/bin/sail artisan migrate:fresh --seed
```  
   
---  
### 5.フロントエンドのライブラリ（Tailwindなど）をインストール  
```bash
#ライブラリをインストール
./vendor/bin/sail npm install
```  
CSS/JavaScriptをビルド  
```bash
#ライブラリをビルド
./vendor/bin/sail npm run build
```  
```bash
#ライブラリの起動
./vendor/bin/sail npm run dev
```

> [!IMPORTANT]
※ `./vendor/bin/sail npm run dev` を実行しているターミナルは、閉じずにそのままにしておいてください。  
  
---  
### 6.メール認証の設定について  
開発環境でのメールテストには、Mailtrapを使用しています。  

「事前準備」  
1. [Mailtrap](https://mailtrap.io/ja/)にログインする。
> [!TIP]
※別タブで開くことを推奨します。
Macなら Command、Windowsなら Ctrl キーを押しながらクリックすると、
このページを閉じずに別タブで開くことができます。
2. マイサンドボックスを開き、⚙️設定内の Code Samples で Laravel 9+
に変更して表示された内容をcopyをクリックしてコピーする。
3. 表示された以下の情報を、プロジェクトの `.env` ファイルに反映してください。   
`MAIL_MAILER=smtp`  
`MAIL_HOST`  
`MAIL_PORT`  
`MAIL_USERNAME`  
`MAIL_PASSWORD`  
これにより、新規登録時の認証メールが Mailtrap の管理画面上で確認できるようになります。  
  
※アカウントがない場合  
1. [Mailtrap](https://mailtrap.io/ja/)でアカウントを作成する。  
2. サンドボックスを選択。  
3. メールサンドボックスのテストを開始する。  
4. カスタムでLaravelを選択。  
5. Code Samplesのセレクターで「Laravel 9+」を選択します。  
> [!TIP]
※表示された内容をcopyをクリックしてコピーしてください。  
6. 表示された以下の情報を、プロジェクトの `.env` ファイルに反映してください。
> `MAIL_MAILER=smtp`  
`MAIL_HOST`  
`MAIL_PORT`  
`MAIL_USERNAME`  
`MAIL_PASSWORD`  
  
---  
### 7.Stripeの設定  
決済機能を有効にするため、Stripeダッシュボードから取得したAPIキーを設定します。  
「事前準備」  
1. [Stripeダッシュボード](https://dashboard.stripe.com/login)へログインします。
※ アカウントがない場合は作成してください。  
> [!TIP]
※別タブで開くことを推奨します。
Macなら Command、Windowsなら Ctrl キーを押しながらクリックすると、
このページを閉じずに別タブで開くことができます。  
  
2. ホーム画面の右側に表示されているAPIキー（標準キー）をそれぞれクリックしてコピーします。  
3. Stripeのテスト用APIキー(トークン)を `.env` にそれぞれ設定してください。  
  
| カラム名 | 設定キー |
|:---|:---|   
|公開可能キー | STRIPE_KEY | 
|シークレットキー | STRIPE_SECRET |   
  
```bash
# 公開可能キーを設定
STRIPE_KEY=pk_test_...
# シークレットキーを設定
STRIPE_SECRET=sk_test_...
```   
  
---  
### 8.Stripe Webhookの設定（決済状態の自動更新に必要）  
本プロジェクトでは、決済完了（カード・コンビニ等）を正確に検知するためにWebhookを使用しています。  
ローカル環境で動作確認を行うには、以下の手順で Stripe CLI を起動する必要があります。  

1. 新しくターミナルを開きます。プロジェクトディレクトリに移動。
```bash
cd ...
```  
2. [Stripe CLI](https://docs.stripe.com/stripe-cli) をインストールしてください。
> [!TIP]
※ 別タブで開くことを推奨します。
Macなら Command、Windowsなら Ctrl キーを押しながらクリックすると、
このページを閉じずに別タブで開くことができます。

> [!TIP]
※ Stripe CLIのインストール方法がわからない場合は、
**Macなら Homebrew、Windowsなら zipダウンロード** を選ぶのが最も安全です。
インストール後、ターミナルで stripe -v と打ってバージョンが表示されれば成功です。  

3. Homebrewの場合
```bash
brew install stripe/stripe-cli/stripe
```  
4. ターミナルでStripeにログインします。  
```Bash
#Stripeにログイン
stripe login
```
5. 実行すると Your pairing code is: xxxx-xxxx... と表示され、一時停止します。  
   次に、Enterキーを押してください。自動的にブラウザ（Stripeの管理画面）が開きます。   
   ブラウザに「ペアリングコードを承認しますか？」という画面が出るので、  
   ターミナルに表示されているコード Your pairing code is: の後のコードと  
   同じであることを確認し、[アクセスを許可する] ボタンをクリックしてください。    
   ターミナルに Done! The Stripe CLI is configured... と表示されれば完了です。  
  
5. Webhookの転送を開始します。  
```Bash
#Webhookの転送を開始
stripe listen --forward-to localhost/stripe/webhook
```
> [!IMPORTANT]
※ stripe listen を実行しているターミナルは、閉じずにそのままにしておいてください。  

    
ターミナルに表示された whsec_ で始まる署名シークレットを `.env` に追記してください。  
`
STRIPE_WEBHOOK_SECRET=whsec_...
`   
> [!IMPORTANT]
※stripe listen を実行している間のみ、決済後の「Sold」状態への自動更新が機能します。  
※確認の為 `.env` は gitignore に含まれているか必ず確認してください。  


新しいターミナルを開きます。  
プロジェクトに移動
```bash
cd ...
```  
  
設定を変更したので、`.env`を保存後に設定を反映させるため、今開いた**新しいターミナル**で以下のコマンドを打ってください。  
```bash
#設定変更を反映(キャッシュクリア)
./vendor/bin/sail artisan config:clear
```  
※キャッシュをクリアしたので、そのままブラウザで動作確認してください。  

  
---   
## テストケース  
本プロジェクトでは、テストケース一覧の要件に基づいた全16項目の自動テストを実装しています。  
  
**テストの実行方法**  
```Bash
#全16項目の自動テストを実装
./vendor/bin/sail artisan test
```
  
### テストケース一覧
設計書に基づいた全16項目の自動テストを実装しています。  
機能テスト18個  
システム起動確認用テスト1個
  
| テストファイル | 対応ID | 検証内容の要約 |
| :--- | :--- | :--- |
| **AuthTest** | 1, 2, 3, 16 | 会員登録・ログイン・ログアウト・メール認証 |
| **ExhibitionTest** | 15 | 商品出品（画像保存・複数カテゴリ・バリデーション） |
| **ProductTest** | 4, 5, 6, 7, 8, 9 | 商品一覧・検索・詳細・いいね・コメント |
| **ProfileTest** | 14 | プロフィール更新処理 |
| **PurchaseTest** | 10, 11, 12 | 購入確定・支払い方法選択・配送先連動 |
| **UserTest** | 13, 14, 16 | マイページ表示・初期値保持・メール認証フロー |
| **ExampleTest** | - | システム起動確認用テスト |
  
---  
## 動作確認フロー  
  
### 画像の表示と管理について  
本プロジェクトでは、Modelアクセサ（`image_url`）により、以下の優先順位で画像を自動判別して表示します。  
  
1. **外部URL**: `http` で始まる場合はそのまま表示（S3等）。
2. **初期サンプル**: `images/sample/` で始まる場合は `public` フォルダから表示。
3. **ユーザー投稿**: 上記以外は `storage` フォルダ（シンボリックリンク経由）から表示。
4. **未登録時**: 画像がない場合は `public/images/no-image.png` を表示。  
  
| 種類 | 物理パス | DB保存値の例 |
| :--- | :--- | :--- |
| **初期サンプル** | `public/images/sample/` | `images/sample/airPods.jpg` |
| **ユーザー投稿** | `storage/app/public/` | `abc123.png` |  
    
### ディレクトリ構造（主要部分）  
```text
.
├── public/
│   ├── images/
│   │   ├── COACHTECH.png  <-- ロゴ等の共通素材
│   │   └── sample/         <-- 【重要】初期データ用画像（Git管理対象）
│   │       ├── airPods.jpg
│   │       └── ...
│   └── storage -> ...     <-- ユーザー投稿画像へのリンク
└── storage/
    └── app/
        └── public/        <-- 実際にユーザーがアップロードした画像の保存先
```
  
|画像の種類|保存場所|役割|
|:---|:---|:---|
|初期サンプル|public/images/sample/|php artisan db:seed で入るテスト用画像|
|ユーザー投稿|storage/app/public/|出品機能やプロフィールで保存される画像|  
  
> [!TIP]
> **もし商品画像が表示されない場合**
> 1. `public/storage` というショートカット（シンボリックリンク）があるか確認してください。
> 2. 無い場合は、`./vendor/bin/sail artisan storage:link` を実行してください。
> 3. 初期データの画像が表示されない場合は、画像ファイルが public/images/sample/ に存在するか確認してください。
> 4. 検証ツールを開いて画像のパスを確認してください。  
    
### テスト用ログイン情報  
- email :
```bash
test@example.com
```
- password : 
```bash
password
```
> ※ migrate:fresh --seed 実行後に利用可能になります。  

1. `http://localhost/register` で新規会員登録を行う。
2. `Mailtrap` に届く認証メール内のリンクをクリックする。
3. 自動でプロフィール設定画面へ移動することを確認後、「画像」、「郵便番号」、「住所」、「建物名」を登録する。
4. 商品を出品し、画像が表示されるか確認する。
5. Stripeのテスト
カード番号 : 4242 4242 4242 4242  
有効期限 : 未来の日付であれば問題ありません。
cvc : 適当な番号で問題ありません。
  
---  
## 一覧表示仕様
- 取得方式：JavaScript (fetch API) による無限スクロール
- 読み込み単位：1ページ 20件 (paginate(20))
      
---  
## 開発環境　  
MacBook Air M4を使用して開発。  
[**認証**]  
  
- 会員登録画面: http://localhost/register  
- ログイン画面: http://localhost/login  
- メール認証誘導画面: http://localhost/email/verify
  
[**商品**]
  
- 商品一覧画面（トップ画面）: http://localhost/  
- 商品詳細画面: http://localhost/item/{item_id}  
- 商品出品画面: http://localhost/sell
  
[**購入**]
  
- 商品購入画面: http://localhost/purchase/{item_id}  
- 送付先住所変更画面: http://localhost/purchase/address/{item_id}?payment_method=
  
[**ユーザー**]
  
- プロフィール画面: http://localhost/mypage  
- プロフィール編集画面（設定画面）: http://localhost/mypage/profile
  
[**ツール**]
  
- phpMyAdmin: http://localhost:8080/  

    
---  
## テーブル設計  
※全体設計として、ER図に基づきリレーションを構成しています。
- Users(1) : Products(0または多)
- Users(1) : Comments(0または多)
- Users(1) : Likes(0または多)
- Products(1) : Orders(0または1)
- Products(多) : Conditions(1)
- Products(1) : Comments(0または多)
- Products(1) : Likes(0または多)
- Products(1) : category_product(1または多)
- category_product(多) : Categories(1)
  
---  

  
### Usersテーブル(ユーザー情報)

| カラム名            | 論理名 | 型                | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY    |
| :--- | :--- | :--- | :---: | :---: | :---: | :---: |
| id                 | ID    | bigint unsigned  | **PK**         |    -        | ◯        |    -            |
| name               | 氏名   | varchar(255)     |     -        |     -       | ◯        |    -            |
| email              | メールアドレス | varchar(255)     |     -        | **UQ**        | ◯        |   -             |
| email_verified_at  | メール認証日時 | timestamp     |     -        |    -        |   -       |   -             |
| password           | パスワード    | varchar(255)     |    -         |    -        | ◯        |  -              |
| image_path         | プロフィール画像パス | varchar(255)     |    -         |   -         |   -       |  -              |
| post_code          | 郵便番号      | varchar(255)     |    -         |   -         |   -       |    -            |
| address            | 住所         | varchar(255)     |   -          |  -          |   -       |    -            |
| building           | 建物名       | varchar(255)     |   -          |  -          |  -        |    -            |
| remember_token     | ログイン保持用トークン            |varchar(100)     |   -          |  -          | ◯        |     -           |
| created_at         | 作成日時            |timestamp        |  -           |   -         | ◯        |     -           |
| updated_at         | 更新日時            |timestamp        |  -           |   -         | ◯        |     -           |

  
### Productsテーブル(商品情報)

| カラム名            | 論理名 | 型                | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY    |
|:---|:---|:---|:---:|:---:|:---:|:---:|
| id                 | ID | bigint unsigned  | **PK**          |   -         | ◯        |    -            |
| user_id            | 出品者ID | bigint unsigned  |     -        |  -          | ◯        | users(id)      |
| condition_id       | 商品の状態ID | bigint unsigned  |   -          |  -          | ◯        | conditions(id) |
| name               | 商品名 | varchar(255)     |    -         |   -         | ◯        |    -            |
| price              | 価格 | unsigned integer |   -          |   -         | ◯        |   -             |
| brand_name         | ブランド名 | varchar(255)     |  -           | -           |   -       |  -              |
| description        | 商品説明 | text             |    -         |   -         | ◯        |     -           |
| image_path         | 商品画像パス | varchar(255)     |    -         |   -         | ◯        |    -            |
| is_sold            | 販売ステータス | boolean          |    -         |    -        | ◯        |    -            |
| created_at         | 作成日時          |timestamp        |  -           |  -          | ◯        |   -             |
| updated_at         | 更新日時          |timestamp        |  -           |   -         | ◯        |   -             |

  
### Categoriesテーブル(カテゴリ情報)

| カラム名            | 論理名 | 型                | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY    |
|:---|:---|:---|:---:|:---:|:---:|:---:|
| id                 | ID | bigint unsigned  | **PK**         |     -       | ◯        |     -           |
| name               | カテゴリー名 | varchar(255)     |   -          |    -        | ◯        |   -             |
| created_at         | 作成日時 | timestamp        |     -        |    -        | ◯        |   -             |
| updated_at         | 更新日時 | timestamp        |     -        |    -        | ◯        |   -             |

  
### Commentsテーブル(コメント情報)

| カラム名            | 論理名 | 型                | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY    |
|:---|:---|:---|:---:|:---:|:---:|:---:|
| id                 | ID | bigint unsigned  | **PK**          |    -        | ◯        |    -            |
| user_id            | コメントユーザーID | bigint unsigned  |   -          |    -        | ◯        | users(id)      |
| product_id         | コメント商品ID | bigint unsigned  |     -        |    -        | ◯        | products(id)   |
| comment            | コメント | varchar(255)     |      -       |     -       | ◯        |     -           |
| created_at         | 作成日時        | timestamp        |     -        |  -          | ◯        |   -             |
| updated_at         | 更新日時        | timestamp        |     -        |  -          | ◯        |   -             |

  
### Conditionsテーブル(商品状態情報)

| カラム名            | 論理名 | 型                | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY    |
|:---|:---|:---|:---:|:---:|:---:|:---:|
| id                 | ID | bigint unsigned  | **PK**          |   -         | ◯        |    -            |
| name               | 商品状態 | varchar(255)     |     -        |  -          | ◯        |   -             |
| created_at         | 作成日時        | timestamp        |    -         |  -          | ◯        |     -           |
| updated_at         | 更新日時        | timestamp        |    -         |  -          | ◯        |     -           |

  
### Ordersテーブル(購入履歴情報)

| カラム名            | 論理名 | 型                | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY    |
|:---|:---|:---|:---:|:---:|:---:|:---:|
| id                 | ID | bigint unsigned  | **PK**          |    -        | ◯        |    -            |
| user_id            | 購入者ID | bigint unsigned  |     -        |   -         | ◯        | users(id)      |
| product_id         | 購入商品ID | bigint unsigned  |   -          |  -          | ◯        | products(id)   |
| payment_method     | 支払い方法 | varchar(255)     |   -          |  -          | ◯        |   -             |
| post_code          | 配送先郵便番号 | varchar(255)     |  -           | -           | ◯        |    -            |
| address            | 配送先住所 | varchar(255)     |   -          |   -         | ◯        |     -           |
| building           | 配送先建物名 | varchar(255)     |  -           |  -          | ◯        |    -            |
| created_at         | 作成日時           | timestamp        |   -          |  -          | ◯        |   -             |
| updated_at         | 更新日時           | timestamp        |   -          |  -          | ◯        |   -             |

  
### Likesテーブル(お気に入り情報)

| カラム名            | 論理名 | 型                | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY    |
|:---|:---|:---|:---:|:---:|:---:|:---:|
| id                 | ID | bigint unsigned  | **PK**          |    -        | ◯        |  -              |
| user_id            | お気に入りしたユーザーID | bigint unsigned  |    -         |  -          | ◯        | users(id)      |
| product_id         | お気に入りした商品ID | bigint unsigned  |     -        |   -         | ◯        | products(id)   |
| created_at         | 作成日時                  | timestamp        |     -        |     -       | ◯        |     -           |
| updated_at         | 更新日時                  | timestamp        |     -        |     -       | ◯        |     -           |
  
  
### category_productテーブル(カテゴリ・商品中間テーブル）

| カラム名             | 論理名| 型                | PRIMARY KEY | UNIQUE KEY | NOT NULL | FOREIGN KEY    |
|:---|:---|:---|:---:|:---:|:---:|:---:|
| id                 | ID | bigint unsigned  | **PK**          |  -          | ◯        |     -           |
| category_id        | カテゴリーID | bigint unsigned  |    -         |    -        | ◯        | categories(id)  |
| product_id         | 商品ID | bigint unsigned  |     -        |        -    | ◯        | products(id)   |
| created_at         | 作成日時 | timestamp        |     -        |        -    | ◯        |     -           |
| updated_at         | 更新日時 | timestamp        |      -       |         -   | ◯        |     -           |

  
---  
## ER図  
![ER図](images/er_diagram.png)  
  
---  
## トラブルシューティング
`./vendor/bin/sail npm run dev` 実行時に  
`Cannot find module @rollup/rollup-linux-arm64-gnu` と出る場合  
Docker(Sail)環境とローカル環境の依存関係の不整合が原因です。  
以下の手順で依存関係をリセットしてください。  
  
**1. 念のため、現在動いているSailを停止させます**  
```bash
#現在動いているSailを停止
./vendor/bin/sail stop
```  
**2. 古い部品（フォルダ）と、設定の記録（ファイル）を削除します**  
> ※ Mac/Linuxのコマンドです。慎重に実行してください。  
```bash
#古い部品（フォルダ）と、設定の記録（ファイル）を削除
rm -rf node_modules package-lock.json
```  
**3. Sailをバックグラウンドで起動します**  
```bash
#Sailをバックグラウンドで起動
./vendor/bin/sail up -d
```   
**4. 改めて部品をインストールし直します**  
```bash
#再インストール
./vendor/bin/sail npm install
```  
**5. 再度、起動を試みます** 
```bash
#再起動
./vendor/bin/sail npm run dev
```  
   
