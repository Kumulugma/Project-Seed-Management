<?php
/**
 * LOKALIZACJA: models/SownSeed.php
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class SownSeed extends ActiveRecord
{
    // Stałe dla statusu kiełkowania
    const STATUS_SOWN = 'sown';
    const STATUS_GERMINATED = 'germinated';
    const STATUS_NOT_GERMINATED = 'not_germinated';

    public static function tableName()
    {
        return '{{%sown_seed}}';
    }

    public function rules()
    {
        return [
            [['seed_id', 'sown_date'], 'required'],
            [['seed_id'], 'integer'],
            [['sown_date', 'created_at', 'updated_at'], 'safe'],
            [['status'], 'in', 'range' => [self::STATUS_SOWN, self::STATUS_GERMINATED, self::STATUS_NOT_GERMINATED]],
            [['status'], 'default', 'value' => self::STATUS_SOWN],
            [['sowing_code'], 'string', 'max' => 50],
            [['notes'], 'string'],
            [['seed_id'], 'exist', 'targetClass' => Seed::class, 'targetAttribute' => 'id'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'seed_id' => 'Nasiono',
            'sown_date' => 'Data wysiewu',
            'status' => 'Status kiełkowania',
            'sowing_code' => 'Kod wysiewu',
            'notes' => 'Notatki',
            'created_at' => 'Data utworzenia',
            'updated_at' => 'Data aktualizacji',
        ];
    }

    /**
     * Relacja z modelem Seed
     */
    public function getSeed()
    {
        return $this->hasOne(Seed::class, ['id' => 'seed_id']);
    }

    /**
     * Opcje dla statusu kiełkowania
     */
    public function getStatusOptions()
    {
        return [
            self::STATUS_SOWN => 'Wysiany',
            self::STATUS_GERMINATED => 'Wykiełkował',
            self::STATUS_NOT_GERMINATED => 'Nie wykiełkował',
        ];
    }

    /**
     * Formatuje status dla wyświetlenia
     */
    public function getStatusLabel()
    {
        $options = $this->getStatusOptions();
        return isset($options[$this->status]) ? $options[$this->status] : $this->status;
    }

    /**
     * Zwraca kolor CSS dla statusu
     */
    public function getStatusColor()
    {
        switch ($this->status) {
            case self::STATUS_GERMINATED:
                return 'success';
            case self::STATUS_NOT_GERMINATED:
                return 'danger';
            case self::STATUS_SOWN:
            default:
                return 'warning';
        }
    }

    /**
     * Hook przed zapisem - generuje kod wysiewu
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert && empty($this->sowing_code)) {
                $this->sowing_code = $this->generateSowingCode();
            }
            return true;
        }
        return false;
    }

    /**
     * Generuje unikalny kod wysiewu
     */
    private function generateSowingCode()
    {
        if (!$this->seed_id || !$this->sown_date) {
            return null;
        }

        $seed = Seed::findOne($this->seed_id);
        if (!$seed) {
            return null;
        }

        // Prefiks z nazwy nasiona (pierwsze 3 litery)
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $seed->name), 0, 3));
        
        // Data w formacie MMDD
        $dateCode = date('md', strtotime($this->sown_date));
        
        // Licznik dla tego samego nasiona w tym samym dniu
        $count = self::find()
            ->where(['seed_id' => $this->seed_id])
            ->andWhere(['DATE(sown_date)' => date('Y-m-d', strtotime($this->sown_date))])
            ->count();
        
        $counter = sprintf('%02d', $count + 1);
        
        return $prefix . $dateCode . $counter;
    }

    /**
     * Statystyki kiełkowania dla nasiona
     */
    public static function getGerminationStats($seedId = null)
    {
        $query = self::find();
        
        if ($seedId) {
            $query->where(['seed_id' => $seedId]);
        }
        
        $total = $query->count();
        $germinated = $query->andWhere(['status' => self::STATUS_GERMINATED])->count();
        $notGerminated = $query->andWhere(['status' => self::STATUS_NOT_GERMINATED])->count();
        $sown = $query->andWhere(['status' => self::STATUS_SOWN])->count();
        
        return [
            'total' => $total,
            'germinated' => $germinated,
            'not_germinated' => $notGerminated,
            'sown' => $sown,
            'germination_rate' => $total > 0 ? round(($germinated / ($germinated + $notGerminated)) * 100, 1) : 0,
        ];
    }

    /**
     * Pobiera ostatnio wysiałe nasiona do sprawdzenia kiełkowania
     */
    public static function getRecentSownSeeds($limit = 10)
    {
        return self::find()
            ->joinWith('seed')
            ->where(['sown_seed.status' => self::STATUS_SOWN])
            ->orderBy(['sown_date' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    /**
     * Pobiera nasiona wysiałe w ostatnich X dniach
     */
    public static function getSownSeedsInLastDays($days = 14)
    {
        $dateLimit = date('Y-m-d', strtotime("-{$days} days"));
        
        return self::find()
            ->joinWith('seed')
            ->where(['>=', 'sown_date', $dateLimit])
            ->orderBy(['sown_date' => SORT_DESC])
            ->all();
    }

    /**
     * Zwraca liczbę dni od wysiewu
     */
    public function getDaysFromSowing()
    {
        $today = new \DateTime();
        $sownDate = new \DateTime($this->sown_date);
        $interval = $today->diff($sownDate);
        return $interval->days;
    }

    /**
     * Sprawdza czy nasiono powinno już kiełkować (po X dniach)
     */
    public function shouldBeGerminated($expectedDays = 14)
    {
        return $this->getDaysFromSowing() >= $expectedDays && $this->status === self::STATUS_SOWN;
    }

    /**
     * Scope dla nasion oczekujących na sprawdzenie kiełkowania
     */
    public static function findPendingGermination($days = 7)
    {
        $dateLimit = date('Y-m-d', strtotime("-{$days} days"));
        
        return self::find()
            ->where(['status' => self::STATUS_SOWN])
            ->andWhere(['<=', 'sown_date', $dateLimit])
            ->orderBy(['sown_date' => SORT_ASC]);
    }
}