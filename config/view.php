<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views. Of course
    | the usual Laravel view path has already been registered for you.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored for your application. Typically, this is within the storage
    | directory. However, as usual, you are free to change this value.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

    'timezones' => array(
        'Pacific/Midway'       => "-11",
        'US/Samoa'             => "-11",
        'US/Hawaii'            => "-10",
        'US/Alaska'            => "-9",
        'US/Pacific'           => "-8",
        'America/Tijuana'      => "-8",
        'US/Arizona'           => "-7",
        'US/Mountain'          => "-7",
        'America/Chihuahua'    => "-7",
        'America/Mazatlan'     => "-7",
        'America/Mexico_City'  => "-6",
        'America/Monterrey'    => "-6",
        'Canada/Saskatchewan'  => "-6",
        'US/Central'           => "-6",
        'US/Eastern'           => "-5",
        'US/East-Indiana'      => "-5",
        'America/Bogota'       => "-5",
        'America/Lima'         => "-5",
        'America/Caracas'      => "-4.5",
        'Canada/Atlantic'      => "-4",
        'America/La_Paz'       => "-4",
        'America/Santiago'     => "-4",
        'Canada/Newfoundland'  => "-3.5",
        'America/Buenos_Aires' => "-3",
        'Greenland'            => "-3",
        'Atlantic/Stanley'     => "-2",
        'Atlantic/Azores'      => "-1",
        'Atlantic/Cape_Verde'  => "-1",
        'Africa/Casablanca'    => "0",
        'Europe/Dublin'        => "0",
        'Europe/Lisbon'        => "0",
        'Europe/London'        => "0",
        'Africa/Monrovia'      => "0",
        'Europe/Amsterdam'     => "+1",
        'Europe/Belgrade'      => "+1",
        'Europe/Berlin'        => "+1",
        'Europe/Bratislava'    => "+1",
        'Europe/Brussels'      => "+1",
        'Europe/Budapest'      => "+1",
        'Europe/Copenhagen'    => "+1",
        'Europe/Ljubljana'     => "+1",
        'Europe/Madrid'        => "+1",
        'Europe/Paris'         => "+1",
        'Europe/Prague'        => "+1",
        'Europe/Rome'          => "+1",
        'Europe/Sarajevo'      => "+1",
        'Europe/Skopje'        => "+1",
        'Europe/Stockholm'     => "+1",
        'Europe/Vienna'        => "+1",
        'Europe/Warsaw'        => "+1",
        'Europe/Zagreb'        => "+1",
        'Europe/Athens'        => "+2",
        'Europe/Bucharest'     => "+2",
        'Africa/Cairo'         => "+2",
        'Africa/Harare'        => "+2",
        'Europe/Helsinki'      => "+2",
        'Europe/Istanbul'      => "+2",
        'Asia/Jerusalem'       => "+2",
        'Europe/Kiev'          => "+2",
        'Europe/Minsk'         => "+2",
        'Europe/Riga'          => "+2",
        'Europe/Sofia'         => "+2",
        'Europe/Tallinn'       => "+2",
        'Europe/Vilnius'       => "+2",
        'Asia/Baghdad'         => "+3",
        'Asia/Kuwait'          => "+3",
        'Africa/Nairobi'       => "+3",
        'Asia/Riyadh'          => "+3",
        'Europe/Moscow'        => "+3",
        'Asia/Tehran'          => "+3.5",
        'Asia/Baku'            => "+4",
        'Europe/Volgograd'     => "+4",
        'Asia/Muscat'          => "+4",
        'Asia/Tbilisi'         => "+4",
        'Asia/Yerevan'         => "+4",
        'Asia/Kabul'           => "+4.5",
        'Asia/Karachi'         => "+5",
        'Asia/Tashkent'        => "+5",
        'Asia/Kolkata'         => "+5.5",
        'Asia/Kathmandu'       => "+5.75",
        'Asia/Yekaterinburg'   => "+6",
        'Asia/Almaty'          => "+6",
        'Asia/Dhaka'           => "+6",
        'Asia/Novosibirsk'     => "+7",
        'Asia/Bangkok'         => "+7",
        'Asia/Jakarta'         => "+7",
        'Asia/Krasnoyarsk'     => "+8",
        'Asia/Chongqing'       => "+8",
        'Asia/Hong_Kong'       => "+8",
        'Asia/Kuala_Lumpur'    => "+8",
        'Australia/Perth'      => "+8",
        'Asia/Singapore'       => "+8",
        'Asia/Taipei'          => "+8",
        'Asia/Ulaanbaatar'     => "+8",
        'Asia/Urumqi'          => "+8",
        'Asia/Irkutsk'         => "+9",
        'Asia/Seoul'           => "+9",
        'Asia/Tokyo'           => "+9",
        'Australia/Adelaide'   => "+9.5",
        'Australia/Darwin'     => "+9.5",
        'Asia/Yakutsk'         => "+10",
        'Australia/Brisbane'   => "+10",
        'Australia/Canberra'   => "+10",
        'Pacific/Guam'         => "+10",
        'Australia/Hobart'     => "+10",
        'Australia/Melbourne'  => "+10",
        'Pacific/Port_Moresby' => "+10",
        'Australia/Sydney'     => "+10",
        'Asia/Vladivostok'     => "+11",
        'Asia/Magadan'         => "+12",
        'Pacific/Auckland'     => "+12",
        'Pacific/Fiji'         => "+12",
    )

];
