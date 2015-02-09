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
 * Dispenses a new bean, own(ed) or shared of a master bean and displays its template.
 *
 * @package Cinnebar
 * @subpackage Plugin
 * @version $Id$
 */
class Plugin_Attach extends Cinnebar_Plugin
{
	/**
	 * Dispenses a blank Bean as either own or shared and outputs the template.
	 *
	 * @uses controller() to fetch the calling controller
	 * @param string $prefix
	 * @param string $type
	 * @param mixed (optional) $id
	 * @return void
	 */
	public function execute($prefix, $type, $id = 0)
	{
        session_start();
        $this->controller()->cache()->deactivate();
        
		$n = md5(microtime(true));
        $record = R::dispense($type);
        $this->controller()->view = $this->controller()->makeView(sprintf('model/%s/form/%s/%s', $this->controller()->type, $prefix, $type));
        $this->controller()->view->n = $n;
        $this->controller()->view->$type = $record;
        $this->controller()->trigger('edit', 'before');
        echo $this->controller()->view->render();
		return true;
	}
}
