<?php

namespace App\Services\File;

use App\Services\File\Informations;

final class PostFormInformations implements Informations
{

    /** @var array **/
    private $cache;
    /** @var string **/
    private $directory;
    /** @var array **/
    private $file;

    /**
     * @param string $directory the directory where the file will be written
     * @param array  $file      the POST file informations
     */
    public function __construct(string $directory, array $file)
    {
        $this->cache     = [];
        $this->directory = $directory;
        $this->file      = $file;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->cachedInformations()['name'];
    }

    /**
    * @inheritDoc
    */
    public function directory(): string
    {
        return $this->cachedInformations()['directory'];
    }

    /**
    * @inheritDoc
    */
    public function extension(): string
    {
        return $this->cachedInformations()['extension'];
    }

    /**
     * Returns the file informations and cache them if necessary.
     *
     * @return array the informations as follows: [ name:string, directory:string, extension:string ]
     */
    private function cachedInformations(): array
    {
        if (false === empty($this->cache)) {
            return $this->cache;
        }

        $infos = pathinfo(sprintf(
            '%s/%s',
            $this->directory,
            $this->file['name'][0]
        ));

        $this->cache['name']      = $infos['filename'];
        $this->cache['directory'] = $infos['dirname'];
        $this->cache['extension'] = $infos['extension'];

        return $this->cache;
    }

}
