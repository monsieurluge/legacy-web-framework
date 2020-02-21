<?php

namespace App\Services\Request\CustomRequest\Api\Get;

use App\Services\Request\Request;

final class Stats
{
    /** @var Request **/
    private $request;

    /**
     * @codeCoverageIgnore
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function courbe(): string
    {
        return $this->request->queryParameter('courbe', '');
    }

    public function date1(): string
    {
        return sprintf(
            '%s 00:00:00',
            $this->request->queryParameter('date1', '2013-11-25')
        );
    }

    public function date2(): string
    {
        return sprintf(
            '%s 23:59:59',
            $this->request->queryParameter('date2', date('Y-m-d'))
        );
    }

    public function donnee(): string
    {
        return $this->request->queryParameter('donnee', '');
    }

    public function echelle(): string
    {
        return $this->request->queryParameter('echelle', 'jour');
    }

    public function echChamp(): string
    {
        return $this->request->queryParameter('ech_champ', '');
    }
}
