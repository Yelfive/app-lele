<?php

/**
 * @author Felix Huang <yelfivehuang@gmail.com>
 * @date 2017-06-26
 */

namespace App\Console\Controllers;

use App\Components\MongoDB;
use App\Models\User;
use Illuminate\Console\Command;

class UserToMongoController extends Command
{

    public $name = 'user2mongo';

    public function handle()
    {
        $collection = MongoDB::collection('user');
        $collection->createIndex(['location' => '2dsphere'], ['background' => true]);
        /** @var User $user */
        foreach (User::cursor() as $user) {
            $document = $user->getAttributes();
            $document['location'] = [
                'type' => 'Point',
                'coordinates' => [104.0712219292, 30.5763307666]
            ];
            $document['location_uploaded_at'] = $_SERVER['REQUEST_TIME'];
            if ($collection->count(['_id' => $user->id])) {
                $collection->updateOne(['_id' => $user->id], ['$set' => $document]);
            } else {
                $document['_id'] = $user->id;
                $collection->insertOne($document);
            }
        }
    }

}