<?php

namespace App\Services\File;

use Exception;
use App\Services\File\File;
use App\Services\File\Informations;

/**
 * A file sent from a web form, using a HTTP POST request.
 */
final class PostFormFile implements File
{

    /** @var array **/
    private $cache;
    /** @var Informations **/
    private $informations;
    /** @var string **/
    private $postTmpName;

    /**
     * @param Informations $informations
     * @param array        $postInformations
     */
    public function __construct(Informations $informations, array $postInformations)
    {
        $this->cache        = [];
        $this->informations = $informations;
        $this->postTmpName  = $postInformations['tmp_name'][0];
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->versionedName(
            $this->informations->directory(),
            $this->informations->name(),
            $this->informations->extension()
        );
    }

    /**
     * @inheritDoc
     */
    public function path(): string
    {
        return sprintf(
            '%s/%s',
            $this->informations->directory(),
            $this->name()
        );
    }

    /**
     * @inheritDoc
     */
    public function write(): File
    {
        if (false === file_exists($this->informations->directory())) {
            mkdir($this->informations->directory(), 0750);
        }

        $written = move_uploaded_file($this->postTmpName, $this->path());

        if (false === $written) {
            throw new Exception(sprintf(
                'failed to write the POST file "%s.%s" to "%s"',
                $this->informations->name(),
                $this->informations->extension(),
                $this->path()
            ));
        }

        return $this;
    }
    /**

     * Returns the file name, including its version. The version is increased
     *   until no file with the same name is found.
     *
     * @param  string  $directory
     * @param  string  $name
     * @param  string  $extension
     * @param  integer $version
     *
     * @return string
     */
    private function versionedName(string $directory, string $name, string $extension, int $version = 0): string
    {
        if (true === isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $fullName = sprintf(
            '%s%s.%s',
            $name,
            0 === $version
                ? ''
                : ' (' . $version . ')',
            $extension
        );

        if (true === file_exists(sprintf('%s/%s', $directory, $fullName))) {
            return $this->versionedName($directory, $name, $extension, $version + 1);
        }

        $this->cache[$name] = $fullName;

        return $fullName;
    }

}
