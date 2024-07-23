<?php
require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'db',
    'database'  => 'a_tower',
    'username'  => 'user',
    'password'  => 'password',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Create tables if they do not exist
if (!Capsule::schema()->hasTable('sensors')) {
    Capsule::schema()->create('sensors', function ($table) {
        $table->id();
        $table->enum('face', ['north', 'east', 'south', 'west']);
        $table->boolean('isOn');
    });
}

if (!Capsule::schema()->hasTable('sensor_data')) {
    Capsule::schema()->create('sensor_data', function ($table) {
        $table->id();
        $table->timestamp('timestamp');
        $table->integer('sensor_id');
        $table->enum('face', ['north', 'east', 'south', 'west']);
        $table->double('temperature');
    });
}

if (!Capsule::schema()->hasTable('hourly_averages')) {
    Capsule::schema()->create('hourly_averages', function ($table) {
        $table->id();
        $table->timestamp('hour');
        $table->enum('face', ['north', 'east', 'south', 'west']);
        $table->double('average_temperature');
    });
}

if (!Capsule::schema()->hasTable('malfunctioning_sensors')) {
    Capsule::schema()->create('malfunctioning_sensors', function ($table) {
        $table->integer('sensor_id');
        $table->double('average_temperature');
        $table->enum('face', ['north', 'east', 'south', 'west']);
    });
}

echo "Migration completed.\n";
