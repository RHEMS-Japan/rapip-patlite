<?php

namespace Fuel\Tasks;

use Patlite;

class PatliteDemo {

    public static function run() {
        // 全点灯3秒
        Patlite::action(2, 2, 2, 0, 3);
        // 全点滅5秒
        Patlite::action(1, 1, 1, 0, 5);
        // ビープ（ぴー)
        Patlite::action(0, 0, 0, 1, 2);
        // ビープ（ぴっぴっぴ)
        Patlite::action(0, 0, 0, 2, 2);
    }

}
