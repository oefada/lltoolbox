<?php
class Amenity extends AppModel
{
	public $name = 'Amenity';
	public $useTable = 'amenity';
	public $primaryKey = 'amenityId';
	public $displayField = 'amenityName';
	public $multisite = true;

	public $hasMany = array(
		'ClientAmenityRel' => array(
			'className'		=> 'ClientAmenityRel',
			'foreignKey'	=> 'amenityId'
		)
	);

	public $belongsTo = array(
		'amenityType' => array(
			'foreignKey' => 'amenityTypeId'
		)
	);

	/**
	 * @param	mixed $data
	 * @return	mixed
	 */
	public function afterFind($data)
	{
		$parentAmenities = $this->getParents();
		foreach($data as &$amenity) {
			if (isset($amenity['Amenity']['parentAmenityId'])) {
				$amenity['Amenity']['parentAmenity'] = $parentAmenities[$amenity['Amenity']['parentAmenityId']];
			} else {
				$amenity['Amenity']['parentAmenity'] = '';
			}
		}
		return $data;
	}

	/**
	 * @return array
	 */
	public function getParents()
	{
		$conditions = array(
			'Amenity.parentAmenityId IS NULL'
		);
		$fields = array('Amenity.amenityId', 'Amenity.amenityName');
		$params = array(
			'fields'		=> $fields,
			'conditions'	=> $conditions,
			'order'			=> 'Amenity.amenityName',
			'recursive'		=> -1,
			'callbacks' => false
		);

		return $this->find('list', $params);
	}
}
?>
