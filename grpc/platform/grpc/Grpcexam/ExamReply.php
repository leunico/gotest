<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: exam.proto

namespace Grpcexam;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;
use Google\Protobuf\Internal\GPBWrapperUtils;

/**
 * Reply 响应结构
 *
 * Generated from protobuf message <code>grpcexam.ExamReply</code>
 */
class ExamReply extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>int32 status = 1;</code>
     */
    private $status = 0;
    /**
     * Generated from protobuf field <code>map<string, string> values = 3;</code>
     */
    private $values;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type int $status
     *     @type array|\Google\Protobuf\Internal\MapField $values
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Exam::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>int32 status = 1;</code>
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Generated from protobuf field <code>int32 status = 1;</code>
     * @param int $var
     * @return $this
     */
    public function setStatus($var)
    {
        GPBUtil::checkInt32($var);
        $this->status = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>map<string, string> values = 3;</code>
     * @return \Google\Protobuf\Internal\MapField
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Generated from protobuf field <code>map<string, string> values = 3;</code>
     * @param array|\Google\Protobuf\Internal\MapField $var
     * @return $this
     */
    public function setValues($var)
    {
        $arr = GPBUtil::checkMapField($var, \Google\Protobuf\Internal\GPBType::STRING, \Google\Protobuf\Internal\GPBType::STRING);
        $this->values = $arr;

        return $this;
    }

}

