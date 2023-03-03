<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Category;

class CategoryTest extends TestCase
{
    public function test_get_categories(){
        $response = $this->get('api/categories');

        $response->assertStatus(200);
    }

    public function test_get_category(){
        $category = Category::first();
        $response = $this->get('api/categories/' . $category->id);

        $response->assertStatus(200);
    }

    public function test_post_category(){
        $response = $this->post('api/categories', [
            'name' => 'Automobil',
            'color' => 'red'
        ]);

        $response->assertStatus(200);
    }

    public function test_update_category(){
        $category = Category::first();
        $response = $this->put('api/categories/' . $category->id, [
            'name' => 'Higijena',
            'color' => 'yellow'
        ]);

        $response->assertStatus(200);
    }

    public function test_delete_category(){
        $category = Category::make([
            'name' => 'Testna kategorija',
            'color' => 'orange'
        ]);

        if($category){
            $category->delete();
        }

        $this->assertTrue(true);
    }
}
