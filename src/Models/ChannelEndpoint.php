<?php
namespace RerootAgency\LaReRootSocketIO\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class ChannelEndpoint extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'channel_endpoints';

    protected $guarded = [];

    public $timestamps = false;
}