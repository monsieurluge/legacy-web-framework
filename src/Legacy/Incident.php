<?php

namespace Legacy;

use Exception;
use PDO;
use App\ServiceInterfaces\Log\LoggerInterface;
use App\Services\Mail\DTO\EMail;
use App\Services\Database\DatabaseFactory;
use App\Services\Database\DatabaseTools;
use App\Services\Text\Windows1252Text;
use Legacy\DbToClass;

/**
 * Incident
 */
class Incident extends DbToClass
{

    /** @var string */
    const CLASSNAME = 'Incident';
    /** @var string */
    const TABLE = 'incident';
    /** @var string */
    const PRIMARYKEY = 'id';

    /** @var [type] */
    protected $id = null;
    /** @var [type] */
    protected $id_createur = null;
    /** @var [type] */
    protected $id_assigne = null;
    /** @var [type] */
    protected $id_developpeur = null;
    /** @var [type] */
    protected $id_ssii = null;
    /** @var [type] */
    protected $id_etude = null;
    /** @var [type] */
    protected $id_do = null;
    /** @var [type] */
    protected $origine = null;
    /** @var [type] */
    protected $categorie = null;
    /** @var [type] */
    protected $context;
    /** @var DatabaseFactory */
    private $dbFactory;
    /** @var LoggerInterface */
    private $logger;
    /** @var [type] */
    protected $priorite = null;
    /** @var [type] */
    protected $statut = null;
    /** @var [type] */
    protected $titre = null;
    /** @var [type] */
    protected $description = null;
    /** @var [type] */
    protected $date_ouverture = null;
    /** @var [type] */
    protected $date_fermeture = null;
    /** @var [type] */
    protected $date_modif = null;
    /** @var [type] */
    protected $temps = 0;
    /** @var [type] */
    protected $escalade = 0;
    /** @var [type] */
    protected $non_lu = 0;
    /** @var [type] */
    protected $email_id = null;
    /** @var [type] */
    protected $type = null;
    /** @var [type] */
    protected $interlocuteur = null;
    /** @var string */
    protected $interlocuteur_email = '';
    /** @var string */
    protected $interlocuteur_telephone = '';
    /** @var string */
    protected $actions = '';
    /** @var string */
    protected $conditions = '';

    /**
     * Creates an Incident object
     * @param int             $issueId
     * @param LoggerInterface $logger
     * @param DatabaseFactory $dbFactory
     */
    public function __construct($issueId = null, LoggerInterface $logger, $dbFactory)
    {
        $this->dbFactory = $dbFactory;
        $this->logger    = $logger;

        if (empty($issueId)) {
            $this->statut         = 'nouveau';
            $this->date_ouverture = date('Y-m-d H:i:s');
            $this->date_modif     = date('Y-m-d H:i:s');
        } else {
            $this->charger($issueId);
        }
    }

    /**
     * TODO [setCreateur description]
     * @param string $createur
     */
    public function setCreateur($createur)
    {
        $this->id_createur = $createur;
        $this->id_assigne  = $createur;
    }

    /**
     * TODO [hasIdEtude description]
     * @return bool
     */
    public function hasIdEtude()
    {
        return !empty($this->id_etude);
    }

    /**
     * Updates the Issue using the given key-value pairs
     *
     * @param array $newValues
     */
    public function setByArray($newValues)
    {
        $sanitized = $this->sanitizeValues($newValues);

        // priority
        if (isset($sanitized['priorite']) && $this->isNewPriority($sanitized['priorite'])) {
            $this->setEscalade($sanitized['priorite'], $this->priorite);
        }

        // context
        if (isset($sanitized['context_id'])) {
            $sanitized['context'] = $sanitized['context_id'];
            unset($sanitized['context_id']);
        }

        return parent::setByArray($sanitized);
    }

    /**
     * Is the given value a valid new priority ?
     *
     * @param misc $newValue
     * @return bool
     */
    private function isNewPriority($newValue): bool
    {
        return false === empty($newValue)
            && null !== $this->priorite
            && $newValue != $this->priorite;
    }

    /**
     * TODO [setByEmail description]
     * @param EMail $email
     * @param int   $emailID
     */
    public function setByEmail(EMail $email, int $emailID)
    {
        $subject = mb_substr($email->subject(), 0, 70);

        $this->non_lu   = 1;
        $this->origine  = 'email';
        $this->priorite = 2;
        $this->titre    = empty($subject) ? 'sans titre' : (new Windows1252Text($subject))->toString();
        $this->email_id = $emailID;
        $this->actions  = 'N/A';

        $this->description = sprintf(
            'Ticket créé automatiquement par import de mail.%sExpéditeur : %s',
            PHP_EOL,
            $email->from()
        );
    }

    /**
     * Escalade :
     * 1 = baisse de priorité
     * 2 = hausse de priorité
     * 3 = baisse puis hausse de priorité
     * 4 = hausse puis baisse de priorité
     * 5 = multi changement
     * @param int $avant Priorité avant
     * @param int $apres Priorité après
     */
    protected function setEscalade($avant, $apres)
    {
        if ($this->escalade == 0) {
            $this->escalade = ($apres > $avant) ? 2 : 1;
        } else {
            if ($this->escalade == 1 && $apres > $avant) {
                $this->escalade = 3;
            } else if ($this->escalade == 2 && $apres < $avant) {
                $this->escalade = 4;
            } else if ($this->escalade >= 2) {
                $this->escalade = 5;
            }
        }
    }

    /**
     * Charge un incident par son id
     * @param int $issueId
     */
    public function charger($issueId)
    {
        $this->id = $issueId;

        $this->loadByDb(DB_INCIDENT['base'], self::TABLE, self::PRIMARYKEY, true);
    }

    /**
     * TODO [creer description]
     * @param  [type] $userId
     * @return [type]
     */
    public function creer($userId) {
        $this->statut         = 'nouveau';
        $this->id_createur    = $userId;
        $this->date_ouverture = date('Y-m-d H:i:s');
        $retour               = $this->saveInDb(DB_INCIDENT['base'], self::TABLE, self::PRIMARYKEY, true);

        return $retour;
    }

    /**
     * Enregistre l'incident
     *
     * @return mixed
     * @throws Exception
     */
    public function enregistrer()
    {
        if (is_null($this->origine)) {
            $this->origine = 'support';
        }

        if ($this->date_fermeture == null && $this->statut == 'traite') {
            $this->date_fermeture = date('Y-m-d H:i:s');
        }

        $retour = $this->saveInDb(DB_INCIDENT['base'], self::TABLE, self::PRIMARYKEY, true);

        if ($retour) {
            if ($this->statut == 'attente') {
                $this->pause();
            } else if ($this->statut == 'traite') {
                $this->arret();
            } else {
                $this->lecture();
            }
        }

        if ('integer' === gettype($retour) && is_null($this->id)) {
            $this->id = $retour;
        }

        if (false === $retour) {
            throw new Exception(sprintf(
                'l\'incident #%s n\'a pas pu être sauvegardé',
                $this->id
            ));
        }

        return $retour;
    }

    /**
     * TODO [majDateModif description]
     */
    public function majDateModif()
    {
        $this->date_modif = date('Y-m-d H:i:s');

        DatabaseTools::update(
            DB_INCIDENT['base'],
            $this->logger(),
            self::TABLE,
            [ 'date_modif' => date('Y-m-d H:i:s') ],
            self::PRIMARYKEY,
            $this->id,
            true
        );
    }

    /**
     * TODO [majNonLu description]
     */
    public function majNonLu()
    {
        $this->date_modif = date('Y-m-d H:i:s');
        $this->non_lu     = 1;

        DatabaseTools::update(
            DB_INCIDENT['base'],
            $this->logger(),
            self::TABLE,
            [
                'non_lu'     => 1,
                'date_modif' => date('Y-m-d H:i:s')
            ],
            self::PRIMARYKEY,
            $this->id,
            true
        );
    }

    /**
     * TODO [majLu description]
     */
    public function majLu()
    {
        $this->date_modif = date('Y-m-d H:i:s');
        $this->non_lu     = 0;

        DatabaseTools::update(
            DB_INCIDENT['base'],
            $this->logger(),
            self::TABLE,
            [
                'non_lu'     => 0,
                'date_modif' => date('Y-m-d H:i:s')
            ],
            self::PRIMARYKEY,
            $this->id,
            true
        );
    }

    /**
     * TODO [addHisto description]
     * @param string $utilisateur
     * @param string $texte
     * @param array  $diff
     */
    public function addHisto($utilisateur, $texte, $diff = [])
    {
        $texteTab[] = empty($texte)
            ? 'aucun message d\'historique'
            : $texte;

        if (!empty($diff)) {
            foreach ($diff as $champ => $d) {
                $texteTab[] = $champ . ' : ' . substr($d, 0, 100);
            }
        }

        $str = implode($texteTab, "\n");

        $data = [
            'incident_id' => $this->id,
            'date'        => date('Y-m-d H:i:s'),
            'utilisateur' => $utilisateur,
            'texte'       => (new Windows1252Text($str))->toString()
        ];

        DatabaseTools::insert(
            DB_INCIDENT['base'],
            $this->logger(),
            'incident_histo',
            $data,
            false,
            true
        );

        return $this;
    }

    /**
     * TODO [addPJ description]
     * @param string $utilisateur
     * @param string $attachment
     */
    public function addPJ($utilisateur, $attachment)
    {
        DatabaseTools::insert(
            DB_INCIDENT['base'],
            $this->logger(),
            'incident_pj',
            [
                'incident_id' => $this->id,
                'date'        => date('Y-m-d H:i:s'),
                'utilisateur' => $utilisateur,
                'pj'          => $attachment
            ],
            true,
            true
        );
    }

    /**
     * TODO [addIntervenant description]
     * @param array $intervenant
     */
    public function addIntervenant($intervenant)
    {
        $intervenant['incident_id'] = $this->id;

        return DatabaseTools::insert(
            DB_INCIDENT['base'],
            $this->logger(),
            'incident_intervenant',
            $intervenant,
            false,
            true
        );
    }

    /**
     * TODO [getClass description]
     * @return array
     */
    public function getClass()
    {
        $classes = [
            1 => 'active',
            2 => 'active',
            3 => 'warning',
            4 => 'error'
        ];

        return $classes[$this->priorite];
    }

    /**
     * TODO [getOrigine description]
     * @return mixed
     */
    public function getOrigine()
    {
        return $this->origine;
    }

    /**
     * TODO [getSSII description]
     * @return array
     */
    public function getSSII()
    {
        if (empty($this->id_ssii)) {
            return [];
        }

        $query = 'select * from ssii where id_ssii = ' . $this->id_ssii;

        $results = $this->dbFactory()
            ->createDatabase(DB_SSII['base'], true)
            ->query($query, PDO::FETCH_ASSOC);

        return $results > 0
            ? $results[0]
            : $results;
    }

    /**
     * TODO [getRefLegacy description]
     * @return string
     */
    public function getRefLegacy()
    {
        return str_pad($this->id_etude, 4, '0', STR_PAD_LEFT);
    }

    /**
     * TODO [getContactsSSII description]
     * @return array
     */
    public function getContactsSSII()
    {
        if (!empty($this->id_ssii)) {
            $query = 'select * from auteur where type=\'ssii\' and id_type = ' . $this->id_ssii . ' order by nom, prenom';

            return $this->dbFactory()
                ->createDatabase(DB_SSII['base'], true)
                ->query($query, PDO::FETCH_ASSOC);
        }

        return [];
    }

    /**
     * TODO [toArray description]
     * @return array
     */
    public function toArray()
    {
        $data = parent::toArray();

        $data['id_etude'] = empty($data['id_etude'])
            ? null
            : intval(str_pad($data['id_etude'], 4, '0', STR_PAD_LEFT));

        // context
        $data['context_id'] = $data['context'];
        unset($data['context']);

        return $data;
    }

    /**
     * TODO [estEnLecture description]
     * @return bool
     */
    public function estEnLecture()
    {
        $query = 'select * from incident_temps where incident_id = :id and fin is null';

        $result = $this->dbFactory()
            ->createDatabase(DB_INCIDENT['base'], false)
            ->query($query, PDO::FETCH_ASSOC, array('id' => $this->id));

        return (count($result) === 1);
    }

    /**
     * TODO [lecture description]
     * @return mixed
     */
    public function lecture()
    {
        if ($this->estEnLecture()) {
            return false;
        }

        $update = 'insert incident_temps set incident_id = :id, debut=now()';

        return $this->dbFactory()
            ->createDatabase(DB_INCIDENT['base'], DatabaseTools::$UTF8)
            ->exec($update, array('id' => $this->id));
    }

    /**
     * TODO [pause description]
     * @return mixed
     */
    public function pause()
    {
        if ($this->estEnLecture() == false) {
            return false;
        }

        $query    = 'select id from incident_temps where incident_id = :id and fin is null';
        $database = $this->dbFactory()->createDatabase(DB_INCIDENT['base'], DatabaseTools::$UTF8);

        $result = $database->query($query, PDO::FETCH_ASSOC, array('id' => $this->id));
        $update = 'update incident_temps set fin = now(), duree = ABS(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(debut)) where id = :id';

        return $database->exec($update, array('id' => $result[0]['id']));
    }

    /**
     * TODO [arret description]
     * @return mixed
     */
    public function arret()
    {
        if ($this->estEnLecture() === false) {
            // TODO revoir la possibilité d'arrêter le temps de traitement
            return false;
        }

        $selectQuery = 'select id from incident_temps where incident_id = :id and fin is null';
        $updateQuery = 'update incident_temps set fin = now(), duree = ABS(UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(debut)) where id = :id';

        $database = $this->dbFactory()->createDatabase(DB_INCIDENT['base'], DatabaseTools::$UTF8);
        $select   = $database->query($selectQuery, PDO::FETCH_ASSOC, array('id' => $this->id));

        return $database->exec($updateQuery, array('id' => $select[0]['id']));
    }

    /**
     * TODO [getDureeLecture description]
     * @return mixed
     */
    public function getDureeLecture()
    {
        $query = 'select SUM(TIMESTAMPDIFF(SECOND, debut, IFNULL(fin, now()))) total from incident_temps where incident_id = :id';

        $result = $this->dbFactory()
            ->createDatabase(DB_INCIDENT['base'], DatabaseTools::$UTF8)
            ->query($query, PDO::FETCH_ASSOC, array('id' => $this->id));

        return intval($result[0]['total']);
    }

    /**
     * TODO [getDureePause description]
     * @return int
     */
    public function getDureePause()
    {
        $query = 'select * from incident_temps where incident_id = :id order by id asc';

        $result = $this->dbFactory()
            ->createDatabase(DB_INCIDENT['base'], DatabaseTools::$UTF8)
            ->query($query, PDO::FETCH_ASSOC, array('id' => $this->id));

        if (count($result) <= 1) {
            return 0;
        }

        $total = 0;

        for ($index = 1, $leni = count($result); $index < $leni; $index++) {
            $result1 = $result[$index - 1];
            $result2 = $result[$index];

            if (isset($result1['arret']) && $result1['arret'] == 0) {
                $total1 = date_mysql_to_timestamp($result1['fin']);
                $total2 = date_mysql_to_timestamp($result2['debut']);

                $total += $total2 - $total1;
            }
        }

        return $total;
    }

    /**
     * TODO [setModeFichierErreur description]
     */
    public function setModeFichierErreur()
    {
        if (is_null($this->temps)) {
            $this->temps = 1;
        }
    }

    /**
     * @inheritDoc
     */
    protected function logger()
    {
        return $this->logger;
    }

    /**
     * Returns the Database Factory
     * @return DatabaseFactory
     */
    private function dbFactory()
    {
        return $this->dbFactory;
    }

    /**
     * Removes all the non UTF8 characters from the string values
     *
     * @param  array $propertiesToUpdate
     * @return array
     */
    private function sanitizeValues(array $propertiesToUpdate): array
    {
        return array_map(
            function ($value) {
                return is_string($value)
                    ? (new Windows1252Text($value))->toString()
                    : $value;
            },
            $propertiesToUpdate
        );
    }

}
