<?php
/**
 * LOKALIZACJA: controllers/SiteController.php
 * UWAGA: To jest modyfikacja istniejącego pliku SiteController.php z Yii2 Basic
 */

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     * Przekierowanie do dashboardu dla zalogowanych użytkowników
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/dashboard/index']);
        }
        
        return $this->redirect(['/site/login']);
    }

    /**
     * Login action.
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['/dashboard/index']);
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['/dashboard/index']);
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect(['/site/login']);
    }

    /**
     * Displays contact page.
     * Usunięte - nie potrzebne w systemie zarządzania nasionami
     */

    /**
     * Displays about page.
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Resetowanie hasła - formularz żądania
     */
    public function actionRequestPasswordReset()
    {
        $model = new \yii\base\DynamicModel(['email']);
        $model->addRule(['email'], 'required');
        $model->addRule(['email'], 'email');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::find()->where(['email' => $model->email])->one();
            
            if ($user) {
                // W rzeczywistej aplikacji tutaj wysłałbyś email
                // Na potrzeby tej aplikacji po prostu wyświetl komunikat
                Yii::$app->session->setFlash('success', 
                    'Jeśli adres email istnieje w systemie, został wysłany link do resetowania hasła. ' .
                    'W wersji demonstracyjnej skontaktuj się z administratorem.');
            } else {
                Yii::$app->session->setFlash('error', 'Nie znaleziono użytkownika o podanym adresie email.');
            }
            
            return $this->redirect(['login']);
        }

        return $this->render('request-password-reset', [
            'model' => $model,
        ]);
    }

    /**
     * Zmiana hasła dla zalogowanego użytkownika
     */
    public function actionChangePassword()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/site/login']);
        }

        $model = new \yii\base\DynamicModel(['currentPassword', 'newPassword', 'confirmPassword']);
        $model->addRule(['currentPassword', 'newPassword', 'confirmPassword'], 'required');
        $model->addRule(['newPassword'], 'string', ['min' => 6]);
        $model->addRule(['confirmPassword'], 'compare', ['compareAttribute' => 'newPassword']);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::findOne(Yii::$app->user->id);
            
            if ($user->validatePassword($model->currentPassword)) {
                $user->setPassword($model->newPassword);
                $user->generateAuthKey();
                
                if ($user->save()) {
                    Yii::$app->session->setFlash('success', 'Hasło zostało zmienione pomyślnie.');
                    return $this->redirect(['/dashboard/index']);
                } else {
                    Yii::$app->session->setFlash('error', 'Wystąpił błąd podczas zmiany hasła.');
                }
            } else {
                $model->addError('currentPassword', 'Aktualne hasło jest nieprawidłowe.');
            }
        }

        return $this->render('change-password', [
            'model' => $model,
        ]);
    }

    /**
     * Test połączenia z bazą danych
     */
    public function actionTestDb()
    {
        if (YII_ENV_DEV) {
            try {
                $connection = Yii::$app->db;
                $command = $connection->createCommand('SELECT 1');
                $result = $command->queryScalar();
                
                $userCount = User::find()->count();
                $seedCount = \app\models\Seed::find()->count();
                
                return $this->renderContent("
                    <h2>Test połączenia z bazą danych</h2>
                    <p><strong>Status:</strong> ✅ Połączenie OK</p>
                    <p><strong>Test query:</strong> {$result}</p>
                    <p><strong>Liczba użytkowników:</strong> {$userCount}</p>
                    <p><strong>Liczba nasion:</strong> {$seedCount}</p>
                    <p><a href='/dashboard'>Przejdź do dashboardu</a></p>
                ");
                
            } catch (\Exception $e) {
                return $this->renderContent("
                    <h2>Test połączenia z bazą danych</h2>
                    <p><strong>Status:</strong> ❌ Błąd połączenia</p>
                    <p><strong>Błąd:</strong> " . $e->getMessage() . "</p>
                ");
            }
        } else {
            throw new \yii\web\NotFoundHttpException();
        }
    }
}