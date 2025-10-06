<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/countries', function (Request $request) {
    return response()->json([
        ['name' => 'Australia', 'code' => 'AU'],
        ['name' => 'Brazil', 'code' => 'BR'],
        ['name' => 'Canada', 'code' => 'CA'],
        ['name' => 'China', 'code' => 'CN'],
        ['name' => 'Egypt', 'code' => 'EG'],
        ['name' => 'France', 'code' => 'FR'],
        ['name' => 'Germany', 'code' => 'DE'],
        ['name' => 'India', 'code' => 'IN'],
        ['name' => 'Japan', 'code' => 'JP'],
        ['name' => 'Spain', 'code' => 'ES'],
        ['name' => 'United Kingdom', 'code' => 'GB'],
        ['name' => 'United States', 'code' => 'US']
    ]);
});