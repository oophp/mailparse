<?php

namespace OOPHP\Mailparse;

use OOPHP\Mailparse\Exceptions\NonReadableStream;

/**
 * Class Parse
 *
 * @package OOPHP\Mailparse
 */
class Mailparse
{
    /**
     * @var resource
     */
    protected $resource;

    public function __destruct()
    {
        if ($this->resource) {
            mailparse_msg_free($this->resource);
        }
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath(string $path)
    {
        $this->resource = mailparse_msg_parse_file($path);

        return $this;
    }

    /**
     * @param resource $stream
     *
     * @return $this
     * @throws NonReadableStream
     */
    public function setStream($stream)
    {
        $meta = @stream_get_meta_data($stream);
        if ( ! $meta || ! $meta['mode'] || $meta['mode'][0] != 'r' || $meta['eof']) {
            throw new NonReadableStream();
        }

        $this->resource = mailparse_msg_create();
        while ( ! feof($stream)) {
            mailparse_msg_parse($this->resource, fread($stream, 2082));
        }

        return $this;
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function setText(string $text)
    {
        $this->resource = mailparse_msg_create();
        $eaten          = 0;
        $text_length    = strlen($text);
        while ($eaten < $text_length) {
            mailparse_msg_parse($this->resource, substr($text, $eaten, 2082));
            $eaten += 2082;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function determineBestXferEncoding()
    {
        return mailparse_determine_best_xfer_encoding($this->resource);
    }

    /**
     * @param mixed $filename
     * @param callable $callback [optional]
     *
     * @return string
     */
    public function extractPartFile($filename, callable $callback = null)
    {
        return mailparse_msg_extract_part_file($this->resource, $filename, $callback);
    }

    /**
     * @param string $msgBody
     * @param callable $callback
     *
     * @return void
     */
    public function extractPart(string $msgBody, callable $callback)
    {
        mailparse_msg_extract_part($this->resource, $msgBody, $callback);
    }

    /**
     * @param string $filename
     * @param callable $callback
     *
     * @return void
     */
    public function extractWholePartFile(string $filename, callable $callback)
    {
        mailparse_msg_extract_whole_part_file($this->resource, $filename, $callback);
    }

    /**
     * @return mixed
     */
    public function getPartData()
    {
        return mailparse_msg_get_part_data($this->resource);
    }

    /**
     * @param string $section
     *
     * @return mixed
     */
    public function getPart(string $section)
    {
        return mailparse_msg_get_part($this->resource, $section);
    }

    /**
     * @return mixed
     */
    public function getStructure()
    {
        return mailparse_msg_get_structure($this->resource);
    }
}
