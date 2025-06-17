<?php
/**
 * LOKALIZACJA: models/Seed.php
 * POPRAWIONY MODEL Z OBSŁUGĄ DAT MM-DD
 */

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

class Seed extends ActiveRecord
{
    // Stałe dla typu nasion
    const TYPE_VEGETABLES = 'vegetables';
    const TYPE_FLOWERS = 'flowers';
    const TYPE_HERBS = 'herbs';
    
    // Stałe dla wysokości roślin
    const HEIGHT_LOW = 'low';
    const HEIGHT_HIGH = 'high';
    
    // Stałe dla typu rośliny
    const PLANT_TYPE_ANNUAL = 'annual';
    const PLANT_TYPE_PERENNIAL = 'perennial';
    
    // Stałe dla statusu
    const STATUS_AVAILABLE = 'available';
    const STATUS_USED = 'used';
    
    // Właściwość dla uploadu pliku
    public $imageFile;

    public static function tableName()
    {
        return '{{%seed}}';
    }

    public function rules()
    {
        return [
            [['name', 'type', 'height', 'plant_type', 'sowing_start', 'sowing_end'], 'required'],
            [['description', 'notes'], 'string'],
            [['expiry_date', 'created_at', 'updated_at'], 'safe'],
            [['purchase_year', 'priority'], 'integer'],
            [['purchase_year'], 'integer', 'min' => 2000, 'max' => 2030],
            [['priority'], 'integer', 'min' => 0, 'max' => 10],
            [['name'], 'string', 'max' => 255],
            [['image_path'], 'string', 'max' => 255],
            
            // POPRAWIONA WALIDACJA DAT MM-DD
            [['sowing_start', 'sowing_end'], 'string', 'length' => 5],
            [['sowing_start', 'sowing_end'], 'match', 
                'pattern' => '/^(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/', 
                'message' => 'Format daty musi być MM-DD (np. 03-15)'
            ],
            [['sowing_start', 'sowing_end'], 'validateMonthDay'],
            
            [['type'], 'in', 'range' => [self::TYPE_VEGETABLES, self::TYPE_FLOWERS, self::TYPE_HERBS]],
            [['height'], 'in', 'range' => [self::HEIGHT_LOW, self::HEIGHT_HIGH]],
            [['plant_type'], 'in', 'range' => [self::PLANT_TYPE_ANNUAL, self::PLANT_TYPE_PERENNIAL]],
            [['status'], 'in', 'range' => [self::STATUS_AVAILABLE, self::STATUS_USED]],
            [['status'], 'default', 'value' => self::STATUS_AVAILABLE],
            [['priority'], 'default', 'value' => 0],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif'],
            [['imageFile'], 'file', 'maxSize' => 1024 * 1024 * 2], // 2MB
        ];
    }

    /**
     * Walidacja dat w formacie MM-DD
     */
    public function validateMonthDay($attribute, $params)
    {
        $value = $this->$attribute;
        if (empty($value)) {
            return;
        }

        // Sprawdź format MM-DD
        if (!preg_match('/^(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/', $value)) {
            $this->addError($attribute, 'Format daty musi być MM-DD (np. 03-15)');
            return;
        }

        // Sprawdź czy data jest prawidłowa
        list($month, $day) = explode('-', $value);
        $month = (int)$month;
        $day = (int)$day;

        // Sprawdź czy miesiąc jest prawidłowy
        if ($month < 1 || $month > 12) {
            $this->addError($attribute, 'Miesiąc musi być z zakresu 01-12');
            return;
        }

        // Sprawdź czy dzień jest prawidłowy dla danego miesiąca
        $daysInMonth = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31]; // 29 dla lutego (rok przestępny)
        if ($day < 1 || $day > $daysInMonth[$month - 1]) {
            $this->addError($attribute, "Dzień {$day} jest nieprawidłowy dla miesiąca " . sprintf('%02d', $month));
            return;
        }

        // Sprawdź luty szczególnie
        if ($month == 2 && $day > 28) {
            // Dla prostoty przyjmujemy że lata przestępne są co 4 lata
            // W rzeczywistości to bardziej skomplikowane, ale dla dat MM-DD wystarczy
            $this->addError($attribute, 'Dla lutego maksymalny dzień to 28 (lub 29 w roku przestępnym)');
            return;
        }
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nazwa',
            'description' => 'Opis',
            'notes' => 'Notatki',
            'image_path' => 'Zdjęcie opakowania',
            'imageFile' => 'Zdjęcie opakowania',
            'expiry_date' => 'Data ważności',
            'purchase_year' => 'Rok zakupu',
            'height' => 'Wysokość rośliny',
            'type' => 'Typ',
            'sowing_start' => 'Początek okresu wysiewu (MM-DD)',
            'sowing_end' => 'Koniec okresu wysiewu (MM-DD)',
            'plant_type' => 'Typ rośliny',
            'status' => 'Status',
            'priority' => 'Priorytet (0-10)',
            'created_at' => 'Data utworzenia',
            'updated_at' => 'Data aktualizacji',
        ];
    }

    /**
     * Hook przed zapisem - nie konwertuj dat MM-DD
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Upewnij się że daty są w formacie MM-DD
            if ($this->sowing_start && !preg_match('/^\d{2}-\d{2}$/', $this->sowing_start)) {
                // Jeśli przyszła pełna data z formularza, wyciągnij MM-DD
                if (preg_match('/^\d{4}-(\d{2})-(\d{2})$/', $this->sowing_start, $matches)) {
                    $this->sowing_start = $matches[1] . '-' . $matches[2];
                }
            }
            
            if ($this->sowing_end && !preg_match('/^\d{2}-\d{2}$/', $this->sowing_end)) {
                // Jeśli przyszła pełna data z formularza, wyciągnij MM-DD
                if (preg_match('/^\d{4}-(\d{2})-(\d{2})$/', $this->sowing_end, $matches)) {
                    $this->sowing_end = $matches[1] . '-' . $matches[2];
                }
            }
            
            return true;
        }
        return false;
    }

    /**
     * Hook po wczytaniu - konwertuj daty MM-DD do pełnych dat dla formularza
     */
    public function afterFind()
    {
        parent::afterFind();
        
        // Te konwersje są potrzebne tylko do wyświetlania w niektórych miejscach
        // Podstawowe przechowywanie pozostaje MM-DD
    }

    /**
     * Konwertuje datę MM-DD na pełną datę dla aktualnego roku (do wyświetlania)
     */
    public function getFullSowingDate($field, $year = null)
    {
        if ($year === null) {
            $year = date('Y');
        }
        
        $monthDay = $this->$field;
        if (!$monthDay || !preg_match('/^\d{2}-\d{2}$/', $monthDay)) {
            return null;
        }
        
        return $year . '-' . $monthDay;
    }

    /**
     * Zwraca sformatowaną datę do wyświetlania (dd.mm)
     */
    public function getFormattedSowingDate($field)
    {
        $monthDay = $this->$field;
        if (!$monthDay || !preg_match('/^(\d{2})-(\d{2})$/', $monthDay, $matches)) {
            return '';
        }
        
        return $matches[2] . '.' . $matches[1]; // DD.MM
    }

    /**
     * Pobiera nasiona do wysiewu w danym terminie
     */
    public static function getSowingSeeds($date = null)
    {
        if ($date === null) {
            $date = date('Y-m-d');
        }
        
        $currentMonthDay = date('m-d', strtotime($date));
        
        return self::find()
            ->where(['status' => self::STATUS_AVAILABLE])
            ->andWhere([
                'or',
                // Normalny przypadek - sowing_start <= current <= sowing_end
                [
                    'and',
                    ['<=', 'sowing_start', $currentMonthDay],
                    ['>=', 'sowing_end', $currentMonthDay]
                ],
                // Przypadek przejścia przez nowy rok (np. 12-01 do 02-28)
                [
                    'and',
                    ['>', 'sowing_start', 'sowing_end'],
                    [
                        'or',
                        ['<=', 'sowing_start', $currentMonthDay],
                        ['>=', 'sowing_end', $currentMonthDay]
                    ]
                ]
            ])
            ->orderBy(['priority' => SORT_DESC, 'name' => SORT_ASC])
            ->all();
    }

    /**
     * Sprawdza czy nasiono jest w okresie wysiewu
     */
    public function isInSowingPeriod($date = null)
    {
        if ($date === null) {
            $date = date('Y-m-d');
        }
        
        $currentMonthDay = date('m-d', strtotime($date));
        $sowingStart = $this->sowing_start;
        $sowingEnd = $this->sowing_end;
        
        if ($sowingStart <= $sowingEnd) {
            // Normalny przypadek (np. 03-01 do 05-31)
            return $currentMonthDay >= $sowingStart && $currentMonthDay <= $sowingEnd;
        } else {
            // Przejście przez nowy rok (np. 12-01 do 02-28)
            return $currentMonthDay >= $sowingStart || $currentMonthDay <= $sowingEnd;
        }
    }

    /**
     * Opcje dla pola typu
     */
    public function getTypeOptions()
    {
        return [
            self::TYPE_VEGETABLES => 'Warzywa',
            self::TYPE_FLOWERS => 'Kwiaty',
            self::TYPE_HERBS => 'Zioła',
        ];
    }

    /**
     * Opcje dla pola wysokości
     */
    public function getHeightOptions()
    {
        return [
            self::HEIGHT_LOW => 'Niskie',
            self::HEIGHT_HIGH => 'Wysokie',
        ];
    }

    /**
     * Opcje dla typu rośliny
     */
    public function getPlantTypeOptions()
    {
        return [
            self::PLANT_TYPE_ANNUAL => 'Jednoroczna',
            self::PLANT_TYPE_PERENNIAL => 'Bylina',
        ];
    }

    /**
     * Opcje dla statusu
     */
    public function getStatusOptions()
    {
        return [
            self::STATUS_AVAILABLE => 'Dostępne',
            self::STATUS_USED => 'Zużyte',
        ];
    }

    /**
     * Formatuje typ dla wyświetlenia
     */
    public function getTypeLabel()
    {
        $options = $this->getTypeOptions();
        return isset($options[$this->type]) ? $options[$this->type] : $this->type;
    }

    /**
     * Formatuje wysokość dla wyświetlenia
     */
    public function getHeightLabel()
    {
        $options = $this->getHeightOptions();
        return isset($options[$this->height]) ? $options[$this->height] : $this->height;
    }

    /**
     * Formatuje typ rośliny dla wyświetlenia
     */
    public function getPlantTypeLabel()
    {
        $options = $this->getPlantTypeOptions();
        return isset($options[$this->plant_type]) ? $options[$this->plant_type] : $this->plant_type;
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
     * Upload pliku zdjęcia
     */
    public function upload()
    {
        if ($this->validate() && $this->imageFile) {
            $fileName = time() . '_' . uniqid() . '.' . $this->imageFile->extension;
            $filePath = Yii::getAlias('@webroot/uploads/') . $fileName;
            
            if ($this->imageFile->saveAs($filePath)) {
                // Usuń poprzednie zdjęcie jeśli istnieje
                if ($this->image_path) {
                    $oldFile = Yii::getAlias('@webroot/uploads/') . $this->image_path;
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
                
                $this->image_path = $fileName;
                return true;
            }
        }
        return false;
    }

    /**
     * Relacja z wysialiskami
     */
    public function getSownSeeds()
    {
        return $this->hasMany(SownSeed::class, ['seed_id' => 'id']);
    }

    /**
     * Zwraca ścieżkę do zdjęcia
     */
    public function getImageUrl()
    {
        if ($this->image_path) {
            return Yii::getAlias('@web/uploads/') . $this->image_path;
        }
        return null;
    }
}