<?php

namespace OOPHP\Mailparse;

use OOPHP\Mailparse\Exception\ZlibExtensionsMissingException;

class ZlibMailparse extends Mailparse
{
    public function __construct()
    {
        if (!extension_loaded('zlib')) {
            throw new ZlibExtensionsMissingException();
        }
    }

    /**
     * @param string $text Gzip encoded text
     *
     * @return $this
     */
    public function setText(string $text)
    {
        return parent::setText(gzdecode($text));
    }
}
