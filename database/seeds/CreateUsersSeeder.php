<?php

use Illuminate\Database\Seeder;
use App\User;


class CreateUsersSeeder extends Seeder
{

    /**

     * Run the database seeds.

     *

     * @return void

     */

    public function run()

    {

        $user = [

            [

               'username'=>'misterminsk',

               'email'=>'admin@something.com',

               'type'=>'editor',

               'password'=> bcrypt('password123'),

            ],

            [

               'username'=>'gergokee',

               'email'=>'gergokee@hotmail.com',

               'type'=>'admin',

               'password'=> bcrypt('Asdfgh88'),

            ],

        ];

  

        foreach ($user as $key => $value) {

            User::create($value);

        }

    }

}