<?php

namespace Patlite;

/**
 * PHPMake\SerialPort (Gorilla)
 * 
 * @link http://sandbox.n-3.so/Gorilla/
 */
use PHPMake\SerialPort;
use Config;

// 通信エラーによる例外
class PatliteException extends \FuelException {
    
}

/**
 * Patliteパッケージ
 * PHPMake\SerialPortを利用してPATLITE社 PHE-3FBを制御します
 * 
 * @version 0.1
 * @author zERobYTecODe <hac@rhems-japan.co.jp>
 */
class Patlite {

    /**
     * Patlite::bitsplit
     * 
     * 数値表現からビット配列へ分割
     * 
     * @static
     * @param integer $col 0=消灯,1=点灯,2=点滅
     * @return array 2ビットを表現する文字列を配列として返す
     */
    private static function bit_split($col) {
        $col1 = '0';
        $col2 = '0';
        switch ($col) {
            case 0: // 消灯
                break;
            case 1: // 点滅
                $col1 = '1';
                break;
            case 2: // 点灯
                $col2 = '1';
                break;
        }
        return array($col1, $col2);
    }

    /**
     * Patlite::build_command
     * 
     * パトライト向けにデータを構成する
     * 
     * @static
     * @param integer $onoff 1=ON, 2=OFF
     * @param integer $red 0=指定なし, 1=点滅, 2=点灯
     * @param integer $orange 0=指定なし, 1=点滅, 2=点灯
     * @param integer $green 0=指定なし, 1=点滅, 2=点灯
     * @param integer $beep 0=指定なし, 1=ピーピーピー, 2=ピピピピ
     * @return string コントロールコードのデータ
     */
    private static function build_command($onoff, $red, $orange, $green, $beep) {
        list($red1, $red2) = self::bit_split($red);
        list($orange1, $orange2) = self::bit_split($orange);
        list($green1, $green2) = self::bit_split($green);
        list($beep1, $beep2) = self::bit_split($beep);
        $dt1 = '0011' . $green1 . $orange1 . $red1 . $beep1;
        $dt2 = '0011' . $beep2 . $green2 . $orange2 . $red2;
        $cmd = ($onoff) ? '31' : '30';
        return hex2bin('403F3F' . $cmd . dechex(bindec($dt1)) . dechex(bindec($dt2)) . '21');
    }

    /**
     * Patlite::serial_open
     * 
     * RS232Cデバイスをオープンする
     * 
     * @static
     * @return SerialPort Object オープン済のシリアルポートオブジェクト
     */
    private static function serial_open() {
        Config::load('patlite', true);
        $serialport = Config::get('patlite.serialport');
        $port = new SerialPort();
        $port->open($serialport);
        $port->setBaudRate(SerialPort::BAUD_RATE_9600);
        $port->setFlowControl(SerialPort::FLOW_CONTROL_NONE);
        $port->setCanonical(false)->setVTime(1)->setVMin(0);
        return $port;
    }

    /**
     * Patlite::serial_close
     * 
     * RS232Cデバイスをクローズする
     * 
     * @static
     * @param SerialPort Object $port オープン済のシリアルポートオブジェクト
     */
    private static function serial_close($port) {
        if ($port->isOpen()) {
            $port->close();
        }
    }

    /**
     * Patlite::serial_output
     * 
     * RS232Cへデータを出力する
     * 
     * @static
     * @param object $port オープン済PHPMake\SerialPortオブジェクト
     * @param string $data 通信データ
     * @throws PatliteException パトライトとの通信エラー時
     */
    private static function serial_output($port, $data) {
        if ($port->isOpen()) {
            $port->write($data);
            $result = $port->read(1);
        } else {
            throw new PatliteException('SerialPort is not open.');
        }
        if ($result != hex2bin('06')) {
            throw new PatliteException('Bad command receive or timeout.');
        }
    }

    /**
     * Patlite::action
     * 
     * パトライトを作動させる
     * 
     * @static
     * @param integer $red 0=指定なし, 1=点滅, 2=点灯
     * @param integer $orange 0=指定なし, 1=点滅, 2=点灯
     * @param integer $green 0=指定なし, 1=点滅, 2=点灯
     * @param integer $beep 0=指定なし, 1=ピーピーピー, 2=ピピピピ
     * @param integer $act_time 0以上で作動時間, -1で時間指定なし
     */
    public static function action($red, $orange, $green, $beep = 0, $act_time = 0) {
        $port = static::serial_open();
        $data = static::build_command(true, $red, $orange, $green, $beep);
        static::serial_output($port, $data);
        if ($act_time > 0) {
            sleep($act_time);
            $data = hex2bin('403F3F303F3F21');
            static::serial_output($port, $data);
        }
        static::serial_close($port);
    }

    /**
     * Patlite::reset
     * 
     * パトライトをリセットする
     * 
     * @static
     */
    public static function reset() {
        $port = static::serial_open();
        $data = hex2bin('403F3F303F3F21');
        static::serial_output($port, $data);
        static::serial_close($port);
    }

}
