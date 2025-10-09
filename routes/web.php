<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/countries', function (Request $request) {
    $search = $request->input('search', '');
    
    // All countries
    $allCountries = [
        ['name' => 'Afghanistan', 'code' => 'AF'],
        ['name' => 'Albania', 'code' => 'AL'],
        ['name' => 'Algeria', 'code' => 'DZ'],
        ['name' => 'Andorra', 'code' => 'AD'],
        ['name' => 'Angola', 'code' => 'AO'],
        ['name' => 'Argentina', 'code' => 'AR'],
        ['name' => 'Armenia', 'code' => 'AM'],
        ['name' => 'Australia', 'code' => 'AU'],
        ['name' => 'Austria', 'code' => 'AT'],
        ['name' => 'Azerbaijan', 'code' => 'AZ'],
        ['name' => 'Bahamas', 'code' => 'BS'],
        ['name' => 'Bahrain', 'code' => 'BH'],
        ['name' => 'Bangladesh', 'code' => 'BD'],
        ['name' => 'Barbados', 'code' => 'BB'],
        ['name' => 'Belarus', 'code' => 'BY'],
        ['name' => 'Belgium', 'code' => 'BE'],
        ['name' => 'Belize', 'code' => 'BZ'],
        ['name' => 'Benin', 'code' => 'BJ'],
        ['name' => 'Bhutan', 'code' => 'BT'],
        ['name' => 'Bolivia', 'code' => 'BO'],
        ['name' => 'Bosnia and Herzegovina', 'code' => 'BA'],
        ['name' => 'Botswana', 'code' => 'BW'],
        ['name' => 'Brazil', 'code' => 'BR'],
        ['name' => 'Brunei', 'code' => 'BN'],
        ['name' => 'Bulgaria', 'code' => 'BG'],
        ['name' => 'Canada', 'code' => 'CA'],
        ['name' => 'Chile', 'code' => 'CL'],
        ['name' => 'China', 'code' => 'CN'],
        ['name' => 'Colombia', 'code' => 'CO'],
        ['name' => 'Costa Rica', 'code' => 'CR'],
        ['name' => 'Croatia', 'code' => 'HR'],
        ['name' => 'Cuba', 'code' => 'CU'],
        ['name' => 'Cyprus', 'code' => 'CY'],
        ['name' => 'Czech Republic', 'code' => 'CZ'],
        ['name' => 'Denmark', 'code' => 'DK'],
        ['name' => 'Egypt', 'code' => 'EG'],
        ['name' => 'Finland', 'code' => 'FI'],
        ['name' => 'France', 'code' => 'FR'],
        ['name' => 'Germany', 'code' => 'DE'],
        ['name' => 'Greece', 'code' => 'GR'],
        ['name' => 'India', 'code' => 'IN'],
        ['name' => 'Indonesia', 'code' => 'ID'],
        ['name' => 'Iran', 'code' => 'IR'],
        ['name' => 'Iraq', 'code' => 'IQ'],
        ['name' => 'Ireland', 'code' => 'IE'],
        ['name' => 'Israel', 'code' => 'IL'],
        ['name' => 'Italy', 'code' => 'IT'],
        ['name' => 'Japan', 'code' => 'JP'],
        ['name' => 'Mexico', 'code' => 'MX'],
        ['name' => 'Netherlands', 'code' => 'NL'],
        ['name' => 'Norway', 'code' => 'NO'],
        ['name' => 'Pakistan', 'code' => 'PK'],
        ['name' => 'Poland', 'code' => 'PL'],
        ['name' => 'Portugal', 'code' => 'PT'],
        ['name' => 'Russia', 'code' => 'RU'],
        ['name' => 'Saudi Arabia', 'code' => 'SA'],
        ['name' => 'Singapore', 'code' => 'SG'],
        ['name' => 'South Africa', 'code' => 'ZA'],
        ['name' => 'South Korea', 'code' => 'KR'],
        ['name' => 'Spain', 'code' => 'ES'],
        ['name' => 'Sweden', 'code' => 'SE'],
        ['name' => 'Switzerland', 'code' => 'CH'],
        ['name' => 'Thailand', 'code' => 'TH'],
        ['name' => 'Turkey', 'code' => 'TR'],
        ['name' => 'Ukraine', 'code' => 'UA'],
        ['name' => 'United Arab Emirates', 'code' => 'AE'],
        ['name' => 'United Kingdom', 'code' => 'GB'],
        ['name' => 'United States', 'code' => 'US'],
        ['name' => 'Vietnam', 'code' => 'VN'],
    ];
    
    // Filter by search term
    if (!empty($search)) {
        $filtered = array_filter($allCountries, function($country) use ($search) {
            return stripos($country['name'], $search) !== false;
        });
        return response()->json(array_values($filtered));
    }
    
    // Return all countries if no search term
    return response()->json($allCountries);
});
Route::get('/cities', function (Request $request) {
    $search = $request->input('search', '');
    
    // All countries
    $allCities =  [
    ['id' => 1, 'name' => 'New York'],
    ['id' => 2, 'name' => 'Los Angeles'],
    ['id' => 3, 'name' => 'London'],
    ['id' => 4, 'name' => 'Paris'],
    ['id' => 5, 'name' => 'Tokyo'],
    ['id' => 6, 'name' => 'Dubai'],
    ['id' => 7, 'name' => 'Singapore'],
    ['id' => 8, 'name' => 'Hong Kong'],
    ['id' => 9, 'name' => 'Sydney'],
    ['id' => 10, 'name' => 'Toronto'],
    ['id' => 11, 'name' => 'Chicago'],
    ['id' => 12, 'name' => 'San Francisco'],
    ['id' => 13, 'name' => 'Berlin'],
    ['id' => 14, 'name' => 'Madrid'],
    ['id' => 15, 'name' => 'Rome'],
    ['id' => 16, 'name' => 'Barcelona'],
    ['id' => 17, 'name' => 'Amsterdam'],
    ['id' => 18, 'name' => 'Vienna'],
    ['id' => 19, 'name' => 'Prague'],
    ['id' => 20, 'name' => 'Budapest'],
    ['id' => 21, 'name' => 'Moscow'],
    ['id' => 22, 'name' => 'Istanbul'],
    ['id' => 23, 'name' => 'Cairo'],
    ['id' => 24, 'name' => 'Mumbai'],
    ['id' => 25, 'name' => 'Delhi'],
    ['id' => 26, 'name' => 'Shanghai'],
    ['id' => 27, 'name' => 'Beijing'],
    ['id' => 28, 'name' => 'Bangkok'],
    ['id' => 29, 'name' => 'Seoul'],
    ['id' => 30, 'name' => 'Kuala Lumpur'],
    ['id' => 31, 'name' => 'Jakarta'],
    ['id' => 32, 'name' => 'Manila'],
    ['id' => 33, 'name' => 'Melbourne'],
    ['id' => 34, 'name' => 'Brisbane'],
    ['id' => 35, 'name' => 'Auckland'],
    ['id' => 36, 'name' => 'Vancouver'],
    ['id' => 37, 'name' => 'Montreal'],
    ['id' => 38, 'name' => 'Mexico City'],
    ['id' => 39, 'name' => 'São Paulo'],
    ['id' => 40, 'name' => 'Rio de Janeiro'],
    ['id' => 41, 'name' => 'Buenos Aires'],
    ['id' => 42, 'name' => 'Lima'],
    ['id' => 43, 'name' => 'Bogotá'],
    ['id' => 44, 'name' => 'Santiago'],
    ['id' => 45, 'name' => 'Johannesburg'],
    ['id' => 46, 'name' => 'Cape Town'],
    ['id' => 47, 'name' => 'Lagos'],
    ['id' => 48, 'name' => 'Nairobi'],
    ['id' => 49, 'name' => 'Athens'],
    ['id' => 50, 'name' => 'Lisbon'],
];
      
    
    // Filter by search term
    if (!empty($search)) {
        $filtered = array_filter($allCities, function($citiy) use ($search) {
            return stripos($citiy['name'], $search) !== false;
        });
        return response()->json(array_values($filtered));
    }
    
    // Return all countries if no search term
    return response()->json($allCities);
});
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
