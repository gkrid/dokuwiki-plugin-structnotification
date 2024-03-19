<?php

use dokuwiki\Extension\Plugin;

/**
 * DokuWiki Plugin structnotification (Helper Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Szymon Olewniczak <dokuwiki@cosmocode.de>
 */
class helper_plugin_structnotification_db extends Plugin
{
    /** @var helper_plugin_sqlite */
    protected $sqlite;

    /**
     * helper_plugin_struct_db constructor.
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Initialize the database
     *
     * @throws Exception
     */
    protected function init()
    {
        $this->sqlite = plugin_load('helper', 'sqlite');
        if (!$this->sqlite) {
            if (defined('DOKU_UNITTEST')) {
                throw new \Exception('Couldn\'t load sqlite.');
            }
            return;
        }

        if ($this->sqlite->getAdapter()->getName() != DOKU_EXT_PDO) {
            if (defined('DOKU_UNITTEST')) {
                throw new \Exception('Couldn\'t load PDO sqlite.');
            }
            $this->sqlite = null;
            return;
        }
        $this->sqlite->getAdapter()->setUseNativeAlter(true);

        // initialize the database connection
        if (!$this->sqlite->init('structnotification', DOKU_PLUGIN . 'structnotification/db/')) {
            if (defined('DOKU_UNITTEST')) {
                throw new \Exception('Couldn\'t init sqlite.');
            }
            $this->sqlite = null;
        }
    }

    /**
     * @return helper_plugin_sqlite|null|bool
     */
    public function getDB()
    {
        global $conf;
        $len = strlen($conf['metadir']);
        if ($this->sqlite && $conf['metadir'] != substr($this->sqlite->getAdapter()->getDbFile(), 0, $len)) {
            $this->init();
        }
        if (!$this->sqlite) {
            msg($this->getLang('error sqlite missing'), -1);
            return false;
        }
        return $this->sqlite;
    }
}
