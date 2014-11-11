# rapip-patlite

## 概要

PATLITE社の[PHE-3FB](http://www.patlite.jp/product/phe_3fbe1.html)のコントロールを行うFuelPHPパッケージです  
パトライトのランプと警報音をFuelPHPにて制御できるようになります。

実績としてはRaspberry Pi(Raspbian)とESXi5上のゲストOS(Ubuntu)にて制御に成功しています

## ディレクトリ構造


	fuel/packages/patlite
	    ├── bootstrap.php
	    ├── classes
	    │   └── patlite.php
	    ├── config
	    │   └── patlite.php
	    ├── README.md
	    └── tasks
	        ├── patlitecmd.php
	        └── patlitedemo.php

## インストール方法
----------------

RS232C経由での制御には PHPMake\SerialPort (Gorilla)を利用させて頂きました  
Gorillaのインストールについては配布元Webサイトに従って下さい

[シリアル通信のためのPHP拡張 Gorilla](http://sandbox.n-3.so/Gorilla/)

本プログラムはFuelPHPのパッケージディレクトリに配置されることを期待しています
プロジェクトのpackagesディレクトリにpatliteとして展開してください

    $ git https://github.com/RHEMS-Japan/rapip-patlite.git patlite

必要であれば patlite/config/patlite.php を編集しRS232Cデバイス名を正しいものに設定して下さい  
デフォルトは "/dev/ttyUSB0" です

	return array(
	    // シリアルポートを指定
	    'serialport' => '/dev/ttyUSB0',
	);


パッケージを利用するには app/config/config.php の always_load へ追加するか呼び出し側のプロジェクトで予め Package::load() を呼び出す必要があります

	'always_load' => array(
	    'packages' = array(
	        'patlite',
	    ),
	)

または

    Package::load('patlite');

動作確認にはデモタスクを実行できます

    php oil r patlitedemo

## コマンドラインからの利用方法
--------------------------

oilコマンドで呼び出しができます

    $ php oil r patlitecmd [red] [orange] [green] [beep] [act_time]
    
		[red] 赤ランプを制御する 0=未制御, 1=点滅, 2=点灯
		[orange] 橙ランプを制御する 0=未制御, 1=点滅, 2=点灯
		[green] 緑ランプを制御する 0=未制御, 1=点滅, 2=点灯
		[beep] ブザー音を制御する 0=未制御, 1=ピー, 2=ピッピッピッ
		[act_time] 動作時間を制御する 0で無制限, 0以上でその秒数

赤点灯、橙と緑を点滅させる

    $ php oil r patlitecmd 2 1 1

全てのパトライト動作を停止する

    $ php oil r patlitecmd reset

赤と緑を点滅、橙を点灯しピッピッピとブザーを鳴らす。動作は5秒間

    $ php oil r patlitecmd 1 2 1 2 5

## Fuelプロジェクトからの利用方法
----------------------------


ランプとブザーを制御します

    Patlite::action($red, $orange, $green, $beep, $act_time);
    
        $red 赤ランプを制御する 0=未制御, 1=点滅, 2=点灯
        $orange 橙ランプを制御する 0=未制御, 1=点滅, 2=点灯
        $green 緑ランプを制御する 0=未制御, 1=点滅, 2=点灯
        $beep ブザー音を制御する 0=未制御, 1=ピー, 2=ピッピッピッ
        $act_time 動作時間を制御する 0で無制限, 0以上でその秒数のみ作動


全てのランプとブザーの動作を停止します

    Patlite::reset()

## 謝辞
------

Gorillaを開発して下さったoasynnoum氏とそのコミュニティに感謝致します  
そしてこのパトライトを買ったのに使わなくなって僕に寄付してくれた弊社 佐藤マイベルゲン玲に感謝します  

