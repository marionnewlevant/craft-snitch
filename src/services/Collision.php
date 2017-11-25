<?php

namespace marionnewlevant\snitch\services;

use Craft;
use craft\helpers\Db;
use marionnewlevant\snitch\Plugin as Snitch;
use marionnewlevant\snitch\models\SnitchModel;
use marionnewlevant\snitch\records\SnitchRecord;
use yii\base\Component;

class Collision extends Component
{
    public function remove(string $elementId, $userId = null)
    {
        $userId = $this->_userId($userId);
        $transaction = Craft::$app->getDb()->beginTransaction();
        try {
            $record = SnitchRecord::findOne([
                'elementId' => $elementId,
                'userId' => $userId
            ]);

            if ($record) {
                $record->delete();
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function register(string $elementId, $userId = null, \DateTime $now = null)
    {
        $now = $this->_now($now);
        $userId = $this->_userId($userId);
        $transaction = Craft::$app->getDb()->beginTransaction();
        try {
            // look for existing record to update
            $record = SnitchRecord::findOne([
                'elementId' => $elementId,
                'userId' => $userId
            ]);

            if (!$record) {
                $record = new SnitchRecord();
                $record->elementId = $elementId;
                $record->userId = $userId;
            }
            $record->whenEntered = $now;
            $record->save();

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function getCollisions(string $elementId)
    {
        $result = [];
        $rows = SnitchRecord::findAll([
            'elementId' => $elementId
        ]);
        foreach ($rows as $row)
        {
            $result[] = new SnitchModel($row);
        }
        return $result;
    }

    public function expire(\DateTime $now = null)
    {
        $now = $this->_now($now);
        $timeOut = $this->_serverPollInterval() * 10;
        $old = clone $now;
        $old->sub(new \DateInterval('PT'.$timeOut.'S'));
        $transaction = Craft::$app->getDb()->beginTransaction();
        try {
            $allExpired = SnitchRecord::find()
                ->where(['<', 'whenEntered', Db::prepareDateForDb($old)])
                ->all();
            foreach ($allExpired as $expired)
            {
                $expired->delete();
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function userData(array $snitchModels, $userId = null)
    {
        $userId = $this->_userId($userId);
        $result = [];
        $userIds = [];
        foreach ($snitchModels as $model)
        {
            $userIds[] = $model->userId;
        }
        $userIds = array_unique($userIds);
        
        foreach ($userIds as $id)
        {
            if ($id !== $userId)
            {
                $user = Craft::$app->users->getUserById($id);
                if ($user)
                {
                    $result[] = [
                        'name' => $user->getFriendlyName(),
                        'email' => $user->email,
                    ];
                }
            }
        }
        return $result;
    }

    // ============== default values =============
    private function _userId($userId)
    {
        if (!$userId) {
            $currentUser = Craft::$app->getUser()->getIdentity();
            if ($currentUser) {
                $userId = $currentUser->id;
            }
        }
        return (int)($userId);
    }

    private function _now(\DateTime $now = null)
    {
        return ($now ? $now : new \DateTime());
    }

    private function _serverPollInterval()
    {
        $settings = Snitch::$plugin->getSettings();
        return $settings['serverPollInterval'];
    }
}