<?php

namespace App\Services\Tracer;

use App\Services\Tracer\TracerAbstract;

/**
 * TracerFile
 * Utilisation
 * $trace = new TracerFile();
 * $trace->log('test');
 */
class TracerFile extends TracerAbstract
{

    /** @var [type] */
    private $_path;
    /** @var [type] */
    private $_deep;
    /** @var [type] */
    private $_timeInFilename;
    /** @var [type] */
    private $_timeByHour;

    /**
     * Retourne le chemin d'ecriture des traces globals
     * @return string
     */
    public function getDirTrace()
    {
            return TracerAbstract::_DIR_TRACE;
    }

    /**
     * rajoute la date a la fin du nom du fichier
     * @param bool $timeInfileName
     */
    public function setTimeInFilename($timeInfileName)
    {
        $this->_timeInFilename = $timeInfileName;
    }

    /**
     * Si le mode TimeInFilename est actif, rajoute l'heure en plus de la date
     * @param bool $timeByHour
     */
    public function setTimeByHour($timeByHour)
    {
        $this->_timeByHour = $timeByHour;
    }

    /**
     * Constructeur
     * @param bool   $timeInFilename Place ou non la date d'écriture dans le nom de la trace
     * @param bool   $timeByHour Place ou non l'heure d'écriture dans le nom de la trace
     * @param string $forceDir Permet de determiner manuellement l'emplacement du dossier
     * @param string $forceName Permet de determiner manuellement le nom du fichier
     * @param int    $deeper
     */
    public function __construct($timeInFilename = false, $timeByHour = false, $forceDir = '', $forceName = '', $deeper = 0)
    {
        $this->deep = $deeper;
        $path       = $this->getDirTrace();

        if ($forceDir != '') {
            $path = $forceDir;
        }

        if (substr($path, -1) != '/') {
            $path .= '/';
        }

        if (!$forceName) {
            $historique = debug_backtrace();
            $deep       = $this->deep;

            if (!isset($historique[$deep])) {
                $deep = count($historique) - 1;
            }

            if (isset($historique[$deep]['file'])) {
                $file = $historique[$deep]['file'];
            } else {
                $file = $path . 'Inconnu';
            }

            $this->_path = $this->findWorkpath($file, $path);
        } else {
            $this->_path = $path . $forceName;
        }

        $this->_timeByHour     = $timeByHour;
        $this->_timeInFilename = $timeInFilename;
    }

    /**
     * Trace une information selon le fichier émeteur dans un fichier
     * @param string $msg le message a enregistré.
     * @param int $type Logger::_DEFAULT, Logger::_WARN, Logger::_ERR, Logger::_LOG
     */
    public function log($msg, $type = TracerAbstract::_DEFAULT)
    {
        if (!is_string($type)) {
            $type = $this->setTraceType($type);
        }

        $filename = $this->_path . ($this->_timeInFilename ? '_' . date('Ymd') : '') . ($this->_timeByHour ? '_' . date('H') : '');

        $this->wLineInFile($filename . '.trc', $type, $msg);

        if ($type != 'trc') {
            $this->wLineInFile($filename . '.' . $type, $type, $msg);
        }
    }

    /**
     * Retourne le chemin complet vers le fichier de trace
     * @return [type]
     */
    public function getFilePath()
    {
        return $this->_path . ($this->_timeInFilename ? '_' . date('Ymd') : '') . ($this->_timeByHour ? '_' . date('H') : '') . '.trc';
    }

    /**
     * TODO [wLineInFile description]
     * @param  [type] $filename
     * @param  [type] $type
     * @param  [type] $msg
     */
    private function wLineInFile($filename, $type, $msg)
    {
        $historique = debug_backtrace();
        $deep       = $this->deep + 1;

        if (!isset($historique[$deep])) {
            $deep = count($historique) - 1;
        }

        $line = '	';

        if (isset($historique[$deep]['line'])) {
            $line = '(' . $historique[$deep]['line'] . ')';
        }

        $fp = @fopen($filename, "a+");

        if ($fp) {
            $writeligne = str_pad(date("H:i:s"), 8, ' ') . '	' . str_pad(substr($type, 0, 4), 4, ' ') . $line . ': ' . $msg . "\r\n";

            fwrite($fp, $writeligne);

            fclose($fp);
        }
    }

    /**
     * TODO [findWorkpath description]
     * @param  [type] $file
     * @param  [type] $defaultPath
     * @return [type]
     */
    private function findWorkpath($file, $defaultPath)
    {
        $basename = basename($file);
        $workpath = substr(str_replace($basename, '', $file), 0, -1);

        return $defaultPath . (substr($defaultPath, -1, 1) != '/' ? '/' : '') . $this->getDiffDir($workpath, $defaultPath) . str_replace('.php', '', $basename);
    }

    /**
     * TODO [getDiffDir description]
     * @param  [type] $scriptDir [description]
     * @param  [type] $basedir   [description]
     * @return [type]            [description]
     */
    private function getDiffDir($scriptDir, $basedir)
    {
        $scriptDir = str_replace('\\', '/', realpath($scriptDir));
        $baseDir   = str_replace('\\', '/', realpath($basedir));
        $scriptDir = explode('/', $scriptDir);
        $base_dir  = explode('/', $baseDir);
        $finalPath = '';
        $pos       = 0;

        // on cherche les donnée qui differe
        foreach ($scriptDir as $dir) {
            while (!isset($base_dir[$pos]) && $pos < count($base_dir)) {
                $pos++;
            }

            if (!isset($base_dir[$pos]) || $base_dir[$pos] != $dir) {
                $finalPath .= $dir . '_';
            }

            $pos++;
        }

        return $finalPath;
    }

}
