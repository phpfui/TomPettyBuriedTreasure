<?php

namespace Laminas\Validator\File;

use function array_keys;
use function array_unique;
use function hash_file;
use function is_readable;

/**
 * Validator for the sha1 hash of given files
 *
 * @deprecated Since 2.61.0 Use the {@link Hash} validator and specify `sha1` as the algorithm
 *
 * @final
 */
class Sha1 extends Hash
{
    use FileInformationTrait;

    /**
     * @const string Error constants
     */
    public const DOES_NOT_MATCH = 'fileSha1DoesNotMatch';
    public const NOT_DETECTED   = 'fileSha1NotDetected';
    public const NOT_FOUND      = 'fileSha1NotFound';

    /** @var array Error message templates */
    protected $messageTemplates = [
        self::DOES_NOT_MATCH => 'File does not match the given sha1 hashes',
        self::NOT_DETECTED   => 'A sha1 hash could not be evaluated for the given file',
        self::NOT_FOUND      => 'File is not readable or does not exist',
    ];

    /**
     * Options for this validator
     *
     * @var string
     */
    protected $options = [
        'algorithm' => 'sha1',
        'hash'      => null,
    ];

    /**
     * Returns all set sha1 hashes
     *
     * @deprecated Since 2.61.0 - All getters and setters will be removed in 3.0
     *
     * @return array
     */
    public function getSha1()
    {
        return $this->getHash();
    }

    /**
     * Sets the sha1 hash for one or multiple files
     *
     * @deprecated Since 2.61.0 - All getters and setters will be removed in 3.0
     *
     * @param  string|array $options
     * @return Hash Provides a fluent interface
     */
    public function setSha1($options)
    {
        $this->setHash($options);
        return $this;
    }

    /**
     * Adds the sha1 hash for one or multiple files
     *
     * @deprecated Since 2.61.0 - All getters and setters will be removed in 3.0
     *
     * @param  string|array $options
     * @return Hash Provides a fluent interface
     */
    public function addSha1($options)
    {
        $this->addHash($options);
        return $this;
    }

    /**
     * Returns true if and only if the given file confirms the set hash
     *
     * @param (int|string)[]|string $value Filename to check for hash
     * @param array                 $file  File data from \Laminas\File\Transfer\Transfer (optional)
     * @return bool
     */
    public function isValid($value, $file = null)
    {
        $fileInfo = $this->getFileInfo($value, $file);

        $this->setValue($fileInfo['filename']);

        // Is file readable ?
        if (empty($fileInfo['file']) || false === is_readable($fileInfo['file'])) {
            $this->error(self::NOT_FOUND);
            return false;
        }

        $hashes   = array_unique(array_keys($this->getHash()));
        $filehash = hash_file('sha1', $fileInfo['file']);
        if ($filehash === false) {
            $this->error(self::NOT_DETECTED);
            return false;
        }

        foreach ($hashes as $hash) {
            if ($filehash === $hash) {
                return true;
            }
        }

        $this->error(self::DOES_NOT_MATCH);
        return false;
    }
}
