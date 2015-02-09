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
 * Install controller.
 *
 * @package Cinnebar
 * @subpackage Controller
 * @version $Id$
 */
class Controller_Install extends Cinnebar_Controller
{
    /**
     * Displays the install page.
     */
    public function index()
    {
        $this->cache()->deactivate();
        session_start();
        $view = $this->makeView('install');
        $view->record = R::dispense('install');
        if ($this->input()->post()) {
            $view->record->import($this->input()->post('dialog'));
            $this->makeDatabase();
        }
        $view->nav = with(new Cinnebar_Menu)->add('install', $view->url('/install'));
        echo $view->render();
    }
    
    /**
     * Generates the database schema using RedBeanPHP.
     *
     * @throws Exception when something fails
     */
    private function makeDatabase()
    {
        R::nuke();
        // admin user
        $admin = R::dispense('user');
        $admin->email = 'info@example.com';
        $admin->name = 'admin';
        $admin->pw = 'admin';
        $admin->admin = true;
        
        $lngs = R::dispense('language', 2);

        // de
        $lngs[0]->iso = 'de';
        $lngs[0]->enabled = true;
        // en
        $lngs[1]->iso = 'en';
        $lngs[1]->enabled = true;
        
        // domains
        $domains = R::dispense('domain', 6);
        unset($domains[0]);
        // describe our hierarchy
        $domains[1]->name = 'system';
        $domains[1]->url = '/system';
        $domains[1]->sequence = 10;
        $domains[1]->invisible = false;
        $domains[1]->blessed = true;
    
        $domains[2]->name = 'file';
        $domains[2]->url = '/file';
        $domains[2]->sequence = 20;
        $domains[2]->invisible = false;
        
        $domains[3]->name = 'token';
        $domains[3]->url = '/token';
        $domains[3]->sequence = 10;
        $domains[3]->invisible = false;
        
        $domains[4]->name = 'user';
        $domains[4]->url = '/user';
        $domains[4]->sequence = 20;
        $domains[4]->invisible = false;
        
        $domains[5]->name = 'home';
        $domains[5]->url = '/home';
        $domains[5]->sequence = 10;
        $domains[5]->invisible = false;
        
        $domains[2]->ownDomain = array($domains[3], $domains[4]); // setting/token
        
        // structure our hierarchy
        $domains[1]->ownDomain = array($domains[5], $domains[2]); // system
        
        // try to save our hierarchy
        R::begin();
        try {
            R::store($admin);
            R::store($domains[1]);
            R::storeAll($lngs);
            R::commit();
        } catch (Exception $e) {
            R::rollback();
            throw new Exception($e);
        }
    }
}
