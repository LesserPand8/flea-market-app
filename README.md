# flea market app

## 環境構築

**Docker ビルド**

1. `git clone git@github.com:LesserPand8/flea-market-app.git`
2. DockerDesktop アプリを立ち上げる
3. `docker-compose up -d --build`

**Laravel 環境構築**

1. `docker-compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.env ファイルを作成（新規作成する場合のコマンド：cp .env.example .env）
4. .env に以下の環境変数を追加

```text
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

MAIL_FROM_ADDRESS=example@example.com
MAIL_FROM_NAME="フリマアプリ"
```

5. アプリケーションキーの作成

```bash
php artisan key:generate
```

6. マイグレーションの実行

```bash
php artisan migrate
```

7. シーディングの実行

```bash
php artisan db:seed
```

## 使用技術(実行環境)

- PHP 8.3.0
- Laravel 8.83.27
- MySQL 8.0.26
- nginx 1.21.1
- Docker / Docker Compose
- phpMyAdmin
- Mailhog
- Node.js
- Composer

## ER 図

![alt](erd.png)

## URL

- 商品一覧画面：http://localhost/
- ログイン画面：http://localhost/login
- phpMyAdmin：http://localhost:8080/
- mailhog：http://localhost:8025/
