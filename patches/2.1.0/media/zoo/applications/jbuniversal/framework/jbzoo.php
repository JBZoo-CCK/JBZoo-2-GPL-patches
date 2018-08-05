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


!defined('JBZOO_APP_GROUP') && define('JBZOO_APP_GROUP', 'jbuniversal');
!defined('DIRECTORY_SEPERATOR') && define('DIRECTORY_SEPERATOR', '/');
!defined('DS') && define('DS', DIRECTORY_SEPARATOR);

/**
 * Class JBZoo
 */
class JBZoo
{
    /**
     * @var Application
     */
    public $app = null;

    /**
     * App group name
     * @var string
     */
    private $_group = JBZOO_APP_GROUP;

    /**
     * Init JBZoo application
     * @static
     * @return JBZoo
     */
    public static function init()
    {
        static $jbzoo;

        if (!isset($jbzoo)) {
            $jbzoo = new self();
        }

        return $jbzoo;
    }

    /**
     * Initialization JBZoo App
     */
    private function __construct()
    {
        $this->app = App::getInstance('zoo');

        $this->_initPaths();
        $this->_initConfig();
        $this->_initModels();
        $this->_initLanguages();
        $this->_initFilterElements();
        $this->_initEvents();
        $this->_initAssets();
    }

    /**
     * Add directory path
     */
    private function _initPaths()
    {
        $this->_addPath('applications:' . $this->_getGroup(), 'jbapp');
        $this->_addPath('jbapp:framework', 'jbzoo');
        $this->_addPath('jbapp:assets', 'jbassets');
        $this->_addPath('jbassets:zoo', 'assets');
        $this->_addPath('jbapp:config', 'jbconfig');
        $this->_addPath('jbzoo:elements', 'jbelements');
        $this->_addPath('jbapp:types', 'jbtypes');
        $this->_addPath('jbzoo:helpers', 'helpers');
        $this->_addPath('jbzoo:helpers-std', 'helpers');
        $this->_addPath('jbzoo:classes-std', 'classes');
        $this->_addPath('jbzoo:render', 'renderer');
        $this->_addPath('jbzoo:views', 'jbviews');
        $this->_addPath('jbapp:config', 'jbxml');
        $this->_addPath('jbviews:', 'partials');
        $this->_addPath('jbapp:joomla/elements', 'fields');
        $this->_addPath('jbapp:templates', 'jbtmpl');
        $this->_addPath('modules:mod_jbzoo_search', 'mod_jbzoo_search');
        $this->_addPath('modules:mod_jbzoo_props', 'mod_jbzoo_props');
        $this->_addPath('modules:mod_jbzoo_basket', 'mod_jbzoo_basket');
        $this->_addPath('modules:mod_jbzoo_category', 'mod_jbzoo_category');
        $this->_addPath('plugins:/system/jbzoo', 'plugin_jbzoo');

        if ($this->app->jbenv->isSite()) {
            $this->_addPath('jbzoo:controllers', 'controllers');
        }

        require $this->app->path->path('jbzoo:controllers/base.php');

        JLoader::register('AppView', $this->app->path->path('classes:view.php'), true);
    }

    /**
     * Include models classes
     */
    private function _initModels()
    {
        // defines
        define('ZOO_TABLE_JBZOO_SKU', '#__zoo_jbzoo_sku');
        define('ZOO_TABLE_JBZOO_FAVORITE', '#__zoo_jbzoo_favorite');

        // query builder
        $path = JPATH_SITE . '/media/zoo/applications/jbuniversal/framework/classes';
        require $path . '/database/JBDatabaseQuery.php';
        require $path . '/database/JBDatabaseQueryElement.php';

        // models
        $path = JPATH_SITE . '/media/zoo/applications/jbuniversal/framework/models';
        require $path . '/jbmodel.php';
        require $path . '/jbmodel.element.php';
        require $path . '/jbmodel.autocomplete.php';
        require $path . '/jbmodel.element.country.php';
        require $path . '/jbmodel.element.itemdate.php';
        require $path . '/jbmodel.element.itemauthor.php';
        require $path . '/jbmodel.element.itemcategory.php';
        require $path . '/jbmodel.element.itemcreated.php';
        require $path . '/jbmodel.element.itemfrontpage.php';
        require $path . '/jbmodel.element.itemmodified.php';
        require $path . '/jbmodel.element.itemname.php';
        require $path . '/jbmodel.element.itempublish_down.php';
        require $path . '/jbmodel.element.itempublish_up.php';
        require $path . '/jbmodel.element.itemtag.php';
        require $path . '/jbmodel.element.jbimage.php';
        require $path . '/jbmodel.element.jbselectcascade.php';
        require $path . '/jbmodel.element.range.php';
        require $path . '/jbmodel.element.rating.php';
        require $path . '/jbmodel.element.jbpriceadvance.php';
        require $path . '/jbmodel.favorite.php';
        require $path . '/jbmodel.filter.php';
        require $path . '/jbmodel.item.php';
        require $path . '/jbmodel.app.php';
        require $path . '/jbmodel.category.php';
        require $path . '/jbmodel.order.php';
        require $path . '/jbmodel.related.php';
        require $path . '/jbmodel.searchindex.php';
        require $path . '/jbmodel.values.php';
        require $path . '/jbmodel.sku.php';
    }

    /**
     * Load lang files
     */
    private function _initLanguages()
    {
        $lang = JFactory::getLanguage();
        $lang->load('com_zoo');
        $lang->load('com_jbzoo', $this->app->path->path('jbapp:'), null, true);
        $lang->load('com_zoo', JPATH_ADMINISTRATOR);
    }

    /**
     * Load others libraries
     */
    private function _initFilterElements()
    {
        jimport('joomla.html.parameter.element');

        $path = JPATH_SITE . '/media/zoo/applications/jbuniversal/framework/render/filter';
        require $path . '/element.php';
        require $path . '/element.author.php';
        require $path . '/element.author.checkbox.php';
        require $path . '/element.author.radio.php';
        require $path . '/element.author.select.php';
        require $path . '/element.author.select.chosen.php';
        require $path . '/element.author.text.php';
        require $path . '/element.category.php';
        require $path . '/element.category.chosen.php';
        require $path . '/element.checkbox.php';
        require $path . '/element.country.php';
        require $path . '/element.country.checkbox.php';
        require $path . '/element.country.radio.php';
        require $path . '/element.country.select.php';
        require $path . '/element.country.select.chosen.php';
        require $path . '/element.date.php';
        require $path . '/element.date.range.php';
        require $path . '/element.frontpage.php';
        require $path . '/element.frontpage.jqueryui.php';
        require $path . '/element.hidden.php';
        require $path . '/element.imageexists.php';
        require $path . '/element.imageexists.jqueryui.php';
        require $path . '/element.jbselectcascade.php';
        require $path . '/element.jqueryui.php';
        require $path . '/element.name.php';
        require $path . '/element.name.checkbox.php';
        require $path . '/element.name.radio.php';
        require $path . '/element.name.select.php';
        require $path . '/element.name.select.chosen.php';
        require $path . '/element.radio.php';
        require $path . '/element.rating.php';
        require $path . '/element.rating.ranges.php';
        require $path . '/element.rating.slider.php';
        require $path . '/element.select.php';
        require $path . '/element.select.chosen.php';
        require $path . '/element.slider.php';
        require $path . '/element.tag.php';
        require $path . '/element.tag.checkbox.php';
        require $path . '/element.tag.radio.php';
        require $path . '/element.tag.select.php';
        require $path . '/element.tag.select.chosen.php';
        require $path . '/element.text.php';
        require $path . '/element.text.range.php';
    }

    /**
     * Register and connect events
     */
    private function _initEvents()
    {
        $path = JPATH_SITE . '/media/zoo/applications/jbuniversal/framework/events';
        require $path . '/jbevent.php';
        require $path . '/jbevent.application.php';
        require $path . '/jbevent.basket.php';
        require $path . '/jbevent.category.php';
        require $path . '/jbevent.comment.php';
        require $path . '/jbevent.element.php';
        require $path . '/jbevent.item.php';
        require $path . '/jbevent.jbzoo.php';
        require $path . '/jbevent.layout.php';
        require $path . '/jbevent.submission.php';
        require $path . '/jbevent.tag.php';
        require $path . '/jbevent.type.php';
        require $path . '/jbevent.payment.php';

        $event = $this->app->event;
        $dispatcher = $event->dispatcher;

        $event->register('JBEventApplication');
        $dispatcher->connect('application:init', ['JBEventApplication', 'init']);
        $dispatcher->connect('application:saved', ['JBEventApplication', 'saved']);
        $dispatcher->connect('application:deleted', ['JBEventApplication', 'deleted']);
        $dispatcher->connect('application:addmenuitems', ['JBEventApplication', 'addmenuitems']);
        $dispatcher->connect('application:installed', ['JBEventApplication', 'installed']);
        $dispatcher->connect('application:configparams', ['JBEventApplication', 'configparams']);
        $dispatcher->connect('application:sefbuildroute', ['JBEventApplication', 'sefbuildroute']);
        $dispatcher->connect('application:sefparseroute', ['JBEventApplication', 'sefparseroute']);
        $dispatcher->connect('application:sh404sef', ['JBEventApplication', 'sh404sef']);

        $event->register('JBEventCategory');
        $dispatcher->connect('category:init', ['JBEventCategory', 'init']);
        $dispatcher->connect('category:saved', ['JBEventCategory', 'saved']);
        $dispatcher->connect('category:deleted', ['JBEventCategory', 'deleted']);
        $dispatcher->connect('category:stateChanged', ['JBEventCategory', 'stateChanged']);

        $event->register('JBEventItem');
        $dispatcher->connect('item:init', ['JBEventItem', 'init']);
        $dispatcher->connect('item:saved', ['JBEventItem', 'saved']);
        $dispatcher->connect('item:deleted', ['JBEventItem', 'deleted']);
        $dispatcher->connect('item:stateChanged', ['JBEventItem', 'stateChanged']);
        $dispatcher->connect('item:beforedisplay', ['JBEventItem', 'beforeDisplay']);
        $dispatcher->connect('item:afterdisplay', ['JBEventItem', 'afterDisplay']);
        $dispatcher->connect('item:orderquery', ['JBEventItem', 'orderQuery']);
        $dispatcher->connect('item:beforeSaveCategoryRelations', ['JBEventItem', 'beforeSaveCategoryRelations']);

        $event->register('JBEventComment');
        $dispatcher->connect('comment:init', ['JBEventComment', 'init']);
        $dispatcher->connect('comment:saved', ['JBEventComment', 'saved']);
        $dispatcher->connect('comment:deleted', ['JBEventComment', 'deleted']);
        $dispatcher->connect('comment:stateChanged', ['JBEventComment', 'stateChanged']);

        $event->register('JBEventSubmission');
        $dispatcher->connect('submission:init', ['JBEventSubmission', 'init']);
        $dispatcher->connect('submission:saved', ['JBEventSubmission', 'saved']);
        $dispatcher->connect('submission:deleted', ['JBEventSubmission', 'deleted']);
        $dispatcher->connect('submission:beforesave', ['JBEventSubmission', 'beforeSave']);

        $event->register('JBEventElement');
        $dispatcher->connect('element:download', ['JBEventElement', 'download']);
        $dispatcher->connect('element:configform', ['JBEventElement', 'configForm']);
        $dispatcher->connect('element:configparams', ['JBEventElement', 'configParams']);
        $dispatcher->connect('element:configxml', ['JBEventElement', 'configXML']);
        $dispatcher->connect('element:afterdisplay', ['JBEventElement', 'afterDisplay']);
        $dispatcher->connect('element:beforedisplay', ['JBEventElement', 'beforeDisplay']);
        $dispatcher->connect('element:aftersubmissiondisplay', ['JBEventElement', 'afterSubmissionDisplay']);
        $dispatcher->connect('element:beforesubmissiondisplay', ['JBEventElement', 'beforeSubmissionDisplay']);
        $dispatcher->connect('element:beforeedit', ['JBEventElement', 'beforeEdit']);
        $dispatcher->connect('element:afteredit', ['JBEventElement', 'afterEdit']);

        $event->register('JBEventLayout');
        $dispatcher->connect('layout:init', ['JBEventLayout', 'init']);

        $event->register('JBEventTag');
        $dispatcher->connect('tag:saved', ['JBEventTag', 'saved']);
        $dispatcher->connect('tag:deleted', ['JBEventTag', 'deleted']);

        $event->register('JBEventType');
        $dispatcher->connect('type:beforesave', ['JBEventType', 'beforesave']);
        $dispatcher->connect('type:aftersave', ['JBEventType', 'aftersave']);
        $dispatcher->connect('type:copied', ['JBEventType', 'copied']);
        $dispatcher->connect('type:deleted', ['JBEventType', 'deleted']);
        $dispatcher->connect('type:editdisplay', ['JBEventType', 'editDisplay']);
        $dispatcher->connect('type:coreconfig', ['JBEventType', 'coreconfig']);
        $dispatcher->connect('type:assignelements', ['JBEventType', 'assignelements']);

        $event->register('JBEventJBZoo');
        $dispatcher->connect('jbzoo:beforeInit', ['JBEventJBZoo', 'beforeInit']);
        $dispatcher->notify($event->create($this, 'jbzoo:beforeInit'));

        $event->register('JBEventBasket');
        $dispatcher->connect('basket:beforesave', ['JBEventBasket', 'beforeSave']);
        $dispatcher->connect('basket:saved', ['JBEventBasket', 'saved']);

        $event->register('JBEventPayment');
        $dispatcher->connect('payment:callback', ['JBEventPayment', 'callback']);
    }

    /**
     * Init assets for admin path
     */
    private function _initAssets()
    {
        if (!$this->app->jbenv->isSite()) {
            $this->app->jbassets->admin();
            $this->_initAdminMenu();
        }
    }

    /**
     * Init Admin menu
     */
    private function _initAdminMenu()
    {
        if (defined('JBZOO_CONFIG_ADMINMENU') && !JBZOO_CONFIG_ADMINMENU) {
            return false;
        }

        $appList = JBModelApp::model()->getSimpleList();
        $findJBZooApp = false;
        foreach ($appList as $app) {
            if ($app->application_group == JBZOO_APP_GROUP) {
                $findJBZooApp = true;
                break;
            }
        }

        if (!$findJBZooApp) {
            return false;
        }

        $router = $this->app->jbrouter;
        $cache = JFactory::getCache('jbzooadmin', 'output', 'file');
        $cache->setCaching(1);
        $cache->setLifeTime(900);

        $menuItems = $cache->get('jbadminitems');

        if ($menuItems === false) {
            $menuItems = [];

            $appPath = $this->app->path->path('jbapp:');
            $appIcon = $this->app->jbimage->resize($appPath . '/application.png', 16, 16);

            if (!empty($appList)) {
                foreach ($appList as $app) {

                    if ($iconPath = $this->app->path->path('jbassets:app_icons/' . $app->alias . '.png')) {
                        $appIcon = $this->app->jbimage->resize($iconPath, 16, 16);
                    }

                    $menuItems['app-' . $app->alias] = [
                        'name'     => $app->name,
                        'url'      => $router->admin(['changeapp' => $app->id, 'controller' => 'item']),
                        'icon'     => $appIcon->url,
                        'children' => [
                            'items'      => [
                                'name' => JText::_('JBZOO_ADMINMENU_ITEMS'),
                                'url'  => $router->admin([
                                    'changeapp'  => $app->id,
                                    'controller' => 'item',
                                    'task'       => ''
                                ])
                            ],
                            'categories' => [
                                'name' => JText::_('JBZOO_ADMINMENU_CATEGORIES'),
                                'url'  => $router->admin(['changeapp'  => $app->id,
                                                          'controller' => 'category',
                                                          'task'       => ''
                                ])
                            ],
                            'frontpage'  => [
                                'name' => JText::_('JBZOO_ADMINMENU_FRONTPAGE'),
                                'url'  => $router->admin(['changeapp'  => $app->id,
                                                          'controller' => 'frontpage',
                                                          'task'       => ''
                                ])
                            ],
                            'comments'   => [
                                'name' => JText::_('JBZOO_ADMINMENU_COMMENTS'),
                                'url'  => $router->admin(['changeapp'  => $app->id,
                                                          'controller' => 'comment',
                                                          'task'       => ''
                                ])
                            ],
                            'sep-1'      => 'divider',
                            'config'     => [
                                'name' => JText::_('JBZOO_ADMINMENU_CONFIG'),
                                'url'  => $router->admin(['changeapp'  => $app->id,
                                                          'controller' => 'configuration',
                                                          'task'       => ''
                                ])
                            ],
                        ]
                    ];
                }
            }

            $menuItems['item-config'] = [
                'name' => JText::_('JBZOO_ADMINMENU_MAINCONFIG'),
                'url'  => $this->app->jbrouter->admin(['task'       => 'types',
                                                       'group'      => 'jbuniversal',
                                                       'controller' => 'manager'
                ]),
            ];

            $types = $this->app->jbtype->getSimpleList();
            if (!empty($types)) {
                $children = [];
                foreach ($types as $alias => $type) {
                    $children['type-' . $alias] = [
                        'name' => $type,
                        'url'  => $router->admin([
                            'controller' => 'manager',
                            'group'      => 'jbuniversal',
                            'task'       => 'editelements',
                            'cid'        => ['0' => $alias]
                        ])
                    ];
                }

                $menuItems['item-config']['children'] = $children;
            }

            $menuItems['sep-1'] = 'divider';

            $menuItems['jbzoo-admin'] = [
                'name'     => JText::_('JBZOO_ADMINMENU_JBZOOPAGE'),
                'url'      => $router->admin(['controller' => 'jbindex', 'task' => 'index']),
                'children' => [
                    'performance'  => [
                        'name' => JText::_('JBZOO_ADMINMENU_TOOLS'),
                        'url'  => $router->admin(['controller' => 'jbtools', 'task' => 'index']),
                    ],
                    'systemreport' => [
                        'name' => JText::_('JBZOO_ADMINMENU_CONFIGS'),
                        'url'  => $router->admin(['controller' => 'jbconfig', 'task' => 'index']),
                    ],
                ],
            ];

            $menuItems['jbzoo-import'] = [
                'name'     => JText::_('JBZOO_ADMINMENU_IMPORT'),
                'url'      => $router->admin(['controller' => 'jbimport', 'task' => 'index']),
                'children' => [
                    'items'      => [
                        'name' => JText::_('JBZOO_ADMINMENU_IMPORT_ITEMS'),
                        'url'  => $router->admin(['controller' => 'jbimport', 'task' => 'items']),
                    ],
                    'categories' => [
                        'name' => JText::_('JBZOO_ADMINMENU_IMPORT_CATEGORIES'),
                        'url'  => $router->admin(['controller' => 'jbimport', 'task' => 'categories']),
                    ],
                    'stdandart'  => [
                        'name' => JText::_('JBZOO_ADMINMENU_IMPORT_STANDART'),
                        'url'  => $router->admin(['controller' => 'jbimport', 'task' => 'standart']),
                    ],
                ],
            ];

            $menuItems['jbzoo-export'] = [
                'name'     => JText::_('JBZOO_ADMINMENU_EXPORT'),
                'url'      => $router->admin(['controller' => 'jbexport', 'task' => 'index']),
                'children' => [
                    'items'      => [
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_ITEMS'),
                        'url'  => $router->admin(['controller' => 'jbexport', 'task' => 'items']),
                    ],
                    'categories' => [
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_CATEGORIES'),
                        'url'  => $router->admin(['controller' => 'jbexport', 'task' => 'categories']),
                    ],
                    'types'      => [
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_TYPES'),
                        'url'  => $router->admin(['controller' => 'jbexport', 'task' => 'types']),
                    ],
                    'yandexyml'  => [
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_YANDEXYML'),
                        'url'  => $router->admin(['controller' => 'jbexport', 'task' => 'yandexyml']),
                    ],
                    'stdandart'  => [
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_STANDART'),
                        'url'  => $router->admin(['controller' => 'jbexport', 'task' => 'standart']),
                    ],
                    'zoobackup'  => [
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_ZOOBACKUP'),
                        'url'  => $router->admin(['controller' => 'jbexport', 'task' => 'zoobackup']),
                    ],
                ],
            ];

            $menuItems['sep-2'] = 'divider';

            $menuItems['jbzoo-info'] = [
                'name'     => JText::_('JBZOO_ADMINMENU_INFO'),
                'url'      => $router->admin(['controller' => 'jbinfo', 'task' => 'index']),
                'children' => [
                    'performance'  => [
                        'name' => JText::_('JBZOO_ADMINMENU_PERFORMANCE'),
                        'url'  => $router->admin(['controller' => 'jbinfo', 'task' => 'performance']),
                    ],
                    'systemreport' => [
                        'name' => JText::_('JBZOO_ADMINMENU_SYSTEMREPORT'),
                        'url'  => $router->admin(['controller' => 'jbinfo', 'task' => 'systemreport']),
                    ],
                    'server'       => array(
                        'name'   => 'Get Last JBZoo',
                        'url'    => 'https://github.com/JBZoo/JBZoo',
                        'target' => '_blank',
                    ),
                ]
            ];
            $menuItems['jbzoo-support'] = [
                'name'   => JText::_('JBZOO_ADMINMENU_SUPPORT'),
                'url'    => 'http://forum.jbzoo.com/',
                'target' => '_blank',
            ];

            // save to cache
            $cache->store($menuItems, 'jbadminitems');
        }

        $this->app->jbassets->addVar('JBAdminItems', [
            'name'  => JText::_('JBZOO_ADMINMENU_CAPTION'),
            'items' => $menuItems,
        ]);
    }

    /**
     * Init config file
     */
    private function _initConfig()
    {
        if ($configFile = $this->app->path->path('jbapp:config/config.php')) {
            require $configFile;
        }
    }

    /**
     * Get group name
     * @return string
     */
    private function _getGroup()
    {
        return $this->_group;
    }

    /**
     * Register new path in system
     * @param string $path
     * @param string $pathName
     * @return mixed
     */
    private function _addPath($path, $pathName)
    {
        if ($fullPath = $this->app->path->path($path)) {
            return $this->app->path->register($fullPath, $pathName);
        }

        return null;
    }

    /**
     * Get domain name
     * @param bool $isAny
     * @return string
     */
    static function getDomain($isAny = false)
    {
        $domain = '';
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $domain = isset($headers['Host']) ? $headers['Host'] : '';
        }

        if ($isAny && !$domain) {
            $domain = $_SERVER['HTTP_HOST'];
        }

        $domain = preg_replace('#^www\.#', '', $domain);
        list($domain) = explode(':', $domain);

        return $domain;
    }

    /**
     * Get hash
     * @param $string
     * @return string
     */
    static public function getHash($string)
    {
        return '';
    }
}
