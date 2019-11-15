<?php
namespace RerootAgency\LaReRootSocketIO\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Channel extends Model
{
    const CONVERSATION_TYPE = 'conversation';
    const NOTIFICATION_TYPE = 'notification';
    const ENDPOINT_TYPE = 'endpoint';

    protected $connection = 'mongodb';

    protected $collection = 'channels';

    protected $guarded = [];

    public $timestamps = false;
}