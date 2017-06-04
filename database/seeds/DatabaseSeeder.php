<?php

use Illuminate\Database\Seeder;

spl_autoload_register(function ($name) {
    $file = __DIR__ . "/$name.php";
    if (is_file($file)) {
        include $file;
    }
});

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $this->call();
    }
}
