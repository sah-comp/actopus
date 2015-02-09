<?php
/**
 * Setting Rbac page template.
 *
 * This template generates a form with sections for each role containing all domains with checkboxes
 * for the allowed actions.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php echo $this->partial('shared/html5/head') ?>
<?php echo $this->partial('shared/master/header') ?>

<div id="main" class="main">

    <form
    	id="setting-rbac"
    	class="panel"
    	action="<?php echo $this->url('/setting/rbac'); ?>"
    	method="post"
    	accept-charset="utf-8">


    	<fieldset>
    		<legend class="verbose"><?php echo __('setting_legend_rbac'); ?></legend>

    		<?php
    		foreach ($roles as $role_id=>$role_name):
    		?>
    		<table class="rbac">
    			<thead class="sticky">
    				<tr>
    					<th class="role">
    						<?php echo __('role_'.$role_name); ?>
    					</th>
    					<?php
    					foreach ($actions as $action_id=>$action_name):
    					?>
    					<th>
    						<?php echo __('action_'.$action_name); ?>
    					</th>
    					<?php
    					endforeach;
    					?>
    				</tr>
    			</thead>
    			<tfoot>
    			</tfoot>
    			<tbody>
    				<?php
    				foreach ($domains as $domain_id=>$domain_name):
    					if ( ! $rbac = R::findOne('rbac', ' role_id = ? AND domain_id = ? LIMIT 1', array($role_id, $domain_id))) {
    						$rbac = R::dispense('rbac');
    					}
    				?>
    				<tr>
    					<th class="domain">
    						<?php echo __('domain_'.$domain_name); ?>
    					</th>
    					<?php
    					foreach ($actions as $action_id=>$action_name):
    						if ( ! $permission = R::findOne('permission', ' rbac_id = ? AND action_id = ? LIMIT 1', array($rbac->getId(), $action_id))) {
    							$permission = R::dispense('permission');
    						}
    					?>
    					<td class="switch">

    						<input
    							type="hidden"
    							name="dialog[<?php echo $role_id; ?>][<?php echo $domain_id; ?>][<?php echo $action_id; ?>]"
    							value="0" />
    						<input
    							type="checkbox"
    							value="1"
    							name="dialog[<?php echo $role_id; ?>][<?php echo $domain_id; ?>][<?php echo $action_id; ?>]"
    							<?php echo ($permission->allow) ? self::CHECKED : ''; ?> />

    					</td>
    					<?php
    					endforeach;
    					?>
    				</tr>
    				<?php
    				endforeach;
    				?>
    			</tbody>
    		</table>
    		<?php
    		endforeach;
    		?>
    	</fieldset>

    	<div class="toolbar">
    		<input
    			type="submit"
    			id="submit"
    			class="default"
    			name="submit"
    			value="<?php echo __('rbac_submit'); ?>"
    			accesskey="s" />
    	</div>
    </form>

</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
