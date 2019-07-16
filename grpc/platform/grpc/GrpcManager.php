<?php

namespace GrpcClient;

// use InvalidArgumentException;

/**
 * @mixin \Illuminate\Redis\Connections\Connection
 */
class GrpcManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * The Grpc server configurations.
     *
     * @var array
     */
    protected $config;

    /**
     * The Grpc connections.
     *
     * @var mixed
     */
    protected $connections;

    /**
     * Create a new Grpc manager instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @param  array  $config
     * @return void
     */
    public function __construct($app, array $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * Get a Grpc connection by name.
     *
     * @param  string|null  $name
     * @return \GrpcClient\GrpcExamClient
     */
    public function connection($name = null)
    {
        $name = $name ?: 'default';

        if (isset($this->connections[$name])) {
            return $this->connections[$name];
        }

        $config = $this->config[$name];
        // dd($config);
        return $this->connections[$name] = new GrpcExamClient($config['host'] . ':' . $config['port'], [
            'credentials' => \Grpc\ChannelCredentials::createInsecure()
        ]);
    }

    /**
     * Return all of the created connections.
     *
     * @return array
     */
    public function connections()
    {
        return $this->connections;
    }

    /**
     * Pass methods onto the default Redis connection.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->connection()->{$method}(...$parameters);
    }
}
