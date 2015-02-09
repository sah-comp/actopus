<?php
/**
 * Cinnebar.
 *
 * My lightweight no-framework framework written in PHP.
 *
 * @package Cinnebar
 * @author $Author$
 * @version $Id$
 */

/**
 * The messenger class of the cinnebar system.
 *
 * @package Cinnebar
 * @subpackage Messenger
 * @version $Id$
 */
class Cinnebar_Messenger
{
    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Deliver an (internal) message to a certain user.
     *
     * @param mixed $user
     * @param string $msg
     * @param string $type can be 'alert', 'error', 'warn' or alike
     * @param mixed $sender
     * @return bool
     */
    public function notify($user, $msg = 'This is a test message. Ignore.', $type = 'alert', $from = null)
    {
        $notification = R::dispense('notification');
        $notification->payload = $msg;
        $notification->template = $type;
        try {
            R::store($notification);
            if (is_a($user, 'RedBean_OODBBean')) R::associate($user, $notification);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Returns an array with this users notifications and dismisses them.
     *
     * The notifications are ony fetchable once. If you do not want to trash them you have to
     * set the optional parameter to skip the trashing of retrieved notification beans.
     *
     * @param RedBean_OODBBean $bean
     * @param string $sql optional $sql
     * @param array $values optional array with values for the sql jokers
     * @param bool (optional) $trash defaults to true, so notifications are only fetchable once
     * @return array $arrayOfNotifications
     */
    public function notifications(RedBean_OODBBean $bean, $sql = '', array $values = array(), $trash = true)
    {
        $all = R::related($bean, 'notification', $sql, $values);
        if ($trash) R::trashAll($all);
        return $all;
    }
}
