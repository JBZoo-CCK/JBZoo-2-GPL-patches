<?php
/**
 * JBZoo Application
 *
 * This file is part of the JBZoo CCK package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Application
 * @license    GPL-2.0
 * @copyright  Copyright (C) JBZoo.com, All rights reserved.
 * @link       https://github.com/JBZoo/JBZoo
 * @author     Denis Smetannikov <denis@jbzoo.com>
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


// include jbzoo init class
require_once dirname(__FILE__) . '/framework/jbzoo.php';

/**
 * Class JBUniversalApplication
 * JBZoo Application class
 */
final class JBUniversalApplication extends Application
{
    const JBZOO_VERSION = '2.3.0';

    /**
     * @var JBDebugHelper
     */
    private $_jbdebug = null;

    /**
     * @var JBRequestHelper
     */
    private $_jbrequest = null;

    /**
     * @var bool
     */
    private $_isSite = false;

    /**
     * Constructor
     */
    final public function __construct()
    {
        parent::__construct();

        JBZoo::init();
        $this->app = App::getInstance('zoo');

        $this->_jbrequest = $this->app->jbrequest;
        $this->_jbdebug   = $this->app->jbdebug;

        $this->_jbdebug->mark('application::init::start');
        $this->_isSite = $this->app->jbenv->isSite();
    }

    /**
     * Register controller path, only for frontend
     */
    final private function _initAssets()
    {
        if ($this->_isSite) {
            $this->app->jbassets->setAppCSS($this->alias);
            $this->app->jbassets->setAppJS($this->alias);
        }
    }

    /**
     * JBZoo Auto check
     */
    final private function _autoCheck()
    {
        if (!$this->_isSite) {

            // check is event plugin enabled
            if (!JPluginHelper::isEnabled('system', 'jbzoo')) {
                $link = JUri::root() . 'administrator/index.php?option=com_plugins&filter_search=jbzoo';
                $this->app->jbnotify->notice('"System - JBZoo" plugin is not install or not enabled, <a href="' . $link . '">find it</a>');
            }
        }
    }

    /**
     * Dispatch
     */
    final public function dispatch()
    {
        define('JBZOO_DISPATCHED', true);

        // start check acl
        $this->_autoCheck();

        $this->app->jbdebug->mark('application::dispatch::before');

        $this->_initAssets();

        // dispatcher hack
        $ctrlRequest = $this->_jbrequest->getCtrl();

        // try to get current controller
        if ($this->_isSite) {
            $newCtrlPath = $this->app->path->path('jbzoo:/controllers/' . $ctrlRequest . '.php');

        } else {
            $this->app->jbtoolbar->toolbar();
            $newCtrlPath = $this->app->path->path('jbzoo:/controllers/admin.' . $ctrlRequest . '.php');

            // hack for index page in Joomla CP
            $task = $this->_jbrequest->getWord('task');
            if (strpos($ctrlRequest, 'jb') === 0 && empty($task)) {
                $this->app->request->set('task', 'index');
            }
        }

        // check is override controller exists
        if ($newCtrlPath) {
            $newCtrlPath = JPath::clean($newCtrlPath);
            require_once $newCtrlPath;

            $newCtrl = $ctrlRequest . $this->getGroup();

            $this->_jbrequest->set('controller', $newCtrl);
            $this->app->dispatch($newCtrl);

        } else {
            parent::dispatch();
        }

        $this->app->jbdebug->mark('application::dispatch::after');
    }

    /**
     * Init form elements
     * @return null
     */
    final public function getParamsForm()
    {
        // get parameter xml file
        if ($xml = $this->app->path->path($this->getResource() . $this->metaxml_file)) {

            // get form
            $form = $this->app->parameterform->create($xml);

            // add own joomla elements
            $form->addElementPath($this->app->path->path('applications:' . $this->getGroup() . '/joomla/elements'));

            return $form;
        }

        return null;
    }

    /**
     * Check has application icon
     * @return bool
     */
    final public function hasAppIcon()
    {
        return (bool)$this->app->path->path($this->getResource() . 'assets/app_icons/' . $this->alias . '.png');
    }

    /**
     * Get application icon
     * @return string
     */
    final public function getIcon()
    {
        if ($this->hasAppIcon()) {
            return $this->app->path->url($this->getResource() . 'assets/app_icons/' . $this->alias . '.png');
        } else {
            if ($this->hasIcon()) {
                return $this->app->path->url($this->getResource() . 'application.png');
            } else {
                return $this->app->path->url('assets:images/zoo.png');
            }
        }
    }

    /**
     * Get JBZoo application group
     * @return string
     */
    final public function getGroup()
    {
        return JBZOO_APP_GROUP;
    }

    /**
     * Gat hash by var
     * @param $string
     * @return string
     */
    final public function getHash($string)
    {
        return JBZoo::getHash($string);
    }

    /**
     * @param $data
     * @return array
     */
    final public function getLicenceData($data)
    {
        return [];
    }

    /**
     * Check JBZoo Licence
     * @param bool $getResponce
     * @return mixed
     * @throws Exception
     */
    final public function checkLicence($getResponce = false)
    {
        return null;
    }

    /**
     * @param bool $isAny
     * @return string
     */
    final public function getDomain($isAny = false)
    {
        return JBZoo::getDomain($isAny);
    }


    /**
     * @param $order
     * @param $prevResult
     * @return array
     */
    public function setItemOrder($order, $prevResult)
    {
        $jbtables = $this->app->jbtables;
        $jborder  = $this->app->jborder;

        $orders = $jborder->convert($order);

        $joinList  = array();
        $orderList = array();
        $columns   = array();

        $isRandom = $this->app->jbarray->recursiveSearch('random', $orders);
        if ($isRandom !== false) {
            return array('', ' RAND() ');
        }

        foreach ($orders as $orderParams) {

            $order = $jborder->getOrderDirection($orderParams['order']);

            if ($orderParams['field'] == 'corename') {
                $orderList[] = 'a.name ' . $order;

            } elseif ($orderParams['field'] == 'corealias') {
                $orderList[] = 'a.alias ' . $order;

            } elseif ($orderParams['field'] == 'corecreated') {
                $orderList[] = 'a.created ' . $order;

            } elseif ($orderParams['field'] == 'corehits') {
                $orderList[] = 'a.hits ' . $order;

            } elseif ($orderParams['field'] == 'coremodified') {
                $orderList[] = 'a.modified ' . $order;

            } elseif ($orderParams['field'] == 'corepublish_down') {
                $orderList[] = 'a.publish_down ' . $order;

            } elseif ($orderParams['field'] == 'corepublish_up') {
                $orderList[] = 'a.publish_up ' . $order;

            } elseif ($orderParams['field'] == 'coreauthor') {
                $orderList[]              = 'tJoomlaUsers.name ' . $order;
                $joinList['tJoomlaUsers'] = 'LEFT JOIN #__users AS tJoomlaUsers ON a.created_by = tJoomlaUsers.id';

            } elseif (strpos($orderParams['field'], '__')) {
                list ($elementId, $priceField) = explode('__', $orderParams['field']);

                if (in_array($priceField, array('sku', 'total', 'price'), true)) {
                    $orderList[]              = 'tItemSku.' . $priceField . ' ' . $order;
                    $joinList['tJoomlaUsers'] = 'LEFT JOIN ' . ZOO_TABLE_JBZOO_SKU . ' AS tItemSku ON a.id = tItemSku.item_id';
                }

            } else {
                $itemType = $this->app->jbentity->getItemTypeByElementId($orderParams['field']);

                if (!empty($itemType)) {

                    $tableName          = $jbtables->getIndexTable($itemType);
                    $tableSqlName       = 'tIndex' . str_replace('#__', '', $tableName);
                    $columns[$itemType] = $jbtables->getFields($tableName);

                    $elementId = $this->app->jbtables->getFieldName($orderParams['field'], $orderParams['mode']);
                    if (in_array($elementId, $columns[$itemType], true)) {
                        $joinList[$itemType] = 'LEFT JOIN ' . $tableName
                            . ' AS ' . $tableSqlName . ' ON a.id = ' . $tableSqlName . '.item_id'
                            . ' AND ' . $tableSqlName . '.' . $elementId . ' IS NOT NULL';

                        $orderList[] = $tableSqlName . '.' . $elementId . ' ' . $order;
                    }
                }
            }
        }

        if (!empty($orderList)) {
            return array(' ' . implode(' ', $joinList) . ' ', implode(', ', $orderList));

        } else if (!empty($prevResult)) {
                return $prevResult;
            }

        return array('', ' a.id ASC ');
    }
}
