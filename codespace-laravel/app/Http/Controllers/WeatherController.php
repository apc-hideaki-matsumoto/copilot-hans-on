<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WeatherController extends Controller
{
    /**
     * 大阪の1週間天気予報を取得
     */
    public function index()
    {
        try {
            // Open-Meteo API から大阪の天気予報を取得
            $response = Http::timeout(10)->withOptions(['verify' => false])->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => 34.6937, // 大阪の緯度
                'longitude' => 135.5023, // 大阪の経度
                'daily' => 'temperature_2m_max,temperature_2m_min,weathercode,precipitation_sum,windspeed_10m_max',
                'timezone' => 'Asia/Tokyo',
                'forecast_days' => 7
            ]);

            if (!$response->successful()) {
                throw new \Exception('天気情報の取得に失敗しました: ' . $response->body());
            }

            $weatherData = $response->json();
            $weatherConditions = $this->getWeatherConditions();
            $forecast = [];
            for ($i = 0; $i < 7; $i++) {
                $weatherCode = $weatherData['daily']['weathercode'][$i];
                $forecast[] = [
                    'date' => $weatherData['daily']['time'][$i],
                    'max_temp' => $weatherData['daily']['temperature_2m_max'][$i],
                    'min_temp' => $weatherData['daily']['temperature_2m_min'][$i],
                    'weather_code' => $weatherCode,
                    'weather_condition' => $weatherConditions[$weatherCode] ?? '不明',
                    'precipitation' => $weatherData['daily']['precipitation_sum'][$i],
                    'wind_speed' => $weatherData['daily']['windspeed_10m_max'][$i],
                ];
            }

            return view('weather.index', compact('forecast'));

        } catch (\Exception $e) {
            \Log::error('WeatherController error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return view('weather.index', [
                'error' => $e->getMessage(),
                'forecast' => []
            ]);
        }
    }

    /**
     * 天気コードを日本語の天気状況に変換
     */
    private function getWeatherConditions(): array
    {
        return [
            0 => '快晴',
            1 => '晴れ',
            2 => '一部曇り',
            3 => '曇り',
            45 => '霧',
            48 => '霧雨',
            51 => '弱い霧雨',
            53 => '霧雨',
            55 => '強い霧雨',
            56 => '弱い凍雨',
            57 => '強い凍雨',
            61 => '弱い雨',
            63 => '雨',
            65 => '強い雨',
            66 => '弱い凍雨',
            67 => '強い凍雨',
            71 => '弱い雪',
            73 => '雪',
            75 => '大雪',
            77 => '雪粒',
            80 => '弱いにわか雨',
            81 => 'にわか雨',
            82 => '激しいにわか雨',
            85 => '弱いにわか雪',
            86 => '激しいにわか雪',
            95 => '雷雨',
            96 => '弱いひょうを伴う雷雨',
            99 => '強いひょうを伴う雷雨',
        ];
    }
}
