<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use App\Models\Bill;
use App\Models\Category;

class APIHelpers {

    public static function createAPIResponse($is_error, $code, $message, $content){
        $result = [];

        if($is_error){
            $result['success'] = false;
            $result['code'] = $code;
            $result['message'] = $message;
        } else{
            $result['success'] = true;
            $result['code'] = $code;

            if($content == null){
                $result['message'] = $message;
            }else{
                $result['data'] = $content;
            }
        }

        return $result;
    }

    public static function collectData($params, $operation){
        $where[0] = ['user_id', Auth::id()];
        $count = 1;
    
        foreach($params as $key => $value){
            switch ($key) {
                case 'start_date' :
                    $where[$count] = ['bills.created_at', '>=', date('Y-m-d H:i:s' , strtotime($value))];
                    $data['start_date'] = $value;
                    break ;
                case 'end_date' :
                    $where[$count] = ['bills.created_at', '<=', date('Y-m-d H:i:s', strtotime($value))];
                    $data['end_date'] = $value;
                    break ;
                case 'category_id' :
                    $where[$count] = ['category_id', $value];
                    $category = Category::where('id', $value)->first();
                    if($category){
                        $data['category'] = $category->name;
                    }
                    break ;
             }

             $count++;
        }

        $result = Bill::where($where)->join('categories', 'categories.id','=','bills.category_id')->$operation('price'); 
        $data[$operation] = $result;

        return $data;

    }

}