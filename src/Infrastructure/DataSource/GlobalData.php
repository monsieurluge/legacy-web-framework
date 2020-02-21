<?php

namespace App\Infrastructure\DataSource;

use PDO;
use App\Services\Database\Database;
use App\Services\Security\User\User;

/**
 * "Legacy" Global Data
 */
final class GlobalData
{

    /** @var Database **/
    private $dbIncident;
    /** @var Database **/
    private $dbSsii;
    /** @var User **/
    private $user;

    /**
     * @param Database $dbIncident
     * @param Database $dbSsii
     * @param User     $user
     */
    public function __construct(Database $dbIncident, Database $dbSsii, User $user)
    {
        $this->dbIncident = $dbIncident;
        $this->dbSsii     = $dbSsii;
        $this->user       = $user;
    }

    /**
     * Returns the GlobalData as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        $categories = $this->getCategories();

        return [
            'categories'    => $this->arrayToStruct($categories, 'id', 'parent'),
            'allCategories' => $categories,
            'statuts'       => $this->getStatuts(),
            'priorites'     => $this->getPriorites(),
            'ssiis'         => $this->getSSiis(),
            'utilisateurs'  => $this->getUtilisateurs(),
            'context'       => $this->getContext(),
            'types'         => $this->getTypes(),
            'origines'      => [
                [ 'code' => 'tel', 'libelle' => 'Téléphone' ],
                [ 'code' => 'email', 'libelle' => 'Email' ],
                [ 'code' => 'fichier', 'libelle' => 'Fichier erreur' ]
            ],
            'default'       => [ 'id_assigne' => $this->user->toArray()['id'] ],
            'major'         => $this->getMajorIssues()
        ];
    }

    /**
     * Returns the categories
     *
     * @return array
     */
    private function getCategories(): array
    {
        $query = 'select id, parent, nom label from categorie where desactive = 0 order by parent asc, label asc';

        return $this->dbIncident->query($query, PDO::FETCH_ASSOC);
    }

    /**
     * Returns the context list
     *
     * @return array
     */
    private function getContext(): array
    {
        $query = 'select id, name from context';

        return $this->dbIncident->query($query, PDO::FETCH_ASSOC);
    }

    /**
     * TODO [arrayToStruct description]
     * TODO en doublon avec GestionIncident::arrayToStruct
     *
     * @param  [type] $array
     * @param  [type] $champId
     * @param  [type] $champParent
     * @param  [type] $idCourant
     * @return [type]
     */
    private function arrayToStruct($array, $champId, $champParent, $idCourant = null)
    {
        $niveau = [];
        $autres = [];

        foreach ($array as $ligne) {
            if ($ligne[$champParent] == $idCourant) {
                $niveau[] = $ligne;
            } else {
                $autres[] = $ligne;
            }
        }

        foreach ($niveau as $i => $ligne) {
            $items = $this->arrayToStruct($autres, $champId, $champParent, $ligne[$champId]);

            if (count($items) > 0) {
                $niveau[$i]['children'] = $items;
            }
        }

        return $niveau;
    }

    /**
     * Returns the opened major issues, if any
     *
     * @return array
     */
    private function getMajorIssues(): array
    {
        $query = 'SELECT id, titre as title, date_ouverture as createdAt, context_id as context'
            . ' FROM incident'
            . ' WHERE incident.statut IN ("nouveau", "attente")'
            . ' AND incident.type = "majeur"'
            . ' ORDER BY date_ouverture ASC';

        return $this->dbIncident->query($query, PDO::FETCH_ASSOC);
    }

    /**
     * Returns the priorities
     *
     * @return array
     */
    private function getPriorites(): array
    {
        $query = 'select id,libelle from priorite order by id asc';

        return $this->dbIncident->query($query, PDO::FETCH_ASSOC);
    }

    /**
     * Returns the SSII list
     *
     * @return array
     */
    private function getSSiis(): array
    {
        $query = 'select id_ssii id,raison_sociale nom, e_mail1,e_mail2,telephone from ssii order by nom asc';

        return $this->dbSsii->query($query, PDO::FETCH_ASSOC);
    }

    /**
     * Returns the status list
     *
     * @return array
     */
    private function getStatuts(): array
    {
        $query = 'select code,libelle,couleur from statut order by ordre asc';

        return $this->dbIncident->query($query, PDO::FETCH_ASSOC);
    }

    /**
     * Returns the types
     *
     * @return array
     */
    private function getTypes(): array
    {
        $query = 'SELECT code, libelle FROM type_incident ORDER BY code ASC';

        return $this->dbIncident->query($query, PDO::FETCH_ASSOC);
    }

    /**
     * Returns the users
     *
     * @return array
     */
    private function getUtilisateurs(): array
    {
        $query = 'select id,login,nom,prenom,concat(prenom,\' \',nom) libelle from utilisateur order by login asc';

        return $this->dbIncident->query($query, PDO::FETCH_ASSOC);
    }

}
