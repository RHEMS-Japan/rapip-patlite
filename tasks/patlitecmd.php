<?php

namespace Fuel\Tasks;

use Patlite;
use Patlite\PatliteException;
use Cli;

class PatliteCmd {

    public static function run($red = 0, $orange = 0, $green = 0, $beep = 0, $act_time = 0) {
        try {
            switch ($red) {
                case 'reset':
                    Patlite::reset();
                    break;
                default:
                    Patlite::action($red, $orange, $green, $beep, $act_time);
                    break;
            }
        } catch (PatliteException $e) {
            Cli::write($e->getMessage());
        }
    }

}
