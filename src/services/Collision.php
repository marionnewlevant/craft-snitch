<?php
/**
 * Snitch plugin for Craft CMS
 *
 * Report when two people might be editing the same entry, category, or global
 *
 * @link      http://marion.newlevant.com
 * @copyright Copyright (c) 2019 Marion Newlevant
 */

namespace marionnewlevant\snitch\services;

use DateInterval;
use DateTime;
use Exception;
use marionnewlevant\snitch\Snitch;
use marionnewlevant\snitch\models\SnitchModel;
use marionnewlevant\snitch\records\SnitchRecord;

use Craft;
use craft\base\Component;
use craft\helpers\Db;
use craft\web\View;

/**
 * Collision Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Marion Newlevant
 * @package   Snitch
 * @since     1.0.0
 */
class Collision extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * From any other plugin file, call it like this:
     *
     *     Snitch::$plugin->collision->remove()
     *
     * @param int $snitchId
     * @param string $snitchType
     * @param null $userId
     *
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function remove(int $snitchId, string $snitchType, $userId = null): void
    {
        $userId = $this->_userId($userId);
        $transaction = Craft::$app->getDb()->beginTransaction();
        try {
            $record = SnitchRecord::findOne([
                'snitchId' => $snitchId,
                'snitchType' => $snitchType,
                'userId' => $userId
            ]);

            $record?->delete();

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }


    public function register(int $snitchId, string $snitchType, $userId = null, DateTime $now = null)
    {
        $now = $this->_now($now);
        $userId = $this->_userId($userId);
        $transaction = Craft::$app->getDb()->beginTransaction();
        try {
            // look for existing record to update
            $record = SnitchRecord::findOne([
                'snitchId' => $snitchId,
                'snitchType' => $snitchType,
                'userId' => $userId
            ]);

            if (!$record) {
                $record = new SnitchRecord();
                $record->snitchId = $snitchId;
                $record->snitchType = $snitchType;
                $record->userId = $userId;
            }
            $record->whenEntered = $now;
            $record->save();

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function getCollisions(int $snitchId, string $snitchType, $userId = null): array
    {
        $userId = $this->_userId($userId);
        $result = [];
        $rows = SnitchRecord::findAll([
            'snitchId' => $snitchId,
            'snitchType' => $snitchType,
        ]);
        foreach ($rows as $row)
        {
            if ($row->userId !== $userId)
            {
                $result[] = new SnitchModel($row->toArray());
            }
        }
        return $result;
    }

    public function expire(DateTime $now = null)
    {
        $now = $this->_now($now);
        $timeOut = $this->_serverPollInterval() * 10;
        $old = clone $now;
        $old->sub(new DateInterval('PT'.$timeOut.'S'));
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
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function collidingUsers(array $snitchModels): array
    {
        $result = [];
        $userIds = [];
        foreach ($snitchModels as $model)
        {
            $userIds[] = $model->userId;
        }
        $userIds = array_unique($userIds);
        
        foreach ($userIds as $id)
        {
            $user = Craft::$app->users->getUserById($id);
            if ($user)
            {
                $result[] = $user;
            }
        }
        return $result;
    }

    public function collisionMessages(array $collidingUsers, string $messageTemplate): array
    {
        $result = [];

        // save cp template path and set to site templates
        $oldMode = Craft::$app->view->getTemplateMode();
        Craft::$app->view->setTemplateMode(View::TEMPLATE_MODE_SITE);
        foreach ($collidingUsers as $user)
        {
            $message = Craft::$app->view->renderString($messageTemplate, ['user' => $user]);
            $result[] = [
                'email' => $user->email,
                'message' => $message,
            ];
        }
        // restore cp template paths
        Craft::$app->view->setTemplateMode($oldMode);

        return $result;
    }

    // ============== default values =============
    private function _userId($userId): int
    {
        if (!$userId) {
            $currentUser = Craft::$app->getUser()->getIdentity();
            if ($currentUser) {
                $userId = $currentUser->id;
            }
        }
        return (int)($userId);
    }

    private function _now(DateTime $now = null): DateTime
    {
        return ($now ?: new DateTime());
    }

    private function _serverPollInterval()
    {
        $settings = Snitch::$plugin->getSettings();
        return $settings['serverPollInterval'];
    }
}
