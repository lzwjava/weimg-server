<?php

/**
 * Created by PhpStorm.
 * User: lzw
 * Date: 16/1/30
 * Time: 下午2:29
 */
class ScoreHelper
{
    private function points($ups, $downs)
    {
        return $ups - $downs;
    }

    private function epoch_seconds($date)
    {
        $epoch = new DateTime('1970-1-1');
        $diff = $date->getTimestamp() - $epoch->getTimestamp();
        return $diff;
    }

    private function startEpoch()
    {
        $start = new DateTime('2016-1-30');
        return $this->epoch_seconds($start);
    }

    public function hot($ups, $downs, $date)
    {
        $p = $this->points($ups, $downs);
        $order = log(max(abs($p), 1), 10);
        if ($p > 0) {
            $sign = 1;
        } else if ($p < 0) {
            $sign = -1;
        } else {
            $sign = 0;
        }
        $seconds = $this->epoch_seconds($date) - $this->startEpoch();
        return round($sign * $order + $seconds / 45000, 7);
    }
}
