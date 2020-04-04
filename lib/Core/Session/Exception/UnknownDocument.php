<?php

namespace Phpactor\LanguageServer\Core\Session\Exception;

use Exception;

class UnknownDocument extends Exception
{
    public function __construct(string $documentUri)
    {
        parent::__construct(sprintf(
            'Unknown text document "%s"',
            $documentUri
        ));
    }
}
