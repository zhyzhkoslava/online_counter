<?php

namespace App\Http\Controllers;

use App\Online;
use Illuminate\Http\Request;
use App\CookieManager;

class IndexController extends Controller
{
    public function index(Request $request){
        $ip = $request->ip();
        $online = Online::where('ip', $ip)->first();
        //dd($time);
        $cookie_key = 'online-cache';
        if ($online)
        {
            $do_update = false;
            //Update
            if (CookieManager::stored($cookie_key))
            {   //via cookies
                $c = (array) @json_decode(CookieManager::read($cookie_key), true);
                if ($c)
                {
                    if ( $c['lastvisit'] < (time() - (60 * 5)) )
                    {
                        $do_update = true;
                    }
                } else {
                    //without cookies
                    $do_update = true;
                }
            } else
            {
                $do_update - true;
            }
                //update if required
            if ($do_update)
            {
                $time = time();
                $online->lastvisit = time();
                $online->save();
                CookieManager::store($cookie_key, json_encode(array(
                    'id' => $online->id,
                    'lastvisit' => $time)));
            }
            }

        else {
            //Create
            $time = time();
            $online = new Online();
            $online->lastvisit = time();
            $online->ip = $request->ip();
            $online->save();
            CookieManager::store($cookie_key, json_encode(array(
                'id' => $online->id,
                'lastvisit' => $time)));
        }


        $online_count = Online::where('lastvisit', '>', (time() - (3600) ))->count();
        //dd($online_count);

        return view('welcome',[
            'online_count' => $online_count
        ]);
    }
}
