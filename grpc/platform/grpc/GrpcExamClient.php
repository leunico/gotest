<?php

namespace GrpcClient;

class GrpcExamClient extends \Grpc\BaseStub
{
    public function __construct($hostname, $opts, $channel = null)
    {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * rpc Sget(TestRequest) returns (TestReply) {}
     */
    public function sGet(\Grpcexam\ExamRequest $argument, $metadata=[], $options=[])
    {
        return $this->_simpleRequest(
            'course.CourseService/Get',
            $argument,
            ['Grpcexam\ExamReply', 'decode'],
            $metadata,
            $options
        );
    }
}
