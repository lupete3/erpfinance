<?php

use App\Models\CompanySetting;
use Illuminate\Support\Facades\Auth;

if (!function_exists('company')) {
    function company()
    {
      $company = [];

      $company = CompanySetting::where('tenant_id', Auth::user()?->tenant_id)->first() ?? 
                CompanySetting::first();
      
        return $company;
    }
}