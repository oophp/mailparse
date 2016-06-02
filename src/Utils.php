<?php

namespace OOPHP\Mailparse;

class Utils
{
    /**
     * @param resource $filePointer A valid file pointer, which must be seek-able.
     *
     * @return string Returns one of the character encodings supported by the mbstring module.
     */
    public static function determineBestXferEncoding($filePointer)
    {
        return mailparse_determine_best_xfer_encoding($filePointer);
    }

    /**
     * @param resource $srcFilePointer  A valid file handle. The file is streamed through the parser.
     * @param resource $destFilePointer The destination file handle in which the encoded data will be written.
     * @param string   $encoding        One of the character encodings supported by the mbstring module.
     *
     * @return bool Returns TRUE on success or FALSE on failure.
     */
    public function encodeStream($srcFilePointer, $destFilePointer, string $encoding)
    {
        return mailparse_stream_encode($srcFilePointer, $destFilePointer, $encoding);
    }

    /**
     * @param string $addresses A string containing addresses, like in: Wez Furlong <wez@example.com>, doe@example.com
     *
     * @return array Returns an array of associative arrays with the following keys for each recipient:
     *               - display  => The recipient name, for display purpose. If this part is not set for a recipient,
     *                             this key will hold the same value as address.
     *               - address  => The email address
     *               - is_group => TRUE if the recipient is a newsgroup, FALSE otherwise.
     */
    public function parseRFC822Addresses(string $addresses)
    {
        return mailparse_rfc822_parse_addresses($addresses);
    }

    /**
     * @param resource $filePointer A valid file pointer.
     *
     * @return array Returns an array of associative arrays listing filename information.
     *               - filename     => Path to the temporary file name created
     *               - origfilename => The original filename, for uuencoded parts only
     *               The first filename entry is the message body. The next entries are the decoded uuencoded files.
     */
    public function uudecodeAll($filePointer)
    {
        return mailparse_uudecode_all($filePointer);
    }
}
