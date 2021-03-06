<?php

namespace app\models;

use yii\db\ActiveRecord;

class MonthlyStatistics extends ActiveRecord
{
    public static function tableName()
    {
        return '{{monthly_statistics}}';
    }

    /**
     * Возвращает данные статистики за прошлый месяц.
     * @param $input
     * @return string
     */
    public static function getStatisticsForLastMonth($array): string
    {
        // определяем способ получения input_id
        // может быть получен через query или professional_area
        if (!empty(Input::getDataByProfessionalArea($array))) {
            $input = Input::getDataByProfessionalArea($array);
        } elseif (!empty(Input::getDataByQuery($array))) {
            $input = Input::getDataByQuery($array);
        }

        $monthChange = MonthlyStatistics::find()
            ->asArray()
            ->select(['date', 'daily_median_for_last_month', 'change_per_month'])
            ->where([
                'date' => date('Y-m-01'),
                'input_id' => $input[0]['id']
            ])
            ->one();

        if (!empty($monthChange)){
            $json = json_decode($monthChange['change_per_month']);
            if (isset($json->color) && gmp_sign($json->count) == 1){
                return "Дневная медиана в прошлом месяце: <span style=\"color:" . $json->color . "\">" . round($monthChange['daily_median_for_last_month']) . " (+" . $json->percent . "%)</span>; ";
            } elseif (isset($json->color) && gmp_sign($json->count) == -1) {
                return "Дневная медиана в прошлом месяце: <span style=\"color:" . $json->color . "\">" . round($monthChange['daily_median_for_last_month']) . " (-" . $json->percent . "%)</span>; ";
            }
        }
        return '';
    }
}