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
require_once realpath(dirname(__FILE__) . '/framework/jbzoo.php');

/**
 * Class JBUniversalApplication
 * JBZoo Application class
 */
final class JBUniversalApplication extends Application
{
    /**
     * Constructor
     */
    final public function __construct()
    {
        parent::__construct();

        JBZoo::init();
        $this->app = App::getInstance('zoo');
        $this->app->jbdebug->mark('application::init::start');
    }

    /**
     * Register controller path, only for frontend
     */
    private function _initAssets()
    {
        if ($this->app->jbenv->isSite()) {
            $this->app->jbassets->setAppCSS($this->alias);
            $this->app->jbassets->setAppJS($this->alias);
        }
    }

    /**
     * JBZoo Auto check
     */
    final private function _autoCheck()
    {
        if (!$this->app->jbenv->isSite()) {

            // check is event plugin enabled
            if (!JPluginHelper::isEnabled('system', 'jbzoo')) {
                $link = JUri::root() . 'administrator/index.php?option=com_plugins&filter_search=jbzoo';
                $this->app->jbnotify->notice('"System - JBZoo" plugin is not install or not enabled, <a href="' . $link . '">find it</a>');
            }

            // check new version
            $this->app->jbupdate->checkNewVersion();
        }
    }

    /**
     * Dispatch
     */
    final public function dispatch()
    {
        define('JBZOO_DISPATCHED', true);
        $this->_autoCheck();

        $this->app->jbdebug->mark('application::dispatch::before');

        $this->_initAssets();

        $ctrlRequest = str_replace('jbuniversal', '', $this->app->request->get('controller', 'word', 'default'));

        // get current controller
        if ($this->app->jbenv->isSite()) {
            $newControllerPath = $this->app->path->path('jbzoo:/controllers/' . $ctrlRequest . '.php');

        } else {
            $this->app->jbtoolbar->toolbar();
            $newControllerPath = $this->app->path->path('jbzoo:/controllers/admin.' . $ctrlRequest . '.php');
        }

        // check is override controller exists
        if ($newControllerPath && JFile::exists($newControllerPath)) {
            $ctrlName = $ctrlRequest . $this->getGroup();

            // set and dispatch it
            $this->app->request->set('controller', $ctrlName);
            require_once($newControllerPath);

            $this->app->dispatch($ctrlName);

        } else {
            parent::dispatch();
        }

        $this->app->jbdebug->mark('application::dispatch::after');
    }

    /**
     * Init form elements
     * @return null
     */
    public function getParamsForm()
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
    public function hasAppIcon()
    {
        return (bool)$this->app->path->path($this->getResource() . 'assets/app_icons/' . $this->alias . '.png');
    }

    /**
     * Get application icon
     * @return string
     */
    public function getIcon()
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
    public function getGroup()
    {
        return JBZOO_APP_GROUP;
    }

    /**
     * Check JBZoo Licence
     * @param bool $getResponce
     * @return mixed
     * @throws Exception
     */
    public function checkLicence($getResponce = false) {
        return null;
    }
}
