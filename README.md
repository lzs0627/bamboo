IQnote/Bamboo
=====================

## コーディングルール

* [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md))に従う
* 以下のコマンドでチェックすること

```
vendor/bin/phpcs --ignore=bootstrap.php src tests
```

* クラスの命名規則とファイル階層は [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md), [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)に従う
* 抽象化クラスは ``Abstract`` プレフィックスをクラス名につける
* あるデータベーステーブルを中心とした機能の実装は ``Model`` 名前空間にテーブル名のクラスを作成して実装する
* ユーザ認証などあるデータベーステーブルをベースにするには大きな範囲の場合 ``Service`` 名前空間にサービス名のクラスを作成して実装する。

## テスト

phpunitを使う

```
vendor/bin/phpunit
```

## app

アプリケーションフォルダ

## src

ライブラリフォルダ

## config

設定ファイルを格納するフォルダ

## www

document root

