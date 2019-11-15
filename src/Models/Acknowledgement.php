<?php
namespace RerootAgency\LaReRootSocketIO\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Acknowledgement extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'acknowledgements';

    protected $guarded = [];

    public $timestamps = false;
}