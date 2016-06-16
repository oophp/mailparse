<?php

namespace OOPHP\Mailparse;

class ZlibMailparse extends Mailparse
{
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
