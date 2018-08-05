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
        $this->_initClasses();
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
        $this->_addPath('jbapp:cart-elements', 'cart-elements');
        $this->_addPath('jbapp:types', 'jbtypes');

        $this->_addPath('jbzoo:helpers', 'helpers');
        $this->_addPath('jbzoo:helpers-std', 'helpers');
        $this->_addPath('helpers:fields', 'fields');

        $this->_addPath('jbzoo:tables', 'tables');
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
        $this->_addPath('modules:mod_jbzoo_item', 'mod_jbzoo_item');
        $this->_addPath('modules:mod_jbzoo_currency', 'mod_jbzoo_currency');

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
    private function _initClasses()
    {
        jimport('joomla.html.parameter.element');

        $this->app->loader->register('AppValidator', 'classes:validator.php');

        // defines
        define('ZOO_TABLE_JBZOO_SKU', '#__zoo_jbzoo_sku');
        define('ZOO_TABLE_JBZOO_FAVORITE', '#__zoo_jbzoo_favorite');
        define('ZOO_TABLE_JBZOO_CONFIG', '#__zoo_jbzoo_config');
        define('ZOO_TABLE_JBZOO_ORDER', '#__zoo_jbzoo_orders');

        $frmwork = JPATH_SITE . '/media/zoo/applications/jbuniversal/framework';
        $clsPath = $frmwork . '/classes';
        $modPath = $frmwork . '/models';
        $filPath = $frmwork . '/render/filter';
        $evtPath = $frmwork . '/events';

        $classList = array(
            'JBDatabaseQuery'                    => $clsPath . '/database/JBDatabaseQuery.php',
            'JBDatabaseQueryElement'             => $clsPath . '/database/JBDatabaseQueryElement.php',
            'JBCartOrder'                        => $clsPath . '/cart/jborder.php',
            'JBCart'                             => $clsPath . '/cart/jbcart.php',
            'JBCartValue'                        => $clsPath . '/cart/jbvalue.php',
            'JBCartVariant'                      => $clsPath . '/cart/jbvariant.php',
            'JBCartVariantList'                  => $clsPath . '/cart/jbvariantlist.php',
            'JBTemplate'                         => $clsPath . '/jbtemplate.php',

        // models
            'JBModel'                            => $modPath . '/jbmodel.php',
            'JBModelConfig'                      => $modPath . '/jbmodel.config.php',
            'JBModelElement'                     => $modPath . '/jbmodel.element.php',
            'JBModelAutoComplete'                => $modPath . '/jbmodel.autocomplete.php',
            'JBModelElementCountry'              => $modPath . '/jbmodel.element.country.php',
            'JBModelElementDate'                 => $modPath . '/jbmodel.element.date.php',
            'JBModelElementItemDate'             => $modPath . '/jbmodel.element.itemdate.php',
            'JBModelElementItemauthor'           => $modPath . '/jbmodel.element.itemauthor.php',
            'JBModelElementItemCategory'         => $modPath . '/jbmodel.element.itemcategory.php',
            'JBModelElementItemCreated'          => $modPath . '/jbmodel.element.itemcreated.php',
            'JBModelElementItemFrontpage'        => $modPath . '/jbmodel.element.itemfrontpage.php',
            'JBModelElementItemModified'         => $modPath . '/jbmodel.element.itemmodified.php',
            'JBModelElementItemName'             => $modPath . '/jbmodel.element.itemname.php',
            'JBModelElementItemPublish_down'     => $modPath . '/jbmodel.element.itempublish_down.php',
            'JBModelElementItemPublish_up'       => $modPath . '/jbmodel.element.itempublish_up.php',
            'JBModelElementItemTag'              => $modPath . '/jbmodel.element.itemtag.php',
            'JBModelElementJBImage'              => $modPath . '/jbmodel.element.jbimage.php',
            'JBModelElementJBSelectCascade'      => $modPath . '/jbmodel.element.jbselectcascade.php',
            'JBModelElementRange'                => $modPath . '/jbmodel.element.range.php',
            'JBModelElementRating'               => $modPath . '/jbmodel.element.rating.php',
            'JBModelElementJBPrice'              => $modPath . '/jbmodel.element.jbprice.php',
            'JBModelElementJBPricePlain'         => $modPath . '/jbmodel.element.jbprice.plain.php',
            'JBModelElementJBPriceCalc'          => $modPath . '/jbmodel.element.jbprice.calc.php',
            'JBModelElementJBComments'           => $modPath . '/jbmodel.element.jbcomments.php',
            'JBModelElementTextarea'             => $modPath . '/jbmodel.element.textarea.php',
            'JBModelFavorite'                    => $modPath . '/jbmodel.favorite.php',
            'JBModelFilter'                      => $modPath . '/jbmodel.filter.php',
            'JBModelItem'                        => $modPath . '/jbmodel.item.php',
            'JBModelApp'                         => $modPath . '/jbmodel.app.php',
            'JBModelCategory'                    => $modPath . '/jbmodel.category.php',
            'JBModelOrder'                       => $modPath . '/jbmodel.order.php',
            'JBModelRelated'                     => $modPath . '/jbmodel.related.php',
            'JBModelSearchindex'                 => $modPath . '/jbmodel.searchindex.php',
            'JBModelValues'                      => $modPath . '/jbmodel.values.php',
            'JBModelSku'                         => $modPath . '/jbmodel.sku.php',

            // filter
            'JBFilterElement'                    => $filPath . '/element.php',
            'JBFilterElementAuthor'              => $filPath . '/element.author.php',
            'JBFilterElementAuthorCheckbox'      => $filPath . '/element.author.checkbox.php',
            'JBFilterElementAuthorRadio'         => $filPath . '/element.author.radio.php',
            'JBFilterElementAuthorSelect'        => $filPath . '/element.author.select.php',
            'JBFilterElementAuthorChosen'        => $filPath . '/element.author.select.chosen.php',
            'JBFilterElementAuthorText'          => $filPath . '/element.author.text.php',
            'JBFilterElementCategory'            => $filPath . '/element.category.php',
            'JBFilterElementCategoryChosen'      => $filPath . '/element.category.chosen.php',
            'JBFilterElementCategoryHidden'      => $filPath . '/element.category.hidden.php',
            'JBFilterElementCheckbox'            => $filPath . '/element.checkbox.php',
            'JBFilterElementCountry'             => $filPath . '/element.country.php',
            'JBFilterElementCountryCheckbox'     => $filPath . '/element.country.checkbox.php',
            'JBFilterElementCountryRadio'        => $filPath . '/element.country.radio.php',
            'JBFilterElementCountrySelect'       => $filPath . '/element.country.select.php',
            'JBFilterElementCountryChosen'       => $filPath . '/element.country.select.chosen.php',
            'JBFilterElementDate'                => $filPath . '/element.date.php',
            'JBFilterElementDateRange'           => $filPath . '/element.date.range.php',
            'JBFilterElementFrontpage'           => $filPath . '/element.frontpage.php',
            'JBFilterElementFrontpageJqueryUI'   => $filPath . '/element.frontpage.jqueryui.php',
            'JBFilterElementHidden'              => $filPath . '/element.hidden.php',
            'JBFilterElementImageexists'         => $filPath . '/element.imageexists.php',
            'JBFilterElementImageexistsJqueryui' => $filPath . '/element.imageexists.jqueryui.php',
            'JBFilterElementJBColor'             => $filPath . '/element.jbcolor.php',
            'JBFilterElementJBPriceCalc'         => $filPath . '/element.jbpricecalc.php',
            'JBFilterElementJBPricePlain'        => $filPath . '/element.jbpriceplain.php',
            'JBFilterElementJbselectcascade'     => $filPath . '/element.jbselectcascade.php',
            'JBFilterElementJqueryui'            => $filPath . '/element.jqueryui.php',
            'JBFilterElementName'                => $filPath . '/element.name.php',
            'JBFilterElementNameCheckbox'        => $filPath . '/element.name.checkbox.php',
            'JBFilterElementNameRadio'           => $filPath . '/element.name.radio.php',
            'JBFilterElementNameSelect'          => $filPath . '/element.name.select.php',
            'JBFilterElementNameChosen'          => $filPath . '/element.name.select.chosen.php',
            'JBFilterElementRadio'               => $filPath . '/element.radio.php',
            'JBFilterElementRating'              => $filPath . '/element.rating.php',
            'JBFilterElementRatingRanges'        => $filPath . '/element.rating.ranges.php',
            'JBFilterElementRatingSlider'        => $filPath . '/element.rating.slider.php',
            'JBFilterElementSelect'              => $filPath . '/element.select.php',
            'JBFilterElementSelectChosen'        => $filPath . '/element.select.chosen.php',
            'JBFilterElementSlider'              => $filPath . '/element.slider.php',
            'JBFilterElementTag'                 => $filPath . '/element.tag.php',
            'JBFilterElementTagCheckbox'         => $filPath . '/element.tag.checkbox.php',
            'JBFilterElementTagRadio'            => $filPath . '/element.tag.radio.php',
            'JBFilterElementTagSelect'           => $filPath . '/element.tag.select.php',
            'JBFilterElementTagSelectChosen'     => $filPath . '/element.tag.select.chosen.php',
            'JBFilterElementText'                => $filPath . '/element.text.php',
            'JBFilterElementTextRange'           => $filPath . '/element.text.range.php',

            // events
            'JBZooSystemPlugin'                  => $evtPath . '/jsystem.php',
            'JBEvent'                            => $evtPath . '/jbevent.php',
            'JBEventApplication'                 => $evtPath . '/jbevent.application.php',
            'JBEventBasket'                      => $evtPath . '/jbevent.basket.php',
            'JBEventCategory'                    => $evtPath . '/jbevent.category.php',
            'JBEventComment'                     => $evtPath . '/jbevent.comment.php',
            'JBEventElement'                     => $evtPath . '/jbevent.element.php',
            'JBEventItem'                        => $evtPath . '/jbevent.item.php',
            'JBEventJBZoo'                       => $evtPath . '/jbevent.jbzoo.php',
            'JBEventLayout'                      => $evtPath . '/jbevent.layout.php',
            'JBEventSubmission'                  => $evtPath . '/jbevent.submission.php',
            'JBEventTag'                         => $evtPath . '/jbevent.tag.php',
            'JBEventType'                        => $evtPath . '/jbevent.type.php',
        );

        foreach ($classList as $className => $path) {
            JLoader::register($className, $path);
        }
    }

    /**
     * Get domain name
     * @param bool $isAny
     * @return string
     */
    static function getDomain($isAny = false)
    {
        if (defined('STDIN') || php_sapi_name() == "cli") {
            return 'console';
        }

        $domain = '';
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $domain  = isset($headers['Host']) ? $headers['Host'] : '';
        }

        if ($isAny && !$domain) {
            $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
        }

        $domain = preg_replace('#^www\.#', '', $domain);
        list($domain) = explode(':', $domain);

        return $domain;
    }

    /**
     * Load lang files
     */
    private function _initLanguages()
    {
        $lang = JFactory::getLanguage();
        $lang->load('com_jbzoo', $this->app->path->path('jbapp:'), null, true);
    }

    /**
     * Register and connect events
     */
    private function _initEvents()
    {
        $event      = $this->app->event;
        $dispatcher = $event->dispatcher;

        $event->register('JBEventApplication');
        $dispatcher->connect('application:init', array('JBEventApplication', 'init'));
        $dispatcher->connect('application:saved', array('JBEventApplication', 'saved'));
        $dispatcher->connect('application:deleted', array('JBEventApplication', 'deleted'));
        $dispatcher->connect('application:addmenuitems', array('JBEventApplication', 'addmenuitems'));
        $dispatcher->connect('application:installed', array('JBEventApplication', 'installed'));
        $dispatcher->connect('application:configparams', array('JBEventApplication', 'configparams'));
        $dispatcher->connect('application:sefbuildroute', array('JBEventApplication', 'sefbuildroute'));
        $dispatcher->connect('application:sefparseroute', array('JBEventApplication', 'sefparseroute'));
        $dispatcher->connect('application:sh404sef', array('JBEventApplication', 'sh404sef'));

        $event->register('JBEventCategory');
        $dispatcher->connect('category:init', array('JBEventCategory', 'init'));
        $dispatcher->connect('category:saved', array('JBEventCategory', 'saved'));
        $dispatcher->connect('category:deleted', array('JBEventCategory', 'deleted'));
        $dispatcher->connect('category:stateChanged', array('JBEventCategory', 'stateChanged'));

        $event->register('JBEventItem');
        $dispatcher->connect('item:init', array('JBEventItem', 'init'));
        $dispatcher->connect('item:saved', array('JBEventItem', 'saved'));
        $dispatcher->connect('item:deleted', array('JBEventItem', 'deleted'));
        $dispatcher->connect('item:stateChanged', array('JBEventItem', 'stateChanged'));
        $dispatcher->connect('item:beforedisplay', array('JBEventItem', 'beforeDisplay'));
        $dispatcher->connect('item:afterdisplay', array('JBEventItem', 'afterDisplay'));
        $dispatcher->connect('item:orderquery', array('JBEventItem', 'orderQuery'));
        $dispatcher->connect('item:beforeSaveCategoryRelations', array('JBEventItem', 'beforeSaveCategoryRelations'));
        $dispatcher->connect('item:beforeRenderLayout', array('JBEventItem', 'beforeRenderLayout'));
        $dispatcher->connect('item:afterRenderLayout', array('JBEventItem', 'afterRenderLayout'));

        $event->register('JBEventComment');
        $dispatcher->connect('comment:init', array('JBEventComment', 'init'));
        $dispatcher->connect('comment:saved', array('JBEventComment', 'saved'));
        $dispatcher->connect('comment:deleted', array('JBEventComment', 'deleted'));
        $dispatcher->connect('comment:stateChanged', array('JBEventComment', 'stateChanged'));

        $event->register('JBEventSubmission');
        $dispatcher->connect('submission:init', array('JBEventSubmission', 'init'));
        $dispatcher->connect('submission:saved', array('JBEventSubmission', 'saved'));
        $dispatcher->connect('submission:deleted', array('JBEventSubmission', 'deleted'));
        $dispatcher->connect('submission:beforesave', array('JBEventSubmission', 'beforeSave'));

        $event->register('JBEventElement');
        $dispatcher->connect('element:download', array('JBEventElement', 'download'));
        $dispatcher->connect('element:configform', array('JBEventElement', 'configForm'));
        $dispatcher->connect('element:configparams', array('JBEventElement', 'configParams'));
        $dispatcher->connect('element:configxml', array('JBEventElement', 'configXML'));
        $dispatcher->connect('element:afterdisplay', array('JBEventElement', 'afterDisplay'));
        $dispatcher->connect('element:beforedisplay', array('JBEventElement', 'beforeDisplay'));
        $dispatcher->connect('element:aftersubmissiondisplay', array('JBEventElement', 'afterSubmissionDisplay'));
        $dispatcher->connect('element:beforesubmissiondisplay', array('JBEventElement', 'beforeSubmissionDisplay'));
        $dispatcher->connect('element:beforeedit', array('JBEventElement', 'beforeEdit'));
        $dispatcher->connect('element:afteredit', array('JBEventElement', 'afterEdit'));

        $event->register('JBEventLayout');
        $dispatcher->connect('layout:init', array('JBEventLayout', 'init'));

        $event->register('JBEventTag');
        $dispatcher->connect('tag:saved', array('JBEventTag', 'saved'));
        $dispatcher->connect('tag:deleted', array('JBEventTag', 'deleted'));

        $event->register('JBEventType');
        $dispatcher->connect('type:beforesave', array('JBEventType', 'beforesave'));
        $dispatcher->connect('type:aftersave', array('JBEventType', 'aftersave'));
        $dispatcher->connect('type:copied', array('JBEventType', 'copied'));
        $dispatcher->connect('type:deleted', array('JBEventType', 'deleted'));
        $dispatcher->connect('type:editdisplay', array('JBEventType', 'editDisplay'));
        $dispatcher->connect('type:coreconfig', array('JBEventType', 'coreconfig'));
        $dispatcher->connect('type:assignelements', array('JBEventType', 'assignelements'));

        $event->register('JBEventJBZoo');
        $dispatcher->connect('jbzoo:beforeInit', array('JBEventJBZoo', 'beforeInit'));
        $dispatcher->notify($event->create($this, 'jbzoo:beforeInit'));

        $event->register('JBEventBasket');
        $dispatcher->connect('basket:beforeSave', array('JBEventBasket', 'beforeSave'));
        $dispatcher->connect('basket:saved', array('JBEventBasket', 'saved'));
        $dispatcher->connect('basket:afterSave', array('JBEventBasket', 'afterSave'));
        $dispatcher->connect('basket:updated', array('JBEventBasket', 'updated'));

        $dispatcher->connect('basket:addItem', array('JBEventBasket', 'addItem'));
        $dispatcher->connect('basket:updateItem', array('JBEventBasket', 'updateItem'));
        $dispatcher->connect('basket:changeQuantity', array('JBEventBasket', 'changeQuantity'));
        $dispatcher->connect('basket:removeItem', array('JBEventBasket', 'removeItem'));
        $dispatcher->connect('basket:removeItems', array('JBEventBasket', 'removeItems'));
        $dispatcher->connect('basket:recount', array('JBEventBasket', 'recount'));

        $dispatcher->connect('basket:orderStatus', array('JBEventBasket', 'orderStatus'));
        $dispatcher->connect('basket:paymentStatus', array('JBEventBasket', 'paymentStatus'));
        $dispatcher->connect('basket:shippingStatus', array('JBEventBasket', 'shippingStatus'));

        $dispatcher->connect('basket:paymentSuccess', array('JBEventBasket', 'paymentSuccess'));
        $dispatcher->connect('basket:paymentFail', array('JBEventBasket', 'paymentFail'));
        $dispatcher->connect('basket:paymentCallback', array('JBEventBasket', 'paymentCallback'));
    }

    /**
     * Init assets for admin path
     */
    private function _initAssets()
    {
        if (!$this->app->jbenv->isSite() && !$this->app->jbrequest->isAjax()) {
            $this->app->jbassets->admin();
            $this->_initLanguages();
            $this->_initAdminMenu();
        }
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

    /**
     * Init Admin menu
     */
    private function _initAdminMenu()
    {
        $isNotJ35 = version_compare(JVERSION, '3.6', '<=');

        if ($isNotJ35) {
            $this->app->jbassets->addVar('JBAdminItems', array(
                'name'  => JText::_('JBZOO_ADMINMENU_CAPTION'),
                'items' => $this->getAdminMenu(),
            ));
        }
    }

    /**
     * Init Admin menu
     */
    public function getAdminMenu()
    {
        $config = JBModelConfig::model()->getGroup('config.custom', $this->app->jbconfig->getList());
        if (!$config->get('adminmenu_show', 1)) {
            return false;
        }

        $curApp = $this->app->system->application->getUserState('com_zooapplication', 0);

        $appList = JBModelApp::model()->getSimpleList();

        $findJBZooApp = false;
        $dispatched   = false;
        foreach ($appList as $app) {
            if ($app->application_group == JBZOO_APP_GROUP) {
                $findJBZooApp = true;
                if ($curApp == $app->id) {
                    $dispatched = true;
                }
            }
        }

        if (!$findJBZooApp) {
            return false;
        }

        $router = $this->app->jbrouter;

        $menuItems = array();

        if (!empty($appList)) {
            foreach ($appList as $app) {

                $menuItems['app-' . $app->alias] = array(
                    'name'     => $app->name,
                    'url'      => $router->admin(array('changeapp' => $app->id, 'controller' => 'item')),
                    'children' => array(
                        'add-item'   => array(
                            'name' => JText::_('JBZOO_ADMINMENU_ADD_ITEM'),
                            'url'  => $router->admin(array('changeapp' => $app->id, 'controller' => 'item', 'task' => 'add')),
                        ),
                        'sep-1'      => 'divider',
                        'items'      => array(
                            'name' => JText::_('JBZOO_ADMINMENU_ITEMS'),
                            'url'  => $router->admin(array('changeapp' => $app->id, 'controller' => 'item', 'task' => '')),
                        ),
                        'categories' => array(
                            'name' => JText::_('JBZOO_ADMINMENU_CATEGORIES'),
                            'url'  => $router->admin(array('changeapp' => $app->id, 'controller' => 'category', 'task' => '')),
                        ),
                        'frontpage'  => array(
                            'name' => JText::_('JBZOO_ADMINMENU_FRONTPAGE'),
                            'url'  => $router->admin(array('changeapp' => $app->id, 'controller' => 'frontpage', 'task' => '')),
                        ),
                        'comments'   => array(
                            'name' => JText::_('JBZOO_ADMINMENU_COMMENTS'),
                            'url'  => $router->admin(array('changeapp' => $app->id, 'controller' => 'comment', 'task' => '')),
                        ),
                        'sep-1'      => 'divider',
                        'config'     => array(
                            'name' => JText::_('JBZOO_ADMINMENU_CONFIG'),
                            'url'  => $router->admin(array('changeapp' => $app->id, 'controller' => 'configuration', 'task' => '')),
                        ),
                    ),
                );
            }
        }

        $menuItems['sep-1'] = 'divider';

        if ($dispatched) {
            if ((int)JBModelConfig::model()->get('enable', 1, 'cart.config')) {
            $menuItems['orders'] = array(
                'name'     => JText::_('JBZOO_ADMINMENU_ORDERS'),
                'url'      => $this->app->jbrouter->admin(array('task' => 'index', 'controller' => 'jborder')),
                'children' => array(
                    'cart-configs' => array(
                        'name' => JText::_('JBZOO_ADMINMENU_CART_CONFIG'),
                        'url'  => $this->app->jbrouter->admin(array('task' => 'index', 'controller' => 'jbcart')),
                        ),
                    ),
                );

                $menuItems['sep-2'] = 'divider';
            }
        }

        $menuItems['item-config'] = array(
            'name' => JText::_('JBZOO_ADMINMENU_MAINCONFIG'),
            'url'  => $this->app->jbrouter->admin(array('task' => 'types', 'group' => 'jbuniversal', 'controller' => 'manager')),
        );

        $types = $this->app->jbtype->getSimpleList();
        if (!empty($types)) {
            $children = array();
            foreach ($types as $alias => $type) {
                $children['type-' . $alias] = array(
                    'name' => $type,
                    'url'  => $router->admin(array('controller' => 'manager', 'group' => 'jbuniversal', 'task' => 'editelements', 'cid' => array('0' => $alias))),
                );
            }

            $menuItems['item-config']['children'] = $children;
        }

        if ($dispatched) {
            $menuItems['sep-3'] = 'divider';

            $menuItems['jbzoo-admin'] = array(
                'name'     => JText::_('JBZOO_ADMINMENU_JBZOOPAGE'),
                'url'      => $router->admin(array('controller' => 'jbindex', 'task' => 'index')),
                'children' => array(
                    'performance'  => array(
                        'name' => JText::_('JBZOO_ADMINMENU_TOOLS'),
                        'url'  => $router->admin(array('controller' => 'jbtools', 'task' => 'index')),
                    ),
                    'systemreport' => array(
                        'name' => JText::_('JBZOO_ADMINMENU_CONFIGS'),
                        'url'  => $router->admin(array('controller' => 'jbconfig', 'task' => 'index')),
                    ),
                ),
            );

            $menuItems['jbzoo-import'] = array(
                'name'     => JText::_('JBZOO_ADMINMENU_IMPORT'),
                'url'      => $router->admin(array('controller' => 'jbimport', 'task' => 'index')),
                'children' => array(
                    'items'      => array(
                        'name' => JText::_('JBZOO_ADMINMENU_IMPORT_ITEMS'),
                        'url'  => $router->admin(array('controller' => 'jbimport', 'task' => 'items')),
                    ),
                    'categories' => array(
                        'name' => JText::_('JBZOO_ADMINMENU_IMPORT_CATEGORIES'),
                        'url'  => $router->admin(array('controller' => 'jbimport', 'task' => 'categories')),
                    ),
                    'stdandart'  => array(
                        'name' => JText::_('JBZOO_ADMINMENU_IMPORT_STANDARD'),
                        'url'  => $router->admin(array('controller' => 'jbimport', 'task' => 'standart')),
                    ),
                ),
            );

            $menuItems['jbzoo-export'] = array(
                'name'     => JText::_('JBZOO_ADMINMENU_EXPORT'),
                'url'      => $router->admin(array('controller' => 'jbexport', 'task' => 'index')),
                'children' => array(
                    'items'      => array(
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_ITEMS'),
                        'url'  => $router->admin(array('controller' => 'jbexport', 'task' => 'items')),
                    ),
                    'categories' => array(
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_CATEGORIES'),
                        'url'  => $router->admin(array('controller' => 'jbexport', 'task' => 'categories')),
                    ),
                    'types'      => array(
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_TYPES'),
                        'url'  => $router->admin(array('controller' => 'jbexport', 'task' => 'types')),
                    ),
                    'yandexyml'  => array(
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_YANDEXYML'),
                        'url'  => $router->admin(array('controller' => 'jbexport', 'task' => 'yandexyml')),
                    ),
                    'stdandart'  => array(
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_STANDARD'),
                        'url'  => $router->admin(array('controller' => 'jbexport', 'task' => 'standart')),
                    ),
                    'zoobackup'  => array(
                        'name' => JText::_('JBZOO_ADMINMENU_EXPORT_ZOOBACKUP'),
                        'url'  => $router->admin(array('controller' => 'jbexport', 'task' => 'zoobackup')),
                    ),
                ),
            );

            $menuItems['sep-4'] = 'divider';

            $menuItems['jbzoo-info'] = array(
                'name'     => JText::_('JBZOO_ADMINMENU_INFO'),
                'url'      => $router->admin(array('controller' => 'jbinfo', 'task' => 'index')),
                'children' => array(
                    'performance'  => array(
                        'name' => JText::_('JBZOO_ADMINMENU_PERFORMANCE'),
                        'url'  => $router->admin(array('controller' => 'jbinfo', 'task' => 'performance')),
                    ),
                    'systemreport' => array(
                        'name' => JText::_('JBZOO_ADMINMENU_SYSTEMREPORT'),
                        'url'  => $router->admin(array('controller' => 'jbinfo', 'task' => 'systemreport')),
                    ),
                    'server'       => array(
                        'name'   => 'Get Last JBZoo',
                        'url'    => 'https://github.com/JBZoo/JBZoo',
                        'target' => '_blank',
                    ),
                ),
            );
        }

        $menuItems['jbzoo-support'] = array(
            'name'   => JText::_('JBZOO_ADMINMENU_SUPPORT'),
            'url'    => 'http://forum.jbzoo.com/',
            'target' => '_blank',
        );
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
}
