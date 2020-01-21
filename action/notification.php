<?php
/**
 * DokuWiki Plugin structnotification (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Szymon Olewniczak <it@rid.pl>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) {
    die();
}

class action_plugin_structnotification_notification extends DokuWiki_Action_Plugin
{

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     *
     * @return void
     */
    public function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('PLUGIN_NOTIFICATION_REGISTER_SOURCE', 'FIXME', $this, 'handle_plugin_notification_register_source');        $controller->register_hook('PLUGIN_NOTIFICATION_GATHER', 'FIXME', $this, 'handle_plugin_notification_gather');        $controller->register_hook('PLUGIN_NOTIFICATION_CACHE_DEPENDENCIES', 'FIXME', $this, 'handle_plugin_notification_cache_dependencies');
   
    }

    /**
     * [Custom event handler which performs action]
     *
     * Called for event:
     *
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     *
     * @return void
     */
    public function handle_plugin_notification_register_source(Doku_Event $event, $param)
    {
    }
    public function handle_plugin_notification_gather(Doku_Event $event, $param)
    {
    }
    public function handle_plugin_notification_cache_dependencies(Doku_Event $event, $param)
    {
    }

}

