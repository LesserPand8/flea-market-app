# flea market app

## プロジェクトの概要

**サービス名**:coachtech フリマ<br>
**サービス概要**:ある企業が開発した独自のフリマアプリ<br>
**制作の背景と目的**:アイテムの出品と購入を行うためのフリマアプリを開発する<br>

## 環境構築

**Docker ビルド**

1. ターミナルで以下コマンドを実行<br>

```bash
git clone git@github.com:LesserPand8/flea-market-app.git
```

2. ターミナルで以下コマンドを実行<br>

```bash
cd flea-market-app
```

3. DockerDesktop アプリを立ち上げる
4. ターミナルで以下コマンドを実行<br>

```bash
docker-compose up -d --build
```

**Laravel 環境構築**

1. ターミナルで以下コマンドを実行<br>

```bash
docker-compose exec php bash
```

2. ターミナルで以下コマンドを実行<br>

```bash
composer install
```

3. ターミナルで以下コマンドを実行<br>

```bash
composer require laravel/cashier
```

4. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.env ファイルを作成（新規作成する場合のコマンド：cp .env.example .env）
5. 「.env」ファイルで以下の環境変数に修正する

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

6. 「.env」ファイルに以下の環境変数を追加する<br>
   値は、自身の Stripe の API キーを入れる

```text
STRIPE_PUBLIC_KEY={公開可能キー}
STRIPE_SECRET_KEY={シークレットキー}
STRIPE_WEBHOOK_SECRET={シークレットキー}
```

7. ターミナルで以下コマンドを実行<br>

```bash
exit
```

8. 「.env」ファイルを保存する<br>
   保存できない場合は、ターミナルで以下コマンドを実行<br>

```bash
sudo chmod -R 777 *
```

9. ターミナルで以下コマンドを実行<br>

```bash
docker-compose exec php bash
```

10. アプリケーションキーの作成

```bash
php artisan key:generate
```

11. マイグレーションの実行

```bash
php artisan migrate
```

12. シーディングの実行

```bash
php artisan db:seed
```

13. ターミナルで以下コマンドを実行<br>

```bash
exit
```

14. Stripe CLI の webhook リスニング機能の起動<br>

```bash
stripe listen --forward-to localhost:80/stripe/webhook
```

## PHPUnit でのテストについて

**テスト準備**

1. テスト用データベースの作成<br>
   パスワードは root と入力

```bash
docker-compose exec mysql bash
mysql -u root -p
create database test_database;
exit
exit
```

2. config ファイルの変更<br>
   config ディレクトリの中の database.php を開き、mysql の配列部分をコピーして以下に新たに mysql_test を作成する

```text
'mysql' => [
// 中略
],

'mysql_test' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => 'demo_test',
            'username' => 'root',
            'password' => 'root',
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
],
```

3. テスト用の.env ファイル作成

```bash
docker-compose exec php bash
cp .env .env.testing
```

4. 「.env.testing」ファイルの文頭部分にある APP_ENV と APP_KEY を以下のように修正する

```text
APP_ENV=test
APP_KEY=
```

5. 「.env.testing」ファイルにデータベースの接続情報を以下のように修正する

```text
DB_CONNECTION=mysql_test
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=demo_test
DB_USERNAME=root
DB_PASSWORD=root
```

6. アプリケーションキーの作成

```bash
php artisan key:generate --env=testing
```

7. キャッシュの削除

```bash
php artisan config:clear
```

8. マイグレーションの実行

```bash
php artisan migrate --env=testing
```

9. phpunit の編集<br>
   プロジェクトの直下の phpunit.xml を開き、DB_CONNECTION と DB_DATABASE を以下のように変更する

```text
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xsi:noNamespaceSchemaLocation="./vendor/phpunit/phpunit/phpunit.xsd"
bootstrap="vendor/autoload.php"
colors="true"
>
<testsuites>
    <testsuite name="Unit">
        <directory suffix="Test.php">./tests/Unit</directory>
    </testsuite>
    <testsuite name="Feature">
        <directory suffix="Test.php">./tests/Feature</directory>
    </testsuite>
</testsuites>
<coverage processUncoveredFiles="true">
    <include>
        <directory suffix=".php">./app</directory>
    </include>
</coverage>
    <php>
        <server name="APP_ENV" value="testing"/>
        <server name="BCRYPT_ROUNDS" value="4"/>
        <server name="CACHE_DRIVER" value="array"/>
-         <!-- <server name="DB_CONNECTION" value="sqlite"/> -->
-         <!-- <server name="DB_DATABASE" value=":memory:"/> -->
+         <server name="DB_CONNECTION" value="mysql_test"/>
+         <server name="DB_DATABASE" value="demo_test"/>
        <server name="MAIL_MAILER" value="array"/>
        <server name="QUEUE_CONNECTION" value="sync"/>
        <server name="SESSION_DRIVER" value="array"/>
        <server name="TELESCOPE_ENABLED" value="false"/>
    </php>
</phpunit>
```

※.env.testing にも Stripe の API キーを設定してください。

**テスト実行**

1. テストの実行をするため、以下コマンドと実施する

```bash
vendor/bin/phpunit
```

## URL

- 商品一覧画面：http://localhost/
- ログイン画面：http://localhost/login
- phpMyAdmin：http://localhost:8080/
- mailhog：http://localhost:8025/

## ER 図

![alt](erd.png)

## テストアカウント

name: user1  
email: user1@example.com  
password: testtest

---

name: user2  
email: user2@example.com  
password: testtest

---

name: user3  
email: user3@example.com  
password: testtest

---

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
- Stripe
