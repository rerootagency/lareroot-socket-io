<?php
namespace RerootAgency\LaReRootSocketIO\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Endpoint extends Model
{
    const MESSAGE_TYPE = 'endpoint';

    protected $connection = 'mongodb';

    protected $collection = 'user_endpoints';

    protected $guarded = [];

    public $timestamps = false;
}