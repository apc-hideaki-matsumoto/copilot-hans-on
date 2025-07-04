<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>東京1週間天気予報</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    
    <!-- TailwindCSS -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            body { font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif; margin: 0; padding: 20px; background-color: #f8fafc; }
            .container { max-width: 1200px; margin: 0 auto; }
            .header { text-align: center; margin-bottom: 2rem; }
            .title { font-size: 2rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; }
            .subtitle { color: #64748b; font-size: 1rem; }
            .weather-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem; }
            .weather-card { background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s; }
            .weather-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
            .card-date { font-size: 1.125rem; font-weight: 600; color: #1e293b; margin-bottom: 0.5rem; }
            .weather-info { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }
            .weather-condition { font-size: 1rem; color: #3b82f6; font-weight: 500; }
            .temperature { display: flex; flex-direction: column; align-items: flex-end; }
            .temp-max { font-size: 1.5rem; font-weight: 600; color: #dc2626; }
            .temp-min { font-size: 1rem; color: #6b7280; }
            .weather-details { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #e5e7eb; }
            .detail-item { display: flex; align-items: center; font-size: 0.875rem; color: #6b7280; }
            .detail-label { margin-right: 0.5rem; font-weight: 500; }
            .error { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; text-align: center; }
            .nav-link { display: inline-block; background: #3b82f6; color: white; padding: 0.5rem 1rem; border-radius: 6px; text-decoration: none; margin-bottom: 1rem; }
            .nav-link:hover { background: #2563eb; }
            .weather-emoji { font-size: 2rem; margin-right: 0.5rem; }
        </style>
    @endif
</head>
<body>
    @if (!function_exists('getWeatherEmoji'))
    @php
    // 天気コードに対応する絵文字を返す関数
    function getWeatherEmoji($code) {
        $emojiMap = [
            0 => '☀️',   // 快晴
            1 => '🌤️',   // 晴れ
            2 => '⛅',   // 一部曇り
            3 => '☁️',   // 曇り
            45 => '🌫️',  // 霧
            48 => '🌫️',  // 霧雨
            51 => '🌦️',  // 弱い霧雨
            53 => '🌧️',  // 霧雨
            55 => '🌧️',  // 強い霧雨
            56 => '🌧️',  // 弱い凍雨
            57 => '🌧️',  // 強い凍雨
            61 => '🌦️',  // 弱い雨
            63 => '🌧️',  // 雨
            65 => '🌧️',  // 強い雨
            66 => '🌧️',  // 弱い凍雨
            67 => '🌧️',  // 強い凍雨
            71 => '🌨️',  // 弱い雪
            73 => '❄️',  // 雪
            75 => '❄️',  // 大雪
            77 => '🌨️',  // 雪粒
            80 => '🌦️',  // 弱いにわか雨
            81 => '🌧️',  // にわか雨
            82 => '⛈️',  // 激しいにわか雨
            85 => '🌨️',  // 弱いにわか雪
            86 => '❄️',  // 激しいにわか雪
            95 => '⛈️',  // 雷雨
            96 => '⛈️',  // 弱いひょうを伴う雷雨
            99 => '⛈️',  // 強いひょうを伴う雷雨
        ];
        return $emojiMap[$code] ?? '🌤️';
    }
    @endphp
    @endif

    <div class="container">
        <a href="/" class="nav-link">← ホームに戻る</a>
        
        <div class="header">
            <h1 class="title">🌤️ 東京 1週間天気予報</h1>
            <p class="subtitle">Open-Meteo API による天気情報</p>
        </div>

        @if(isset($error))
            <div class="error">
                ⚠️ {{ $error }}
            </div>
        @endif

        @if(count($forecast) > 0)
            <div class="weather-grid">
                @foreach($forecast as $index => $day)
                    <div class="weather-card">
                        <div class="card-date">
                            @if($index === 0)
                                今日 ({{ date('n月j日', strtotime($day['date'])) }})
                            @elseif($index === 1)
                                明日 ({{ date('n月j日', strtotime($day['date'])) }})
                            @else
                                {{ date('n月j日 (D)', strtotime($day['date'])) }}
                            @endif
                        </div>
                        
                        <div class="weather-info">
                            <div style="display: flex; align-items: center;">
                                <span class="weather-emoji">{{ getWeatherEmoji($day['weather_code']) }}</span>
                                <span class="weather-condition">{{ $day['weather_condition'] }}</span>
                            </div>
                            <div class="temperature">
                                <div class="temp-max">{{ round($day['max_temp']) }}°</div>
                                <div class="temp-min">{{ round($day['min_temp']) }}°</div>
                            </div>
                        </div>
                        
                        <div class="weather-details">
                            <div class="detail-item">
                                <span class="detail-label">💧 降水量:</span>
                                <span>{{ $day['precipitation'] }}mm</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">💨 風速:</span>
                                <span>{{ round($day['wind_speed']) }}km/h</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        
        <div style="text-align: center; margin-top: 2rem; color: #6b7280; font-size: 0.875rem;">
            <p>データ提供: <a href="https://open-meteo.com/" target="_blank" style="color: #3b82f6;">Open-Meteo</a></p>
            <p>更新時刻: {{ date('Y年n月j日 H:i') }}</p>
        </div>
    </div>
</body>
</html>
