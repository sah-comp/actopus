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
 * The address model class.
 *
 * @package Cinnebar
 * @subpackage Model
 * @version $Id$
 */
class Model_Address extends Cinnebar_Model
{
    /**
     * Searches for given searchterm within bean and returns the result-set as an multi-dim array
     * after the given layout.
     *
     * @param string $term contains the searchterm as given by jQuery.autocomplete
     * @param string (optional) $layout defaults to "default"
     * @return array
     */
    public function clairvoyant($term, $layout = 'street')
    {   
        $sql = <<<SQL

        SELECT
            address.id AS id,
            CONCAT_WS(' ', address.street, address.zip, address.city, address.county, address.country) AS label,
            address.street AS street,
            address.country AS country,
            address.iso AS iso,
            address.county AS county,
            address.zip AS zip,
            address.city AS city

        FROM
            address

        WHERE
            {$layout} like ?

        ORDER BY
            {$layout}

SQL;
        return R::getAll($sql, array($term.'%'));
    }
    
	/**
	 * Returns an key/value array of contact infos for this bean.
	 *
	 * @return array $arrayOfContactInfos
	 */
	public function contactInfos()
	{
		return array(
			'address',
			'home',
			'work',
			'other'
		);
	}
	
	/**
	 * update.
	 *
	 * @uses formatAddress() to format the postal address depending on the country code
	 */
	public function update()
	{
        $this->bean->formattedaddress = $this->formatAddress();
        parent::update();
	}
	
	/**
	 * Generates a formatted address using a cinnebar formatter.
	 *
	 * The formmatter to be used is determined by the country code (iso) of this postal address.
	 * If no address formatter with the given country code can be found the address is formatted
	 * as if it was a german post office address.
	 *
	 * @return string $stringWithFormattedPostalAddress
	 */
	public function formatAddress()
	{
		$formatter_name = 'Formatter_Address_'.strtoupper($this->bean->iso);
        if ( ! class_exists($formatter_name, true)) {
            return sprintf("%s\n%s %s\n%s\n%s", $this->bean->street, $this->bean->zip, $this->bean->city, $this->bean->county,  $this->bean->country);
        }
        return with(new $formatter_name)->execute($this->bean);
	}
}
