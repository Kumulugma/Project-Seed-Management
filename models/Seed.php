<?php
/**
 * LOKALIZACJA: models/Seed.php
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
            [['expiry_date', 'sowing_start', 'sowing_end', 'created_at', 'updated_at'], 'safe'],
            [['purchase_year', 'priority'], 'integer'],
            [['purchase_year'], 'integer', 'min' => 2000, 'max' => 2030],
            [['priority'], 'integer', 'min' => 0, 'max' => 10],
            [['name'], 'string', 'max' => 255],
            [['image_path'], 'string', 'max' => 255],
            [['type'], 'in', 'range' => [self::TYPE_VEGETABLES, self::TYPE_FLOWERS, self::TYPE_HERBS]],
            [['height'], 'in', 'range' => [self::HEIGHT_LOW, self::HEIGHT_HIGH]],
            [['plant_type'], 'in', 'range' => [self::PLANT_TYPE_ANNUAL, self::PLANT_TYPE_PERENNIAL]],
            [['status'], 'in', 'range' => [self::STATUS_AVAILABLE, self::STATUS_USED]],
            [['status'], 'default', 'value' => self::STATUS_AVAILABLE],
            [['priority'], 'default', 'value' => 0],
            [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif'],
            [['imageFile'], 'file', 'maxSize' => 1024 * 1024 * 2], // 2MB
            // Walidacja dat wysiewu
            ['sowing_end', 'validateSowingPeriod'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nazwa',
            'description' => 'Opis',
            'image_path' => 'Zdjęcie opakowania',
            'imageFile' => 'Zdjęcie opakowania',
            'expiry_date' => 'Data ważności',
            'purchase_year' => 'Rok zakupu',
            'height' => 'Wysokość rośliny',
            'type' => 'Typ',
            'sowing_start' => 'Początek okresu wysiewu',
            'sowing_end' => 'Koniec okresu wysiewu',
            'plant_type' => 'Typ rośliny',
            'status' => 'Status',
            'priority' => 'Priorytet (0-10)',
            'created_at' => 'Data utworzenia',
            'updated_at' => 'Data aktualizacji',
        ];
    }

    /**
     * Walidacja okresu wysiewu
     */
    public function validateSowingPeriod($attribute, $params)
    {
        if ($this->sowing_start && $this->sowing_end) {
            if (strtotime($this->sowing_end) < strtotime($this->sowing_start)) {
                $this->addError($attribute, 'Koniec wysiewu nie może być wcześniejszy niż początek.');
            }
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
                    "DATE_FORMAT(sowing_start, '%m-%d') <= :date",
                    "DATE_FORMAT(sowing_end, '%m-%d') >= :date"
                ],
                // Przypadek przejścia przez nowy rok (np. 12-01 do 02-28)
                [
                    'and',
                    "DATE_FORMAT(sowing_start, '%m-%d') > DATE_FORMAT(sowing_end, '%m-%d')",
                    [
                        'or',
                        "DATE_FORMAT(sowing_start, '%m-%d') <= :date",
                        "DATE_FORMAT(sowing_end, '%m-%d') >= :date"
                    ]
                ]
            ], [':date' => $currentMonthDay])
            ->orderBy(['priority' => SORT_DESC, 'name' => SORT_ASC])
            ->all();
    }

    /**
     * Relacja z wysialiskami
     */
    public function getSownSeeds()
    {
        return $this->hasMany(SownSeed::class, ['seed_id' => 'id']);
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
        $sowingStart = date('m-d', strtotime($this->sowing_start));
        $sowingEnd = date('m-d', strtotime($this->sowing_end));
        
        if ($sowingStart <= $sowingEnd) {
            // Normalny przypadek
            return $currentMonthDay >= $sowingStart && $currentMonthDay <= $sowingEnd;
        } else {
            // Przejście przez nowy rok
            return $currentMonthDay >= $sowingStart || $currentMonthDay <= $sowingEnd;
        }
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
     * Zwraca ścieżkę do zdjęcia
     */
    public function getImageUrl()
    {
        if ($this->image_path) {
            return Yii::getAlias('@web/uploads/') . $this->image_path;
        }
        return null;
    }

    /**
     * Hook przed zapisem
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Upload zdjęcia jeśli zostało wybrane
            if ($this->imageFile) {
                $this->upload();
            }
            return true;
        }
        return false;
    }

    /**
     * Hook przed usunięciem
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            // Usuń plik zdjęcia
            if ($this->image_path) {
                $filePath = Yii::getAlias('@webroot/uploads/') . $this->image_path;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            return true;
        }
        return false;
    }
}