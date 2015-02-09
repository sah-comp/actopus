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
 * Manages CURD on article beans.
 *
 * @todo Make sure model slice->modes() returns all available cms module not just those defined in slice
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Article extends Controller_Scaffold
{
    /**
     * Holds the bean type to apply scaffolding to.
     *
     * @var string
     */
    public $type = 'article';

	/**
	 * Dispenses a blank Bean as either own or shared and outputs the template.
	 *
	 * @param int $region_id
	 * @param string $language_iso code
	 * @return void
	 */
	public function attachownslice($region_id, $language_iso)
	{
        session_start();
        $this->cache()->deactivate();
        
		$n = md5(microtime(true));
        $record = R::dispense('slice');
        $this->view = $this->makeView(sprintf('model/%s/form/%s/%s', $this->type, 'own', 'slice'));
        $this->view->n = $n;
        $this->view->slice = $record;
        $this->view->region_id = $region_id;
        $this->view->iso = $language_iso;
        $this->trigger('edit', 'before');
        echo $this->view->render();
		return true;
	}

    /**
     * This will run before scaffold edit performs.
     *
     * @return void
     */
    public function before_edit()
    {
        $this->pushEnabledLanguagesToView();
    }
    
    /**
     * This will run before scaffold add performs.
     *
     * @return void
     */
    public function before_add()
    {
        $this->pushEnabledLanguagesToView();
    }
}
