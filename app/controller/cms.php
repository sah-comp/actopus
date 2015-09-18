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
 * CMS(Content Management System).
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Cms extends Controller_Page
{
    /**
     * Holds the master bean type.
     *
     * @var string
     */
    public $type = 'page';
    
    /**
     * Container for all beantypes.
     *
     * @var array
     */
    public $beantypes = array(
        'slice',
        'article',
        'page',
        'site'
    );

    /**
     * Renders the index page with all sites and the option to add a new site.
     *
     * @uses $_SESSION['cms'] to store current page, article and slice id
     *
     * All pages that do not belong to another page are sites.
     */
    public function index()
    {
        $this->cache()->deactivate();
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
		if ( ! $this->permission()->allowed($this->user(), 'cms', 'index')) {
			return $this->error('403');
		}
        
		$this->view = $this->makeView('cms/index');
        $this->view->title = __('cms_head_title');
        $this->view->user = $this->user();
        $this->view->record = R::dispense('page');
        $this->view->records = $this->view->record->sites();
        
        $_SESSION['cms'] = array(
            'root' => null,
            'page' => null,
            'article' => null,
            'template' => null,
            'slice' => null
        );
        
        // Last, but not least we create a menu
        $this->view->nav = R::findOne('domain', ' blessed = ? LIMIT 1', array(1))->hierMenu($this->view->url());
        $this->view->nav->add(__('scaffold_add'), $this->view->url(('/cms/add/site')), 'scaffold-add');
        //$this->view->navfunc = $this->view->record->makeMenu('sites', $this->view, $this->view->nav);
        
        $this->view->urhere = with(new Cinnebar_Menu())->add(__('cms_head_title'), $this->view->url('/cms/index/'));
        
        echo $this->view->render();
    }

    /**
     * Sets the root id and renders the cms page.
     *
     * @param int $id of the root page to load
     */
    public function root($id)
    {
		return $this->page($id, null, $id);
	}


    
    /**
     * Renders a complete site with the pages, articles and slices plus giving options to add items
     * or only partly stuff like articles and/or slices.
     *
     * This page will require two jQuery plugins or extensions:
     * - @link {http://boagworld.com/dev/creating-a-draggable-sitemap-with-jquery/}
     * - @link {http://plugins.jquery.com/project/serializeTree/}
     *
     * @param int $id of the page to load
     * @param int (optional) $article_id of the pre-selected article
     * @param int (optional) $page_id of the root page
     */
    public function page($id, $article_id = null, $root_id = null)
    {
        $this->cache()->deactivate();
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
		if ( ! $this->permission()->allowed($this->user(), 'page', 'edit')) {
			return $this->error('403');
		}
		
		if ($this->request()->isAjax()) {
		    // if this is an ajax call we only need articles and slices, not the whole page
            $this->view = $this->makeView('cms/article-slice-container');
		} else {
    		$this->view = $this->makeView('cms/page');
		}
		
		$this->view->user = $this->user();
        $this->view->record = R::load('page', $id);
        $this->view->backend = false;

        $this->view->title = __('cms_head_title');
        $articles = $this->view->articles = $this->view->record->articles();
        if ( ! $articles) {
            Cinnebar_Logger::instance()->log('There are no articles', 'warn');
            echo __('cms_page_or_parent_has_no_articles');
            return false;
        }
        // we have an article:
        // get the first one of them
        if ($article_id === null) {
            $this->view->carticle = reset($articles);
        } else {
            $this->view->carticle = R::load('article', $article_id);
        }
        // which template?
        $this->view->ctemplate = $this->view->carticle->i18n()->template();
        // what regions are in that template?
        $this->view->regions = $this->view->ctemplate->regions();
        // add possible modules to the view
        $this->view->modules = R::dispense('module')->enabled();
        // add possible tempaltes to the view
        $this->pushEnabledTemplatesToView();
        $this->pushImagesToView();
        
        $_SESSION['cms']['page'] = $this->view->record->getId();
        $_SESSION['cms']['article'] = $this->view->carticle->getId();
        $_SESSION['cms']['template'] = $this->view->ctemplate->getId();
        $_SESSION['cms']['slice'] = null;
        if ($root_id !== null) $_SESSION['cms']['root'] = $root_id;
        
        if ($this->request()->isAjax()) {
            echo $this->view->render();
            return true;
        }
        
        $this->view->addJs('libs/jquery/hs_draggable');
        $this->view->addJs('libs/jquery/jquery.serializetree');
        $this->view->sitemap = $this->view->record->hierMenu($this->view->url('/cms/page/'), $this->user()->language(), 'sequence ASC', true);
        $this->view->sitemap->setTemplate('item-open', '<li %s %s><dl><a href="#" class="ir sm2_expander">toggle</a><dt><a href="%s">%s</a></dt></dl>');
        
        // Last, but not least we create a menu
        $this->view->nav = R::findOne('domain', ' blessed = ? LIMIT 1', array(1))->hierMenu($this->view->url());
        
        //$addMenu = new Cinnebar_Menu();
        //$addMenu->add(__('cms_add_page'), $this->view->url('/cms/add/page'), 'scaffold-cms-add-page');
        //$addMenu->add(__('cms_add_article'), $this->view->url('/cms/add/article'), 'scaffold-cms-add-article');
        $this->view->nav->add(__('scaffold_add'), $this->view->url(('/cms/add/article')), 'scaffold-cms-add-article');
        //$this->view->navfunc = $this->view->record->makeMenu('sites', $this->view, $this->view->nav);
        
        $this->view->urhere = with(new Cinnebar_Menu())->add(__('cms_head_title'), $this->view->url('/cms/index/'));
        
        echo $this->view->render();
    }
    
    /**
     * Updates the articlei18n and renders the complete article-slice-container or
     * deletes the article.
     *
     * This is called by an AJAX POST request from cms/meta template.
     */
    public function meta()
    {
        $page_id = $this->input()->post('page_id');
        $id = $this->input()->post('id');
        $article = R::load('article', $id);
        if ($this->input()->post('delete')) {
            try {
                R::trash($article);
            } catch (Exception $e) {
                Cinnebar_Logger::instance()->log($e, 'exceptions');
            }
            return $this->page($page_id);
        }
        try {
            $articlei18n = R::load('articlei18n', $this->input()->post('articlei18n_id'));
            $article->invisible = $this->input()->post('invisible');
            $article->aka_id = null;
            if ($this->input()->post('aka_id')) {
                $aka = R::load('article', $this->input()->post('aka_id'));
                $article->aka = $aka;
            }
            $articlei18n->import($this->input()->post('dialog'));
            R::store($articlei18n);
            R::store($article);
        } catch (Exception $e) {
            Cinnebar_Logger::instance()->log($e, 'exceptions');
        }
        return $this->page($page_id, $id);
    }
    
    /**
     * Renders the slices area according to the given article.
     *
     * This should only be called to update the slice-container.
     * It will be called by an js ajax request when
     * you click on an article in the cms page view.
     *
     * @param int $id of the article to load
     */
    public function article($id)
    {
        $this->cache()->deactivate();
        session_start();
        $this->view = $this->makeView('cms/slices');

    	$this->view->user = $this->user();
        $this->view->carticle = R::load('article', $id);
        $this->view->record = $this->view->carticle->page;
        $this->view->backend = false;
        $this->view->reinitJs = true;

        $this->view->ctemplate = $this->view->carticle->i18n()->template();
        // what areas are in that template?
        $this->view->regions = $this->view->ctemplate->regions();
        $this->view->modules = R::dispense('module')->enabled();
        $this->pushEnabledTemplatesToView();
        $this->pushImagesToView();
        
        $_SESSION['cms']['article'] = $this->view->carticle->getId();
        $_SESSION['cms']['template'] = $this->view->ctemplate->getId();
        
        // get the slices of that article and    
        echo $this->view->render();
    }
    
    /**
     * Renders the slice according to the current mode.
     *
     * This should only be called to update a slice item container.
     * It will be called by an js ajax request when
     * you click on an slice in the cms page view.
     *
     * @param int $id of the slice to load
     */
    public function slice($id)
    {
        $this->cache()->deactivate();
        session_start();
        $this->view = $this->makeView('cms/slice-and-tools');

        $this->view->backend = true;
        $this->view->reinitJs = true;
    	$this->view->user = $this->user();
        $this->view->slice = R::load('slice', $id);
        $this->view->reinitJs = true;
        $this->view->n = $this->view->slice->getId();
        //$this->view->modules = R::dispense('module')->enabled();
        
        $_SESSION['cms']['slice'] = $this->view->slice->getId();
        
        if ($this->input()->post()) {
            // should i delete this?
            $this->view->slice->import($this->input()->post('dialog'));
            try {
                R::store($this->view->slice);
                $this->view->backend = false;
                $this->view->reinitJs = false;
            } catch (Exception $e) {
                // houston, we have a problem
                Cinnebar_Logger::instance()->log($e, 'exceptions');
            }
        }
        
        // render that slice    
        echo $this->view->render();
    }
    
    /**
     * Deletes a certain bean type and id.
     *
     * This is called by an AJAX request on the cms/page.
     *
     * @param string $type of the bean to delete from our cms system
     * @param int $id of the bean to delete
     */
    public function delete($type, $id)
    {
        $this->cache()->deactivate();
        session_start();
        if ( ! in_array(strtolower($type), $this->beantypes)) return null;
        $bean = R::load($type, $id);
        try {
            R::trash($bean);
        } catch (Exception $e) {
            // uups, we have a problem
            Cinnebar_Logger::instance()->log($e, 'exceptions');
        }
        return null;
    }
    
    /**
     * Adds a certain bean type.
     *
     * This is called by an AJAX request when a article or slice is added, but
     * it is a normal http request when a site is added.
     *
     * @param string $type of the bean to add to our cms system
     */
    public function add($type)
    {
        if ( ! in_array(strtolower($type), $this->beantypes)) return null;
        $callback = 'add'.ucfirst(strtolower($type));
        return $this->$callback();
    }
    
    /**
     * Add a slice.
     */
    protected function addSlice()
    {
        $this->cache()->deactivate();
        session_start();
        if ( ! $this->input()->post()) return null;
        $this->view = $this->makeView('cms/article/own/slice');
        
        // how many slice are there for the article, region and language?
        // we need to know so we can set the sequence to sort correctly
        
        $this->view->slice = R::dispense('slice');
        $this->view->slice->import($this->input()->post('dialog'));
        $this->view->slice->sequence = count($this->view->slice->article->sliceByRegionAndLanguage($this->view->slice->region, $this->view->slice->iso, false)) + 1; // next highest number
        try {
            R::store($this->view->slice);
        } catch (Exception $e) {    
            // houston, we have a problem
            Cinnebar_Logger::instance()->log($e, 'exceptions');
        }

        $this->view->backend = true;
        $this->view->reinitJs = true;
    	$this->view->user = $this->user();
        $this->view->reinitJs = true;
        $this->view->n = $this->view->slice->getId();
        $this->view->effect = true;
        $this->view->active = true;
        
        $_SESSION['cms']['slice'] = $this->view->slice->getId();
        
        // get the slices of that article and    
        echo $this->view->render();
    }
    
    /**
     * Add a article.
     *
     * This is called from a AJAX request and it will create a new article using the current
     * article as a starting point.
     */
    protected function addArticle()
    {
        $this->cache()->deactivate();
        session_start();
        $this->view = $this->makeView('cms/page/own/article');
        
        $current_article = R::load('article', $_SESSION['cms']['article']);
        
        $article = R::dispense('article');
        $article->page = $current_article->page;
        $article->sequence = count($article->page->own('article')) + 1;
        
        $enabled_languages = R::dispense('language')->enabled();
        foreach ($enabled_languages as $id => $language) {
            $current_article_i18n = $current_article->i18n($language->iso);
            $article_i18n = R::dispense('articlei18n');
            $article_i18n->template = $current_article_i18n->template();
            $article_i18n->name = __('article_new_article', null, $language->iso);
            $article_i18n->iso = $language->iso;
            $article->ownArticlei18n[] = $article_i18n;
        }
        try {
            R::store($article);
        } catch (Exception $e) {    
            // houston, we have a problem
            Cinnebar_Logger::instance()->log($e, 'exceptions');
        }
        $this->view->carticle = $current_article;
        $this->view->article = $article;
        $this->view->backend = true;
    	$this->view->user = $this->user();
        $this->view->reinitJs = true;
        $this->view->n = $this->view->article->getId();
        $this->view->effect = true;
        $this->view->active = true;
        
        //$_SESSION['cms']['slice'] = $this->view->slice->getId();
        
        // get the slices of that article and    
        echo $this->view->render();
    }
    
    /**
     * Add a site.
     */
    protected function addSite()
    {
        $this->cache()->deactivate();
        session_start();
        if ( ! $this->auth()) $this->redirect(sprintf('/login/?goto=%s', urlencode('/'.$this->router()->internalUrl())));
        
        if ( ! $this->permission()->allowed($this->user(), 'page', 'add')) {
			return $this->error('403');
		}
        $this->view = $this->makeView('cms/site');
        $this->view->title = __('cms_head_add_site');
        $this->view->user = $this->user();
        $this->view->record = R::dispense('page');
        $this->pushEnabledTemplatesToView();
        $this->pushEnabledLanguagesToView();
        
        $this->view->nav = R::findOne('domain', ' blessed = ? LIMIT 1', array(1))->hierMenu($this->view->url());
        $this->view->nav->add(__('scaffold_add'), $this->view->url(('/cms/add/site')), 'scaffold-add');
        $this->view->urhere = with(new Cinnebar_Menu())->add(__('cms_head_title'), $this->view->url('/cms/index')); 
        echo $this->view->render();
    }

    
    /**
     * Reorders the pages within GET parameter pages.
     *
     * This method is called via AJAX from the jquery plugin serializeTree. To learn more about
     * that piece of javascript go to {@link http://plugins.jquery.com/project/serializeTree/}.
     * That javascript will send a hierarchical array in the GET parameter $pages.
     *
     * @uses reorder_workhorse()
     *
     * @return void
     */
    public function reorder()
    {
        $this->cache()->deactivate();
        session_start();
		if ( ! $pages = $this->input()->get('tree')) return null;
		try {
            $this->reorder_workhorse($pages, 'page-'.$_SESSION['cms']['root']);
        } catch (Exception $e) {
            Cinnebar_Logger::instance()->log($e, 'exceptions');
        }
    }

    /**
     * Does the real reordering.
     *
     * @param array $tree is a hierachical array
     * @param string $parent is the parent of the current array handled
     * @param array $stack is the container for all beans in the tree
     */
    protected function reorder_workhorse(array $tree, $parent = '')
    {
        static $i;
        foreach ($tree as $key => $value) {
            $i++;
            if ( ! is_array($value)) {
                $this->storePattern($value, $parent, $i);
            } else {
                $this->storePattern($key, $parent, $i);
                $this->reorder_workhorse($value, $key);
            }
        }
        return true;
    }
    
    /**
     * Store a pattern with or without parent.
     *
     * @param string $pattern
     * @param string
     * @param int $sequence
     */
    protected function storePattern($pattern, $parent = '', $sequence)
    {
        if ( ! $bean = $this->beanByPattern($pattern)) return false;
        $bean->sequence = $sequence;
        if ($parent && $bean_parent = $this->beanByPattern($parent)) {
            $bean->{$bean_parent->getMeta('type').'_id'} = $bean_parent->getId();
        }
        R::store($bean);
        return true;
    }
    
    /**
     * Returns a bean from a string like '[page]-[id]'.
     *
     * @param string $pattern
     * @param string (optional) $divider the character that devides bean-type from bean-id
     * @return RedBean_OODBBean
     */
    protected function beanByPattern($pattern, $divider = '-')
    {
        if ($pattern == 'undefined') return false;
        $typeAndId = explode($divider, $pattern);
        return R::load($typeAndId[0], $typeAndId[1]);
    }
    
    /**
     * Pushes enabled templates in alphabetic order to the view.
     */
    public function pushEnabledTemplatesToView()
    {
        $this->view->templates = R::find('template', ' enabled = 1 ORDER BY name');
    }
    
    /**
     * Pushes image medias to the view.
     */
    public function pushImagesToView()
    {
        $extensions = array('jpg', 'gif', 'png');
        $this->view->medias = R::find('media', ' extension IN ('.R::genSlots($extensions).')', $extensions);
    }
}
