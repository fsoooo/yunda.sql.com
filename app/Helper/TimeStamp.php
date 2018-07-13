<?php

namespace App\Helper;

/**
 * 获取当前毫秒值公共类
 */

class TimeStamp
{
    /**
     * 获取毫秒值
     *
     * @return float
     */
    public static function getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
    }
}
