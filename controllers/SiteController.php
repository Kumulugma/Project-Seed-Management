<?php
/**
 * LOKALIZACJA: controllers/SiteController.php
 * KOMPLETNY KONTROLER Z METODĄ actionResetPassword
 */

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\User;
use app\models\PasswordResetToken;
use yii\base\DynamicModel;

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
                'only' => ['logout', 'change-password'],
                'rules' => [
                    [
                        'actions' => ['logout', 'change-password'],
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
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/site/login']);
        }
        
        return $this->redirect(['/dashboard/index']);
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
        $model = new DynamicModel(['email']);
        $model->addRule(['email'], 'required', ['message' => 'Email jest wymagany.']);
        $model->addRule(['email'], 'email', ['message' => 'Nieprawidłowy format email.']);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = User::find()->where(['email' => $model->email])->one();
            
            if ($user) {
                // Generuj token
                $token = PasswordResetToken::generateToken($user->id);
                
                if ($token) {
                    // W rzeczywistej aplikacji wysłałbyś email z linkiem
                    // Na potrzeby demonstracji pokażemy link w komunikacie
                    $resetUrl = Yii::$app->urlManager->createAbsoluteUrl([
                        'site/reset-password', 
                        'token' => $token
                    ]);
                    
                    Yii::$app->session->setFlash('success', 
                        'Link do resetowania hasła został wygenerowany:<br>' .
                        '<strong><a href="' . $resetUrl . '">' . $resetUrl . '</a></strong><br>' .
                        '<small>W rzeczywistej aplikacji zostałby wysłany na email.</small>'
                    );
                } else {
                    Yii::$app->session->setFlash('error', 'Wystąpił błąd podczas generowania tokenu.');
                }
            } else {
                // Nie ujawniamy czy email istnieje w systemie
                Yii::$app->session->setFlash('success', 
                    'Jeśli adres email istnieje w systemie, został wysłany link do resetowania hasła.'
                );
            }
            
            return $this->redirect(['login']);
        }

        return $this->render('request-password-reset', [
            'model' => $model,
        ]);
    }

    /**
     * Resetowanie hasła - formularz nowego hasła (BRAKUJĄCA METODA!)
     */
    public function actionResetPassword($token = null)
    {
        if (!$token) {
            throw new \yii\web\BadRequestHttpException('Brak tokenu resetowania hasła.');
        }

        // Sprawdź czy token jest ważny
        if (!PasswordResetToken::isTokenValid($token)) {
            Yii::$app->session->setFlash('error', 
                'Token resetowania hasła jest nieprawidłowy lub wygasł. Spróbuj ponownie.');
            return $this->redirect(['request-password-reset']);
        }

        $user = PasswordResetToken::findUserByToken($token);
        if (!$user) {
            Yii::$app->session->setFlash('error', 'Nie znaleziono użytkownika.');
            return $this->redirect(['request-password-reset']);
        }

        $model = new DynamicModel(['newPassword', 'confirmPassword']);
        $model->addRule(['newPassword', 'confirmPassword'], 'required');
        $model->addRule(['newPassword'], 'string', ['min' => 6, 'message' => 'Hasło musi mieć co najmniej 6 znaków.']);
        $model->addRule(['confirmPassword'], 'compare', [
            'compareAttribute' => 'newPassword',
            'message' => 'Hasła muszą być identyczne.'
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // Ustaw nowe hasło
            $user->setPassword($model->newPassword);
            $user->generateAuthKey();
            
            if ($user->save()) {
                // Usuń token po użyciu
                PasswordResetToken::deleteToken($token);
                
                Yii::$app->session->setFlash('success', 
                    'Hasło zostało zmienione pomyślnie. Możesz się teraz zalogować.');
                return $this->redirect(['login']);
            } else {
                Yii::$app->session->setFlash('error', 'Wystąpił błąd podczas zmiany hasła.');
            }
        }

        return $this->render('reset-password', [
            'model' => $model,
            'user' => $user,  // PRZEKAZUJEMY ZMIENNĄ $user DO WIDOKU!
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

        $model = new DynamicModel(['currentPassword', 'newPassword', 'confirmPassword']);
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
     * Czyści wygasłe tokeny resetowania hasła (może być wywołane przez cron)
     */
    public function actionCleanExpiredTokens()
    {
        $deleted = PasswordResetToken::cleanExpiredTokens();
        
        if (Yii::$app->request->isConsoleRequest) {
            echo "Usunięto {$deleted} wygasłych tokenów.\n";
        } else {
            return $this->asJson(['deleted' => $deleted]);
        }
    }

    /**
     * Test połączenia z bazą danych (tylko development)
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