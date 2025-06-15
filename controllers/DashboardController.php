<?php
/**
 * LOKALIZACJA: controllers/DashboardController.php
 */

namespace app\controllers;

use Yii;
use app\models\Seed;
use app\models\SownSeed;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\Response;
use Mpdf\Mpdf;

class DashboardController extends Controller
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
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Główny widok dashboardu
     */
    public function actionIndex()
    {
        // Pobierz nasiona do wysiewu w obecnym okresie
        $currentSeeds = Seed::getSowingSeeds();
        
        // Pobierz ostatnio wysiałe nasiona do sprawdzenia kiełkowania
        $sownSeeds = SownSeed::getRecentSownSeeds(15);
        
        // Statystyki ogólne
        $stats = [
            'total_seeds' => Seed::find()->count(),
            'available_seeds' => Seed::find()->where(['status' => Seed::STATUS_AVAILABLE])->count(),
            'sown_today' => SownSeed::find()->where(['sown_date' => date('Y-m-d')])->count(),
            'pending_germination' => SownSeed::find()->where(['status' => SownSeed::STATUS_SOWN])->count(),
        ];
        
        // Nasiona wymagające uwagi (wygasające w ciągu 3 miesięcy)
        $expireDate = date('Y-m-d', strtotime('+3 months'));
        $expiringSoon = Seed::find()
            ->where(['<=', 'expiry_date', $expireDate])
            ->andWhere(['status' => Seed::STATUS_AVAILABLE])
            ->andWhere(['!=', 'expiry_date', null])
            ->orderBy(['expiry_date' => SORT_ASC])
            ->limit(5)
            ->all();

        return $this->render('index', [
            'currentSeeds' => $currentSeeds,
            'sownSeeds' => $sownSeeds,
            'stats' => $stats,
            'expiringSoon' => $expiringSoon,
        ]);
    }

    /**
     * Generuje PDF z planem wysiewu i przenosi nasiona do tabeli wysianych
     */
    public function actionSowingPdf()
    {
        $selectedSeeds = Yii::$app->request->post('seeds', []);
        
        if (empty($selectedSeeds)) {
            Yii::$app->session->setFlash('error', 'Nie wybrano nasion do wydruku.');
            return $this->redirect(['index']);
        }

        $seeds = Seed::find()->where(['id' => $selectedSeeds])->all();
        
        if (empty($seeds)) {
            Yii::$app->session->setFlash('error', 'Nie znaleziono wybranych nasion.');
            return $this->redirect(['index']);
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            // Dodaj nasiona do tabeli wysiewu
            $sownCount = 0;
            foreach ($seeds as $seed) {
                $sownSeed = new SownSeed();
                $sownSeed->seed_id = $seed->id;
                $sownSeed->sown_date = date('Y-m-d');
                
                if ($sownSeed->save()) {
                    $sownCount++;
                } else {
                    throw new \Exception('Błąd podczas dodawania nasiona: ' . $seed->name);
                }
            }

            // Generuj PDF
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 15,
                'margin_right' => 15,
                'margin_top' => 20,
                'margin_bottom' => 20,
                'default_font' => 'DejaVuSans',
            ]);

            $html = $this->renderPartial('sowing-pdf', [
                'seeds' => $seeds,
                'date' => date('Y-m-d'),
            ]);
            
            $mpdf->WriteHTML($html);
            
            $transaction->commit();
            
            Yii::$app->session->setFlash('success', "Dodano {$sownCount} nasion do wysiewu i wygenerowano PDF.");
            
            // Wyślij PDF do przeglądarki
            $filename = 'wysiew_' . date('Y-m-d') . '.pdf';
            $mpdf->Output($filename, 'D');
            
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', 'Wystąpił błąd: ' . $e->getMessage());
            return $this->redirect(['index']);
        }
    }

    /**
     * Aktualizuje status kiełkowania nasion
     */
    public function actionUpdateGermination()
    {
        if (Yii::$app->request->isPost) {
            $updates = Yii::$app->request->post('germination', []);
            $updatedCount = 0;
            
            foreach ($updates as $id => $status) {
                $sownSeed = SownSeed::findOne($id);
                if ($sownSeed && in_array($status, array_keys($sownSeed->getStatusOptions()))) {
                    $sownSeed->status = $status;
                    if ($sownSeed->save()) {
                        $updatedCount++;
                    }
                }
            }
            
            if ($updatedCount > 0) {
                Yii::$app->session->setFlash('success', "Zaktualizowano status {$updatedCount} nasion.");
            } else {
                Yii::$app->session->setFlash('warning', 'Nie zaktualizowano żadnych nasion.');
            }
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Raport kiełkowania
     */
    public function actionGerminationReport()
    {
        // Wszystkie wysiałe nasiona
        $sownSeeds = SownSeed::find()
            ->joinWith('seed')
            ->orderBy(['sown_date' => SORT_DESC])
            ->all();
        
        // Statystyki ogólne
        $stats = SownSeed::getGerminationStats();
        
        // Statystyki według typu nasion
        $typeStats = [];
        $seedTypes = (new Seed())->getTypeOptions();
        
        foreach ($seedTypes as $typeKey => $typeLabel) {
            $typeSeeds = SownSeed::find()
                ->joinWith('seed')
                ->where(['seed.type' => $typeKey])
                ->all();
            
            if (!empty($typeSeeds)) {
                $typeTotal = count($typeSeeds);
                $typeGerminated = count(array_filter($typeSeeds, function($s) { 
                    return $s->status === SownSeed::STATUS_GERMINATED; 
                }));
                $typeNotGerminated = count(array_filter($typeSeeds, function($s) { 
                    return $s->status === SownSeed::STATUS_NOT_GERMINATED; 
                }));
                
                $typeStats[$typeLabel] = [
                    'total' => $typeTotal,
                    'germinated' => $typeGerminated,
                    'not_germinated' => $typeNotGerminated,
                    'rate' => ($typeGerminated + $typeNotGerminated) > 0 ? 
                        round(($typeGerminated / ($typeGerminated + $typeNotGerminated)) * 100, 1) : 0,
                ];
            }
        }

        return $this->render('germination-report', [
            'sownSeeds' => $sownSeeds,
            'stats' => $stats,
            'typeStats' => $typeStats,
        ]);
    }

    /**
     * Drukowanie etykiet dla wybranych nasion
     */
    public function actionPrintLabels()
    {
        $selectedSeeds = Yii::$app->request->post('seeds', []);
        
        if (empty($selectedSeeds)) {
            Yii::$app->session->setFlash('error', 'Nie wybrano nasion do wydruku etykiet.');
            return $this->redirect(['index']);
        }

        $sownSeeds = SownSeed::find()
            ->joinWith('seed')
            ->where(['sown_seed.id' => $selectedSeeds])
            ->all();
        
        if (empty($sownSeeds)) {
            Yii::$app->session->setFlash('error', 'Nie znaleziono wybranych nasion.');
            return $this->redirect(['index']);
        }

        try {
            // Konfiguracja PDF dla małych etykiet
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => [40, 15], // 40mm x 15mm
                'margin_left' => 1,
                'margin_right' => 1,
                'margin_top' => 1,
                'margin_bottom' => 1,
                'default_font_size' => 6,
                'default_font' => 'DejaVuSans',
            ]);

            foreach ($sownSeeds as $index => $sownSeed) {
                if ($index > 0) {
                    $mpdf->AddPage();
                }
                
                $html = $this->renderPartial('label', ['sownSeed' => $sownSeed]);
                $mpdf->WriteHTML($html);
            }

            $filename = 'etykiety_' . date('Y-m-d_H-i') . '.pdf';
            $mpdf->Output($filename, 'D');
            
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', 'Wystąpił błąd podczas generowania etykiet: ' . $e->getMessage());
            return $this->redirect(['index']);
        }
    }

    /**
     * AJAX - pobiera nasiona do wysiewu dla określonej daty
     */
    public function actionGetSowingSeeds()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $date = Yii::$app->request->get('date', date('Y-m-d'));
        $seeds = Seed::getSowingSeeds($date);
        
        $results = [];
        foreach ($seeds as $seed) {
            $results[] = [
                'id' => $seed->id,
                'name' => $seed->name,
                'type' => $seed->getTypeLabel(),
                'height' => $seed->getHeightLabel(),
                'plant_type' => $seed->getPlantTypeLabel(),
                'priority' => $seed->priority,
                'sowing_period' => date('d.m', strtotime($seed->sowing_start)) . ' - ' . date('d.m', strtotime($seed->sowing_end)),
            ];
        }
        
        return ['seeds' => $results];
    }

    /**
     * Kalendarz wysiewów
     */
    public function actionSowingCalendar()
    {
        $year = Yii::$app->request->get('year', date('Y'));
        
        // Pobierz wszystkie nasiona z okresami wysiewu
        $seeds = Seed::find()
            ->where(['status' => Seed::STATUS_AVAILABLE])
            ->orderBy(['priority' => SORT_DESC, 'name' => SORT_ASC])
            ->all();
        
        // Przygotuj dane dla kalendarza
        $calendar = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $calendar[$month] = [];
            
            foreach ($seeds as $seed) {
                if ($seed->isInSowingPeriod($year . '-' . sprintf('%02d', $month) . '-15')) {
                    $calendar[$month][] = $seed;
                }
            }
        }

        return $this->render('sowing-calendar', [
            'calendar' => $calendar,
            'year' => $year,
        ]);
    }

    /**
     * Usuwa wysiałe nasiono
     */
    public function actionDeleteSown($id)
    {
        $sownSeed = SownSeed::findOne($id);
        
        if ($sownSeed) {
            $sownSeed->delete();
            Yii::$app->session->setFlash('success', 'Usunięto zapis wysiewu.');
        } else {
            Yii::$app->session->setFlash('error', 'Nie znaleziono rekordu do usunięcia.');
        }
        
        $referer = Yii::$app->request->headers->get('referer');
        if ($referer) {
            return $this->redirect($referer);
        }
        
        return $this->redirect(['index']);
    }

    /**
     * Dodaje notatkę do wysiałego nasiona
     */
    public function actionAddNote($id)
    {
        $sownSeed = SownSeed::findOne($id);
        
        if (!$sownSeed) {
            Yii::$app->session->setFlash('error', 'Nie znaleziono rekordu.');
            return $this->redirect(['index']);
        }
        
        if (Yii::$app->request->isPost) {
            $notes = Yii::$app->request->post('notes', '');
            $sownSeed->notes = $notes;
            
            if ($sownSeed->save()) {
                Yii::$app->session->setFlash('success', 'Dodano notatkę.');
            } else {
                Yii::$app->session->setFlash('error', 'Błąd podczas zapisywania notatki.');
            }
            
            return $this->redirect(['index']);
        }
        
        return $this->render('add-note', [
            'sownSeed' => $sownSeed,
        ]);
    }
}