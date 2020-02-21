<?php

namespace Legacy;

use Exception;
use PDO;
use App\Infrastructure\DataSource\GlobalData;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Templating\TemplateEngine;
use App\Services\Database\DatabaseFactory;
use App\Services\Database\DatabaseTools;
use App\Services\Security\User\User;
use Legacy\Incident;
use Legacy\MaintenanceInterface;

/**
 * GestionIncident
 */
class GestionIncident
{

    /** @var DatabaseFactory */
    private $dbFactory;
    /** @var GlobalData */
    private $globalData;
    /** @var LoggerInterface */
    private $logger;
    /** @var MaintenanceInterface */
    private $maintenance;
    /** @var TemplateEngine */
    private $templateEngine;
    /** @var User */
    private $user;

    /**
     * @param TemplateEngine       $templateEngine
     * @param LoggerInterface      $logger
     * @param DatabaseFactory      $dbFactory
     * @param MaintenanceInterface $maintenance
     * @param User                 $user
     * @param GlobalData           $globalData
     */
    public function __construct(
        TemplateEngine $templateEngine,
        LoggerInterface $logger,
        $dbFactory,
        MaintenanceInterface $maintenance,
        User $user,
        GlobalData $globalData
    ) {
        DatabaseTools::$UTF8 = true;

        $this->dbFactory      = $dbFactory;
        $this->globalData     = $globalData;
        $this->logger         = $logger;
        $this->maintenance    = $maintenance;
        $this->templateEngine = $templateEngine;
        $this->user           = $user;
    }

    /**
     * TODO [main description]
     */
    public function main()
    {
        return $this->templateEngine
            ->setTemplateDir(DOSSIER_TPL)
            ->assign('userId', $this->user->toArray()['id'])
            ->assign('username', $this->user->toArray()['lastname'])
            ->assign('version', PROJECT_VERSION)
            ->fetch('index.tpl');
    }

    /**
     * TODO [__call description]
     * @param  string $fonction
     * @param  array  $params
     * @return mixed
     * @throws Exception if the method doesn't exist
     */
    public function __call($fonction, $params)
    {
        if (false === str_start_by($fonction, 'get')) {
            throw new Exception('GestionIncident n\'accepte que les appels GET');
        }

        $queries = array(
            'getPriorites' => array(
                'db' => DB_INCIDENT['base'],
                'query' => 'select id,libelle from priorite order by id asc'
            ),
            'getStatuts' => array(
                'db' => DB_INCIDENT['base'],
                'query' => 'select code,libelle,couleur from statut order by ordre asc'
            ),
            'getDeveloppeurs' => array(
                'db' => DB_INCIDENT['base'],
                'query' => 'select id,login,nom,prenom,concat(prenom,\' \',nom) libelle from utilisateur where type=\'DEV\' order by login asc'
            ),
            'getUtilisateurs' => array(
                'db' => DB_INCIDENT['base'],
                'query' => 'select id,login,nom,prenom,concat(prenom,\' \',nom) libelle from utilisateur order by login asc'
            ),
            'getSSiis' => array(
                'db' => DB_SSII['base'],
                'query' => 'select id_ssii id,raison_sociale nom, e_mail1,e_mail2,telephone from ssii order by nom asc'
            ),
            'getCategoriesFull' => array(
                'db' => DB_INCIDENT['base'],
                'query' => 'select id, parent,nom label from categorie order by parent asc, label asc'
            ),
            'getCategories' => array(
                'db' => DB_INCIDENT['base'],
                'query' => 'select id, parent,nom label from categorie where desactive = 0 order by parent asc, label asc'
            )
        );

        if (false === isset($queries[$fonction])) {
            throw new Exception('La méthode GestionIncident->' . $fonction . '() n\'existe pas');
        }

        return $this->dbFactory
            ->createDatabase($queries[$fonction]['db'], true)
            ->query($queries[$fonction]['query'], PDO::FETCH_ASSOC);
    }

    /**
     * TODO [getEmail description]
     * @param  string      $emailId
     * @return string|null
     */
    protected function getEmail($emailId)
    {
        $query = 'select * from email where id = :id';

        $result = $this->dbFactory
            ->createDatabase(DB_INCIDENT['base'], true)
            ->query($query, PDO::FETCH_ASSOC, [ 'id' => $emailId ]);

        if (empty($result)) {
            return null;
        }

        return (object) $result[0];
    }

    /**
     * TODO [getIncident description]
     * @param  string $issueId
     * @return mixed
     */
    protected function getIncident($issueId)
    {
        $issue                 = new Incident($issueId, $this->logger, $this->dbFactory);
        $issueAsArray          = $issue->toArray();
        $issueAsArray['email'] = $this->getEmail($issueAsArray['email_id']);

        return (object) $issueAsArray;
    }

    /**
     * TODO [getIncidentHisto description]
     * @param  string $historyId
     * @return mixed
     */
    private function getIncidentHisto($historyId)
    {
        $query = 'select * from incident_histo a left join utilisateur b on a.utilisateur = b.id where incident_id = :id order by a.date desc';

        return $this->dbFactory
            ->createDatabase(DB_INCIDENT['base'], true)
            ->query($query, PDO::FETCH_ASSOC, [ 'id' => $historyId ]);
    }

    /**
     * TODO [getIncidentsRecents description]
     * @param  string $officeId
     * @return mixed
     */
    private function getIncidentsRecents($officeId)
    {
        $query = 'select a.*,b.nom,b.prenom,b.initiale '
            . 'from incident a '
            . 'left join utilisateur b '
            . 'on a.id_createur = b.id '
            . 'where id_etude = :id '
            . 'order by a.id desc limit 20';

        return $this->dbFactory
            ->createDatabase(DB_INCIDENT['base'], true)
            ->query($query, PDO::FETCH_ASSOC, array('id' => intval($officeId)));
    }

    /**
     * TODO [getIncidentsPrios description]
     * @param  string $officeId
     * @return mixed
     */
    private function getIncidentsPrios($officeId)
    {
        $query = 'select a.*,b.nom,b.prenom,b.initiale '
            . 'from incident a '
            . 'left join utilisateur b '
            . 'on a.id_createur = b.id '
            . 'where id_etude = :id and statut != \'traite\' '
            . 'order by a.priorite desc limit 20';

        return $this->dbFactory
            ->createDatabase(DB_INCIDENT['base'], true)
            ->query($query, PDO::FETCH_ASSOC, array('id' => intval($officeId)));
    }

    /**
     * TODO [getCategoriesStruct description]
     * @return mixed
     */
    private function getCategoriesStruct()
    {
        return $this->arrayToStruct($this->getCategories(), 'id', 'parent');
    }

    /**
     * TODO [arrayToStruct description]
     * TODO en doublon avec GlobalData::arrayToStruct
     * @param  [type] $array
     * @param  [type] $champId
     * @param  [type] $champParent
     * @param  [type] $idCourant
     * @return [type]
     */
    private function arrayToStruct($array, $champId, $champParent, $idCourant = null) {
        $niveau = [];
        $autres = [];

        foreach ($array as $ligne) {
            $ligne[$champParent] == $idCourant
                ? $niveau[] = $ligne
                : $autres[] = $ligne;
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
     * TODO [getGlobalData description]
     * @return array
     */
    protected function getGlobalData()
    {
        return $this->globalData->toArray();
    }

    /**
     * TODO [getIncidentIntervenant description]
     * @param  string $contactId
     * @return mixed
     */
    private function getIncidentIntervenant($contactId)
    {
        $query = 'select * from incident_intervenant where incident_id = :id order by id asc';

        return $this->dbFactory
            ->createDatabase(DB_INCIDENT['base'], true)
            ->query($query, PDO::FETCH_ASSOC, [ 'id' => $contactId ]);
    }

    /**
     * TODO [getIncidentPJ description]
     * @param  string $issueId
     * @return mixed
     */
    private function getIncidentPJ($issueId)
    {
        $query = 'select * from incident_pj where incident_id = :id order by id asc';

        return $this->dbFactory
            ->createDatabase(DB_INCIDENT['base'], true)
            ->query($query, PDO::FETCH_ASSOC, [ 'id' => $issueId ]);
    }

    /**
     * TODO [jsonFormIncident description]
     */
    public function jsonFormIncident()
    {
        $data    = $this->getGlobalData();
        $issueId = filter_input(INPUT_POST, 'id');
        $issue   = new Incident($issueId, $this->logger, $this->dbFactory);

        $data['ssii']              = $issue->getSSII();
        $data['ssii_contacts']     = $issue->getContactsSSII();
        $data['incident']          = $issue->toArray();
        $data['incident']['play']  = $issue->estEnLecture();
        $data['incident']['email'] = $this->getEmail($data['incident']['email_id']);
        $data['pjs']               = $this->getIncidentPJ($issueId);
        $data['intervenants']      = $this->getIncidentIntervenant($issueId);

        $data['etude'] = empty($data['incident']['id_etude'])
            ? (object) []
            : (object) $this->getEtude($data['incident']['id_etude']);

        return json_encode((object) $data);
    }

    /**
     * TODO [jsonCategoriesManager description]
     */
    public function jsonCategoriesManager()
    {
        return json_encode($this->getCategoriesStruct());
    }

    /**
     * TODO [jsonIncidentHisto description]
     */
    public function jsonIncidentHisto()
    {
        return json_encode($this->getIncidentHisto(filter_input(INPUT_POST, 'id')));
    }

    /**
     * TODO [jsonIncidentRecents description]
     */
    public function jsonIncidentRecents()
    {
        $officeId = intval(filter_input(INPUT_POST, 'id_etude'));
        $tabs     = array();

        if (!empty($officeId)) {
            $tabs = $this->getIncidentsRecents($officeId);
        }

        return json_encode($tabs);
    }

    /**
     * TODO [jsonIncidentPrios description]
     */
    public function jsonIncidentPrios()
    {
        $officeId = intval(filter_input(INPUT_POST, 'id_etude'));
        $tabs     = array();

        if (!empty($officeId)) {
            $tabs = $this->getIncidentsPrios($officeId);
        }

        return json_encode($tabs);
    }

    /**
     * TODO [getEtude description]
     * @param  string $officeID
     * @return mixed
     */
    private function getEtude($officeID)
    {
        $query = 'select * from etudes where code_unique_hj = :etude';

        $results = $this->dbFactory
            ->createDatabase(DB_GLOBAL['base'], true)
            ->query($query, PDO::FETCH_ASSOC, [ 'etude' => intval($officeID) ]);

        return nvl($results[0], array());
    }

    /**
     * TODO [enregCategorieEdit description]
     */
    public function enregCategorieEdit()
    {
        $categoryId = filter_input(INPUT_POST, 'id');
        $nom        = filter_input(INPUT_POST, 'nom');

        DatabaseTools::update(
            DB_INCIDENT['base'],
            $this->logger,
            'categorie',
            [ 'nom' => $nom ],
            'id',
            $categoryId,
            true);

        return 'id=' . $categoryId;
    }

    /**
     * TODO [enregCategorieSuppr description]
     */
    public function enregCategorieSuppr()
    {
        DatabaseTools::update(
            DB_INCIDENT['base'],
            $this->logger,
            'categorie',
            [ 'desactive' => 1 ],
            'id',
            filter_input(INPUT_POST, 'id'),
            true);

        return 'ok';
    }

    /**
     * TODO [enregCategorieAddChild description]
     */
    public function enregCategorieAddChild()
    {
        $parent = filter_input(INPUT_POST, 'parent');
        $nom    = filter_input(INPUT_POST, 'nom');

        $categoryId = DatabaseTools::insert(
            DB_INCIDENT['base'],
            $this->logger,
            'categorie',
            [
                'parent' => $parent,
                'nom'    => $nom
            ],
            false,
            true
        );

        return 'id=' . $categoryId;
    }

    /**
     * TODO [enregDroitAffectation description]
     */
    public function enregDroitAffectation()
    {
        $right        = filter_input(INPUT_POST, 'droit');
        $category     = filter_input(INPUT_POST, 'categorie');
        $createIfTrue = filter_input(INPUT_POST, 'valeur');

        $createIfTrue === 'true'
            ? $this->createRight($right, $category)
            : $this->deleteRight($right, $category);

        return 'ok';
    }

    /**
     * Creates a right
     *
     * @param [type] $right
     * @param [type] $category
     */
    private function createRight($right, $category)
    {
        DatabaseTools::insert(
            DB_INCIDENT['base'],
            $this->logger,
            'droitaffect',
            [
                'categorie' => $category,
                'droit'     => $right
            ],
            false,
            true
        );
    }

    /**
     * Deletes a right
     *
     * @param [type] $right
     * @param [type] $category
     */
    private function deleteRight($right, $category)
    {
        $query      = 'delete from droitaffect where categorie = :category and droit = :right';
        $parameters = [ 'category' => $category, 'right' => $right ];

        $this->dbFactory
            ->createDatabase(DB_INCIDENT['base'], true)
            ->exec($query, $parameters);
    }

    /**
     * TODO [rewriteDataWithKey description]
     * @param  array  $data
     * @param  string $key
     * @return array
     */
    private function rewriteDataWithKey($data, $key)
    {
        $result = [];

        foreach ($data as $item) {
            $result[$item[$key]] = $item;
        }

        return $result;
    }

    /**
     * TODO [getDroitsCategories description]
     * @return mixed
     */
    protected function getDroitsCategories()
    {
        $sql = 'select * from catdroit order by libelle asc';

        $data = $this->dbFactory
            ->createDatabase(DB_INCIDENT['base'], true)
            ->query($sql, PDO::FETCH_ASSOC);

        return $this->rewriteDataWithKey($data, 'code');
    }

    /**
     * TODO [getDroits description]
     * @return mixed
     */
    protected function getDroits()
    {
        $sql = 'select * from droit order by categorie asc,libelle asc';

        $data = $this->dbFactory
            ->createDatabase(DB_INCIDENT['base'], true)
            ->query($sql, PDO::FETCH_ASSOC);

        return $this->rewriteDataWithKey($data, 'code');
    }

    /**
     * TODO [getDroitsAffectations description]
     * @return mixed
     */
    protected function getDroitsAffectations()
    {
        $sql = 'select * from droitaffect';

        return $this->dbFactory
            ->createDatabase(DB_INCIDENT['base'], true)
            ->query($sql, PDO::FETCH_ASSOC);
    }

    /**
     * TODO [getUtilisateursCategories description]
     * @return mixed
     */
    protected function getUtilisateursCategories() {
        $sql = 'select * from type_utilisateur order by libelle asc';

        $data = $this->dbFactory
            ->createDatabase(DB_INCIDENT['base'], true)
            ->query($sql, PDO::FETCH_ASSOC);

        return $this->rewriteDataWithKey($data, 'code');
    }

    /**
     * TODO [jsonDroits description]
     */
    public function jsonDroits()
    {
        return json_encode((object) array(
            'catdroits'    => $this->getDroitsCategories(),
            'droits'       => $this->getDroits(),
            'catusers'     => $this->getUtilisateursCategories(),
            'affectations' => $this->getDroitsAffectations()
        ));
    }

    /**
     * TODO [jsonMaintenanceAction description]
     */
    public function jsonMaintenanceAction()
    {
        $code = filter_input(INPUT_POST, 'code', FILTER_SANITIZE_SPECIAL_CHARS);

        return json_encode($this->maintenance->actionBouton($code));
    }

}
