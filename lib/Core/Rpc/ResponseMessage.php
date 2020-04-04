<?php

namespace Phpactor\LanguageServer\Core\Rpc;

class ResponseMessage extends Message
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var mixed
     */
    public $result;

    /**
     * @var ResponseError|null
     */
    public $responseError;

    /**
     * @param mixed $result
     */
    public function __construct(int $id, $result, ?ResponseError $responseError = null)
    {
        $this->id = $id;
        $this->result = $result;
        $this->responseError = $responseError;
    }
}
