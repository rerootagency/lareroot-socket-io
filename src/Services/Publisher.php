<?php
namespace RerootAgency\LaReRootSocketIO\Services;

use Illuminate\Support\Facades\Redis;
use RerootAgency\LaReRootSocketIO\Models\Channel;
use RerootAgency\LaReRootSocketIO\Models\ChannelEndpoint;
use RerootAgency\LaReRootSocketIO\Models\Acknowledgement;
use RerootAgency\LaReRootSocketIO\Models\Endpoint;
use Illuminate\Contracts\Auth\Authenticatable;

class Publisher
{
    const ENDPOINT_KEY = 'endpoint';

    public function publishNotification(
        Channel $channel,
        array $payload,
        array $exclude = [],
        bool $force_ack = true
    ) {
        if($channel->type != Channel::NOTIFICATION_TYPE) {

            throw new \Exception('Channel must be of "'.Channel::NOTIFICATION_TYPE.'" type.');
        }

        $time = time();

        $message_id = uuid();

        $data = [
            'meta' => [
                'exclude' => $exclude
            ],
            'channel' => [
                'channel'=> $channel->channel,
                'message_type' => $channel->type,
                'data'=> [
                    'key' => $channel->key
                ]
            ],
            'message_id' => $message_id,
            'payload' => $payload,
            'force_ack' => $force_ack,
            'time' => $time
        ];

        if($force_ack) {
            $insert_builder = ChannelEndpoint::where('channel', $channel->channel);

            if(!empty($exclude)) {
                $insert_builder = $insert_builder->whereNotIn('endpoint', $exclude);
            }

            $insert = $insert_builder->get()
                ->map(function($item) use ($time, $data, $message_id) {
                    return [
                        'message_id' => $message_id,
                        'endpoint' => $item->endpoint,
                        'data' => $data,
                        'time' => $time,
                    ];
                })
                ->toArray();

            if(!empty($insert)) {
                Acknowledgement::insert($insert);
            }
        }

        Redis::publish($channel->channel, json_encode($data));
    }

    public function publishToEndpoint(
        Endpoint $endpoint,
        array $payload,
        bool $force_ack = true
    ) {
        $time = time();

        $message_id = uuid();

        $data = [
            'meta' => [
                'exclude' => []
            ],
            'channel' => [
                'channel'=> $endpoint->endpoint,
                'message_type' => Endpoint::MESSAGE_TYPE,
                'data' => null
            ],
            'message_id' => $message_id,
            'payload' => $payload,
            'force_ack' => $force_ack,
            'time' => $time
        ];

        if($force_ack) {
            Acknowledgement::create([
                'message_id' => $message_id,
                'endpoint' => $endpoint->endpoint,
                'data' => $data,
                'time' => $time,
            ]);
        }

        Redis::publish($endpoint->endpoint, json_encode($data));
    }

    /**
     * ToDo Conversation publishing.
     */
    public function publishMessage(
        Authenticatable $user,
        Channel $channel,
        array $payload,
        bool $force_ack = true
    ) {
        if($channel->type != Channel::CONVERSATION_TYPE) {

            throw new \Exception('Channel must be of "'.Channel::CONVERSATION_TYPE.'" type.');
        }

        $time = time();

        $message_id = uuid();

        $source_endpoint = null;

        $endpoint = Endpoint::where('user_id', $user->getKey())
            ->first();

        if($endpoint) {
            $source_endpoint = $endpoint->endpoint;
        }

        $data = [
            'meta' => [
                'exclude' => [
                    $source_endpoint
                ]
            ],
            'channel' => [
                'channel'=> $channel->channel,
                'message_type' => $channel->type,
                'data' => [
                    'conversation_name' => $channel->name,
                    'from_user' => $user->name,
                ]
            ],
            'message_id' => $message_id,
            'payload' => $payload,
            'force_ack' => $force_ack,
            'time' => $time
        ];

        if($force_ack) {
            $insert_builder = ChannelEndpoint::where('channel', $channel->channel);

            if(!empty($source_endpoint)) {
                $insert_builder = $insert_builder->where('endpoint', '!=', $source_endpoint);
            }

            $insert = $insert_builder->get()
                ->map(function($item) use ($time, $data, $message_id) {
                    return [
                        'message_id' => $message_id,
                        'endpoint' => $item->endpoint,
                        'data' => $data,
                        'time' => $time,
                    ];
                })
                ->toArray();

            if(!empty($insert)) {
                Acknowledgement::insert($insert);
            }
        }

        Redis::publish($channel->channel, json_encode($data));
    }
}