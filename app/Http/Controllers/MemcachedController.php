<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Memcached;

class MemcachedController extends Controller
{
    public function index()
    {
        $allData = Cache::get('*');
        dd($allData);
    }

    public function mem()
    {
        $memcached = Cache::store('memcached');
    }
}
