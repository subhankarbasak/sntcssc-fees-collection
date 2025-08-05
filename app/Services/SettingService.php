<?php

// app/Services/SettingService.php
namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    public function get($key, $default = null)
    {
        $value = Cache::remember('setting_' . $key, 3600, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            
            if (! $setting) {
                return $default;
            }
            
            return $this->castValue($setting->value, $setting->type);
        });
        
        return $value;
    }
    
    public function set($key, $value, $type = 'string')
    {
        $setting = Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
            ]
        );
        
        Cache::forget('setting_' . $key);
        
        return $setting;
    }
    
    private function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }
    
    public function getAll()
    {
        $settings = Setting::all();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->key] = $this->castValue($setting->value, $setting->type);
        }
        
        return $result;
    }
}