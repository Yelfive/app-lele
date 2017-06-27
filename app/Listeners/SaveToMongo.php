<?php

namespace App\Listeners;

use App\Components\MongoDB;
use App\Events\UserSaved;

class SaveToMongo
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  UserSaved $event
     */
    public function handle(UserSaved $event)
    {
        $user = $event->user;
        $collection = MongoDB::collection('user');

        $collection->createIndex(['location' => '2dsphere'], ['background' => true]);

        $filter = ['_id' => ['$eq' => $user->id]];
        $exists = $collection->count($filter);
        $document = $user->getAttributes();
        $document['_id'] = $user->id;
        if ($exists) {
            $collection->updateOne($filter, ['$set' => $document]);
        } else {
            $result = $collection->insertOne($document);
            if (!$result->getInsertedCount()) $user->errors['id'] = ['更新Mongo失败'];
        }
    }
}
