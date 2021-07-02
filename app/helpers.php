<?php

use Illuminate\Support\Facades\Http;



function orderPremium($user_course) {
    $url = env('SERVICE_PAYMENT_URL').'orders/';
    try {
        $res = Http::post($url, $user_course);
        $data = $res->json();
        $data['status_code'] = $res->getStatusCode();
        return $data;
    } catch (\Throwable $th) {
        return [
            'status_code' => 500,
            'status' => 'error',
            'message' => 'service payment down or unavailable'
        ];
    }
}

function getUserbyId($user_id)
{

    $url = env('SERVICE_USER_URL').'users/'.$user_id;
    //dd(env('SERVICE_USER_URL'));

    try {
        $res = Http::timeout(20)->get($url);
        $data = $res->json();
        $data['status_code'] = $res->getStatusCode();
        return $data;
    } catch (\Throwable $th) {
        return [
            'status_code' => 500,
            'status' => 'error',
            'message' => 'service user down or unavailable'
        ];
    }
}

function getUserbyIds($user_ids = [])
{
    $url = env('SERVICE_USER_URL').'users/';

    try {
        if (count($user_ids) === 0){
            return[ 
                'status_code' => 200,
                'status' => 'success',
                'data' => []
            ];
        }
        $res = Http::timeout(20)->get($url, ['user_ids[]'=>$user_ids]);
        $data = $res->json();
        $data['status_code'] = $res->getStatusCode();
        return $data;
    } catch (\Throwable $th) {
        return [
            'status_code' => 500,
            'status' => 'error',
            'message' => 'service user down or unavailable'
        ];
    }
}



