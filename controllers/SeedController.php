<?php
/**
 * LOKALIZACJA: controllers/SeedController.php
 * POPRAWIONY KONTROLER Z OBSŁUGĄ COMPANY
 */

namespace app\controllers;

use Yii;
use app\models\Seed;
use app\models\SownSeed;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\data\Pagination;

class SeedController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // tylko zalogowani użytkownicy
                    ],
                ],
            ],
        ];
    }

    /**
     * Lista wszystkich nasion
     */
    public function actionIndex()
    {
        $searchModel = new Seed();
        
        // Obsługa wyszukiwania
        $query = Seed::find();
        
        if (Yii::$app->request->get('search')) {
            $searchTerm = Yii::$app->request->get('search');
            $query->where(['like', 'name', $searchTerm])
                  ->orWhere(['like', 'description', $searchTerm])
                  ->orWhere(['like', 'company', $searchTerm]); // DODANO WYSZUKIWANIE PO FIRMIE
        }
        
        // Obsługa filtrowania
        if (Yii::$app->request->get('type')) {
            $query->andWhere(['type' => Yii::$app->request->get('type')]);
        }
        
        if (Yii::$app->request->get('status')) {
            $query->andWhere(['status' => Yii::$app->request->get('status')]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['name' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Wyświetla szczegóły nasiona
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        // Pobierz historię wysiewów dla tego nasiona
        $sownSeeds = SownSeed::find()
            ->where(['seed_id' => $id])
            ->orderBy(['sown_date' => SORT_DESC])
            ->all();

        return $this->render('view', [
            'model' => $model,
            'sownSeeds' => $sownSeeds,
        ]);
    }

    /**
     * Tworzy nowe nasiono
     */
    public function actionCreate()
    {
        $model = new Seed();

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Nasiono zostało dodane pomyślnie.');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Wystąpił błąd podczas zapisywania nasiona.');
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Aktualizuje istniejące nasiono
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Nasiono zostało zaktualizowane pomyślnie.');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', 'Wystąpił błąd podczas aktualizacji nasiona.');
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Usuwa nasiono
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        // Sprawdź czy można usunąć (czy nie ma powiązanych wysiewów)
        $sownCount = SownSeed::find()->where(['seed_id' => $id])->count();
        
        if ($sownCount > 0) {
            Yii::$app->session->setFlash('error', 'Nie można usunąć nasiona, które ma już historię wysiewów.');
        } else {
            $model->delete();
            Yii::$app->session->setFlash('success', 'Nasiono zostało usunięte.');
        }

        return $this->redirect(['index']);
    }

    /**
     * AJAX wyszukiwarka nasion
     */
    public function actionSearch()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $query = Yii::$app->request->get('q', '');
        
        if (strlen($query) < 2) {
            return ['results' => []];
        }
        
        $seeds = Seed::find()
            ->where(['like', 'name', $query])
            ->orWhere(['like', 'description', $query])
            ->orWhere(['like', 'company', $query]) // DODANO WYSZUKIWANIE PO FIRMIE
            ->limit(10)
            ->all();
        
        $results = [];
        foreach ($seeds as $seed) {
            $results[] = [
                'id' => $seed->id,
                'name' => $seed->name,
                'company' => $seed->company, // DODANO FIRMĘ
                'type' => $seed->getTypeLabel(),
                'status' => $seed->getStatusLabel(),
                'height' => $seed->getHeightLabel(),
                'plant_type' => $seed->getPlantTypeLabel(),
                'sowing_period' => date('d.m', strtotime('2024-' . $seed->sowing_start)) . ' - ' . date('d.m', strtotime('2024-' . $seed->sowing_end)),
                'priority' => $seed->priority,
                'url' => \yii\helpers\Url::to(['view', 'id' => $seed->id]),
            ];
        }
        
        return ['results' => $results];
    }

    /**
     * Oznacza nasiono jako zużyte
     */
    public function actionMarkAsUsed($id)
    {
        $model = $this->findModel($id);
        $model->status = Seed::STATUS_USED;
        
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Nasiono zostało oznaczone jako zużyte.');
        } else {
            Yii::$app->session->setFlash('error', 'Wystąpił błąd podczas aktualizacji statusu.');
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Przywraca nasiono jako dostępne
     */
    public function actionMarkAsAvailable($id)
    {
        $model = $this->findModel($id);
        $model->status = Seed::STATUS_AVAILABLE;
        
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Nasiono zostało oznaczone jako dostępne.');
        } else {
            Yii::$app->session->setFlash('error', 'Wystąpił błąd podczas aktualizacji statusu.');
        }
        
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * Kopiuje nasiono (przydatne dla odmian)
     */
    public function actionCopy($id)
    {
        $original = $this->findModel($id);
        
        $model = new Seed();
        $model->attributes = $original->attributes;
        $model->id = null; // Wyczyść ID dla nowego rekordu
        $model->name = $original->name . ' (kopia)';
        $model->status = Seed::STATUS_AVAILABLE;
        $model->image_path = null; // Wyczyść ścieżkę zdjęcia
        
        if ($model->load(Yii::$app->request->post())) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Kopia nasiona została utworzona pomyślnie.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Eksport do CSV
     */
    public function actionExport()
    {
        $seeds = Seed::find()->orderBy(['name' => SORT_ASC])->all();
        
        $filename = 'nasiona_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        
        // Dodaj BOM dla UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Nagłówki CSV (z dodaną firmą)
        fputcsv($output, [
            'ID',
            'Nazwa',
            'Firma/Producent',
            'Opis',
            'Typ',
            'Wysokość',
            'Typ rośliny',
            'Początek wysiewu',
            'Koniec wysiewu',
            'Data ważności',
            'Rok zakupu',
            'Status',
            'Priorytet'
        ], ';');
        
        // Dane
        foreach ($seeds as $seed) {
            fputcsv($output, [
                $seed->id,
                $seed->name,
                $seed->company, // DODANO FIRMĘ
                $seed->description,
                $seed->getTypeLabel(),
                $seed->getHeightLabel(),
                $seed->getPlantTypeLabel(),
                $seed->getFormattedSowingDate('sowing_start') ?: $seed->sowing_start,
                $seed->getFormattedSowingDate('sowing_end') ?: $seed->sowing_end,
                $seed->expiry_date,
                $seed->purchase_year,
                $seed->getStatusLabel(),
                $seed->priority
            ], ';');
        }
        
        fclose($output);
        exit;
    }

    /**
     * Pobiera statystyki nasion
     */
    public function actionStats()
    {
        $stats = [
            'total' => Seed::find()->count(),
            'available' => Seed::find()->where(['status' => Seed::STATUS_AVAILABLE])->count(),
            'used' => Seed::find()->where(['status' => Seed::STATUS_USED])->count(),
            'by_type' => [],
            'by_height' => [],
            'by_plant_type' => [],
            'by_company' => [], // DODANO STATYSTYKI PO FIRMACH
            'expiring_soon' => [],
        ];
        
        // Statystyki według typu
        $typeOptions = (new Seed())->getTypeOptions();
        foreach ($typeOptions as $key => $label) {
            $stats['by_type'][$label] = Seed::find()->where(['type' => $key])->count();
        }
        
        // Statystyki według wysokości
        $heightOptions = (new Seed())->getHeightOptions();
        foreach ($heightOptions as $key => $label) {
            $stats['by_height'][$label] = Seed::find()->where(['height' => $key])->count();
        }
        
        // Statystyki według typu rośliny
        $plantTypeOptions = (new Seed())->getPlantTypeOptions();
        foreach ($plantTypeOptions as $key => $label) {
            $stats['by_plant_type'][$label] = Seed::find()->where(['plant_type' => $key])->count();
        }
        
        // DODANO: Statystyki według firm
        $companies = Seed::find()
            ->select('company')
            ->where(['not', ['company' => null]])
            ->andWhere(['not', ['company' => '']])
            ->groupBy('company')
            ->orderBy(['company' => SORT_ASC])
            ->column();
            
        foreach ($companies as $company) {
            $stats['by_company'][$company] = Seed::find()->where(['company' => $company])->count();
        }
        
        // Nasiona wygasające w ciągu 6 miesięcy
        $expireDate = date('Y-m-d', strtotime('+6 months'));
        $stats['expiring_soon'] = Seed::find()
            ->where(['<=', 'expiry_date', $expireDate])
            ->andWhere(['!=', 'expiry_date', null])
            ->orderBy(['expiry_date' => SORT_ASC])
            ->all();

        return $this->render('stats', [
            'stats' => $stats,
        ]);
    }

    /**
     * Znajduje model Seed na podstawie ID
     */
    protected function findModel($id)
    {
        if (($model = Seed::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Nie znaleziono nasiona o podanym ID.');
    }
}