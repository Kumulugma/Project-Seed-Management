<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class PasswordResetToken extends ActiveRecord
{
    const TOKEN_EXPIRY_HOURS = 24; // Token ważny przez 24 godziny

    public static function tableName()
    {
        return '{{%password_reset_token}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'token'], 'required'],
            [['user_id'], 'integer'],
            [['token'], 'string', 'max' => 255],
            [['created_at', 'expires_at'], 'safe'],
            [['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => 'id'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Użytkownik',
            'token' => 'Token',
            'created_at' => 'Data utworzenia',
            'expires_at' => 'Data wygaśnięcia',
        ];
    }

    /**
     * Relacja z użytkownikiem
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Generuje nowy token dla użytkownika
     */
    public static function generateToken($userId)
    {
        // Usuń stare tokeny dla tego użytkownika
        self::deleteAll(['user_id' => $userId]);

        // Utwórz nowy token
        $token = new self();
        $token->user_id = $userId;
        $token->token = Yii::$app->security->generateRandomString(64);
        $token->created_at = date('Y-m-d H:i:s');
        $token->expires_at = date('Y-m-d H:i:s', strtotime('+' . self::TOKEN_EXPIRY_HOURS . ' hours'));

        if ($token->save()) {
            return $token->token;
        }

        return false;
    }

    /**
     * Sprawdza czy token jest ważny
     */
    public static function isTokenValid($token)
    {
        $tokenRecord = self::find()
            ->where(['token' => $token])
            ->andWhere(['>', 'expires_at', date('Y-m-d H:i:s')])
            ->one();

        return $tokenRecord !== null;
    }

    /**
     * Znajduje użytkownika na podstawie tokenu
     */
    public static function findUserByToken($token)
    {
        $tokenRecord = self::find()
            ->where(['token' => $token])
            ->andWhere(['>', 'expires_at', date('Y-m-d H:i:s')])
            ->one();

        if ($tokenRecord) {
            return User::findOne($tokenRecord->user_id);
        }

        return null;
    }

    /**
     * Usuwa token po użyciu
     */
    public static function deleteToken($token)
    {
        return self::deleteAll(['token' => $token]);
    }

    /**
     * Czyści wygasłe tokeny
     */
    public static function cleanExpiredTokens()
    {
        return self::deleteAll(['<', 'expires_at', date('Y-m-d H:i:s')]);
    }
}
